<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributeur;
use App\Models\DeletionRequest;
use App\Services\BackupService;
use App\Services\DeletionValidationService;
use Illuminate\Http\Request;
use App\Traits\HasPermissions;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DistributeurController extends Controller
{
    private BackupService $backupService;
    private DeletionValidationService $validationService;

    public function __construct(BackupService $backupService, DeletionValidationService $validationService)
    {
        $this->backupService = $backupService;
        $this->validationService = $validationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Construire la requête de base
        $distributeurs = Distributeur::with(['parent', 'children'])
            ->orderBy('created_at', 'desc');

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $distributeurs->where(function($query) use ($search) {
                $query->where('nom_distributeur', 'LIKE', "%{$search}%")
                      ->orWhere('pnom_distributeur', 'LIKE', "%{$search}%")
                      ->orWhere('distributeur_id', 'LIKE', "%{$search}%")
                      ->orWhere('tel_distributeur', 'LIKE', "%{$search}%")
                      ->orWhere('adress_distributeur', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par grade
        if ($request->filled('grade_filter')) {
            $distributeurs->where('etoiles_id', $request->input('grade_filter'));
        }

        // Filtre par statut
        if ($request->filled('status_filter')) {
            $distributeurs->where('statut_validation_periode', $request->boolean('status_filter'));
        }

        // Paginer les résultats
        $distributeurs = $distributeurs->paginate(20)->withQueryString();

        // Statistiques pour l'interface
        $stats = $this->getDistributeursStats();

        return view('admin.distributeurs.index', compact('distributeurs', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Récupérer les distributeurs potentiels comme parents
        $potentialParents = Distributeur::orderBy('nom_distributeur')
                                      ->select('id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur')
                                      ->get();

        return view('admin.distributeurs.create', compact('potentialParents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation des données
        $validatedData = $request->validate([
            'distributeur_id' => 'required|string|max:20|unique:distributeurs,distributeur_id',
            'nom_distributeur' => 'required|string|max:100',
            'pnom_distributeur' => 'required|string|max:100',
            'tel_distributeur' => 'nullable|string|max:20',
            'adress_distributeur' => 'nullable|string|max:255',
            'id_distrib_parent' => 'nullable|exists:distributeurs,id',
            'etoiles_id' => 'nullable|integer|min:0|max:10',
            'rang' => 'nullable|integer|min:0',
            'statut_validation_periode' => 'nullable|boolean'
        ]);

        DB::beginTransaction();
        try {
            // Vérifications métier spécifiques
            if (isset($validatedData['id_distrib_parent'])) {
                $parent = Distributeur::find($validatedData['id_distrib_parent']);
                if (!$parent) {
                    throw new \Exception('Le distributeur parent sélectionné n\'existe pas.');
                }

                // Vérifier qu'on ne crée pas de boucle dans la hiérarchie
                if ($this->wouldCreateLoop($validatedData['id_distrib_parent'], null)) {
                    throw new \Exception('Cette assignation créerait une boucle dans la hiérarchie.');
                }
            }

            // Valeurs par défaut
            $validatedData['etoiles_id'] = $validatedData['etoiles_id'] ?? 1;
            $validatedData['rang'] = $validatedData['rang'] ?? 0;
            $validatedData['statut_validation_periode'] = $validatedData['statut_validation_periode'] ?? false;

            // Créer le distributeur
            $distributeur = Distributeur::create($validatedData);

            DB::commit();

            Log::info("Nouveau distributeur créé", [
                'id' => $distributeur->id,
                'matricule' => $distributeur->distributeur_id,
                'nom' => $distributeur->full_name,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('admin.distributeurs.show', $distributeur)
                ->with('success', 'Distributeur créé avec succès. Matricule: ' . $distributeur->distributeur_id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création distributeur", [
                'error' => $e->getMessage(),
                'data' => $validatedData,
                'user_id' => Auth::id()
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
        // Charger les relations nécessaires avec comptages
        $distributeur->load([
            'parent',
            'children' => function($query) {
                $query->orderBy('nom_distributeur');
            },
            'achats' => function($query) {
                $query->latest()->limit(10);
            },
            'levelCurrents' => function($query) {
                $query->latest()->limit(5);
            }
        ]);

        // Si requête AJAX, retourner JSON
        if (request()->ajax()) {
            return response()->json([
                'distributeur' => $distributeur,
                'statistics' => $this->getDistributeurStatistics($distributeur)
            ]);
        }

        // Calculer des statistiques détaillées
        $statistics = $this->getDistributeurStatistics($distributeur);

        // Récupérer l'historique des modifications récentes
        $recentChanges = $this->getRecentChanges($distributeur);

        return view('admin.distributeurs.show', compact('distributeur', 'statistics', 'recentChanges'));
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

        // Historique des modifications pour cet utilisateur
        $modificationHistory = $this->getModificationHistory($distributeur);

        return view('admin.distributeurs.edit', compact('distributeur', 'potentialParents', 'modificationHistory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Distributeur $distributeur): RedirectResponse
    {
        // Validation des données (matricule unique sauf pour ce distributeur)
        $validatedData = $request->validate([
            'distributeur_id' => 'required|string|max:20|unique:distributeurs,distributeur_id,' . $distributeur->id,
            'nom_distributeur' => 'required|string|max:100',
            'pnom_distributeur' => 'required|string|max:100',
            'tel_distributeur' => 'nullable|string|max:20',
            'adress_distributeur' => 'nullable|string|max:255',
            'id_distrib_parent' => 'nullable|exists:distributeurs,id',
            'etoiles_id' => 'nullable|integer|min:0|max:10',
            'rang' => 'nullable|integer|min:0',
            'statut_validation_periode' => 'nullable|boolean'
        ]);

        DB::beginTransaction();
        try {
            // Sauvegarder l'état avant modification pour l'audit
            $originalData = $distributeur->toArray();

            // Vérifications métier spécifiques
            if (isset($validatedData['id_distrib_parent']) && $validatedData['id_distrib_parent'] != $distributeur->id_distrib_parent) {
                // Changement de parent détecté - validation avancée
                if ($validatedData['id_distrib_parent']) {
                    $newParent = Distributeur::find($validatedData['id_distrib_parent']);
                    if (!$newParent) {
                        throw new \Exception('Le nouveau distributeur parent sélectionné n\'existe pas.');
                    }

                    // Vérifier qu'on ne crée pas de boucle
                    if ($this->wouldCreateLoop($validatedData['id_distrib_parent'], $distributeur->id)) {
                        throw new \Exception('Cette assignation créerait une boucle dans la hiérarchie.');
                    }
                }

                // Log du changement de parent (action sensible)
                Log::warning("Changement de parent détecté", [
                    'distributeur_id' => $distributeur->id,
                    'ancien_parent' => $distributeur->id_distrib_parent,
                    'nouveau_parent' => $validatedData['id_distrib_parent'],
                    'user_id' => Auth::id()
                ]);
            }

            // Détection de changement de grade forcé
            if (isset($validatedData['etoiles_id']) && $validatedData['etoiles_id'] != $distributeur->etoiles_id) {
                Log::warning("Changement de grade forcé détecté", [
                    'distributeur_id' => $distributeur->id,
                    'ancien_grade' => $distributeur->etoiles_id,
                    'nouveau_grade' => $validatedData['etoiles_id'],
                    'user_id' => Auth::id()
                ]);
            }

            // Mettre à jour
            $distributeur->update($validatedData);

            // Enregistrer l'audit des modifications
            $this->logModificationAudit($distributeur, $originalData, $validatedData);

            DB::commit();

            Log::info("Distributeur mis à jour", [
                'id' => $distributeur->id,
                'matricule' => $distributeur->distributeur_id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('admin.distributeurs.show', $distributeur)
                ->with('success', 'Distributeur mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur mise à jour distributeur", [
                'id' => $distributeur->id,
                'error' => $e->getMessage(),
                'data' => $validatedData,
                'user_id' => Auth::id()
            ]);
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la confirmation de suppression avec analyse détaillée
     */
    public function confirmDeletion(Distributeur $distributeur): View
    {
        // Validation complète de la suppression
        $validationResult = $this->validationService->validateDistributeurDeletion($distributeur);

        // Suggestions d'actions de nettoyage
        $cleanupActions = $this->validationService->suggestCleanupActions($validationResult);

        return view('admin.distributeurs.confirm-deletion', compact(
            'distributeur',
            'validationResult',
            'cleanupActions'
        ));
    }

    /**
     * Traite la demande de suppression (avec ou sans workflow selon la criticité)
     */
    public function requestDeletion(Request $request, Distributeur $distributeur): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
            'force_immediate' => 'nullable|boolean'
        ]);

        try {
            // Validation de la suppression
            $validationResult = $this->validationService->validateDistributeurDeletion($distributeur);

            // Déterminer si une approbation est nécessaire
            $needsApproval = $this->needsApproval($distributeur, $validationResult);
            $forceImmediate = $request->boolean('force_immediate') && $this->userCanForceDelete();

            if ($needsApproval && !$forceImmediate) {
                // Créer une demande d'approbation
                $deletionRequest = DeletionRequest::createForDistributeur(
                    $distributeur,
                    $request->input('reason'),
                    $validationResult
                );

                Log::info("Demande de suppression créée", [
                    'distributeur_id' => $distributeur->id,
                    'deletion_request_id' => $deletionRequest->id,
                    'needs_approval' => true,
                    'user_id' => Auth::id()
                ]);

                return redirect()
                    ->route('admin.distributeurs.index')
                    ->with('warning', 'Demande de suppression soumise pour approbation. Référence: #' . $deletionRequest->id);
            }

            // Suppression immédiate (cas simples ou forcés)
            return $this->executeImmediateDeletion($distributeur, $request->input('reason'), $validationResult);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la demande de suppression", [
                'distributeur_id' => $distributeur->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Erreur lors de la demande de suppression: ' . $e->getMessage());
        }
    }

    /**
     * Exécute une suppression immédiate (cas simples)
     */
    private function executeImmediateDeletion(Distributeur $distributeur, string $reason, array $validationResult): RedirectResponse
    {
        // Vérifier que la suppression est possible
        if (!$validationResult['can_delete']) {
            return back()->with('error', 'Suppression impossible. Veuillez résoudre les problèmes bloquants d\'abord.');
        }

        DB::beginTransaction();
        try {
            // 1. Créer un backup complet
            $backupResult = $this->backupService->createDeletionBackup(
                'distributeur',
                $distributeur->id,
                $validationResult['related_data'] ?? []
            );

            if (!$backupResult['success']) {
                throw new \Exception("Échec de la création du backup: " . $backupResult['error']);
            }

            // 2. Nettoyer les données liées si nécessaire
            $this->cleanupRelatedData($distributeur, $validationResult);

            // 3. Log détaillé avant suppression
            Log::info("Suppression immédiate distributeur", [
                'id' => $distributeur->id,
                'matricule' => $distributeur->distributeur_id,
                'nom' => $distributeur->full_name,
                'reason' => $reason,
                'backup_id' => $backupResult['backup_id'],
                'user_id' => Auth::id()
            ]);

            // 4. Supprimer le distributeur
            $distributeur->delete();

            DB::commit();

            return redirect()
                ->route('admin.distributeurs.index')
                ->with('success', 'Distributeur supprimé avec succès. Backup: #' . $backupResult['backup_id']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur suppression immédiate distributeur", [
                'id' => $distributeur->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Exécute une suppression approuvée (appelée par DeletionRequestController)
     */
    public function executeDeletion(DeletionRequest $deletionRequest): RedirectResponse
    {
        if (!$deletionRequest->canBeExecuted()) {
            return back()->with('error', 'Cette demande ne peut pas être exécutée.');
        }

        $distributeur = $deletionRequest->entity();
        if (!$distributeur || !($distributeur instanceof Distributeur)) {
            return back()->with('error', 'Le distributeur à supprimer n\'existe plus.');
        }

        DB::beginTransaction();
        try {
            // Re-valider avant exécution (au cas où la situation aurait changé)
            $validationResult = $this->validationService->validateDistributeurDeletion($distributeur);

            if (!$validationResult['can_delete']) {
                throw new \Exception('La suppression n\'est plus possible. Situation changée depuis l\'approbation.');
            }

            // Créer backup
            $backupResult = $this->backupService->createDeletionBackup(
                'distributeur',
                $distributeur->id,
                $validationResult['related_data'] ?? []
            );

            if (!$backupResult['success']) {
                throw new \Exception("Échec backup: " . $backupResult['error']);
            }

            // Nettoyer les données liées
            $this->cleanupRelatedData($distributeur, $validationResult);

            // Supprimer
            $distributeur->delete();

            // Marquer la demande comme exécutée
            $deletionRequest->markAsCompleted([
                'backup_id' => $backupResult['backup_id'],
                'executed_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()
                ->route('admin.deletion-requests.index')
                ->with('success', 'Suppression exécutée avec succès. Backup: #' . $backupResult['backup_id']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur exécution suppression", [
                'deletion_request_id' => $deletionRequest->id,
                'distributeur_id' => $distributeur->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Erreur lors de l\'exécution: ' . $e->getMessage());
        }
    }

    /**
     * Méthode destroy originale modifiée pour rediriger vers le nouveau workflow
     */
    public function destroy(Distributeur $distributeur): RedirectResponse
    {
        // Rediriger vers la nouvelle interface de confirmation
        return redirect()->route('admin.distributeurs.confirm-deletion', $distributeur);
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

    // ===== MÉTHODES PRIVÉES UTILITAIRES =====

    /**
     * Détermine si une approbation est nécessaire
     */
    private function needsApproval(Distributeur $distributeur, array $validationResult): bool
    {
        // Approbation nécessaire si :

        // 1. Il y a des blockers
        if (!empty($validationResult['blockers'])) {
            return true;
        }

        // 2. Impact hiérarchique important
        if (isset($validationResult['impact_analysis']['hierarchy'])) {
            $hierarchyImpact = $validationResult['impact_analysis']['hierarchy'];
            if ($hierarchyImpact['total_descendants'] > 10) {
                return true;
            }
        }

        // 3. Distributeur avec beaucoup d'enfants directs
        if ($distributeur->children()->count() > 3) {
            return true;
        }

        // 4. Données financières importantes
        $totalAchats = $distributeur->achats()->sum('montant_total_ligne');
        if ($totalAchats > 100000) { // Seuil configurable
            return true;
        }

        return false;
    }

    /**
     * Nettoie les données liées avant suppression
     */
    private function cleanupRelatedData(Distributeur $distributeur, array $validationResult): void
    {
        // Supprimer les level_currents orphelins
        if (isset($validationResult['related_data']['level_currents'])) {
            $distributeur->levelCurrents()->delete();
        }
    }

    /**
     * Vérifier si un changement de parent créerait une boucle
     */
    private function wouldCreateLoop($newParentId, $distributeurId): bool
    {
        if (!$newParentId || !$distributeurId) {
            return false;
        }

        // Parcourir la hiérarchie vers le haut depuis le nouveau parent
        $currentId = $newParentId;
        $visited = [];

        while ($currentId && !in_array($currentId, $visited)) {
            $visited[] = $currentId;

            // Si on trouve le distributeur qu'on veut assigner, c'est une boucle
            if ($currentId == $distributeurId) {
                return true;
            }

            // Monter d'un niveau
            $parent = Distributeur::find($currentId);
            $currentId = $parent ? $parent->id_distrib_parent : null;
        }

        return false;
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

    /**
     * Obtenir des statistiques globales sur les distributeurs
     */
    private function getDistributeursStats(): array
    {
        try {
            return [
                'total_distributeurs' => Distributeur::count(),
                'distributeurs_actifs' => Distributeur::where('statut_validation_periode', true)->count(),
                'nouveaux_ce_mois' => Distributeur::whereMonth('created_at', now()->month)->count(),
                'par_grade' => Distributeur::selectRaw('etoiles_id, COUNT(*) as count')
                                          ->groupBy('etoiles_id')
                                          ->orderBy('etoiles_id')
                                          ->pluck('count', 'etoiles_id')
                                          ->toArray()
            ];
        } catch (\Exception $e) {
            Log::error("Erreur calcul statistiques distributeurs", ['error' => $e->getMessage()]);
            return [
                'total_distributeurs' => 0,
                'distributeurs_actifs' => 0,
                'nouveaux_ce_mois' => 0,
                'par_grade' => []
            ];
        }
    }

    /**
     * Obtenir des statistiques détaillées pour un distributeur
     */
    private function getDistributeurStatistics(Distributeur $distributeur): array
    {
        try {
            return [
                'total_children' => $distributeur->children()->count(),
                'total_achats' => $distributeur->achats()->count(),
                'total_points' => $distributeur->achats()->sum('points_unitaire_achat'),
                'montant_total_achats' => $distributeur->achats()->sum('montant_total_ligne'),
                'last_achat' => $distributeur->achats()->latest()->first(),
                'total_bonus' => $distributeur->bonuses()->sum('montant'),
                'profondeur_hierarchie' => $this->calculateDepth($distributeur->id_distrib_parent),
                'descendants_total' => count($this->getDescendantIds($distributeur->id))
            ];
        } catch (\Exception $e) {
            Log::error("Erreur calcul statistiques distributeur", [
                'distributeur_id' => $distributeur->id,
                'error' => $e->getMessage()
            ]);
            return [
                'total_children' => 0,
                'total_achats' => 0,
                'total_points' => 0,
                'montant_total_achats' => 0,
                'last_achat' => null,
                'total_bonus' => 0,
                'profondeur_hierarchie' => 0,
                'descendants_total' => 0
            ];
        }
    }

    /**
     * Obtenir l'historique des modifications récentes
     */
    private function getRecentChanges(Distributeur $distributeur): array
    {
        // Cette méthode peut être étendue pour implémenter un vrai système d'audit
        // Pour l'instant, on retourne un tableau vide
        return [];
    }

    /**
     * Obtenir l'historique des modifications pour un distributeur
     */
    private function getModificationHistory(Distributeur $distributeur): array
    {
        // Cette méthode peut être étendue pour implémenter un vrai système d'audit
        // Pour l'instant, on retourne un tableau vide
        return [];
    }

    /**
     * Enregistrer un audit des modifications
     */
    private function logModificationAudit(Distributeur $distributeur, array $originalData, array $newData): void
    {
        $changes = [];
        foreach ($newData as $key => $value) {
            if (isset($originalData[$key]) && $originalData[$key] != $value) {
                $changes[$key] = [
                    'from' => $originalData[$key],
                    'to' => $value
                ];
            }
        }

        if (!empty($changes)) {
            Log::info("Modifications distributeur auditées", [
                'distributeur_id' => $distributeur->id,
                'matricule' => $distributeur->distributeur_id,
                'changes' => $changes,
                'user_id' => Auth::id()
            ]);
        }
    }

    /**
     * Vérifier si l'utilisateur peut forcer une suppression
     */
    private function userCanForceDelete(): bool
    {
        $user = Auth::user();

        // Si le trait HasPermissions est disponible
        if (method_exists($user, 'hasPermission')) {
            return $user->hasPermission('force_delete');
        }

        // Fallback temporaire - vérifier par rôle ou champ
        if (isset($user->role)) {
            return $user->role === 'super_admin';
        }

        // Ou si vous avez des champs booléens
        if (isset($user->is_super_admin)) {
            return $user->is_super_admin === true;
        }

        // Par défaut, pas de permission
        return false;
    }
}
