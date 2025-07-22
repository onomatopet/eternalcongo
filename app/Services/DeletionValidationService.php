<?php

namespace App\Services;

use App\Models\Distributeur;
use App\Models\Achat;
use App\Models\Bonus;
use App\Models\LevelCurrent;
use Illuminate\Support\Facades\DB;

class DeletionValidationService
{
    /**
     * Valide si un distributeur peut être supprimé
     */
    public function validateDistributeurDeletion(Distributeur $distributeur): array
    {
        $validationResult = [
            'can_delete' => true,
            'warnings' => [],
            'blockers' => [],
            'related_data' => [],
            'impact_analysis' => []
        ];

        // 1. Vérifier les enfants directs
        $children = $distributeur->children()->with(['achats', 'bonuses'])->get();
        if ($children->count() > 0) {
            $validationResult['blockers'][] = [
                'type' => 'children_exist',
                'message' => "Ce distributeur a {$children->count()} enfant(s) direct(s) dans le réseau",
                'details' => $children->map(fn($child) => [
                    'id' => $child->id,
                    'matricule' => $child->distributeur_id,
                    'nom' => $child->full_name,
                    'achats_count' => $child->achats->count(),
                    'bonus_count' => $child->bonuses->count()
                ])->toArray(),
                'suggested_action' => 'Réassigner les enfants à un autre parent ou les supprimer d\'abord'
            ];
            $validationResult['can_delete'] = false;
        }

        // 2. Vérifier les achats
        $achats = $distributeur->achats()->get();
        if ($achats->count() > 0) {
            $totalMontant = $achats->sum('montant_total_ligne');
            $periodesImpactees = $achats->pluck('period')->unique();
            
            $validationResult['blockers'][] = [
                'type' => 'achats_exist',
                'message' => "Ce distributeur a {$achats->count()} achat(s) enregistré(s)",
                'details' => [
                    'total_achats' => $achats->count(),
                    'montant_total' => $totalMontant,
                    'periodes_impactees' => $periodesImpactees->toArray(),
                    'dernier_achat' => optional($achats->sortByDesc('created_at')->first())->created_at
                ],
                'suggested_action' => 'Supprimer ou archiver les achats d\'abord'
            ];
            $validationResult['can_delete'] = false;
            $validationResult['related_data']['achats'] = $achats->toArray();
        }

        // 3. Vérifier les bonus
        $bonuses = $distributeur->bonuses()->get();
        if ($bonuses->count() > 0) {
            $totalBonus = $bonuses->sum('montant');
            
            $validationResult['blockers'][] = [
                'type' => 'bonuses_exist',
                'message' => "Ce distributeur a {$bonuses->count()} bonus enregistré(s)",
                'details' => [
                    'total_bonus' => $bonuses->count(),
                    'montant_total' => $totalBonus,
                    'dernier_bonus' => optional($bonuses->sortByDesc('created_at')->first())->created_at
                ],
                'suggested_action' => 'Vérifier et archiver les bonus d\'abord'
            ];
            $validationResult['can_delete'] = false;
            $validationResult['related_data']['bonuses'] = $bonuses->toArray();
        }

        // 4. Vérifier les données de niveau actuel
        $levelCurrents = $distributeur->levelCurrents()->get();
        if ($levelCurrents->count() > 0) {
            $validationResult['warnings'][] = [
                'type' => 'level_currents_exist',
                'message' => "Ce distributeur a des données de performance sur {$levelCurrents->count()} période(s)",
                'details' => $levelCurrents->map(fn($lc) => [
                    'period' => $lc->period,
                    'etoiles' => $lc->etoiles,
                    'cumul_individuel' => $lc->cumul_individuel,
                    'cumul_collectif' => $lc->cumul_collectif
                ])->toArray(),
                'suggested_action' => 'Ces données seront archivées lors de la suppression'
            ];
            $validationResult['related_data']['level_currents'] = $levelCurrents->toArray();
        }

        // 5. Analyser l'impact sur la hiérarchie
        $this->analyzeHierarchyImpact($distributeur, $validationResult);

        // 6. Vérifier les dépendances dans d'autres tables
        $this->checkOtherDependencies($distributeur, $validationResult);

        return $validationResult;
    }

