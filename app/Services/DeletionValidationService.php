<?php

namespace App\Services;

use App\Models\Distributeur;
use App\Models\Achat;
use App\Models\Product;
use App\Models\Bonus;
use App\Models\LevelCurrent;
use App\Models\AvancementHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeletionValidationService
{
    /**
     * Valide la suppression d'un distributeur
     */
    public function validateDistributeurDeletion(Distributeur $distributeur): array
    {
        $blockers = [];
        $warnings = [];
        $relatedData = [];

        // 1. Vérifier les enfants directs
        $children = Distributeur::where('parent_id', $distributeur->id)->get();
        if ($children->count() > 0) {
            $blockers[] = "Ce distributeur a {$children->count()} distributeur(s) enfant(s) qui doivent être réassignés";
            $relatedData['children'] = $children->toArray();
        }

        // 2. Vérifier les achats
        $achats = Achat::where('distributeur_id', $distributeur->id)->get();
        if ($achats->count() > 0) {
            $totalAmount = $achats->sum('montant_total_ligne');
            $warnings[] = "Ce distributeur a {$achats->count()} achat(s) pour un montant total de " . number_format($totalAmount, 0, ',', ' ') . " F CFA";
            $relatedData['achats'] = $achats->toArray();
        }

        // 3. Vérifier les bonus
        $bonuses = Bonus::where('distributeur_id', $distributeur->id)->get();
        if ($bonuses->count() > 0) {
            $totalBonus = $bonuses->sum('montant_total');
            $warnings[] = "Ce distributeur a reçu {$bonuses->count()} bonus pour un montant total de " . number_format($totalBonus, 0, ',', ' ') . " F CFA";
            $relatedData['bonuses'] = $bonuses->toArray();
        }

        // 4. Vérifier le niveau actuel
        $currentLevel = LevelCurrent::where('distributeur_id', $distributeur->id)->first();
        if ($currentLevel && $currentLevel->etoiles >= 3) {
            $warnings[] = "Ce distributeur a un grade élevé ({$currentLevel->etoiles} étoiles)";
        }

        // 5. Vérifier l'historique des avancements
        $advancements = AvancementHistory::where('distributeur_id', $distributeur->id)->count();
        if ($advancements > 0) {
            $warnings[] = "Ce distributeur a {$advancements} avancement(s) dans l'historique";
        }

        // 6. Vérifier si c'est un top performer
        $isTopPerformer = $this->checkIfTopPerformer($distributeur);
        if ($isTopPerformer) {
            $warnings[] = "Ce distributeur fait partie des top performers du réseau";
        }

        // 7. Calculer l'impact sur le réseau
        $networkImpact = $this->calculateNetworkImpact($distributeur);
        if ($networkImpact['affected_count'] > 10) {
            $warnings[] = "La suppression affectera {$networkImpact['affected_count']} distributeurs dans le réseau";
        }

        // Déterminer si la suppression est possible
        $canDelete = count($blockers) === 0;
        $requiresApproval = count($warnings) > 0 || count($blockers) > 0;

        // Calculer le niveau d'impact
        $impactLevel = $this->calculateImpactLevel($blockers, $warnings, $relatedData);

        return [
            'can_delete' => $canDelete,
            'requires_approval' => $requiresApproval,
            'blockers' => $blockers,
            'warnings' => $warnings,
            'impact_level' => $impactLevel,
            'related_data' => $relatedData,
            'network_impact' => $networkImpact,
            'summary' => $this->generateSummary($distributeur, $blockers, $warnings)
        ];
    }

    /**
     * Valide la suppression d'un achat
     */
    public function validateAchatDeletion(Achat $achat): array
    {
        $blockers = [];
        $warnings = [];
        $relatedData = [];

        // 1. Vérifier si l'achat est validé
        if ($achat->validated) {
            $warnings[] = "Cet achat a déjà été validé et pris en compte dans les calculs";
        }

        // 2. Vérifier la période
        $currentPeriod = date('Y-m');
        if ($achat->period === $currentPeriod) {
            $warnings[] = "Cet achat appartient à la période en cours";
        }

        // 3. Vérifier l'impact sur les bonus
        $relatedBonuses = Bonus::where('period', $achat->period)
            ->where('distributeur_id', $achat->distributeur_id)
            ->exists();

        if ($relatedBonuses) {
            $blockers[] = "Des bonus ont déjà été calculés pour cette période. Recalcul nécessaire après suppression";
        }

        // 4. Vérifier l'impact sur les grades
        $levelHistory = LevelCurrent::where('distributeur_id', $achat->distributeur_id)
            ->where('period', $achat->period)
            ->first();

        if ($levelHistory) {
            $warnings[] = "La suppression pourrait affecter le grade du distributeur pour cette période";
        }

        // 5. Calculer l'impact financier
        $financialImpact = [
            'montant' => $achat->montant_total_ligne,
            'pv' => $achat->pv_total,
            'period' => $achat->period
        ];
        $relatedData['financial_impact'] = $financialImpact;

        $canDelete = count($blockers) === 0;
        $requiresApproval = $achat->validated || count($warnings) > 0;
        $impactLevel = $this->calculateImpactLevel($blockers, $warnings, $relatedData);

        return [
            'can_delete' => $canDelete,
            'requires_approval' => $requiresApproval,
            'blockers' => $blockers,
            'warnings' => $warnings,
            'impact_level' => $impactLevel,
            'related_data' => $relatedData,
            'summary' => "Suppression de l'achat #{$achat->id} - Montant: " . number_format($achat->montant_total_ligne, 0, ',', ' ') . " F CFA"
        ];
    }

    /**
     * Suggère des actions de nettoyage
     */
    public function suggestCleanupActions(array $validationResult): array
    {
        $actions = [];

        foreach ($validationResult['blockers'] as $blocker) {
            if (strpos($blocker, 'enfant') !== false) {
                $actions[] = [
                    'type' => 'reassign_children',
                    'description' => 'Réassigner les distributeurs enfants à un autre parent',
                    'priority' => 'high',
                    'action_url' => route('admin.distributeurs.reassign-children')
                ];
            }
        }

        foreach ($validationResult['warnings'] as $warning) {
            if (strpos($warning, 'bonus') !== false) {
                $actions[] = [
                    'type' => 'recalculate_bonuses',
                    'description' => 'Recalculer les bonus après suppression',
                    'priority' => 'medium',
                    'action_url' => route('admin.bonuses.recalculate')
                ];
            }
        }

        return $actions;
    }

    /**
     * Vérifie si un distributeur est un top performer
     */
    private function checkIfTopPerformer(Distributeur $distributeur): bool
    {
        // Top 10 par cumul collectif
        $topByCumul = LevelCurrent::where('period', date('Y-m'))
            ->orderBy('cumul_collectif', 'desc')
            ->limit(10)
            ->pluck('distributeur_id')
            ->contains($distributeur->id);

        // Top 10 par grade
        $topByGrade = LevelCurrent::where('period', date('Y-m'))
            ->orderBy('etoiles', 'desc')
            ->orderBy('cumul_collectif', 'desc')
            ->limit(10)
            ->pluck('distributeur_id')
            ->contains($distributeur->id);

        return $topByCumul || $topByGrade;
    }

    /**
     * Calcule l'impact sur le réseau
     */
    private function calculateNetworkImpact(Distributeur $distributeur): array
    {
        $affectedIds = collect([$distributeur->id]);

        // Récupérer tous les descendants
        $descendants = $this->getAllDescendants($distributeur->id);
        $affectedIds = $affectedIds->merge($descendants);

        // Récupérer les parents jusqu'à la racine
        $currentParentId = $distributeur->parent_id;
        while ($currentParentId) {
            $affectedIds->push($currentParentId);
            $parent = Distributeur::find($currentParentId);
            $currentParentId = $parent ? $parent->parent_id : null;
        }

        return [
            'affected_count' => $affectedIds->unique()->count(),
            'descendants_count' => $descendants->count(),
            'affected_ids' => $affectedIds->unique()->values()->toArray()
        ];
    }

    /**
     * Récupère tous les descendants d'un distributeur
     */
    private function getAllDescendants(int $distributeurId): \Illuminate\Support\Collection
    {
        $descendants = collect();
        $children = Distributeur::where('parent_id', $distributeurId)->pluck('id');

        foreach ($children as $childId) {
            $descendants->push($childId);
            $descendants = $descendants->merge($this->getAllDescendants($childId));
        }

        return $descendants;
    }

    /**
     * Calcule le niveau d'impact
     */
    private function calculateImpactLevel(array $blockers, array $warnings, array $relatedData): string
    {
        if (count($blockers) > 0) {
            return 'critical';
        }

        $score = 0;
        $score += count($warnings) * 2;
        $score += isset($relatedData['children']) ? count($relatedData['children']) * 3 : 0;
        $score += isset($relatedData['achats']) ? min(count($relatedData['achats']) / 10, 5) : 0;
        $score += isset($relatedData['bonuses']) ? min(count($relatedData['bonuses']) / 5, 5) : 0;

        if ($score >= 15) {
            return 'high';
        } elseif ($score >= 8) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Génère un résumé de la validation
     */
    private function generateSummary($entity, array $blockers, array $warnings): string
    {
        $summary = "";

        if ($entity instanceof Distributeur) {
            $summary = "Suppression du distributeur {$entity->full_name} (#{$entity->distributeur_id})";
        } elseif ($entity instanceof Achat) {
            $summary = "Suppression de l'achat #{$entity->id}";
        }

        if (count($blockers) > 0) {
            $summary .= " - BLOQUÉE : " . count($blockers) . " problème(s) bloquant(s)";
        } elseif (count($warnings) > 0) {
            $summary .= " - ATTENTION : " . count($warnings) . " avertissement(s)";
        } else {
            $summary .= " - Suppression simple sans impact majeur";
        }

        return $summary;
    }
}
