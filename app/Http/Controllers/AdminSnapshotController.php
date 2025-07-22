<?php

// Assurez-vous que le namespace correspond à votre structure (ex: App\Http\Controllers\Admin)
namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // Importe le Controller de base Laravel
use Illuminate\Http\Request;        // Pour gérer la requête HTTP
use App\Services\SnapshotService;   // Le service qui fait le travail réel
use Illuminate\Support\Facades\Validator; // Pour valider les données du formulaire
use Illuminate\Support\Facades\Log;      // Pour enregistrer des informations/erreurs
// Si vous optez pour les Jobs (recommandé) :
// use App\Jobs\CreateSnapshotJob;

class AdminSnapshotController extends Controller
{
    /**
     * Instance du service de snapshot.
     * @var SnapshotService
     */
    protected SnapshotService $snapshotService;

    /**
     * Constructeur pour injecter les dépendances.
     *
     * @param SnapshotService $snapshotService Le service pour créer les snapshots.
     */
    public function __construct(SnapshotService $snapshotService)
    {
        $this->snapshotService = $snapshotService;

        // --- Rappel Important ---
        // Assurez-vous que les middlewares 'auth' et votre middleware 'admin'
        // sont appliqués aux ROUTES qui pointent vers ce contrôleur dans
        // votre fichier de routes (routes/web.php ou routes/admin.php).
        // Exemple dans le fichier de routes :
        // Route::middleware(['auth', 'votre_middleware_admin'])->group(function() { ... routes ici ... });
    }

    /**
     * Affiche le formulaire permettant à l'admin de lancer la création d'un snapshot.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        // Suggérer la période du mois précédent comme valeur par défaut
        // Utilise Carbon (intégré à Laravel) via la fonction helper now()
        $suggestedPeriod = now()->subMonth()->format('Y-m');

        Log::debug("Affichage du formulaire de création de snapshot.", ['suggested_period' => $suggestedPeriod]);

        // Retourne la vue Blade (assurez-vous que le chemin est correct)
        return view('admin.snapshots.create', compact('suggestedPeriod'));
    }

    /**
     * Traite la soumission du formulaire et lance la création du snapshot.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Log::info("Requête Administrateur reçue pour stocker un nouveau snapshot.");

        // --- 1. Validation des données du formulaire ---
        $validator = Validator::make($request->all(), [
            'period' => [
                'required',         // Le champ est obligatoire
                'string',           // Doit être une chaîne
                'regex:/^\d{4}-\d{2}$/' // Doit correspondre au format YYYY-MM
            ],
            'force' => [
                'nullable',         // Le champ peut être absent
                'boolean'          // S'il est présent, doit être interprétable comme vrai/faux (ex: '1', true)
            ],
        ]);

        // Si la validation échoue...
        if ($validator->fails()) {
            Log::warning("Validation échouée pour la création de snapshot.", $validator->errors()->toArray());
            // Rediriger vers la page précédente avec les erreurs et les anciennes valeurs
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        // Récupérer les données validées
        $validated = $validator->validated();
        $period = $validated['period'];
        // Si 'force' n'est pas dans la requête, $validated['force'] sera null. ?? false le transforme en booléen.
        $force = $validated['force'] ?? false;

        Log::info("Validation réussie. Lancement du processus de snapshot.", ['period' => $period, 'force' => $force]);

        // --- 2. Appel du Service (Exécution directe) ---
        // ATTENTION : Si beaucoup de distributeurs, cela peut être LONG et causer un timeout.
        // L'utilisation d'un Job en arrière-plan est VIVEMENT recommandée en production.
        try {
            // Appelle la méthode du service qui fait le travail lourd
            $result = $this->snapshotService->createSnapshot($period, $force);

            // --- 3. Redirection avec message de résultat ---
            if ($result['success']) {
                Log::info("Snapshot créé avec succès via le service.", ['result' => $result]);
                // Rediriger vers la page du formulaire avec un message de succès
                return redirect()->route('admin.snapshots.create')
                            ->with('success', $result['message']);
            } else {
                Log::warning("Échec de la création du snapshot via le service.", ['result' => $result]);
                // Rediriger vers la page précédente avec le message d'erreur du service
                return redirect()->back()
                            ->with('error', $result['message'])
                            ->withInput(); // Garder les valeurs saisies par l'utilisateur
            }
        // Gérer les exceptions imprévues qui pourraient survenir dans le service
        } catch (\Exception $e) {
            Log::error("Erreur inattendue lors de l'exécution du SnapshotService.", [
                'period' => $period,
                'force' => $force,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Utile pour le débogage
            ]);
            // Rediriger vers la page précédente avec un message d'erreur générique
            return redirect()->back()
                       ->with('error', 'Une erreur serveur inattendue est survenue. Veuillez consulter les logs.')
                       ->withInput();
        }

        /*
        // --- ALTERNATIVE FORTEMENT RECOMMANDÉE : Utilisation d'un Job ---
        try {
            Log::info("Mise en file d'attente du Job CreateSnapshotJob.", ['period' => $period, 'force' => $force]);

            // Assurez-vous d'avoir créé le Job : php artisan make:job CreateSnapshotJob
            // Le Job injectera et appellera lui-même le SnapshotService dans sa méthode handle()
            \App\Jobs\CreateSnapshotJob::dispatch($period, $force); // Lance le job en arrière-plan

            // Retourner un message indiquant que la tâche est lancée
            return redirect()->route('admin.snapshots.create')
                        ->with('success', "La création du snapshot pour la période {$period} a été mise en file d'attente et sera traitée en arrière-plan.");

        } catch (\Exception $e) {
             Log::error("Impossible de mettre en file d'attente le CreateSnapshotJob.", [
                 'period' => $period,
                 'force' => $force,
                 'error' => $e->getMessage()
             ]);
             return redirect()->back()
                        ->with('error', "Erreur lors du lancement de la tâche de snapshot. Vérifiez la configuration de la file d'attente.")
                        ->withInput();
        }
        */
    }
}
