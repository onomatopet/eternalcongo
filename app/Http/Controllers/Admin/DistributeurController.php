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
        // Validation
        $validatedData = $request->validate([
            'nom_distributeur' => 'required|string|max:100',
            'pnom_distributeur' => 'required|string|max:100',
            'distributeur_id' => 'required|string|max:50|unique:distributeurs,distributeur_id',
            'tel_distributeur' => 'nullable|string|max:20',
            'email_distributeur' => 'nullable|email|max:100|unique:distributeurs,email_distributeur',
            'id_distrib_parent' => 'nullable|integer|exists:distributeurs,id',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier la cohérence de la hiérarchie
            if (!empty($validatedData['id_distrib_parent'])) {
                // CORRECTION : Vérification d'existence du parent
                $parent = Distributeur::find($validatedData['id_distrib_parent']);
                if (!$parent) {
                    throw new \Exception("Le distributeur parent sélectionné n'existe pas.");
                }

                // Calculer la profondeur
                $depth = $this->calculateDepth($validatedData['id_distrib_parent']);
                if ($depth >= 10) {
                    throw new \Exception("La hiérarchie ne peut pas dépasser 10 niveaux.");
                }
            }

            // Créer le distributeur
            $distributeur = Distributeur::create($validatedData);

            DB::commit();
            Log::info("Nouveau distributeur créé", ['id' => $distributeur->id, 'matricule' => $distributeur->distributeur_id]);

            return redirect()
                ->route('admin.distributeurs.show', $distributeur)
                ->with('success', 'Distributeur créé avec succès. Matricule: ' . $distributeur->distributeur_id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création distributeur", [
                'error' => $e->getMessage(),
                'data' => $validatedData
            ]);
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du distributeur: ' . $e->getMessage());
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
            'last_achat' => optional($distributeur->achats()->latest()->first()),
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
            'nom_distributeur' => 'required|string|max:100',
            'pnom_distributeur' => 'required|string|max:100',
            'distributeur_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('distributeurs')->ignore($distributeur->id),
            ],
            'tel_distributeur' => 'nullable|string|max:20',
            'email_distributeur' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('distributeurs')->ignore($distributeur->id),
            ],
            'id_distrib_parent' => 'nullable|integer|exists:distributeurs,id',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier la cohérence de la hiérarchie si parent changé
            if (isset($validatedData['id_distrib_parent']) &&
                $validatedData['id_distrib_parent'] != $distributeur->id_distrib_parent) {

                // CORRECTION : Vérification d'existence du parent
                if ($validatedData['id_distrib_parent']) {
                    $parent = Distributeur::find($validatedData['id_distrib_parent']);
                    if (!$parent) {
                        throw new \Exception("Le distributeur parent sélectionné n'existe pas.");
                    }
                }

                // Ne peut pas être son propre parent
                if ($validatedData['id_distrib_parent'] == $distributeur->id) {
                    throw new \Exception("Un distributeur ne peut pas être son propre parent.");
                }

                // Vérifier qu'on ne crée pas de boucle
                $descendantIds = $this->getDescendantIds($distributeur->id);
                if (in_array($validatedData['id_distrib_parent'], $descendantIds)) {
                    throw new \Exception("Impossible: cela créerait une boucle dans la hiérarchie.");
                }

                // Vérifier la profondeur
                $depth = $this->calculateDepth($validatedData['id_distrib_parent']) + 1;
                if ($depth >= 10) {
                    throw new \Exception("La hiérarchie ne peut pas dépasser 10 niveaux.");
                }
            }

            // Mettre à jour
            $distributeur->update($validatedData);

            DB::commit();
            Log::info("Distributeur mis à jour", ['id' => $distributeur->id]);

            return redirect()
                ->route('admin.distributeurs.show', $distributeur)
                ->with('success', 'Distributeur mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur mise à jour distributeur", [
                'id' => $distributeur->id,
                'error' => $e->getMessage(),
                'data' => $validatedData
            ]);
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distributeur $distributeur): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Vérifier les dépendances
            if ($distributeur->children()->exists()) {
                throw new \Exception("Impossible de supprimer : ce distributeur a des enfants dans le réseau.");
            }

            if ($distributeur->achats()->exists()) {
                throw new \Exception("Impossible de supprimer : ce distributeur a des achats enregistrés.");
            }

            if ($distributeur->bonuses()->exists()) {
                throw new \Exception("Impossible de supprimer : ce distributeur a des bonus enregistrés.");
            }

            // Log avant suppression
            Log::info("Suppression distributeur", [
                'id' => $distributeur->id,
                'matricule' => $distributeur->distributeur_id,
                'nom' => $distributeur->full_name
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
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Recherche AJAX de distributeurs
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');

        // Si la recherche est vide, retourner les 20 premiers distributeurs
        $query = Distributeur::query();

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nom_distributeur', 'LIKE', "%{$search}%")
                  ->orWhere('pnom_distributeur', 'LIKE', "%{$search}%")
                  ->orWhere('distributeur_id', 'LIKE', "%{$search}%")
                  ->orWhere('tel_distributeur', 'LIKE', "%{$search}%");
            });
        }

        $distributeurs = $query->orderBy('nom_distributeur')
                              ->limit(20)
                              ->get(['id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur', 'tel_distributeur']);

        // Formater les résultats pour Select2/Ajax
        $results = $distributeurs->map(function($dist) {
            return [
                'id' => $dist->id,
                'text' => "#{$dist->distributeur_id} - {$dist->pnom_distributeur} {$dist->nom_distributeur}",
                'distributeur_id' => $dist->distributeur_id,
                'nom_distributeur' => $dist->nom_distributeur,
                'pnom_distributeur' => $dist->pnom_distributeur,
                'tel_distributeur' => $dist->tel_distributeur
            ];
        })->toArray();

        // Format attendu par Select2
        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => false
            ]
        ]);
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
