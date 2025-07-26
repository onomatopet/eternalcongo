<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemPeriod;
use App\Models\Achat;
use App\Models\Distributeur;
use App\Models\LevelCurrent;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkflowController extends Controller
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Affiche le tableau de bord du workflow pour une période
     */
    public function index()
    {
        $periods = SystemPeriod::orderBy('period', 'desc')->paginate(12);
        return view('workflow.index', compact('periods'));
    }

    /**
     * Affiche le détail du workflow pour une période spécifique
     */
    public function show($periodId)
    {
        $period = SystemPeriod::findOrFail($periodId);
        $workflowStatus = $this->workflowService->getWorkflowStatus($period);

        return view('workflow.show', compact('period', 'workflowStatus'));
    }

    /**
     * Valide les achats d'une période
     */
    public function validatePurchases(Request $request, $periodId)
    {
        $period = SystemPeriod::findOrFail($periodId);

        // Vérifier les prérequis
        if (!$this->workflowService->canValidatePurchases($period)) {
            return redirect()->back()->with('error', 'La période doit être active pour valider les achats.');
        }

        DB::beginTransaction();
        try {
            // Récupérer les achats à valider
            $achatsToValidate = Achat::where('period', $period->period)
                ->where('status', 'pending')
                ->get();

            $validated = 0;
            $rejected = 0;
            $errors = [];

            foreach ($achatsToValidate as $achat) {
                $validation = $this->validateSinglePurchase($achat);

                if ($validation['valid']) {
                    $achat->status = 'validated';
                    $achat->validated_at = now();
                    $validated++;
                } else {
                    $achat->status = 'rejected';
                    $achat->validated_at = now();
                    $achat->validation_errors = json_encode($validation['errors']);
                    $rejected++;
                    $errors[] = "Achat #{$achat->id}: " . implode(', ', $validation['errors']);
                }

                $achat->save();
            }

            // Mettre à jour le statut du workflow
            $period->purchases_validated = true;
            $period->purchases_validated_at = now();
            $period->purchases_validated_by = Auth::id();
            $period->save();

            DB::commit();

            $message = "Validation terminée: {$validated} validés, {$rejected} rejetés.";
            if (!empty($errors)) {
                $message .= " Erreurs: " . implode('; ', array_slice($errors, 0, 5));
            }

            return redirect()->route('workflow.show', $periodId)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Erreur validation achats période {$period->period}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la validation: ' . $e->getMessage());
        }
    }

    /**
     * Valide un achat individuel
     */
    protected function validateSinglePurchase(Achat $achat): array
    {
        $errors = [];

        // Vérifier que le distributeur existe
        if (!$achat->distributeur) {
            $errors[] = 'Distributeur introuvable';
        }

        // Vérifier que le produit existe
        if (!$achat->product) {
            $errors[] = 'Produit introuvable';
        }

        // Vérifier la cohérence des montants
        if ($achat->product) {
            $expectedTotal = $achat->qt * $achat->prix_unitaire_achat;
            if (abs($expectedTotal - $achat->montant_total_ligne) > 0.01) {
                $errors[] = 'Incohérence dans le calcul du montant total';
            }
        }

        // Vérifier la date d'achat
        if ($achat->purchase_date && $achat->purchase_date > now()) {
            $errors[] = 'Date d\'achat dans le futur';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Agrège les achats (calcul des cumuls)
     */
    public function aggregatePurchases(Request $request, $periodId)
    {
        $period = SystemPeriod::findOrFail($periodId);

        // Vérifier les prérequis
        if (!$this->workflowService->canAggregatePurchases($period)) {
            return redirect()->back()->with('error', 'Les achats doivent être validés avant l\'agrégation.');
        }

        DB::beginTransaction();
        try {
            // Récupérer tous les achats validés de la période
            $achatsValidated = Achat::where('period', $period->period)
                ->where('status', 'validated')
                ->get();

            // Grouper par distributeur
            $achatsParDistributeur = $achatsValidated->groupBy('distributeur_id');

            foreach ($achatsParDistributeur as $distributeurId => $achats) {
                // Calculer le total des points pour ce distributeur
                $totalPoints = $achats->sum('pointvaleur_total');

                // Mettre à jour ou créer le LevelCurrent
                $levelCurrent = LevelCurrent::firstOrNew([
                    'distributeur_id' => $distributeurId,
                    'period' => $period->period
                ]);

                // Si c'est une nouvelle entrée, initialiser avec les valeurs précédentes
                if (!$levelCurrent->exists) {
                    $previousLevel = LevelCurrent::where('distributeur_id', $distributeurId)
                        ->where('period', '<', $period->period)
                        ->orderBy('period', 'desc')
                        ->first();

                    if ($previousLevel) {
                        $levelCurrent->etoiles = $previousLevel->etoiles;
                        $levelCurrent->cumul_individuel = $previousLevel->cumul_individuel;
                        $levelCurrent->cumul_collectif = $previousLevel->cumul_collectif;
                        $levelCurrent->cumul_total = $previousLevel->cumul_total;
                    } else {
                        $levelCurrent->etoiles = 0;
                        $levelCurrent->cumul_individuel = 0;
                        $levelCurrent->cumul_collectif = 0;
                        $levelCurrent->cumul_total = 0;
                    }
                }

                // Ajouter les nouveaux achats
                $levelCurrent->new_cumul = $totalPoints;
                $levelCurrent->cumul_individuel += $totalPoints;
                $levelCurrent->cumul_total += $totalPoints;

                // Le cumul collectif sera calculé lors du ProcessAdvancements

                $levelCurrent->save();
            }

            // Calculer les cumuls collectifs (propagation dans l'arbre)
            $this->calculateCollectiveCumuls($period->period);

            // Mettre à jour le statut du workflow
            $period->purchases_aggregated = true;
            $period->purchases_aggregated_at = now();
            $period->purchases_aggregated_by = Auth::id();
            $period->save();

            DB::commit();

            return redirect()->route('workflow.show', $periodId)
                ->with('success', 'Agrégation des achats terminée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Erreur agrégation achats période {$period->period}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'agrégation: ' . $e->getMessage());
        }
    }

    /**
     * Calcule les cumuls collectifs par propagation dans l'arbre
     */
    protected function calculateCollectiveCumuls($period)
    {
        // Récupérer tous les distributeurs avec leurs parents
        $distributeurs = Distributeur::with('parent')->get();

        // Créer un mapping pour l'accès rapide
        $distributeursMap = $distributeurs->keyBy('id');

        // Récupérer tous les LevelCurrent de la période
        $levels = LevelCurrent::where('period', $period)->get()->keyBy('distributeur_id');

        // Calculer de bas en haut (des feuilles vers la racine)
        foreach ($distributeurs->sortByDesc('id') as $distributeur) {
            if (isset($levels[$distributeur->id])) {
                $level = $levels[$distributeur->id];

                // Initialiser le cumul collectif avec le cumul individuel
                $level->cumul_collectif = $level->cumul_individuel;

                // Ajouter les cumuls collectifs des enfants
                $enfants = $distributeurs->where('id_distrib_parent', $distributeur->id);
                foreach ($enfants as $enfant) {
                    if (isset($levels[$enfant->id])) {
                        $level->cumul_collectif += $levels[$enfant->id]->cumul_collectif;
                    }
                }

                $level->save();
            }
        }
    }

    /**
     * Lance le calcul des avancements
     */
    public function calculateAdvancements(Request $request, $periodId)
    {
        $period = SystemPeriod::findOrFail($periodId);

        if (!$this->workflowService->canCalculateAdvancements($period)) {
            return redirect()->back()->with('error', 'L\'agrégation doit être complétée avant le calcul des avancements.');
        }

        // Exécuter la commande ProcessAdvancements
        \Artisan::call('app:process-advancements', [
            'period' => $period->period,
            '--type' => 'validated_only',
            '--force' => true
        ]);

        // Mettre à jour le statut
        $period->advancements_calculated = true;
        $period->advancements_calculated_at = now();
        $period->advancements_calculated_by = Auth::id();
        $period->save();

        return redirect()->route('workflow.show', $periodId)
            ->with('success', 'Calcul des avancements terminé.');
    }

    /**
     * Crée un snapshot de la période
     */
    public function createSnapshot(Request $request, $periodId)
    {
        $period = SystemPeriod::findOrFail($periodId);

        if (!$this->workflowService->canCreateSnapshot($period)) {
            return redirect()->back()->with('error', 'Les avancements doivent être calculés avant la création du snapshot.');
        }

        DB::beginTransaction();
        try {
            // Copier les données vers les tables d'historique
            DB::statement("
                INSERT INTO LevelCurrent_histories
                SELECT * FROM LevelCurrents
                WHERE period = ?
            ", [$period->period]);

            // Archiver les achats
            DB::statement("
                INSERT INTO achats_archives
                SELECT * FROM achats
                WHERE period = ?
            ", [$period->period]);

            // Mettre à jour le statut
            $period->snapshot_created = true;
            $period->snapshot_created_at = now();
            $period->snapshot_created_by = Auth::id();
            $period->save();

            DB::commit();

            return redirect()->route('workflow.show', $periodId)
                ->with('success', 'Snapshot créé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Erreur création snapshot période {$period->period}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la création du snapshot: ' . $e->getMessage());
        }
    }

    /**
     * Clôture une période
     */
    public function closePeriod(Request $request, $periodId)
    {
        $period = SystemPeriod::findOrFail($periodId);

        if (!$this->workflowService->canClosePeriod($period)) {
            return redirect()->back()->with('error', 'Toutes les étapes doivent être complétées avant la clôture.');
        }

        $period->status = 'closed';
        $period->closed_at = now();
        $period->save();

        return redirect()->route('workflow.index')
            ->with('success', 'Période clôturée avec succès.');
    }
}