    /**
     * Analyse l'impact sur la hiérarchie
     */
    private function analyzeHierarchyImpact(Distributeur $distributeur, array &$validationResult): void
    {
        // Analyser la profondeur de l'arbre en cas de suppression
        $descendants = $this->getAllDescendants($distributeur);
        $descendantsCount = $descendants->count();

        if ($descendantsCount > 0) {
            $validationResult['impact_analysis']['hierarchy'] = [
                'total_descendants' => $descendantsCount,
                'max_depth' => $this->calculateMaxDepth($descendants),
                'active_descendants' => $descendants->filter(function($desc) {
                    return $desc->achats()->where('period', date('Y-m'))->exists();
                })->count(),
                'warning' => $descendantsCount > 10 ? 'Impact majeur sur la hiérarchie' : 'Impact mineur'
            ];

            if ($descendantsCount > 50) {
                $validationResult['warnings'][] = [
                    'type' => 'major_hierarchy_impact',
                    'message' => "La suppression affectera {$descendantsCount} distributeurs dans la descendance",
                    'suggested_action' => 'Considérer une désactivation plutôt qu\'une suppression'
                ];
            }
        }

        // Vérifier si c'est un nœud important dans la hiérarchie
        if ($distributeur->children()->count() > 5) {
            $validationResult['warnings'][] = [
                'type' => 'important_node',
                'message' => 'Ce distributeur est un nœud important avec plusieurs enfants directs',
                'suggested_action' => 'Vérifier la réorganisation de la hiérarchie'
            ];
        }
    }

    /**
     * Vérifier les dépendances dans d'autres tables
     */
    private function checkOtherDependencies(Distributeur $distributeur, array &$validationResult): void
    {
        // Vérifier les références dans level_current_histories
        $historyCount = DB::table('level_current_histories')
            ->where('distributeur_id', $distributeur->id)
            ->count();

        if ($historyCount > 0) {
            $validationResult['warnings'][] = [
                'type' => 'history_records',
                'message' => "{$historyCount} enregistrement(s) d'historique seront orphelins",
                'suggested_action' => 'Ces enregistrements seront conservés pour l\'audit'
            ];
        }

        // Vérifier si le distributeur est référencé comme parent
        $isParentCount = Distributeur::where('id_distrib_parent', $distributeur->id)->count();
        if ($isParentCount > 0) {
            // Ce cas est déjà couvert par la vérification des enfants, mais on l'ajoute pour la cohérence
            $validationResult['related_data']['as_parent'] = $isParentCount;
        }
    }

    /**
     * Propose des actions de nettoyage avant suppression
     */
    public function suggestCleanupActions(array $validationResult): array
    {
        $actions = [];

        foreach ($validationResult['blockers'] as $blocker) {
            switch ($blocker['type']) {
                case 'children_exist':
                    $actions[] = [
                        'action' => 'reassign_children',
                        'description' => 'Réassigner les enfants à un autre parent',
                        'priority' => 'high',
                        'estimated_time' => '5-10 minutes'
                    ];
                    break;

                case 'achats_exist':
                    $actions[] = [
                        'action' => 'archive_achats',
                        'description' => 'Archiver ou supprimer les achats existants',
                        'priority' => 'high',
                        'estimated_time' => '2-5 minutes'
                    ];
                    break;

                case 'bonuses_exist':
                    $actions[] = [
                        'action' => 'handle_bonuses',
                        'description' => 'Vérifier et traiter les bonus existants',
                        'priority' => 'medium',
                        'estimated_time' => '5-15 minutes'
                    ];
                    break;
            }
        }

        return $actions;
    }

    /**
     * Utilitaires privés
     */
    private function getAllDescendants(Distributeur $distributeur): \Illuminate\Database\Eloquent\Collection
    {
        $descendants = collect();
        $this->collectDescendants($distributeur, $descendants);
        return $descendants;
    }

    private function collectDescendants(Distributeur $distributeur, &$descendants): void
    {
        $children = $distributeur->children()->with('achats')->get();
        
        foreach ($children as $child) {
            $descendants->push($child);
            $this->collectDescendants($child, $descendants);
        }
    }

    private function calculateMaxDepth(Collection $descendants): int
    {
        // Calcul simplifié de la profondeur maximale
        return $descendants->map(function($desc) {
            $depth = 0;
            $current = $desc;
            while ($current->parent && $depth < 20) { // Protection contre boucles infinies
                $current = $current->parent;
                $depth++;
            }
            return $depth;
        })->max() ?? 0;
    }
}