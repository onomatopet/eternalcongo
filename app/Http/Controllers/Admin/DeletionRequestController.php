<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeletionRequest;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DeletionRequestController extends Controller
{
    private BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Affiche la liste des demandes de suppression
     */
    public function index(Request $request): View
    {
        $query = DeletionRequest::with(['requestedBy', 'approvedBy'])
                                ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->input('entity_type'));
        }

        if ($request->filled('requested_by')) {
            $query->where('requested_by_id', $request->input('requested_by'));
        }

        $deletionRequests = $query->paginate(20)->withQueryString();

        // Statistiques
        $stats = [
            'total' => DeletionRequest::count(),
            'pending' => DeletionRequest::pending()->count(),
            'approved' => DeletionRequest::approved()->count(),
            'completed' => DeletionRequest::where('status', DeletionRequest::STATUS_COMPLETED)->count(),
            'rejected' => DeletionRequest::where('status', DeletionRequest::STATUS_REJECTED)->count(),
        ];

        return view('admin.deletion-requests.index', compact('deletionRequests', 'stats'));
    }

    /**
     * Affiche les détails d'une demande de suppression
     */
    public function show(DeletionRequest $deletionRequest): View
    {
        $deletionRequest->load(['requestedBy', 'approvedBy']);

        // Charger l'entité si elle existe encore
        $entity = $deletionRequest->entity();

        return view('admin.deletion-requests.show', compact('deletionRequest', 'entity'));
    }

    /**
     * Approuve une demande de suppression
     */
    public function approve(Request $request, DeletionRequest $deletionRequest): RedirectResponse
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
            'execute_immediately' => 'nullable|boolean'
        ]);

        if (!$deletionRequest->canBeApproved()) {
            return back()->with('error', 'Cette demande ne peut pas être approuvée.');
        }

        if (!Auth::user()->hasPermission('approve_deletions')) {
            return back()->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        try {
            // Approuver la demande
            $deletionRequest->approve(Auth::id(), $request->input('note'));

            // Exécuter immédiatement si demandé
            if ($request->boolean('execute_immediately')) {
                return $this->execute($deletionRequest);
            }

            return redirect()
                ->route('admin.deletion-requests.show', $deletionRequest)
                ->with('success', 'Demande approuvée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'approbation: ' . $e->getMessage());
        }
    }

    /**
     * Rejette une demande de suppression
     */
    public function reject(Request $request, DeletionRequest $deletionRequest): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        if (!$deletionRequest->canBeRejected()) {
            return back()->with('error', 'Cette demande ne peut pas être rejetée.');
        }

        if (!Auth::user()->hasPermission('approve_deletions')) {
            return back()->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        try {
            $deletionRequest->reject(Auth::id(), $request->input('rejection_reason'));

            return redirect()
                ->route('admin.deletion-requests.show', $deletionRequest)
                ->with('success', 'Demande rejetée.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du rejet: ' . $e->getMessage());
        }
    }

    /**
     * Exécute une demande de suppression approuvée
     */
    public function execute(DeletionRequest $deletionRequest): RedirectResponse
    {
        if (!$deletionRequest->canBeExecuted()) {
            return back()->with('error', 'Cette demande ne peut pas être exécutée.');
        }

        if (!Auth::user()->hasPermission('execute_deletions')) {
            return back()->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        // Déléguer l'exécution au contrôleur approprié selon le type d'entité
        switch ($deletionRequest->entity_type) {
            case DeletionRequest::ENTITY_DISTRIBUTEUR:
                return app(DistributeurController::class)->executeDeletion($deletionRequest);

            case DeletionRequest::ENTITY_ACHAT:
                return app(AchatController::class)->executeDeletion($deletionRequest);

            default:
                return back()->with('error', 'Type d\'entité non supporté pour l\'exécution.');
        }
    }

    /**
     * Annule une demande de suppression
     */
    public function cancel(DeletionRequest $deletionRequest): RedirectResponse
    {
        if (!$deletionRequest->isPending()) {
            return back()->with('error', 'Seules les demandes en attente peuvent être annulées.');
        }

        // Seul le demandeur ou un admin peut annuler
        if ($deletionRequest->requested_by_id !== Auth::id() && !Auth::user()->hasPermission('manage_all_deletion_requests')) {
            return back()->with('error', 'Vous ne pouvez pas annuler cette demande.');
        }

        try {
            $deletionRequest->update([
                'status' => DeletionRequest::STATUS_CANCELLED,
                'rejection_reason' => 'Annulée par ' . Auth::user()->name
            ]);

            return redirect()
                ->route('admin.deletion-requests.index')
                ->with('success', 'Demande annulée.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Liste les backups disponibles
     */
    public function backups(): View
    {
        $backups = $this->backupService->listBackups();

        return view('admin.deletion-requests.backups', compact('backups'));
    }

    /**
     * Restaure depuis un backup
     */
    public function restoreBackup(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_id' => 'required|string'
        ]);

        if (!Auth::user()->hasPermission('restore_backups')) {
            return back()->with('error', 'Vous n\'avez pas les permissions pour restaurer des backups.');
        }

        try {
            $result = $this->backupService->restoreFromBackup($request->input('backup_id'));

            if ($result['success']) {
                return back()->with('success', 'Backup restauré avec succès.');
            } else {
                return back()->with('error', 'Erreur lors de la restauration: ' . $result['error']);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la restauration: ' . $e->getMessage());
        }
    }

    /**
     * Export des demandes de suppression
     */
    public function export(Request $request)
    {
        $query = DeletionRequest::with(['requestedBy', 'approvedBy']);

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->input('entity_type'));
        }

        $deletionRequests = $query->get();

        $filename = 'deletion_requests_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($deletionRequests) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Type d\'entité',
                'ID entité',
                'Statut',
                'Demandé par',
                'Approuvé par',
                'Raison',
                'Date de demande',
                'Date d\'approbation',
                'Date d\'exécution'
            ]);

            // Données
            foreach ($deletionRequests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->entity_type,
                    $request->entity_id,
                    $request->getStatusLabel(),
                    $request->requestedBy->name ?? 'N/A',
                    $request->approvedBy->name ?? 'N/A',
                    $request->reason,
                    $request->created_at->format('d/m/Y H:i'),
                    $request->approved_at?->format('d/m/Y H:i') ?? 'N/A',
                    $request->completed_at?->format('d/m/Y H:i') ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
