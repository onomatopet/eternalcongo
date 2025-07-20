<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributeur;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DistributeurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // 1. Récupérer le terme de recherche depuis la requête GET
        $searchTerm = $request->query('search');

        // 2. Construire la requête de base avec eager loading
        $distributeursQuery = Distributeur::with('parent');

        // 3. Appliquer le filtre de recherche si un terme est fourni
        if ($searchTerm) {
            Log::info("Recherche de distributeurs avec le terme : {$searchTerm}");
            $distributeursQuery->where(function ($query) use ($searchTerm) {
                $query->where('nom_distributeur', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('pnom_distributeur', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('distributeur_id', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('tel_distributeur', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 4. Trier et paginer
        $distributeurs = $distributeursQuery->orderBy('created_at', 'desc')
                                            ->paginate(15)
                                            ->withQueryString();

        // 5. Passer les données à la vue
        return view('admin.distributeurs.index', [
            'distributeurs' => $distributeurs,
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Récupérer la liste des distributeurs pour le champ parent
        $distributeurs = Distributeur::orderBy('nom_distributeur')
                                    ->select('id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur')
                                    ->get();

        return view('admin.distributeurs.create', compact('distributeurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation avancée
        $validatedData = $request->validate([
            'distributeur_id' => [
                'required',
                'string',
                'max:50',
                'unique:distributeurs,distributeur_id',
                'regex:/^[A-Z0-9\-_]+$/i' // Format alphanumérique avec tirets et underscores
            ],
            'nom_distributeur' => [
                'required',
                'string',
                'max:120',
                'regex:/^[\pL\s\-\']+$/u' // Lettres, espaces, tirets et apostrophes
            ],
            'pnom_distributeur' => [
                'required',
                'string',
                'max:120',
                'regex:/^[\pL\s\-\']+$/u'
            ],
            'tel_distributeur' => [
                'nullable',
                'string',
                'max:120',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/' // Format téléphone international
            ],
            'adress_distributeur' => 'nullable|string|max:255',
            'id_distrib_parent' => [
                'nullable',
                'integer',
                'exists:distributeurs,id',
                function ($attribute, $value, $fail) {
                    // Vérifier la profondeur de la hiérarchie (éviter les chaînes trop longues)
                    if ($value) {
                        $depth = $this->calculateDepth($value);
                        if ($depth >= 10) {
                            $fail('La chaîne de parrainage ne peut pas dépasser 10 niveaux.');
                        }
                    }
                }
            ],
            'etoiles_id' => 'required|integer|min:1|max:10',
            'rang' => 'required|integer|min:0',
            'statut_validation_periode' => 'boolean',
        ], [
            // Messages personnalisés
            'distributeur_id.required' => 'Le matricule est obligatoire.',
            'distributeur_id.unique' => 'Ce matricule existe déjà.',
            'distributeur_id.regex' => 'Le matricule ne peut contenir que des lettres, chiffres, tirets et underscores.',
            'nom_distributeur.required' => 'Le nom est obligatoire.',
            'nom_distributeur.regex' => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'pnom_distributeur.required' => 'Le prénom est obligatoire.',
            'tel_distributeur.regex' => 'Le format du numéro de téléphone est invalide.',
            'id_distrib_parent.exists' => 'Le distributeur parent sélectionné n\'existe pas.',
        ]);

        // Transformer les données
        $validatedData['statut_validation_periode'] = $validatedData['statut_validation_periode'] ?? true;
        $validatedData['rang'] = $validatedData['rang'] ?? 0;

        // Transaction pour garantir l'intégrité
        DB::beginTransaction();
        try {
            $distributeur = Distributeur::create($validatedData);

            // Log de l'action
            Log::info("Distributeur créé", [
                'id' => $distributeur->id,
                'matricule' => $distributeur->distributeur_id,
                'created_by' => auth()->id()
            ]);

            DB::commit();
            return redirect()
                ->route('admin.distributeurs.index')
                ->with('success', 'Distributeur créé avec succès. Matricule: ' . $distributeur->distributeur_id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création distributeur", [
                'error' => $e->getMessage(),
                'data' => $validatedData
            ]);
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du distributeur.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Distributeur $distributeur)
    {
        // Charger les relations nécessaires
        $distributeur->load(['parent', 'children', 'achats' => function($query) {
            $query->latest()->limit(10);
        }]);

        // Si requête AJAX, retourner JSON
        if (request()->ajax()) {
            return response()->json([
                'distributeur' => $distributeur,
                'statistics' => [
                    'total_children' => $distributeur->children()->count(),
                    'total_achats' => $distributeur->achats()->count(),
                    'total_points' => $distributeur->achats()->sum('points_unitaire_achat'),
                ]
            ]);
        }

        // Calculer quelques statistiques
        $statistics = [
            'total_children' => $distributeur->children()->count(),
            'total_achats' => $distributeur->achats()->count(),
            'last_achat' => $distributeur->achats()->latest()->first(),
        ];

        return view('admin.distributeurs.show', compact('distributeur', 'statistics'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distributeur $distributeur): View
    {
        // Récupérer les parents potentiels (exclure le distributeur et ses descendants)
        $excludedIds = $this->getDescendantIds($distributeur->id);
        $excludedIds[] = $distributeur->id;

        $potentialParents = Distributeur::whereNotIn('id', $excludedIds)
                                      ->orderBy('nom_distributeur')
                                      ->select('id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur')
                                      ->get();

        return view('admin.distributeurs.edit', compact('distributeur', 'potentialParents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Distributeur $distributeur): RedirectResponse
    {
        // Validation
        $validatedData = $request->validate([
            'distributeur_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('distributeurs')->ignore($distributeur->id),
                'regex:/^[A-Z0-9\-_]+$/i'
            ],
            'nom_distributeur' => [
                'required',
                'string',
                'max:120',
                'regex:/^[\pL\s\-\']+$/u'
            ],
            'pnom_distributeur' => [
                'required',
                'string',
                'max:120',
                'regex:/^[\pL\s\-\']+$/u'
            ],
            'tel_distributeur' => [
                'nullable',
                'string',
                'max:120',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/'
            ],
            'adress_distributeur' => 'nullable|string|max:255',
            'id_distrib_parent' => [
                'nullable',
                'integer',
                'exists:distributeurs,id',
                function ($attribute, $value, $fail) use ($distributeur) {
                    // Empêcher de se définir comme son propre parent
                    if ($value == $distributeur->id) {
                        $fail('Un distributeur ne peut pas être son propre parent.');
                    }
                    // Empêcher de créer une boucle dans la hiérarchie
                    if ($value && in_array($value, $this->getDescendantIds($distributeur->id))) {
                        $fail('Cette modification créerait une boucle dans la hiérarchie.');
                    }
                }
            ],
            'etoiles_id' => 'required|integer|min:1|max:10',
            'rang' => 'required|integer|min:0',
            'statut_validation_periode' => 'boolean',
        ]);

        $validatedData['statut_validation_periode'] = $validatedData['statut_validation_periode'] ?? false;

        DB::beginTransaction();
        try {
            // Sauvegarder les anciennes valeurs pour le log
            $oldData = $distributeur->toArray();

            $distributeur->update($validatedData);

            Log::info("Distributeur modifié", [
                'id' => $distributeur->id,
                'old_data' => $oldData,
                'new_data' => $validatedData,
                'updated_by' => auth()->id()
            ]);

            DB::commit();
            return redirect()
                ->route('admin.distributeurs.index')
                ->with('success', 'Distributeur modifié avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur modification distributeur", [
                'id' => $distributeur->id,
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
    public function destroy(Distributeur $distributeur): RedirectResponse
    {
        // Vérifications avant suppression
        if ($distributeur->children()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce distributeur car il a des filleuls dans le réseau.');
        }

        if ($distributeur->achats()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce distributeur car il a des achats enregistrés.');
        }

        if ($distributeur->bonuses()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce distributeur car il a des bonus enregistrés.');
        }

        DB::beginTransaction();
        try {
            // Log avant suppression
            Log::info("Distributeur supprimé", [
                'id' => $distributeur->id,
                'matricule' => $distributeur->distributeur_id,
                'deleted_by' => auth()->id()
            ]);

            $distributeur->delete();

            DB::commit();
            return redirect()
                ->route('admin.distributeurs.index')
                ->with('success', 'Distributeur supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur suppression distributeur", [
                'id' => $distributeur->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Recherche AJAX de distributeurs
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');

        $distributeurs = Distributeur::where(function($query) use ($search) {
                $query->where('nom_distributeur', 'LIKE', "%{$search}%")
                      ->orWhere('pnom_distributeur', 'LIKE', "%{$search}%")
                      ->orWhere('distributeur_id', 'LIKE', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur']);

        $results = $distributeurs->map(function($dist) {
            return [
                'id' => $dist->id,
                'text' => "{$dist->distributeur_id} - {$dist->pnom_distributeur} {$dist->nom_distributeur}"
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Calculer la profondeur dans la hiérarchie
     */
    private function calculateDepth($parentId, $depth = 0): int
    {
        if (!$parentId || $depth > 20) return $depth; // Protection contre les boucles infinies

        $parent = Distributeur::find($parentId);
        if (!$parent || !$parent->id_distrib_parent) {
            return $depth;
        }

        return $this->calculateDepth($parent->id_distrib_parent, $depth + 1);
    }

    /**
     * Obtenir tous les IDs des descendants d'un distributeur
     */
    private function getDescendantIds($distributeurId): array
    {
        $descendants = [];
        $children = Distributeur::where('id_distrib_parent', $distributeurId)->pluck('id')->toArray();

        foreach ($children as $childId) {
            $descendants[] = $childId;
            $descendants = array_merge($descendants, $this->getDescendantIds($childId));
        }

        return $descendants;
    }
}
