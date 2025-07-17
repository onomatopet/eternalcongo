<?php

namespace App\Http\Controllers\Admin; // Adaptez si nécessaire

use App\Http\Controllers\Controller;
use App\Models\Bonus; // Importer le modèle Bonus
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class BonusController extends Controller
{
    /**
     * Handle the incoming request.
     * Affiche la liste des bonus, filtrée par période et/ou recherchée par mot-clé (num, distrib).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function __invoke(Request $request): View
    {
        // 1. Récupérer les périodes distinctes pour le filtre
        $availablePeriods = Bonus::select('period')
                                ->distinct()
                                ->orderBy('period', 'desc')
                                ->pluck('period');

        // 2. Récupérer les filtres depuis la requête
        $selectedPeriod = $request->query('period_filter');
        $searchTerm = $request->query('search');

        // 3. Construire la requête de base avec Eager Loading vers Distributeur
        $bonusesQuery = Bonus::with('distributeur') // Charger le distributeur associé
                             ->orderBy('period', 'desc') // Trier par période récente
                             ->orderBy('created_at', 'desc'); // Puis par date de création

        // 4. Appliquer le filtre par période
        if ($selectedPeriod && $availablePeriods->contains($selectedPeriod)) {
            $bonusesQuery->where('bonuses.period', $selectedPeriod);
            $this->info("Filtrage des bonus pour la période : {$selectedPeriod}");
        } else {
            $selectedPeriod = null;
        }

        // 5. Appliquer le filtre de recherche par mot-clé
        if ($searchTerm) {
            $this->info("Recherche de bonus avec le terme: {$searchTerm}");
            $bonusesQuery->where(function ($query) use ($searchTerm) {
                // Recherche sur les champs directs de la table 'bonuses'
                 $query->where('bonuses.num', 'LIKE', "%{$searchTerm}%"); // Rechercher par Numéro de Bonus

                // Recherche sur le distributeur lié
                $query->orWhereHas('distributeur', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('nom_distributeur', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('pnom_distributeur', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('distributeur_id', 'LIKE', "%{$searchTerm}%"); // Matricule
                });
            });
        } else {
             $this->info("Affichage des bonus (aucune recherche par mot-clé).");
        }

        // 6. Exécuter la requête et paginer
        $bonuses = $bonusesQuery->paginate(20)->withQueryString();

        // 7. Passer les données à la vue
        return view('admin.bonuses.index', [
            'bonuses' => $bonuses,
            'availablePeriods' => $availablePeriods,
            'selectedPeriod' => $selectedPeriod,
            'searchTerm' => $searchTerm
        ]);
    }

     // Helper log (optionnel)
     private function info(string $message): void { /* ... */ }
}
