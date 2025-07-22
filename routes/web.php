<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AchatController;
use App\Http\Controllers\Admin\DistributeurController;
use App\Http\Controllers\Admin\ProcessController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BonusController;
use App\Http\Controllers\Admin\DeletionRequestController;
use App\Http\Controllers\Admin\AchatReturnController;
use App\Models\DeletionRequest;
use App\Models\Distributeur;
use App\Services\DeletionValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require __DIR__.'/auth.php';

// ===== ROUTES PUBLIQUES =====

Route::get('/', function () {
    return view('welcome');
});

// ===== ROUTES AUTHENTIFIÉES BASIQUES =====

Route::middleware('auth')->group(function () {

    // Dashboard principal (pour les utilisateurs connectés)
    Route::get('/dashboard', function () {
        if (Auth::check() && Auth::user()->hasPermission('access_admin')) {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===== ROUTES ADMIN AVEC PERMISSIONS =====

Route::middleware(['auth', 'verified', 'check_admin_role'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ===== DASHBOARD ADMIN =====
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // ===== GESTION DES DISTRIBUTEURS =====
        Route::prefix('distributeurs')->name('distributeurs.')->group(function () {

            // Routes CRUD classiques
            Route::get('/', [DistributeurController::class, 'index'])->name('index');
            Route::get('/create', [DistributeurController::class, 'create'])->name('create');
            Route::post('/', [DistributeurController::class, 'store'])->name('store');
            Route::get('/{distributeur}', [DistributeurController::class, 'show'])->name('show');
            Route::get('/{distributeur}/edit', [DistributeurController::class, 'edit'])->name('edit');
            Route::put('/{distributeur}', [DistributeurController::class, 'update'])->name('update');
            Route::patch('/{distributeur}', [DistributeurController::class, 'update'])->name('update');

            // Routes de suppression sécurisée
            Route::get('/{distributeur}/confirm-deletion', [DistributeurController::class, 'confirmDeletion'])
                ->name('confirm-deletion')
                ->middleware('permission:delete_distributeurs');

            Route::post('/{distributeur}/request-deletion', [DistributeurController::class, 'requestDeletion'])
                ->name('request-deletion')
                ->middleware('permission:delete_distributeurs');

            // Ancienne route destroy redirigée vers la nouvelle interface
            Route::delete('/{distributeur}', [DistributeurController::class, 'destroy'])->name('destroy');

            // Route de recherche AJAX (DOIT être AVANT les routes avec paramètres)
            Route::get('/search/ajax', [DistributeurController::class, 'search'])->name('search');
        });

        Route::prefix('achat-returns')->name('achat-returns.')->group(function () {
            Route::get('/', [AchatReturnController::class, 'index'])->name('index');
            Route::get('/create/{achat}', [AchatReturnController::class, 'create'])->name('create');
            Route::post('/{achat}', [AchatReturnController::class, 'store'])->name('store');
            Route::get('/{returnRequest}', [AchatReturnController::class, 'show'])->name('show');
            Route::post('/{returnRequest}/approve', [AchatReturnController::class, 'approve'])->name('approve');
            Route::post('/{returnRequest}/reject', [AchatReturnController::class, 'reject'])->name('reject');
            Route::post('/{returnRequest}/execute', [AchatReturnController::class, 'execute'])->name('execute');
            Route::delete('/{returnRequest}/cancel', [AchatReturnController::class, 'cancel'])->name('cancel');
            Route::get('/report/period', [AchatReturnController::class, 'report'])->name('report');
        });

        // ===== GESTION DES ACHATS =====
        Route::prefix('achats')->name('achats.')->group(function () {
            Route::get('/', [AchatController::class, 'index'])->name('index');
            Route::get('/create', [AchatController::class, 'create'])->name('create');
            Route::post('/', [AchatController::class, 'store'])->name('store');
            Route::get('/{achat}', [AchatController::class, 'show'])->name('show');
            Route::get('/{achat}/edit', [AchatController::class, 'edit'])->name('edit');
            Route::put('/{achat}', [AchatController::class, 'update'])->name('update');
            Route::patch('/{achat}', [AchatController::class, 'update']);
            Route::delete('/{achat}', [AchatController::class, 'destroy'])->name('destroy');

            // Routes utilitaires
            Route::get('/product-info/ajax', [AchatController::class, 'getProductInfo'])->name('product-info');
        });

        // ===== GESTION DES PRODUITS =====
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::patch('/{product}', [ProductController::class, 'update']);
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        });

        // ===== GESTION DES BONUS =====
        Route::prefix('bonuses')->name('bonuses.')->group(function () {
            Route::get('/', [BonusController::class, 'index'])->name('index');
            Route::get('/create', [BonusController::class, 'create'])->name('create');
            Route::post('/', [BonusController::class, 'store'])->name('store');
            Route::get('/{bonus}', [BonusController::class, 'show'])->name('show');
            Route::get('/{bonus}/edit', [BonusController::class, 'edit'])->name('edit');
            Route::put('/{bonus}', [BonusController::class, 'update'])->name('update');
            Route::patch('/{bonus}', [BonusController::class, 'update']);
            Route::delete('/{bonus}', [BonusController::class, 'destroy'])->name('destroy');

            // Génération PDF
            Route::get('/{bonus}/pdf', [BonusController::class, 'generatePdf'])->name('pdf');
        });

        // ===== PROCESSUS MÉTIER (CALCULS ET TRAITEMENTS) =====
        Route::prefix('processes')->name('processes.')->group(function () {
            Route::get('/', [ProcessController::class, 'index'])->name('index');

            // Exécution des processus d'avancement
            Route::post('/advancements', [ProcessController::class, 'processAdvancements'])
                ->name('advancements')
                ->middleware('permission:execute_advancements');

            // Exécution de la régularisation des grades
            Route::post('/regularization', [ProcessController::class, 'regularizeGrades'])
                ->name('regularization')
                ->middleware('permission:execute_regularization');

            // API pour statistiques
            Route::get('/stats', [ProcessController::class, 'apiStats'])->name('stats');
        });

        // ===== GESTION DES DEMANDES DE SUPPRESSION =====
        Route::prefix('deletion-requests')->name('deletion-requests.')->group(function () {

            // Liste et détails des demandes
            Route::get('/', [DeletionRequestController::class, 'index'])
                ->name('index')
                ->middleware('permission:view_deletion_requests');

            Route::get('/{deletionRequest}', [DeletionRequestController::class, 'show'])
                ->name('show')
                ->middleware('permission:view_deletion_requests');

            // Actions d'approbation/rejet
            Route::post('/{deletionRequest}/approve', [DeletionRequestController::class, 'approve'])
                ->name('approve')
                ->middleware('permission:approve_deletions');

            Route::post('/{deletionRequest}/reject', [DeletionRequestController::class, 'reject'])
                ->name('reject')
                ->middleware('permission:approve_deletions');

            // Exécution des suppressions approuvées
            Route::post('/{deletionRequest}/execute', [DeletionRequestController::class, 'execute'])
                ->name('execute')
                ->middleware('permission:execute_deletions');

            // Annulation des demandes
            Route::post('/{deletionRequest}/cancel', [DeletionRequestController::class, 'cancel'])
                ->name('cancel');

            // Export des demandes
            Route::get('/export/csv', [DeletionRequestController::class, 'export'])
                ->name('export')
                ->middleware('permission:export_data');
        });

        // ===== GESTION DES BACKUPS =====
        Route::prefix('backups')->name('backups.')->group(function () {

            // Liste des backups
            Route::get('/', [DeletionRequestController::class, 'backups'])
                ->name('index')
                ->middleware('permission:view_backups');

            // Restauration depuis backup
            Route::post('/restore', [DeletionRequestController::class, 'restoreBackup'])
                ->name('restore')
                ->middleware('permission:restore_backups');
        });

        // ===== ROUTES API POUR AJAX =====
        Route::prefix('api')->name('api.')->group(function () {

            // Statistiques des demandes de suppression
            Route::get('/deletion-requests/stats', function() {
                return response()->json([
                    'pending' => DeletionRequest::pending()->count(),
                    'approved' => DeletionRequest::approved()->count(),
                    'completed' => DeletionRequest::where('status', DeletionRequest::STATUS_COMPLETED)->count(),
                    'rejected' => DeletionRequest::where('status', DeletionRequest::STATUS_REJECTED)->count(),
                ]);
            })->name('deletion-requests.stats');

            // Validation en temps réel pour la suppression
            Route::post('/validate-deletion/{type}/{id}', function(Request $request, string $type, int $id) {
                switch ($type) {
                    case 'distributeur':
                        $entity = Distributeur::findOrFail($id);
                        $validator = app(DeletionValidationService::class);
                        return response()->json($validator->validateDistributeurDeletion($entity));

                    default:
                        return response()->json(['error' => 'Type not supported'], 400);
                }
            })->name('validate-deletion');

            // Statistiques générales du dashboard
            Route::get('/dashboard/stats', function() {
                return response()->json([
                    'distributeurs' => [
                        'total' => \App\Models\Distributeur::count(),
                        'active' => \App\Models\Distributeur::where('statut_validation_periode', true)->count(),
                        'new_this_month' => \App\Models\Distributeur::whereMonth('created_at', now()->month)->count(),
                    ],
                    'achats' => [
                        'total_this_month' => \App\Models\Achat::where('period', date('Y-m'))->count(),
                        'amount_this_month' => \App\Models\Achat::where('period', date('Y-m'))->sum('montant_total_ligne'),
                    ],
                    'processes' => [
                        'pending_deletions' => DeletionRequest::pending()->count(),
                        'last_advancement' => \App\Models\LevelCurrent::latest('updated_at')->value('updated_at'),
                    ]
                ]);
            })->name('dashboard.stats');
        });

        // ===== ROUTES DE TEST ET DEBUG (À SUPPRIMER EN PRODUCTION) =====
        Route::prefix('debug')->name('debug.')->group(function () {
            // Test des contrôleurs
            Route::get('/test-distributeurs', [DistributeurController::class, 'index'])->name('test.distributeurs');
            Route::get('/test-achats', [AchatController::class, 'index'])->name('test.achats');

            // Test des services
            Route::get('/test-backup', function() {
                $backupService = app(\App\Services\BackupService::class);
                $distributeur = \App\Models\Distributeur::first();

                if (!$distributeur) {
                    return response()->json(['error' => 'Aucun distributeur trouvé pour test']);
                }

                $result = $backupService->createDeletionBackup('distributeur', $distributeur->id, []);
                return response()->json($result);
            })->name('test.backup');

            Route::get('/test-validation', function() {
                $validationService = app(\App\Services\DeletionValidationService::class);
                $distributeur = \App\Models\Distributeur::first();

                if (!$distributeur) {
                    return response()->json(['error' => 'Aucun distributeur trouvé pour test']);
                }

                $result = $validationService->validateDistributeurDeletion($distributeur);
                return response()->json($result);
            })->name('test.validation');
        });
    });

// ===== ROUTES DE GESTION DES ERREURS =====

// Route pour les erreurs 404 personnalisées (optionnel)
Route::fallback(function () {
    if (request()->is('admin/*')) {
        return response()->view('admin.errors.404', [], 404);
    }
    return response()->view('errors.404', [], 404);
});

// ===== ROUTES DE MAINTENANCE =====

// Route de maintenance (à activer quand nécessaire)
/*
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');
*/
