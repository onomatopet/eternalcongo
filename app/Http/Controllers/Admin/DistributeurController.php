<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributeur;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class DistributeurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // 1. Récupérer le terme de recherche depuis la requête GET
        $searchTerm = $request->query('search');

        // 2. Construire la requête de base
        $distributeursQuery = Distributeur::query();

        // 3. Appliquer le filtre de recherche si un terme est fourni
        if ($searchTerm) {
            $this->info("Recherche de distributeurs avec le terme : {$searchTerm}");
            $distributeursQuery->where(function ($query) use ($searchTerm) {
                $query->where('nom_distributeur', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('pnom_distributeur', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('distributeur_id', 'LIKE', "%{$searchTerm}%");
            });
        } else {
            $this->info("Affichage de tous les distributeurs (aucune recherche).");
        }

        // 4. Trier et paginer
        $distributeurs = $distributeursQuery->orderBy('nom_distributeur', 'asc')
                                            ->paginate(15)
                                            ->withQueryString();

        // 5. Passer les données à la vue
        return view('admin.distributeurs.index', [
            'distributeurs' => $distributeurs,
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Recherche AJAX de distributeurs
     * Utilisée pour le champ de recherche du parent dans le formulaire de création
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        // Ne rechercher que si la requête a au moins 2 caractères
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Rechercher les distributeurs
        $distributeurs = Distributeur::where(function ($q) use ($query) {
                $q->where('nom_distributeur', 'LIKE', "%{$query}%")
                  ->orWhere('pnom_distributeur', 'LIKE', "%{$query}%")
                  ->orWhere('distributeur_id', 'LIKE', "%{$query}%")
                  ->orWhere('tel_distributeur', 'LIKE', "%{$query}%");
            })
            ->select('id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur', 'tel_distributeur')
            ->orderBy('nom_distributeur')
            ->limit(20) // Limiter à 20 résultats pour la performance
            ->get();

        // Retourner les résultats en JSON
        return response()->json($distributeurs);
    }

    /**
     * Modifiez la méthode create existante
     * Plus besoin de charger tous les parents potentiels
     */
    public function create(): View
    {
        // Ne plus charger les 8500 distributeurs !
        // La recherche AJAX s'en occupe
        return view('admin.distributeurs.create');
    }

    /**
    * Store a newly created resource in storage.
    * Traite la soumission du formulaire d'ajout de distributeur.
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */
    public function store(Request $request): RedirectResponse
    {
        // --- VALIDATION ---
        $validatedData = $request->validate([
            'nom_distributeur' => 'required|string|max:120',
            'pnom_distributeur' => 'required|string|max:120',
            // Assurer unicité du matricule
            'distributeur_id' => 'required|numeric|unique:distributeurs,distributeur_id',
            'tel_distributeur' => 'nullable|string|max:120',
            'adress_distributeur' => 'nullable|string|max:120',
            // id_distrib_parent peut être null, mais s'il est fourni, il doit exister
            'id_distrib_parent' => 'nullable|integer|exists:distributeurs,id',
            // Valeurs initiales pour etoiles/rang/flags
            'etoiles_id' => 'required|integer|min:1',
            'rang' => 'nullable|integer', // CHANGÉ : nullable au lieu de required
            'statut_validation_periode' => 'sometimes|boolean',
        ]);

        // Ajouter les valeurs par défaut si non fournies
        $validatedData['etoiles_id'] = $validatedData['etoiles_id'] ?? 1;
        $validatedData['rang'] = $validatedData['rang'] ?? 0; // Valeur par défaut si non fourni
        $validatedData['statut_validation_periode'] = $validatedData['statut_validation_periode'] ?? 0;

        // --- CREATION ---
        try {
            Distributeur::create($validatedData);

            Log::info("Distributeur créé: Matricule {$validatedData['distributeur_id']}");
            return redirect()->route('admin.distributeurs.index')->with('success', 'Distributeur ajouté avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création du distributeur: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de l\'ajout du distributeur.');
        }
    }

    /**
     * Modifiez aussi la méthode show pour supporter AJAX
     * Pour récupérer les infos d'un distributeur spécifique
     */
    public function show(Distributeur $distributeur)
    {
        // Si c'est une requête AJAX, retourner du JSON
        if (request()->ajax()) {
            return response()->json([
                'id' => $distributeur->id,
                'distributeur_id' => $distributeur->distributeur_id,
                'nom_distributeur' => $distributeur->nom_distributeur,
                'pnom_distributeur' => $distributeur->pnom_distributeur,
                'tel_distributeur' => $distributeur->tel_distributeur,
            ]);
        }

        // Sinon, retourner la vue normale
        return view('admin.distributeurs.show', compact('distributeur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distributeur $distributeur): View
    {
        // Récupérer les parents potentiels (exclure le distributeur lui-même)
        $potentialParents = Distributeur::where('id', '!=', $distributeur->id)
                                      ->orderBy('nom_distributeur')
                                      ->select('id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur')
                                      ->limit(1000)
                                      ->get()
                                      ->mapWithKeys(function ($item) {
                                          return [$item->id => "#{$item->distributeur_id} - {$item->pnom_distributeur} {$item->nom_distributeur}"];
                                      });

        return view('admin.distributeurs.edit', compact('distributeur', 'potentialParents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Distributeur $distributeur): RedirectResponse
    {
        // Validation
        $validatedData = $request->validate([
            'nom_distributeur' => 'required|string|max:120',
            'pnom_distributeur' => 'required|string|max:120',
            'distributeur_id' => 'required|numeric|unique:distributeurs,distributeur_id,' . $distributeur->id,
            'tel_distributeur' => 'nullable|string|max:120',
            'adress_distributeur' => 'nullable|string|max:120',
            'id_distrib_parent' => 'nullable|integer|exists:distributeurs,id',
            'etoiles_id' => 'required|integer|min:1',
            'rang' => 'nullable|integer|min:0', // Changé de required à nullable
            'statut_validation_periode' => 'boolean',
        ]);

        // Vérifier qu'on ne crée pas une boucle (parent de soi-même)
        if (isset($validatedData['id_distrib_parent']) && $validatedData['id_distrib_parent'] == $distributeur->id) {
            return back()->withInput()->with('error', 'Un distributeur ne peut pas être son propre parent.');
        }

        $validatedData['statut_validation_periode'] = $validatedData['statut_validation_periode'] ?? false;

        // Mise à jour
        try {
            $distributeur->update($validatedData);

            Log::info("Distributeur modifié: ID {$distributeur->id}");
            return redirect()->route('admin.distributeurs.index')->with('success', 'Distributeur modifié avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification du distributeur: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de la modification du distributeur.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distributeur $distributeur): RedirectResponse
    {
        try {
            // Vérifier s'il a des enfants
            if ($distributeur->children()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer ce distributeur car il a des enfants dans le réseau.');
            }

            // Vérifier s'il a des achats
            if ($distributeur->achats()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer ce distributeur car il a des achats enregistrés.');
            }

            $distributeur->delete();
            Log::info("Distributeur supprimé: ID {$distributeur->id}");
            return redirect()->route('admin.distributeurs.index')->with('success', 'Distributeur supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression du distributeur: " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression du distributeur.');
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
