<?php

namespace App\Http\Controllers\Admin; // Adaptez si nécessaire

use App\Http\Controllers\Controller;
use App\Models\Distributeur;
use Illuminate\Http\Request; // Important: Importer Request
use Illuminate\View\View;
use Illuminate\Support\Facades\Log; // Pour les logs si besoin
use Illuminate\Http\RedirectResponse;

class DistributeurController extends Controller
{
    /**
     * Display a listing of the resource.
     * Ajout de la recherche par mot-clé.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View // Injecter Request
    {
        // 1. Récupérer le terme de recherche depuis la requête GET
        $searchTerm = $request->query('search'); // 'search' sera le nom du champ input

        // 2. Construire la requête de base
        $distributeursQuery = Distributeur::query(); // Commencer avec query() pour ajouter des conditions

        // 3. Appliquer le filtre de recherche si un terme est fourni
        if ($searchTerm) {
            $this->info("Recherche de distributeurs avec le terme : {$searchTerm}"); // Log facultatif
            $distributeursQuery->where(function ($query) use ($searchTerm) {
                $query->where('nom_distributeur', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('pnom_distributeur', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('distributeur_id', 'LIKE', "%{$searchTerm}%"); // Recherche sur le matricule aussi
                    // Ajoutez d'autres colonnes si pertinent (ex: téléphone, email)
                    // ->orWhere('tel_distributeur', 'LIKE', "%{$searchTerm}%");
            });
        } else {
             $this->info("Affichage de tous les distributeurs (aucune recherche).");
        }

        // 4. Trier et Paginer
        $distributeurs = $distributeursQuery->orderBy('nom_distributeur', 'asc')
                                            ->paginate(15)
                                            ->withQueryString(); // <-- IMPORTANT: pour conserver le 'search' dans les liens de pagination

        // 5. Passer les données à la vue (y compris le terme de recherche pour le réafficher)
        return view('admin.distributeurs.index', [
            'distributeurs' => $distributeurs,
            'searchTerm' => $searchTerm // Passer le terme de recherche à la vue
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * // A implémenter plus tard
     */
    public function create(): View
    {
        // On pourrait passer la liste des parents potentiels ici
        // Mais une recherche AJAX est préférable si la liste est longue
        $potentialParents = Distributeur::orderBy('nom_distributeur')
                                      ->select('id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur')
                                      ->limit(1000) // Limiter pour éviter surcharge initiale
                                      ->get()
                                      ->mapWithKeys(function ($item) {
                                            return [$item->id => "#{$item->distributeur_id} - {$item->pnom_distributeur} {$item->nom_distributeur}"];
                                        });


        return view('admin.distributeurs.create', compact('potentialParents'));
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
             // Assurer unicité du matricule, ignorer l'enregistrement courant si update plus tard
            'distributeur_id' => 'required|numeric|unique:distributeurs,distributeur_id',
            'tel_distributeur' => 'nullable|string|max:120',
            'adress_distributeur' => 'nullable|string|max:120',
            // id_distrib_parent peut être null, mais s'il est fourni, il doit exister comme ID primaire
            'id_distrib_parent' => 'nullable|integer|exists:distributeurs,id',
             // Valeurs initiales pour etoiles/rang/flags (à définir selon vos règles)
             'etoiles_id' => 'required|integer|min:1', // Ou valeur par défaut ?
             'rang' => 'required|integer',            // Ou valeur par défaut ?
             'statut_validation_periode' => 'sometimes|boolean',
        ]);

         // Ajouter les valeurs par défaut si non fournies explicitement ou si validées par 'sometimes'
         $validatedData['etoiles_id'] = $validatedData['etoiles_id'] ?? 1;
         $validatedData['rang'] = $validatedData['rang'] ?? 0;
         $validatedData['statut_validation_periode'] = $validatedData['statut_validation_periode'] ?? 0; // Ou 1 si besoin

        // --- CREATION ---
        try {
            Distributeur::create($validatedData);

            Log::info("Distributeur créé: Matricule {$validatedData['distributeur_id']}");
            return redirect()->route('admin.distributeurs.index')->with('success', 'Distributeur ajouté avec succès.');

        } catch (\Exception $e) {
             Log::error("Erreur lors de la création du distributeur: " . $e->getMessage());
             // Retourner à la page précédente avec les erreurs de validation ET l'erreur générale
             return back()->withInput()->with('error', 'Erreur lors de l\'ajout du distributeur.');
        }
    }

    /**
     * Display the specified resource.
     * // A implémenter plus tard (pour le bouton "Consulter")
     */
    public function show(Distributeur $distributeur) // Utilise le Route Model Binding
    {
        // Exemple: return view('admin.distributeurs.show', compact('distributeur'));
        return "Affichage du distributeur ID: " . $distributeur->id; // Placeholder
    }

    /**
     * Show the form for editing the specified resource.
     * // A implémenter plus tard (pour le bouton "Modifier")
     */
    public function edit(Distributeur $distributeur) // Utilise le Route Model Binding
    {
         // Exemple: return view('admin.distributeurs.edit', compact('distributeur'));
         return "Modification du distributeur ID: " . $distributeur->id; // Placeholder
    }

    /**
     * Update the specified resource in storage.
      * // A implémenter plus tard
     */
    public function update(Request $request, Distributeur $distributeur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * // A implémenter plus tard (pour le bouton "Supprimer")
     */
    public function destroy(Distributeur $distributeur) // Utilise le Route Model Binding
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
