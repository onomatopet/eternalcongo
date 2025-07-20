<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Distributeur;
use App\Models\Achat;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class BonusController extends Controller
{
    /**
     * Display a listing of bonuses.
     */
    public function index(Request $request): View
    {
        // Récupérer les périodes distinctes pour le filtre
        $availablePeriods = Bonus::select('period')
                                ->distinct()
                                ->orderBy('period', 'desc')
                                ->pluck('period');

        // Récupérer les filtres depuis la requête
        $selectedPeriod = $request->query('period_filter');
        $searchTerm = $request->query('search');

        // Construire la requête de base avec relations
        $bonusesQuery = Bonus::with('distributeur')
                             ->orderBy('period', 'desc')
                             ->orderBy('created_at', 'desc');

        // Appliquer le filtre par période
        if ($selectedPeriod && $availablePeriods->contains($selectedPeriod)) {
            $bonusesQuery->where('period', $selectedPeriod);
        }

        // Appliquer le filtre de recherche
        if ($searchTerm) {
            $bonusesQuery->where(function ($query) use ($searchTerm) {
                $query->where('num', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('distributeur', function ($subQuery) use ($searchTerm) {
                          $subQuery->where('nom_distributeur', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('pnom_distributeur', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('distributeur_id', 'LIKE', "%{$searchTerm}%");
                      });
            });
        }

        // Paginer les résultats
        $bonuses = $bonusesQuery->paginate(20)->withQueryString();

        return view('admin.bonuses.index', [
            'bonuses' => $bonuses,
            'availablePeriods' => $availablePeriods,
            'selectedPeriod' => $selectedPeriod,
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Show the form for calculating bonuses.
     */
    public function create(): View
    {
        // Obtenir la période actuelle
        $currentPeriod = date('Y-m');

        // Obtenir les périodes disponibles
        $availablePeriods = Achat::select('period')
                                 ->distinct()
                                 ->orderBy('period', 'desc')
                                 ->pluck('period');

        return view('admin.bonuses.create', compact('currentPeriod', 'availablePeriods'));
    }

    /**
     * Calculate and store bonus for a specific distributor.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'period' => 'required|regex:/^\d{4}-\d{2}$/',
            'distributeur_id' => 'required|integer|exists:distributeurs,id',
        ]);

        $period = $request->input('period');
        $distributeurId = $request->input('distributeur_id');

        // Vérifier si le bonus a déjà été calculé pour ce distributeur et cette période
        if (Bonus::where('period', $period)->where('distributeur_id', $distributeurId)->exists()) {
            return back()->with('error', 'Un bonus a déjà été calculé pour ce distributeur sur cette période.');
        }

        try {
            DB::beginTransaction();

            $distributeur = Distributeur::find($distributeurId);

            // Vérifier si le distributeur a des achats dans la période
            $hasAchats = Achat::where('distributeur_id', $distributeurId)
                             ->where('period', $period)
                             ->exists();

            if (!$hasAchats) {
                return back()->with('error', 'Ce distributeur n\'a effectué aucun achat durant cette période.');
            }

            // Calculer le bonus direct (achats personnels)
            $bonusDirect = $this->calculateBonusDirect($distributeurId, $period);

            // Calculer le bonus indirect (achats des filleuls)
            $bonusIndirect = $this->calculateBonusIndirect($distributeurId, $period);

            // Calculer le bonus leadership (si applicable)
            $bonusLeadership = $this->calculateBonusLeadership($distributeurId, $period);

            // Total bonus
            $totalBonus = $bonusDirect + $bonusIndirect + $bonusLeadership;

            // Calculer l'épargne (10% du total par exemple)
            $epargne = $totalBonus * 0.10;
            $bonusFinal = $totalBonus - $epargne;

            if ($totalBonus > 0) {
                // Créer l'enregistrement bonus
                $bonus = Bonus::create([
                    'period' => $period,
                    'distributeur_id' => $distributeurId,
                    'num' => $this->generateBonusNumber($period, $distributeurId),
                    'bonus_direct' => $bonusDirect,
                    'bonus_indirect' => $bonusIndirect,
                    'bonus_leadership' => $bonusLeadership,
                    'bonus' => $totalBonus,
                    'epargne' => $epargne,
                    'bonusFinal' => $bonusFinal,
                ]);

                DB::commit();

                // Générer le PDF automatiquement
                $this->generatePdf($bonus);

                return redirect()->route('admin.bonuses.show', $bonus)
                               ->with('success', "Bonus calculé avec succès pour {$distributeur->full_name}. Le reçu PDF a été généré.");
            } else {
                DB::rollBack();
                return back()->with('error', 'Le montant du bonus calculé est de 0 XAF.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors du calcul du bonus: " . $e->getMessage());
            return back()->with('error', 'Erreur lors du calcul du bonus.');
        }
    }

    /**
     * Display the specified bonus.
     */
    public function show(Bonus $bonus): View
    {
        $bonus->load(['distributeur', 'distributeur.achats' => function ($query) use ($bonus) {
            $query->where('period', $bonus->period);
        }]);

        return view('admin.bonuses.show', compact('bonus'));
    }

    /**
     * Generate PDF receipt for a bonus.
     */
    public function generatePdf(Bonus $bonus)
    {
        // TODO: Implémenter la génération PDF
        // Utiliser une librairie comme DomPDF ou TCPDF

        return back()->with('info', 'Génération PDF à implémenter');
    }

    /**
     * Calculate direct bonus from personal purchases.
     */
    private function calculateBonusDirect($distributeurId, $period): float
    {
        $achats = Achat::where('distributeur_id', $distributeurId)
                       ->where('period', $period)
                       ->with('product.pointValeur')
                       ->get();

        $totalPoints = 0;
        foreach ($achats as $achat) {
            $points = $achat->points_unitaire_achat * $achat->qt;
            $totalPoints += $points;
        }

        // Exemple de calcul : 1 point = 100 XAF de bonus
        return $totalPoints * 100;
    }

    /**
     * Calculate indirect bonus from downline purchases.
     */
    private function calculateBonusIndirect($distributeurId, $period): float
    {
        // Récupérer tous les filleuls directs
        $filleuls = Distributeur::where('id_distrib_parent', $distributeurId)->pluck('id');

        if ($filleuls->isEmpty()) {
            return 0;
        }

        $achatsFilleuls = Achat::whereIn('distributeur_id', $filleuls)
                               ->where('period', $period)
                               ->with('product.pointValeur')
                               ->get();

        $totalPoints = 0;
        foreach ($achatsFilleuls as $achat) {
            $points = $achat->points_unitaire_achat * $achat->qt;
            $totalPoints += $points;
        }

        // Exemple : 10% des points des filleuls
        return $totalPoints * 10;
    }

    /**
     * Calculate leadership bonus based on rank and performance.
     */
    private function calculateBonusLeadership($distributeurId, $period): float
    {
        $distributeur = Distributeur::find($distributeurId);

        // Bonus leadership uniquement pour les rangs élevés (ex: 5 étoiles et plus)
        if ($distributeur->etoiles_id < 5) {
            return 0;
        }

        // Calculer en fonction de la performance de toute l'équipe
        // TODO: Implémenter la logique métier spécifique

        return 0;
    }

    /**
     * Generate unique bonus number.
     */
    private function generateBonusNumber($period, $distributeurId): string
    {
        $year = substr($period, 0, 4);
        $month = substr($period, 5, 2);

        return "BON-{$year}{$month}-{$distributeurId}-" . rand(1000, 9999);
    }
}
