<?php
// app/Http/Controllers/Admin/WorkflowController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemPeriod;
use App\Models\WorkflowLog;
use App\Models\Achat;
use App\Services\PeriodManagementService;
use App\Services\PurchaseValidationService;
use App\Services\PurchaseAggregationService;
use App\Services\SnapshotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class WorkflowController extends Controller
{
    protected PeriodManagementService $periodService;
    protected PurchaseValidationService $validationService;
    protected PurchaseAggregationService $aggregationService;
    protected SnapshotService $snapshotService;

    public function __construct(
        PeriodManagementService $periodService,
        PurchaseValidationService $validationService,
        PurchaseAggregationService $aggregationService,
        SnapshotService $snapshotService
    ) {
        $this->periodService = $periodService;
        $this->validationService = $validationService;
        $this->aggregationService = $aggregationService;
        $this->snapshotService = $snapshotService;
    }

    /**
     * Affiche le tableau de bord du workflow
     */
    public function index(Request $request)
    {
        $period = $request->get('period');

        if (!$period) {
            $currentPeriod = SystemPeriod::getCurrentPeriod();
            if ($currentPeriod) {
                $period = $currentPeriod->period;
            } else {
                return redirect()->route('admin.periods.index')
                    ->with('error', 'Aucune période active trouvée.');
            }
        }

        $systemPeriod = SystemPeriod::where('period', $period)->first();

        if (!$systemPeriod) {
            return redirect()->route('admin.periods.index')
                ->with('error', 'Période invalide.');
        }

        // Récupérer les statistiques pour chaque étape
        $stats = [
            'validation' => $this->validationService->getValidationStats($period),
            'aggregation' => $this->getAggregationStats($period),
            'advancement' => $this->getAdvancementStats($period),
            'snapshot' => $this->getSnapshotStats($period),
        ];

        // Récupérer les logs récents
        $recentLogs = WorkflowLog::forPeriod($period)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Récupérer toutes les périodes pour le sélecteur
        $allPeriods = SystemPeriod::orderBy('period', 'desc')->pluck('period');

        return view('admin.workflow.index', compact(
            'systemPeriod',
            'period',
            'stats',
            'recentLogs',
            'allPeriods'
        ));
    }

    /**
     * Valide les achats de la période
     */
    public function validatePurchases(Request $request)
    {
        $period = $request->input('period');
        $systemPeriod = SystemPeriod::where('period', $period)->first();

        if (!$systemPeriod || !$systemPeriod->canValidatePurchases()) {
            return redirect()->back()
                ->with('error', 'Impossible de valider les achats pour cette période.');
        }

        // Logger le début
        $log = WorkflowLog::logStart(
            $period,
            WorkflowLog::STEP_VALIDATION,
            'validate_all',
            Auth::id(),
            ['total_purchases' => Achat::where('period', $period)->count()]
        );

        try {
            // Exécuter la validation
            $result = $this->validationService->validatePeriodPurchases($period);

            if ($result['success']) {
                // Mettre à jour le statut
                $systemPeriod->updateWorkflowStep('purchases_validated', Auth::id());

                // Logger le succès
                $log->complete([
                    'validated' => $result['validated'],
                    'rejected' => $result['rejected']
                ]);

                return redirect()->back()
                    ->with('success', $result['message']);
            } else {
                $log->fail($result['message']);
                return redirect()->back()
                    ->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            $log->fail($e->getMessage());
            Log::error('Erreur workflow validation', [
                'period' => $period,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la validation.');
        }
    }

    /**
     * Agrège les achats dans la hiérarchie
     */
    public function aggregatePurchases(Request $request)
    {
        $period = $request->input('period');
        $systemPeriod = SystemPeriod::where('period', $period)->first();

        if (!$systemPeriod || !$systemPeriod->canAggregatePurchases()) {
            return redirect()->back()
                ->with('error', 'Impossible d\'agréger les achats pour cette période.');
        }

        $log = WorkflowLog::logStart(
            $period,
            WorkflowLog::STEP_AGGREGATION,
            'aggregate_all',
            Auth::id()
        );

        try {
            // Exécuter l'agrégation
            $result = $this->aggregationService->aggregateAndApplyPurchases($period);

            // La méthode retourne toujours un array avec 'message' et 'active_distributors_details'
            $success = !isset($result['error']);

            if ($success) {
                // Propager dans la hiérarchie
                $this->propagateCumuls($period);

                // Mettre à jour le statut
                $systemPeriod->updateWorkflowStep('purchases_aggregated', Auth::id());

                $log->complete([
                    'distributors_processed' => $result['active_distributors_details']->count()
                ]);

                return redirect()->back()
                    ->with('success', $result['message']);
            } else {
                $log->fail($result['error'] ?? 'Erreur inconnue');
                return redirect()->back()
                    ->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            $log->fail($e->getMessage());
            Log::error('Erreur workflow agrégation', [
                'period' => $period,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'agrégation.');
        }
    }

    /**
     * Calcule les avancements de grade
     */
    public function calculateAdvancements(Request $request)
    {
        $period = $request->input('period');
        $systemPeriod = SystemPeriod::where('period', $period)->first();

        if (!$systemPeriod || !$systemPeriod->canCalculateAdvancements()) {
            return redirect()->back()
                ->with('error', 'Impossible de calculer les avancements pour cette période.');
        }

        $log = WorkflowLog::logStart(
            $period,
            WorkflowLog::STEP_ADVANCEMENT,
            'calculate_all',
            Auth::id()
        );

        try {
            // Exécuter la commande d'avancement
            $exitCode = Artisan::call('mlm:process-advancements-all', [
                'period' => $period,
                '--validated-only' => true,
                '--force' => true
            ]);

            if ($exitCode === 0) {
                // Récupérer le nombre d'avancements
                $advancements = DB::table('avancement_history')
                    ->where('period', $period)
                    ->count();

                // Mettre à jour le statut
                $systemPeriod->updateWorkflowStep('advancements_calculated', Auth::id());

                $log->complete([
                    'advancements' => $advancements
                ]);

                return redirect()->back()
                    ->with('success', "Calcul des avancements terminé. {$advancements} promotions effectuées.");
            } else {
                $log->fail('Erreur lors de l\'exécution de la commande');
                return redirect()->back()
                    ->with('error', 'Erreur lors du calcul des avancements.');
            }

        } catch (\Exception $e) {
            $log->fail($e->getMessage());
            Log::error('Erreur workflow avancements', [
                'period' => $period,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors du calcul des avancements.');
        }
    }

    /**
     * Crée le snapshot de la période
     */
    public function createSnapshot(Request $request)
    {
        $period = $request->input('period');
        $systemPeriod = SystemPeriod::where('period', $period)->first();

        if (!$systemPeriod || !$systemPeriod->canCreateSnapshot()) {
            return redirect()->back()
                ->with('error', 'Impossible de créer le snapshot pour cette période.');
        }

        $log = WorkflowLog::logStart(
            $period,
            WorkflowLog::STEP_SNAPSHOT,
            'create',
            Auth::id()
        );

        try {
            // Créer le snapshot (forcer si existe déjà)
            $result = $this->snapshotService->createSnapshot($period, true);

            if ($result['success']) {
                // Mettre à jour le statut
                $systemPeriod->updateWorkflowStep('snapshot_created', Auth::id());

                $log->complete([
                    'records_created' => $result['count']
                ]);

                return redirect()->back()
                    ->with('success', $result['message']);
            } else {
                $log->fail($result['message']);
                return redirect()->back()
                    ->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            $log->fail($e->getMessage());
            Log::error('Erreur workflow snapshot', [
                'period' => $period,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création du snapshot.');
        }
    }

    /**
     * Clôture la période
     */
    public function closePeriod(Request $request)
    {
        $period = $request->input('period');
        $systemPeriod = SystemPeriod::where('period', $period)->first();

        if (!$systemPeriod || !$systemPeriod->canClose()) {
            return redirect()->back()
                ->with('error', 'Impossible de clôturer cette période.');
        }

        $log = WorkflowLog::logStart(
            $period,
            WorkflowLog::STEP_CLOSURE,
            'close',
            Auth::id()
        );

        try {
            // Utiliser le service modifié qui ne fait plus l'agrégation ni le snapshot
            $result = $this->periodService->closePeriod($period, Auth::id());

            if ($result['success']) {
                $log->complete([
                    'next_period' => $result['next_period'],
                    'summary' => $result['summary']
                ]);

                return redirect()->route('admin.workflow.index', ['period' => $result['next_period']])
                    ->with('success', $result['message']);
            } else {
                $log->fail($result['message']);
                return redirect()->back()
                    ->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            $log->fail($e->getMessage());
            Log::error('Erreur workflow clôture', [
                'period' => $period,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la clôture.');
        }
    }

    /**
     * Affiche l'historique des actions pour une période
     */
    public function history(Request $request, string $period)
    {
        $logs = WorkflowLog::forPeriod($period)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.workflow.history', compact('period', 'logs'));
    }

    /**
     * Méthodes privées pour les statistiques
     */
    private function getAggregationStats(string $period): array
    {
        $totalDistributors = DB::table('distributeurs')->count();
        $processedDistributors = DB::table('level_currents')
            ->where('period', $period)
            ->where('new_cumul', '>', 0)
            ->count();

        return [
            'total' => $totalDistributors,
            'processed' => $processedDistributors,
            'progress' => $totalDistributors > 0 ? round(($processedDistributors / $totalDistributors) * 100, 2) : 0
        ];
    }

    private function getAdvancementStats(string $period): array
    {
        $advancements = DB::table('avancement_history')
            ->where('period', $period)
            ->selectRaw('COUNT(*) as total, MAX(nouveau_grade) as highest_grade')
            ->first();

        return [
            'total' => $advancements->total ?? 0,
            'highest_grade' => $advancements->highest_grade ?? 0
        ];
    }

    private function getSnapshotStats(string $period): array
    {
        $snapshotCount = DB::table('level_current_histories')
            ->where('period', $period)
            ->count();

        return [
            'exists' => $snapshotCount > 0,
            'records' => $snapshotCount
        ];
    }

    private function propagateCumuls(string $period): void
    {
        // Cette méthode est normalement dans PeriodManagementService
        // mais on peut l'appeler directement ici si nécessaire
        $activeDistributors = Achat::where('period', $period)
            ->distinct('distributeur_id')
            ->pluck('distributeur_id');

        foreach ($activeDistributors as $distributorId) {
            $totalAchats = Achat::where('distributeur_id', $distributorId)
                               ->where('period', $period)
                               ->sum(DB::raw('points_unitaire_achat * qt'));

            if ($totalAchats > 0) {
                // Utiliser le service de cumul si disponible
                // $this->cumulManagement->propagateToParents($distributorId, $totalAchats, $period);
            }
        }
    }
}
