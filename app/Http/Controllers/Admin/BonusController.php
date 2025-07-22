<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Distributeur;
use App\Models\Achat;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BonusController extends Controller
{
    /**
     * Display a listing of bonuses.
     */
    public function index(Request $request): View
    {
        $query = Bonus::with(['distributeur']);

        // Filtrer par période si fournie
        if ($request->has('period') && $request->period) {
            $query->where('period', $request->period);
        }

        // Filtrer par distributeur si fourni
        if ($request->has('distributeur_id') && $request->distributeur_id) {
            $query->where('distributeur_id', $request->distributeur_id);
        }

        $bonuses = $query->orderBy('created_at', 'desc')->paginate(20);

        // Obtenir les périodes distinctes pour le filtre
        $periods = Bonus::distinct()->pluck('period')->sort()->reverse();

        return view('admin.bonuses.index', compact('bonuses', 'periods'));
    }

    /**
     * Show the form for creating a new bonus.
     */
    public function create(): View
    {
        $distributeurs = Distributeur::orderBy('full_name')->get();
        $currentPeriod = Carbon::now()->format('Y-m');
        $availablePeriods = $this->getAvailablePeriods();

        return view('admin.bonuses.create', compact('distributeurs', 'currentPeriod', 'availablePeriods'));
    }

    /**
     * Show bonus calculation form for a specific period.
     */
    public function showCalculation($period): View
    {
        // Récupérer les distributeurs qui ont des achats pour cette période
        $distributeursAvecAchats = Distributeur::whereHas('achats', function ($query) use ($period) {
            $query->where('period', $period);
        })->get();

        // Récupérer les distributeurs qui ont déjà un bonus pour cette période
        $distributeursAvecBonus = Bonus::where('period', $period)->pluck('distributeur_id')->toArray();

        return view('admin.bonuses.calculate', compact('period', 'distributeursAvecAchats', 'distributeursAvecBonus'));
    }

    /**
     * Calculate and store bonus for a distributor.
     */
    public function calculate(Request $request, $period): RedirectResponse
    {
        $request->validate([
            'distributeur_id' => 'required|exists:distributeurs,id',
        ]);

        $distributeurId = $request->distributeur_id;

        // Vérifier si un bonus existe déjà pour ce distributeur et cette période
        if (Bonus::where('distributeur_id', $distributeurId)->where('period', $period)->exists()) {
            return back()->with('error', 'Un bonus a déjà été calculé pour ce distributeur pour cette période.');
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
            'date_generation' => Carbon::now()->format('Y-m-d'),
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
        $filename = 'bonus_' . $bonus->distributeur->distributeur_id . '_' . $bonus->period . '.pdf';
        
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
    
    private function calculateVolumeDescendance($distributeurId, $period)
    {
        // Calculer récursivement le volume total de la descendance
        $volume = 0;
        
        // Récupérer les filleuls directs
        $filleuls = Distributeur::where('id_distrib_parent', $distributeurId)->get();
        
        foreach ($filleuls as $filleul) {
            // Achats du filleul
            $volume += Achat::where('distributeur_id', $filleul->id)
                          ->where('period', $period)
                          ->sum('pointvaleur');
            
            // Récursion pour les sous-filleuls
            $volume += $this->calculateVolumeDescendance($filleul->id, $period);
        }
        
        return $volume;
    }
    
    private function generateBonusNumber($period, $distributeurId)
    {
        // Format: 7770MMYYXXX où MM=mois, YY=année, XXX=numéro séquentiel
        $prefix = '7770';
        
        // Extraire l'année et le mois de la période (format: YYYY-MM)
        $dateParts = explode('-', $period);
        $year = substr($dateParts[0], -2); // Prendre les 2 derniers chiffres de l'année
        $month = $dateParts[1];
        
        // Construire le préfixe complet avec mois puis année
        $fullPrefix = $prefix . $month . $year;
        
        // Obtenir le dernier numéro utilisé pour cette période
        $lastBonus = Bonus::where('period', $period)
                         ->where('num', 'like', $fullPrefix . '%')
                         ->orderBy('id', 'desc')
                         ->first();
        
        if ($lastBonus) {
            // Extraire le numéro séquentiel du dernier bonus
            $lastSequence = intval(substr($lastBonus->num, -3));
            $sequence = $lastSequence + 1;
        } else {
            // Premier bonus de la période
            $sequence = 1;
        }
        
        // Retourner le numéro complet au format 7770MMYYXXX
        return $fullPrefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
    
    private function getAvailablePeriods()
    {
        // Obtenir les périodes où il y a des achats
        $periods = Achat::distinct()
                      ->orderBy('period', 'desc')
                      ->pluck('period')
                      ->toArray();
        
        return $periods;
    }
}