<?php

namespace App\Services;

use App\Models\Distributeur;
use App\Models\LevelCurrent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CumulManagementService
{
    /**
     * Gère le transfert des cumuls lors de la suppression d'un distributeur
     */
    public function handleDistributeurDeletion(Distributeur $distributeur, ?string $period = null): array
    {
        // Utiliser la période courante si non spécifiée
        $period = $period ?: date('Y-m');

        $result = [
            'success' => false,
            'transferred_amount' => 0,
            'affected_parent' => null,
            'message' => ''
        ];

        try {
            // Vérifier si le distributeur a un parent
            if (!$distributeur->id_distrib_parent) {
                $result['message'] = 'Distributeur sans parent, pas de transfert nécessaire';
                $result['success'] = true;
                return $result;
            }

            // Récupérer les cumuls du distributeur à supprimer
            $levelCurrent = LevelCurrent::where('distributeur_id', $distributeur->id)
                                        ->where('period', $period)
                                        ->first();

            if (!$levelCurrent || $levelCurrent->cumul_individuel == 0) {
                $result['message'] = 'Aucun cumul individuel à transférer pour cette période';
                $result['success'] = true;
                return $result;
            }

            // Transférer le cumul individuel au parent
            DB::transaction(function() use ($distributeur, $levelCurrent, $period, &$result) {
                // Mettre à jour le cumul individuel du parent
                $parentUpdated = LevelCurrent::where('distributeur_id', $distributeur->id_distrib_parent)
                                             ->where('period', $period)
                                             ->increment('cumul_individuel', $levelCurrent->cumul_individuel);

                if (!$parentUpdated) {
                    // Si le parent n'a pas d'enregistrement pour cette période, le créer
                    $parent = Distributeur::find($distributeur->id_distrib_parent);
                    if ($parent) {
                        LevelCurrent::create([
                            'distributeur_id' => $parent->id,
                            'period' => $period,
                            'rang' => 0,
                            'etoiles' => $parent->etoiles_id,
                            'cumul_individuel' => $levelCurrent->cumul_individuel,
                            'new_cumul' => 0,
                            'cumul_total' => $levelCurrent->cumul_individuel,
                            'cumul_collectif' => $levelCurrent->cumul_individuel,
                            'id_distrib_parent' => $parent->id_distrib_parent
                        ]);
                    }
                }

                // Réassigner les enfants au parent du distributeur supprimé
                if ($distributeur->children()->exists()) {
                    Distributeur::where('id_distrib_parent', $distributeur->id)
                                ->update(['id_distrib_parent' => $distributeur->id_distrib_parent]);

                    // Mettre à jour les level_currents des enfants
                    LevelCurrent::where('id_distrib_parent', $distributeur->id)
                                ->where('period', $period)
                                ->update(['id_distrib_parent' => $distributeur->id_distrib_parent]);
                }

                $result['success'] = true;
                $result['transferred_amount'] = $levelCurrent->cumul_individuel;
                $result['affected_parent'] = $distributeur->id_distrib_parent;
                $result['message'] = "Cumul individuel de {$levelCurrent->cumul_individuel} transféré avec succès";
            });

            // Log de l'opération
            Log::info("Transfert cumuls suppression distributeur", [
                'distributeur_id' => $distributeur->id,
                'matricule' => $distributeur->distributeur_id,
                'parent_id' => $distributeur->id_distrib_parent,
                'period' => $period,
                'amount_transferred' => $result['transferred_amount']
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur transfert cumuls suppression", [
                'distributeur_id' => $distributeur->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $result['message'] = "Erreur lors du transfert: " . $e->getMessage();
        }

        return $result;
    }

    /**
     * Recalcule les cumuls collectifs après un changement de parent
     */
    public function recalculateAfterParentChange(
        Distributeur $distributeur,
        int $oldParentId,
        int $newParentId,
        ?string $period = null
    ): array {
        $period = $period ?: date('Y-m');

        $result = [
            'success' => false,
            'old_parent_updated' => false,
            'new_parent_updated' => false,
            'amount_moved' => 0,
            'message' => ''
        ];

        try {
            // Calculer le total de la branche qui bouge
            $branchTotal = $this->calculateBranchTotal($distributeur->id, $period);

            if ($branchTotal == 0) {
                $result['message'] = 'Aucun cumul à déplacer pour cette période';
                $result['success'] = true;
                return $result;
            }

            DB::transaction(function() use ($oldParentId, $newParentId, $branchTotal, $period, &$result) {
                // 1. Soustraire de l'ancienne ligne parentale
                if ($oldParentId) {
                    $this->updateParentChainCumuls($oldParentId, -$branchTotal, $period);
                    $result['old_parent_updated'] = true;
                }

                // 2. Ajouter à la nouvelle ligne parentale
                if ($newParentId) {
                    $this->updateParentChainCumuls($newParentId, $branchTotal, $period);
                    $result['new_parent_updated'] = true;
                }

                $result['success'] = true;
                $result['amount_moved'] = $branchTotal;
                $result['message'] = "Cumuls recalculés avec succès";
            });

            Log::info("Recalcul cumuls après changement parent", [
                'distributeur_id' => $distributeur->id,
                'old_parent' => $oldParentId,
                'new_parent' => $newParentId,
                'period' => $period,
                'amount_moved' => $result['amount_moved']
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur recalcul cumuls changement parent", [
                'distributeur_id' => $distributeur->id,
                'error' => $e->getMessage()
            ]);

            $result['message'] = "Erreur lors du recalcul: " . $e->getMessage();
        }

        return $result;
    }

    /**
     * Calcule le total d'une branche (distributeur + tous ses descendants)
     */
    private function calculateBranchTotal(int $distributeurId, string $period): float
    {
        // Récupérer le cumul_total du distributeur pour cette période
        $levelCurrent = LevelCurrent::where('distributeur_id', $distributeurId)
                                    ->where('period', $period)
                                    ->first();

        // Le cumul_total représente déjà tout l'effort de la branche pour la période
        return $levelCurrent ? $levelCurrent->cumul_total : 0;
    }

    /**
     * Met à jour les cumuls de toute la chaîne parentale
     */
    private function updateParentChainCumuls(int $startParentId, float $amount, string $period): void
    {
        $currentParentId = $startParentId;
        $visited = [];

        while ($currentParentId && !in_array($currentParentId, $visited)) {
            $visited[] = $currentParentId;

            // Mettre à jour cumul_total (période courante) et cumul_collectif (cumulatif)
            $updated = LevelCurrent::where('distributeur_id', $currentParentId)
                                   ->where('period', $period)
                                   ->update([
                                       'cumul_total' => DB::raw("cumul_total + {$amount}"),
                                       'cumul_collectif' => DB::raw("cumul_collectif + {$amount}")
                                   ]);

            if (!$updated) {
                // Si pas d'enregistrement, en créer un
                $parent = Distributeur::find($currentParentId);
                if ($parent) {
                    LevelCurrent::create([
                        'distributeur_id' => $parent->id,
                        'period' => $period,
                        'rang' => 0,
                        'etoiles' => $parent->etoiles_id,
                        'cumul_individuel' => 0,
                        'new_cumul' => 0,
                        'cumul_total' => max(0, $amount),
                        'cumul_collectif' => max(0, $amount),
                        'id_distrib_parent' => $parent->id_distrib_parent
                    ]);
                }
            }

            // Monter au parent suivant
            $parent = Distributeur::find($currentParentId);
            $currentParentId = $parent ? $parent->id_distrib_parent : null;
        }
    }

    /**
     * Recalcule les cumuls individuels d'un distributeur
     * (cumul_individuel = cumul_total - somme des cumul_total des enfants directs)
     */
    public function recalculateIndividualCumul(int $distributeurId, string $period): float
    {
        $levelCurrent = LevelCurrent::where('distributeur_id', $distributeurId)
                                    ->where('period', $period)
                                    ->first();

        if (!$levelCurrent) {
            return 0;
        }

        // Somme des cumul_total des enfants directs
        $childrenTotal = LevelCurrent::where('id_distrib_parent', $distributeurId)
                                     ->where('period', $period)
                                     ->sum('cumul_total');

        // Calcul du cumul individuel
        $newIndividualCumul = $levelCurrent->cumul_total - $childrenTotal;

        // Mettre à jour si différent
        if ($levelCurrent->cumul_individuel != $newIndividualCumul) {
            $levelCurrent->update(['cumul_individuel' => $newIndividualCumul]);

            Log::info("Recalcul cumul individuel", [
                'distributeur_id' => $distributeurId,
                'period' => $period,
                'old_value' => $levelCurrent->cumul_individuel,
                'new_value' => $newIndividualCumul
            ]);
        }

        return $newIndividualCumul;
    }
}
