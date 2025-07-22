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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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

        // Vérifier s'il y a des achats dans le système
        $hasAchats = Achat::exists();

        return view('admin.bonuses.create', compact('currentPeriod', 'availablePeriods', 'hasAchats'));
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

            // Calculer l'épargne (10% du total)
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

                // Générer le PDF automatiquement si la méthode existe
                if (method_exists($this, 'generatePdf')) {
                    $this->generatePdf($bonus);
                }

                return redirect()->route('admin.bonuses.show', $bonus)
                            ->with('success', "Bonus calculé avec succès pour {$distributeur->full_name}. Montant net : " . number_format($bonusFinal, 0, ',', ' ') . " FCFA");
            } else {
                DB::rollBack();
                return back()->with('error', 'Le montant du bonus calculé est de 0 FCFA. Vérifiez les achats du distributeur.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors du calcul du bonus: " . $e->getMessage());
            return back()->with('error', 'Erreur lors du calcul du bonus : ' . $e->getMessage());
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
        $bonus->load(['distributeur']);

        // Préparer les données pour le PDF
        $data = [
            'bonus' => $bonus,
            'distributeur' => $bonus->distributeur,
            'periode' => $bonus->period,
            'date_generation' => Carbon::now()->format('d/m/Y'),
            'numero_recu' => $bonus->num,
            'details' => [
                'bonus_direct' => $bonus->bonus_direct,
                'bonus_indirect' => $bonus->bonus_indirect,
                'bonus_leadership' => $bonus->bonus_leadership,
                'total_brut' => $bonus->bonus,
                'epargne' => $bonus->epargne,
                'net_payer' => $bonus->bonusFinal
            ]
        ];

        // Générer le PDF
        $pdf = PDF::loadView('admin.bonuses.pdf', $data);
        
        // Définir les options du PDF
        $pdf->setPaper('A4', 'portrait');
        
        // Nom du fichier
        $filename = 'recu_bonus_' . $bonus->distributeur->distributeur_id . '_' . $bonus->period . '.pdf';
        
        // Retourner le PDF pour téléchargement
        return $pdf->download($filename);
    }

    /**
     * Méthodes de calcul des bonus
     */
    private function calculateBonusDirect($distributeurId, $period)
    {
        // Calculer le bonus direct basé sur les achats personnels
        $achatsPersonnels = Achat::where('distributeur_id', $distributeurId)
                                ->where('period', $period)
                                ->sum('pointvaleur');
        
        // Taux de bonus direct (exemple : 20%)
        $tauxBonusDirect = 0.20;
        
        return $achatsPersonnels * $tauxBonusDirect;
    }

    private function calculateBonusIndirect($distributeurId, $period)
    {
        // Récupérer le distributeur et ses filleuls
        $distributeur = Distributeur::find($distributeurId);
        
        // Calculer le bonus indirect basé sur les achats des filleuls directs
        $achatsFilleuls = Achat::whereIn('distributeur_id', function($query) use ($distributeurId) {
                                $query->select('id')
                                    ->from('distributeurs')
                                    ->where('id_distrib_parent', $distributeurId);
                            })
                            ->where('period', $period)
                            ->sum('pointvaleur');
        
        // Taux de bonus indirect selon le grade
        $tauxBonusIndirect = $this->getTauxBonusIndirect($distributeur->etoiles_id);
        
        return $achatsFilleuls * $tauxBonusIndirect;
    }

    private function calculateBonusLeadership($distributeurId, $period)
    {
        $distributeur = Distributeur::find($distributeurId);
        
        // Le bonus leadership s'applique uniquement aux grades élevés (4+)
        if ($distributeur->etoiles_id < 4) {
            return 0;
        }
        
        // Calculer le volume total de la descendance
        $volumeDescendance = $this->calculateVolumeDescendance($distributeurId, $period);
        
        // Taux de bonus leadership selon le grade
        $tauxLeadership = $this->getTauxBonusLeadership($distributeur->etoiles_id);
        
        return $volumeDescendance * $tauxLeadership;
    }

    private function getTauxBonusIndirect($grade)
    {
        // Taux de bonus indirect selon le grade
        $taux = [
            1 => 0.05,
            2 => 0.10,
            3 => 0.15,
            4 => 0.20,
            5 => 0.25,
        ];
        
        return $taux[$grade] ?? 0.05;
    }

    private function getTauxBonusLeadership($grade)
    {
        // Taux de bonus leadership selon le grade
        $taux = [
            4 => 0.02,
            5 => 0.03,
            6 => 0.04,
            7 => 0.05,
            8 => 0.06,
            9 => 0.07,
            10 => 0.08,
        ];
        
        return $taux[$grade] ?? 0;
    }

    private function calculateVolumeDescendance($distributeurId, $period, $niveau = 0, $maxNiveau = 5)
    {
        if ($niveau >= $maxNiveau) {
            return 0;
        }
        
        // Volume des filleuls directs
        $volumeDirect = Achat::whereIn('distributeur_id', function($query) use ($distributeurId) {
                                $query->select('id')
                                    ->from('distributeurs')
                                    ->where('id_distrib_parent', $distributeurId);
                            })
                            ->where('period', $period)
                            ->sum('pointvaleur');
        
        // Volume des filleuls indirects (récursif)
        $filleuls = Distributeur::where('id_distrib_parent', $distributeurId)->pluck('id');
        $volumeIndirect = 0;
        
        foreach ($filleuls as $filleulId) {
            $volumeIndirect += $this->calculateVolumeDescendance($filleulId, $period, $niveau + 1, $maxNiveau);
        }
        
        return $volumeDirect + $volumeIndirect;
    }

    private function generateBonusNumber($period, $distributeurId)
    {
        // Générer un numéro unique pour le bonus
        // Format: YYYYMM-XXXXX-ID
        $lastBonus = Bonus::where('period', $period)
                        ->orderBy('id', 'desc')
                        ->first();
        
        $sequence = $lastBonus ? (intval(substr($lastBonus->num, 7, 5)) + 1) : 1;
        
        return sprintf('%s-%05d-%d', str_replace('-', '', $period), $sequence, $distributeurId);
    }
}
