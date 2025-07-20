<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achat;
use App\Models\Distributeur;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class AchatController extends Controller
{
    /**
     * Display a listing of the resource.
     * Affiche la liste des achats, filtrée par période ET/OU mot-clé.
     */
    public function index(Request $request): View
    {
        // 1. Périodes pour le filtre
        $availablePeriods = Achat::select('period')
                                ->distinct()
                                ->orderBy('period', 'desc')
                                ->pluck('period');

        // 2. Récupérer les filtres depuis la requête
        $selectedPeriod = $request->query('period_filter');
        $searchTerm = $request->query('search');

        // 3. Construire la requête de base avec Eager Loading
        $achatsQuery = Achat::with(['distributeur', 'product'])
                           ->orderBy('created_at', 'desc');

        // 4. Appliquer le filtre par période
        if ($selectedPeriod && $availablePeriods->contains($selectedPeriod)) {
            $achatsQuery->where('achats.period', $selectedPeriod);
            $this->info("Filtrage achats par période: {$selectedPeriod}");
        } else {
            $selectedPeriod = null;
        }

        // 5. Appliquer le filtre de recherche par mot-clé
        if ($searchTerm) {
            $this->info("Recherche d'achats avec le terme: {$searchTerm}");
            $achatsQuery->where(function ($query) use ($searchTerm) {
                // Recherche sur les champs directs de la table 'achats'
                $query->where('achats.id', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('achats.period', 'LIKE', "%{$searchTerm}%");

                // Recherche sur les colonnes des tables liées
                $query->orWhereHas('distributeur', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('nom_distributeur', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('pnom_distributeur', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('distributeur_id', 'LIKE', "%{$searchTerm}%");
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
        $achats = $achatsQuery->paginate(20)->withQueryString();

        // 7. Passer les données à la vue
        return view('admin.achats.index', [
            'achats' => $achats,
            'availablePeriods' => $availablePeriods,
            'selectedPeriod' => $selectedPeriod,
            'searchTerm' => $searchTerm
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     * Mise à jour pour ne plus charger tous les distributeurs
     */
    public function create(): View
    {
        // Ne plus charger les 8500 distributeurs !
        // La recherche AJAX s'en occupe

        // Charger uniquement les produits (généralement moins nombreux)
        $products = Product::orderBy('nom_produit')
                           ->select('id', 'code_product', 'nom_produit')
                           ->get()
                           ->mapWithKeys(function ($product) {
                                return [$product->id => "{$product->nom_produit} ({$product->code_product})"];
                           });

        return view('admin.achats.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation des données
        $validatedData = $request->validate([
            'period' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'distributeur_id' => 'required|integer|exists:distributeurs,id',
            'products_id' => 'required|integer|exists:products,id',
            'qt' => 'required|integer|min:1',
            'online' => 'boolean',
        ]);

        // 2. Récupérer les infos du produit
        $product = Product::with('pointValeur')->find($validatedData['products_id']);
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
                'online' => $validatedData['online'] ?? false,
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
     */
    public function show(Achat $achat): View
    {
        $achat->load(['distributeur', 'product']);
        return view('admin.achats.show', compact('achat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Achat $achat): View
    {
        // Récupérer les distributeurs et produits pour les selects
        $distributeurs = Distributeur::orderBy('nom_distributeur')
                                      ->select('id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur')
                                      ->get()
                                      ->mapWithKeys(function ($distributeur) {
                                          return [$distributeur->id => "#{$distributeur->distributeur_id} - {$distributeur->pnom_distributeur} {$distributeur->nom_distributeur}"];
                                      });

        $products = Product::orderBy('nom_produit')
                           ->select('id', 'code_product', 'nom_produit')
                           ->get()
                           ->mapWithKeys(function ($product) {
                               return [$product->id => "{$product->nom_produit} ({$product->code_product})"];
                           });

        return view('admin.achats.edit', compact('achat', 'distributeurs', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Achat $achat): RedirectResponse
    {
        // 1. Validation des données
        $validatedData = $request->validate([
            'period' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'distributeur_id' => 'required|integer|exists:distributeurs,id',
            'products_id' => 'required|integer|exists:products,id',
            'qt' => 'required|integer|min:1',
            'online' => 'boolean',
        ]);

        // 2. Récupérer les infos du produit
        $product = Product::with('pointValeur')->find($validatedData['products_id']);
        if (!$product) {
            return back()->withInput()->with('error', 'Produit sélectionné invalide.');
        }

        // 3. Calculer les valeurs
        $points_unitaires = $product->pointValeur->numbers ?? 0;
        $montant_total = $product->prix_product * $validatedData['qt'];

        // 4. Mettre à jour l'enregistrement
        try {
            $achat->update([
                'period' => $validatedData['period'],
                'distributeur_id' => $validatedData['distributeur_id'],
                'products_id' => $validatedData['products_id'],
                'qt' => $validatedData['qt'],
                'points_unitaire_achat' => $points_unitaires,
                'montant_total_ligne' => $montant_total,
                'prix_unitaire_achat' => $product->prix_product,
                'online' => $validatedData['online'] ?? false,
            ]);

            Log::info("Achat modifié ID: {$achat->id}");
            return redirect()->route('admin.achats.index')->with('success', 'Achat modifié avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de l'achat: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de la modification de l\'achat.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Achat $achat): RedirectResponse
    {
        try {
            $achat->delete();
            Log::info("Achat supprimé ID: {$achat->id}");
            return redirect()->route('admin.achats.index')->with('success', 'Achat supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression de l'achat: " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression de l\'achat.');
        }
    }

    /**
     * Helper pour logger
     */
    private function info(string $message): void
    {
        if (app()->runningInConsole() || config('app.debug')) {
            Log::info($message);
        }
    }
}
