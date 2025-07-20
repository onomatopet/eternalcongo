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
use Illuminate\Support\Facades\DB;

class AchatController extends Controller
{
    /**
     * Display a listing of the resource.
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
        $achatsQuery = Achat::with(['distributeur', 'product.pointValeur'])
                           ->orderBy('created_at', 'desc');

        // 4. Appliquer le filtre par période
        if ($selectedPeriod && $availablePeriods->contains($selectedPeriod)) {
            $achatsQuery->where('achats.period', $selectedPeriod);
            Log::info("Filtrage achats par période: {$selectedPeriod}");
        }

        // 5. Appliquer le filtre de recherche par mot-clé
        if ($searchTerm) {
            Log::info("Recherche d'achats avec le terme: {$searchTerm}");
            $achatsQuery->where(function ($query) use ($searchTerm) {
                // Recherche sur les champs directs
                $query->where('achats.id', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('achats.period', 'LIKE', "%{$searchTerm}%");

                // Recherche sur les tables liées
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
        }

        // 6. Paginer avec statistiques
        $achats = $achatsQuery->paginate(20)->withQueryString();

        // 7. Calculer les totaux pour la période sélectionnée
        $totals = null;
        if ($selectedPeriod) {
            $totals = [
                'total_achats' => Achat::where('period', $selectedPeriod)->count(),
                'total_montant' => Achat::where('period', $selectedPeriod)->sum('montant_total_ligne'),
                'total_points' => Achat::where('period', $selectedPeriod)->sum(DB::raw('points_unitaire_achat * qt')),
            ];
        }

        return view('admin.achats.index', compact('achats', 'availablePeriods', 'selectedPeriod', 'searchTerm', 'totals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Récupérer la période courante et les périodes disponibles
        $currentPeriod = date('Y-m');
        $periods = $this->generatePeriods();

        // Récupérer les produits actifs
        $products = Product::with('pointValeur')
                           ->orderBy('nom_produit')
                           ->get()
                           ->map(function ($product) {
                               return [
                                   'id' => $product->id,
                                   'name' => "{$product->nom_produit} ({$product->code_product})",
                                   'price' => $product->prix_product,
                                   'points' => $product->pointValeur->numbers ?? 0
                               ];
                           });

        return view('admin.achats.create', compact('products', 'currentPeriod', 'periods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation avancée
        $validatedData = $request->validate([
            'period' => [
                'required',
                'string',
                'regex:/^\d{4}-\d{2}$/',
                function ($attribute, $value, $fail) {
                    // Empêcher les achats dans le futur
                    if ($value > date('Y-m')) {
                        $fail('La période ne peut pas être dans le futur.');
                    }
                }
            ],
            'distributeur_id' => 'required|integer|exists:distributeurs,id',
            'products_id' => 'required|integer|exists:products,id',
            'qt' => 'required|integer|min:1|max:9999',
            'online' => 'boolean',
        ], [
            'period.required' => 'La période est obligatoire.',
            'period.regex' => 'Le format de la période est invalide (AAAA-MM).',
            'distributeur_id.required' => 'Le distributeur est obligatoire.',
            'distributeur_id.exists' => 'Le distributeur sélectionné n\'existe pas.',
            'products_id.required' => 'Le produit est obligatoire.',
            'products_id.exists' => 'Le produit sélectionné n\'existe pas.',
            'qt.required' => 'La quantité est obligatoire.',
            'qt.min' => 'La quantité doit être au moins de 1.',
            'qt.max' => 'La quantité ne peut pas dépasser 9999.',
        ]);

        DB::beginTransaction();
        try {
            // Récupérer les infos du produit avec verrouillage
            $product = Product::with('pointValeur')->lockForUpdate()->find($validatedData['products_id']);

            if (!$product) {
                throw new \Exception('Produit introuvable');
            }

            // Calculer les valeurs
            $points_unitaires = $product->pointValeur->numbers ?? 0;
            $prix_unitaire = $product->prix_product;
            $montant_total = $prix_unitaire * $validatedData['qt'];
            $points_total = $points_unitaires * $validatedData['qt'];

            // Créer l'achat
            $achat = Achat::create([
                'period' => $validatedData['period'],
                'distributeur_id' => $validatedData['distributeur_id'],
                'products_id' => $validatedData['products_id'],
                'qt' => $validatedData['qt'],
                'points_unitaire_achat' => $points_unitaires,
                'montant_total_ligne' => $montant_total,
                'prix_unitaire_achat' => $prix_unitaire,
                'online' => $validatedData['online'] ?? false,
            ]);

            // Log de l'action
            Log::info("Achat créé", [
                'id' => $achat->id,
                'distributeur_id' => $validatedData['distributeur_id'],
                'produit' => $product->nom_produit,
                'quantité' => $validatedData['qt'],
                'montant' => $montant_total,
                'points' => $points_total,
                'created_by' => auth()->id()
            ]);

            DB::commit();
            return redirect()
                ->route('admin.achats.index')
                ->with('success', "Achat enregistré avec succès. Montant: " . number_format($montant_total, 0, ',', ' ') . " XAF");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création achat", [
                'error' => $e->getMessage(),
                'data' => $validatedData
            ]);
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de l\'achat.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Achat $achat): View
    {
        $achat->load(['distributeur', 'product.category', 'product.pointValeur']);

        // Calculer les totaux du distributeur pour cette période
        $distributeurStats = Achat::where('distributeur_id', $achat->distributeur_id)
                                 ->where('period', $achat->period)
                                 ->selectRaw('COUNT(*) as total_achats, SUM(montant_total_ligne) as total_montant, SUM(points_unitaire_achat * qt) as total_points')
                                 ->first();

        return view('admin.achats.show', compact('achat', 'distributeurStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Achat $achat): View
    {
        $periods = $this->generatePeriods();

        // Récupérer les produits avec leurs infos
        $products = Product::with('pointValeur')
                           ->orderBy('nom_produit')
                           ->get()
                           ->map(function ($product) {
                               return [
                                   'id' => $product->id,
                                   'name' => "{$product->nom_produit} ({$product->code_product})",
                                   'price' => $product->prix_product,
                                   'points' => $product->pointValeur->numbers ?? 0
                               ];
                           });

        // Précharger le distributeur actuel
        $achat->load('distributeur');

        return view('admin.achats.edit', compact('achat', 'products', 'periods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Achat $achat): RedirectResponse
    {
        // Validation
        $validatedData = $request->validate([
            'period' => [
                'required',
                'string',
                'regex:/^\d{4}-\d{2}$/',
                function ($attribute, $value, $fail) {
                    if ($value > date('Y-m')) {
                        $fail('La période ne peut pas être dans le futur.');
                    }
                }
            ],
            'distributeur_id' => 'required|integer|exists:distributeurs,id',
            'products_id' => 'required|integer|exists:products,id',
            'qt' => 'required|integer|min:1|max:9999',
            'online' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Récupérer les infos du produit
            $product = Product::with('pointValeur')->find($validatedData['products_id']);

            // Recalculer les valeurs
            $points_unitaires = $product->pointValeur->numbers ?? 0;
            $prix_unitaire = $product->prix_product;
            $montant_total = $prix_unitaire * $validatedData['qt'];

            // Sauvegarder les anciennes valeurs pour le log
            $oldData = $achat->toArray();

            // Mettre à jour
            $achat->update([
                'period' => $validatedData['period'],
                'distributeur_id' => $validatedData['distributeur_id'],
                'products_id' => $validatedData['products_id'],
                'qt' => $validatedData['qt'],
                'points_unitaire_achat' => $points_unitaires,
                'montant_total_ligne' => $montant_total,
                'prix_unitaire_achat' => $prix_unitaire,
                'online' => $validatedData['online'] ?? false,
            ]);

            Log::info("Achat modifié", [
                'id' => $achat->id,
                'old_data' => $oldData,
                'new_data' => $achat->toArray(),
                'updated_by' => auth()->id()
            ]);

            DB::commit();
            return redirect()
                ->route('admin.achats.index')
                ->with('success', 'Achat modifié avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur modification achat", [
                'id' => $achat->id,
                'error' => $e->getMessage()
            ]);
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la modification.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Achat $achat): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Log avant suppression
            Log::info("Achat supprimé", [
                'id' => $achat->id,
                'data' => $achat->toArray(),
                'deleted_by' => auth()->id()
            ]);

            $achat->delete();

            DB::commit();
            return redirect()
                ->route('admin.achats.index')
                ->with('success', 'Achat supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur suppression achat", [
                'id' => $achat->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Générer une liste de périodes
     */
    private function generatePeriods(): array
    {
        $periods = [];
        $currentDate = now();

        // Générer les 12 derniers mois
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $periods[$date->format('Y-m')] = $date->format('F Y');
        }

        return $periods;
    }
}
