<?php
// app/Services/PeriodManagementService.php

namespace App\Services;

use App\Models\SystemPeriod;
use App\Models\LevelCurrent;
use App\Models\Distributeur;
use App\Models\Achat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PeriodManagementService
{
    protected PurchaseAggregationService $purchaseAggregation;
    protected CumulManagementService $cumulManagement;

    public function __construct(
        PurchaseAggregationService $purchaseAggregation,
        CumulManagementService $cumulManagement
    ) {
        $this->purchaseAggregation = $purchaseAggregation;
        $this->cumulManagement = $cumulManagement;
    }

    /**
     * Clôture la période courante et initialise la suivante
     */
    public function closePeriod(string $currentPeriod, int $userId): array
    {
        $period = SystemPeriod::where('period', $currentPeriod)
                             ->where('is_current', true)
                             ->first();

        if (!$period || !$period->canBeClosed()) {
            return [
                'success' => false,
                'message' => 'La période ne peut pas être clôturée. Elle doit être en phase de validation.'
            ];
        }

        DB::beginTransaction();
        try {
            // 1. Exécuter l'agrégation finale en batch
            Log::info("Début de la clôture de période: {$currentPeriod}");
            $aggregationResult = $this->purchaseAggregation->aggregateAndApplyPurchases($currentPeriod);

            // 2. Propager les cumuls dans la hiérarchie
            $this->propagateCumulsInHierarchy($currentPeriod);

            // 3. Créer le résumé de clôture
            $closureSummary = $this->generateClosureSummary($currentPeriod);

            // 4. Marquer la période comme clôturée
            $period->update([
                'status' => SystemPeriod::STATUS_CLOSED,
                'closed_at' => now(),
                'closed_by_user_id' => $userId,
                'closure_summary' => $closureSummary,
                'is_current' => false
            ]);

            // 5. Créer et initialiser la nouvelle période
            $nextPeriod = $this->initializeNextPeriod($currentPeriod);

            DB::commit();

            return [
                'success' => true,
                'message' => "Période {$currentPeriod} clôturée avec succès. Nouvelle période: {$nextPeriod}",
                'summary' => $closureSummary,
                'next_period' => $nextPeriod
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la clôture de période", [
                'period' => $currentPeriod,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la clôture: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Propage les cumuls dans toute la hiérarchie après agrégation
     */
    protected function propagateCumulsInHierarchy(string $period): void
    {
        // Récupérer tous les distributeurs ayant eu des achats dans la période
        $activeDistributors = Achat::where('period', $period)
            ->distinct('distributeur_id')
            ->pluck('distributeur_id');

        foreach ($activeDistributors as $distributorId) {
            // Récupérer le montant total des achats du distributeur
            $totalAchats = Achat::where('distributeur_id', $distributorId)
                               ->where('period', $period)
                               ->sum('pointvaleur');

            if ($totalAchats > 0) {
                // Propager dans la hiérarchie parentale
                $this->cumulManagement->propagateToParents($distributorId, $totalAchats, $period);
            }
        }

        Log::info("Propagation des cumuls terminée pour {$activeDistributors->count()} distributeurs actifs");
    }

    /**
     * Génère le résumé de clôture
     */
    protected function generateClosureSummary(string $period): array
    {
        return [
            'total_distributeurs_actifs' => Achat::where('period', $period)
                                                ->distinct('distributeur_id')
                                                ->count(),
            'total_achats' => Achat::where('period', $period)->count(),
            'total_points' => Achat::where('period', $period)->sum('pointvaleur'),
            'total_montant' => Achat::where('period', $period)->sum('montant_total_ligne'),
            'nouveaux_grades' => DB::table('avancement_histories')
                                  ->where('period', $period)
                                  ->count(),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Initialise la période suivante avec report des cumuls
     */
    protected function initializeNextPeriod(string $currentPeriod): string
    {
        $current = Carbon::createFromFormat('Y-m', $currentPeriod);
        $next = $current->addMonth();
        $nextPeriod = $next->format('Y-m');

        // 1. Créer la nouvelle période système
        SystemPeriod::create([
            'period' => $nextPeriod,
            'status' => SystemPeriod::STATUS_OPEN,
            'opened_at' => $next->startOfMonth(),
            'is_current' => true
        ]);

        // 2. Reporter les cumuls de tous les distributeurs actifs
        $this->carryOverCumuls($currentPeriod, $nextPeriod);

        return $nextPeriod;
    }

    /**
     * Reporte les cumuls de la période précédente
     */
    protected function carryOverCumuls(string $fromPeriod, string $toPeriod): void
    {
        // Récupérer tous les level_currents de la période clôturée
        $levelCurrents = LevelCurrent::where('period', $fromPeriod)->get();

        $insertData = [];
        foreach ($levelCurrents as $level) {
            $insertData[] = [
                'distributeur_id' => $level->distributeur_id,
                'period' => $toPeriod,
                'rang' => $level->rang,
                'etoiles' => $level->etoiles,
                'cumul_individuel' => $level->cumul_individuel, // Report du cumul
                'new_cumul' => 0, // Remis à zéro pour la nouvelle période
                'cumul_total' => 0, // Remis à zéro pour la nouvelle période
                'cumul_collectif' => $level->cumul_collectif, // Report du cumul historique
                'id_distrib_parent' => $level->id_distrib_parent,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Insertion en batch
        foreach (array_chunk($insertData, 1000) as $chunk) {
            LevelCurrent::insert($chunk);
        }

        Log::info("Report des cumuls effectué pour " . count($insertData) . " distributeurs vers la période {$toPeriod}");
    }

    /**
     * Passe une période en mode validation
     */
    public function startValidationPhase(string $period): array
    {
        $systemPeriod = SystemPeriod::where('period', $period)
                                   ->where('is_current', true)
                                   ->first();

        if (!$systemPeriod || $systemPeriod->status !== SystemPeriod::STATUS_OPEN) {
            return [
                'success' => false,
                'message' => 'La période doit être ouverte pour passer en validation'
            ];
        }

        $systemPeriod->update([
            'status' => SystemPeriod::STATUS_VALIDATION,
            'validation_started_at' => now()
        ]);

        return [
            'success' => true,
            'message' => "Période {$period} passée en phase de validation"
        ];
    }
}
