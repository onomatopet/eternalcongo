<?php

namespace App\Http\Controllers\Admin; // Adaptez si nécessaire

use App\Http\Controllers\Controller;
use App\Models\Achat;
use App\Models\Distributeur;
use App\Models\Product;
use Illuminate\Http\Request; // Important: Importer Request
use Illuminate\View\View;
use Illuminate\Support\Facades\Log; // Pour les logs
use Illuminate\Http\RedirectResponse;

class AchatController extends Controller
{
    /**
     * Handle the incoming request.
     * Affiche la liste des achats, filtrée par période ET/OU mot-clé.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View // Injecter Request
    {
        // 1. Périodes pour le filtre
        $availablePeriods = Achat::select('period')
                                ->distinct()
                                ->orderBy('period', 'desc')
                                ->pluck('period');

        // 2. Récupérer les filtres depuis la requête
        $selectedPeriod = $request->query('period_filter');
        $searchTerm = $request->query('search'); // Nouveau champ de recherche

        // 3. Construire la requête de base avec Eager Loading
        $achatsQuery = Achat::with(['distributeur', 'product']) // Eager Loading ESSENTIEL pour la recherche sur relations
                           ->orderBy('created_at', 'desc');

        // 4. Appliquer le filtre par période
        if ($selectedPeriod && $availablePeriods->contains($selectedPeriod)) {
            $achatsQuery->where('achats.period', $selectedPeriod); // Préfixer avec nom table si ambiguïté potentielle
            $this->info("Filtrage achats par période: {$selectedPeriod}");
        } else {
            $selectedPeriod = null;
        }

        // 5. Appliquer le filtre de recherche par mot-clé
        if ($searchTerm) {
            $this->info("Recherche d'achats avec le terme: {$searchTerm}");
            $achatsQuery->where(function ($query) use ($searchTerm) {
                // Recherche sur les champs directs de la table 'achats'
                 $query->where('achats.id', 'LIKE', "%{$searchTerm}%") // Rechercher par ID Achat?
                       ->orWhere('achats.period', 'LIKE', "%{$searchTerm}%"); // Rechercher dans période?

                // Recherche sur les colonnes des tables liées (distributeur, produit)
                // Nécessite une jointure ou une sous-requête whereHas/orWhereHas
                // Utilisons orWhereHas pour la simplicité (peut être moins performant sur très gros volumes que JOIN)
                $query->orWhereHas('distributeur', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('nom_distributeur', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('pnom_distributeur', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('distributeur_id', 'LIKE', "%{$searchTerm}%"); // Recherche sur matricule
                });

                $query->orWhereHas('product', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('nom_produit', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('code_product', 'LIKE', "%{$searchTerm}%");
                });
            });
        } else {
            $this->info("Affichage des achats (aucune recherche par mot-clé).");
        }

        // 6. Exécuter la requête et paginer
        $achats = $achatsQuery->paginate(20)->withQueryString(); // Paginer et ajouter tous les params query

        // 7. Passer les données à la vue
        return view('admin.achats.index', [
            'achats' => $achats,
            'availablePeriods' => $availablePeriods,
            'selectedPeriod' => $selectedPeriod,
            'searchTerm' => $searchTerm // Passer le terme de recherche
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * // A implémenter plus tard
     */
    public function create(): View
    {
        // --- Récupérer et Formater les Distributeurs ---
        // On récupère les colonnes nécessaires et on crée une chaîne "Matricule - Prénom Nom"
        $distributeurs = Distributeur::orderBy('nom_distributeur')
                                      ->select('id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur')
                                      // ->limit(1000) // Décommentez si la liste est trop longue pour un select standard
                                      ->get()
                                      ->mapWithKeys(function ($distributeur) {
                                          // La clé reste l'ID primaire, la valeur est la chaîne formatée
                                          return [$distributeur->id => "#{$distributeur->distributeur_id} - {$distributeur->pnom_distributeur} {$distributeur->nom_distributeur}"];
                                      });
        // ------------------------------------------------

        // --- Récupérer et Formater les Produits ---
        // On récupère les colonnes nécessaires et on crée une chaîne "Nom (Code)"
        $products = Product::orderBy('nom_produit')
                           ->select('id', 'code_product', 'nom_produit')
                           ->get()
                           ->mapWithKeys(function ($product) {
                                // La clé reste l'ID primaire, la valeur est la chaîne formatée
                                return [$product->id => "{$product->nom_produit} ({$product->code_product})"];
                           });
        // --------------------------------------------

        return view('admin.achats.create', compact('distributeurs', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     * Traite la soumission du formulaire d'ajout d'achat.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse // Importer RedirectResponse
    {
        // 1. Validation des données (ESSENTIEL !)
        $validatedData = $request->validate([
            'period' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'distributeur_id' => 'required|integer|exists:distributeurs,id',
            'products_id' => 'required|integer|exists:products,id',
            'qt' => 'required|integer|min:1',
            'online' => 'required|boolean',
        ]);

        // 2. Récupérer les infos du produit
        $product = Product::with('pointValeur')->find($validatedData['products_id']); // Charger aussi pointValeur ici
        if (!$product) {
            return back()->withInput()->with('error', 'Produit sélectionné invalide.');
        }

        // 3. Calculer les valeurs
         $points_unitaires = $product->pointValeur->numbers ?? 0;
         $montant_total = $product->prix_product * $validatedData['qt'];

        // 4. Créer l'enregistrement Achat
        try {
            Achat::create([
                'period' => $validatedData['period'],
                'distributeur_id' => $validatedData['distributeur_id'],
                'products_id' => $validatedData['products_id'],
                'qt' => $validatedData['qt'],
                'points_unitaire_achat' => $points_unitaires,
                'montant_total_ligne' => $montant_total,
                'prix_unitaire_achat' => $product->prix_product,
                'online' => $validatedData['online'] ?? 0,
            ]);

            Log::info("Achat créé pour distrib ID: {$validatedData['distributeur_id']}, Produit ID: {$validatedData['products_id']}");
            return redirect()->route('admin.achats.index')->with('success', 'Achat enregistré avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de l'achat: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de l\'enregistrement de l\'achat.');
        }
    }

    /**
     * Display the specified resource.
     * // A implémenter plus tard (pour le bouton "Consulter")
     */
    public function show(Achat $distributeur) // Utilise le Route Model Binding
    {
        // Exemple: return view('admin.distributeurs.show', compact('distributeur'));
        return "Affichage du distributeur ID: " . $distributeur->id; // Placeholder
    }

    /**
     * Show the form for editing the specified resource.
     * // A implémenter plus tard (pour le bouton "Modifier")
     */
    public function edit(Achat $distributeur) // Utilise le Route Model Binding
    {
         // Exemple: return view('admin.distributeurs.edit', compact('distributeur'));
         return "Modification du distributeur ID: " . $distributeur->id; // Placeholder
    }

    /**
     * Update the specified resource in storage.
      * // A implémenter plus tard
     */
    public function update(Request $request, Achat $distributeur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * // A implémenter plus tard (pour le bouton "Supprimer")
     */
    public function destroy(Achat $distributeur) // Utilise le Route Model Binding
    {
         // Exemple:
         // $distributeur->delete();
         // return redirect()->route('admin.distributeurs.index')->with('success', 'Distributeur supprimé.');
         return "Suppression du distributeur ID: " . $distributeur->id; // Placeholder
    }


     // Helper pour logger (si pas déjà dans un trait ou classe parente)
     private function info(string $message): void
     {
         if (app()->runningInConsole() || config('app.debug')) { // Log seulement en console ou debug
             Log::info($message);
         }
     }
}
