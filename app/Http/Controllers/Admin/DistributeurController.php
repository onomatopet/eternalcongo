<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributeur;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

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
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Récupérer les parents potentiels
        $potentialParents = Distributeur::orderBy('nom_distributeur')
                                      ->select('id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur')
                                      ->limit(1000)
                                      ->get()
                                      ->mapWithKeys(function ($item) {
                                          return [$item->id => "#{$item->distributeur_id} - {$item->pnom_distributeur} {$item->nom_distributeur}"];
                                      });

        return view('admin.distributeurs.create', compact('potentialParents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation
        $validatedData = $request->validate([
            'nom_distributeur' => 'required|string|max:120',
            'pnom_distributeur' => 'required|string|max:120',
            'distributeur_id' => 'required|numeric|unique:distributeurs,distributeur_id',
            'tel_distributeur' => 'nullable|string|max:120',
            'adress_distributeur' => 'nullable|string|max:120',
            'id_distrib_parent' => 'nullable|integer|exists:distributeurs,id',
            'etoiles_id' => 'required|integer|min:1',
            'rang' => 'nullable|integer|min:0', // Changé de required à nullable
            'statut_validation_periode' => 'boolean',
        ]);

        // Ajouter les valeurs par défaut
        $validatedData['etoiles_id'] = $validatedData['etoiles_id'] ?? 1;
        $validatedData['rang'] = $validatedData['rang'] ?? 0; // Garde la valeur par défaut
        $validatedData['statut_validation_periode'] = $validatedData['statut_validation_periode'] ?? false;

        // Création
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
     * Display the specified resource.
     */
    public function show(Distributeur $distributeur): View
    {
        // Charger les relations
        $distributeur->load(['parent', 'children', 'achats.product']);
        
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