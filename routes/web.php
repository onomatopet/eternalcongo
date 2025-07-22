<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AchatController;
use App\Http\Controllers\Admin\AchatReturnController;
use App\Http\Controllers\Admin\DistributeurController;
use App\Http\Controllers\Admin\ProcessController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BonusController;
use App\Http\Controllers\Admin\DeletionRequestController;
use App\Http\Controllers\Admin\ModificationRequestController;
use App\Models\DeletionRequest;
use App\Models\Distributeur;
use App\Services\DeletionValidationService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
        // Rediriger vers le dashboard admin si l'utilisateur a les permissions admin
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

        // ===== GESTION DES RETOURS ET ANNULATIONS D'ACHATS =====
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

            // Routes utilitaires
            Route::get('/search/ajax', [ProductController::class, 'search'])->name('search');
        });

        // ===== GESTION DES BONUS =====
        Route::prefix('bonuses')->name('bonuses.')->group(function () {
            Route::get('/', [BonusController::class, 'index'])->name('index');
            Route::get('/create', [BonusController::class, 'create'])->name('create');
            Route::post('/', [BonusController::class, 'store'])->name('store');
            Route::get('/{bonus}', [BonusController::class, 'show'])->name('show');
            Route::get('/{bonus}/edit', [BonusController::class, 'edit'])->name('edit');
            Route::put('/{bonus}', [BonusController::class, 'update'])->name('update');
            Route::delete('/{bonus}', [BonusController::class, 'destroy'])->name('destroy');
            
            // Route pour générer le PDF
            Route::get('/{bonus}/pdf', [BonusController::class, 'generatePdf'])->name('pdf');

            // Routes de calcul
            Route::get('/calculate/{period}', [BonusController::class, 'showCalculation'])->name('calculate.show');
            Route::post('/calculate/{period}', [BonusController::class, 'calculate'])->name('calculate');
        });

        // ===== PROCESSUS DE CALCUL =====
        Route::prefix('processes')->name('processes.')->group(function () {
            Route::get('/', [ProcessController::class, 'index'])->name('index');
            
            // Interface web pour les processus
            Route::get('/advancements', [ProcessController::class, 'showAdvancements'])->name('advancements.show');
            Route::post('/advancements/run', [ProcessController::class, 'runAdvancements'])->name('advancements.run');
            
            Route::get('/regularization', [ProcessController::class, 'showRegularization'])->name('regularization.show');
            Route::post('/regularization/run', [ProcessController::class, 'runRegularization'])->name('regularization.run');
            
            Route::get('/recalculation', [ProcessController::class, 'showRecalculation'])->name('recalculation.show');
            Route::post('/recalculation/run', [ProcessController::class, 'runRecalculation'])->name('recalculation.run');
            
            // Historique des exécutions
            Route::get('/history', [ProcessController::class, 'history'])->name('history');
            Route::get('/history/{execution}', [ProcessController::class, 'showExecution'])->name('history.show');
        });

        // ===== GESTION DES DEMANDES DE SUPPRESSION =====
        Route::prefix('deletion-requests')->name('deletion-requests.')->group(function () {
            
            // Liste et détails
            Route::get('/', [DeletionRequestController::class, 'index'])
                ->name('index')
                ->middleware('permission:view_deletion_requests');
            
            Route::get('/{deletionRequest}', [DeletionRequestController::class, 'show'])
                ->name('show')
                ->middleware('permission:view_deletion_requests');
            
            // Workflow d'approbation
            Route::post('/{deletionRequest}/approve', [DeletionRequestController::class, 'approve'])
                ->name('approve')
                ->middleware('permission:approve_deletions');
            
            Route::post('/{deletionRequest}/reject', [DeletionRequestController::class, 'reject'])
                ->name('reject')
                ->middleware('permission:approve_deletions');
            
            Route::post('/{deletionRequest}/execute', [DeletionRequestController::class, 'execute'])
                ->name('execute')
                ->middleware('permission:execute_deletions');
            
            Route::delete('/{deletionRequest}/cancel', [DeletionRequestController::class, 'cancel'])
                ->name('cancel');
        });

        // ===== GESTION DES DEMANDES DE MODIFICATION =====
        Route::prefix('modification-requests')->name('modification-requests.')->group(function () {
            Route::get('/', [ModificationRequestController::class, 'index'])->name('index');
            Route::get('/{modificationRequest}', [ModificationRequestController::class, 'show'])->name('show');
            
            // Création de demandes spécifiques
            Route::get('/create/parent-change/{distributeur}', [ModificationRequestController::class, 'createParentChange'])->name('create.parent-change');
            Route::post('/store/parent-change/{distributeur}', [ModificationRequestController::class, 'storeParentChange'])->name('store.parent-change');
            
            Route::get('/create/grade-change/{distributeur}', [ModificationRequestController::class, 'createGradeChange'])->name('create.grade-change');
            Route::post('/store/grade-change/{distributeur}', [ModificationRequestController::class, 'storeGradeChange'])->name('store.grade-change');
            
            // Actions sur les demandes
            Route::post('/{modificationRequest}/approve', [ModificationRequestController::class, 'approve'])->name('approve');
            Route::post('/{modificationRequest}/reject', [ModificationRequestController::class, 'reject'])->name('reject');
            Route::post('/{modificationRequest}/execute', [ModificationRequestController::class, 'execute'])->name('execute');
            Route::delete('/{modificationRequest}/cancel', [ModificationRequestController::class, 'cancel'])->name('cancel');
            
            // Validation AJAX
            Route::post('/validate', [ModificationRequestController::class, 'validateChange'])->name('validate');
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
            if (app()->environment('local', 'development')) {
                Route::get('/test-deletion-validation/{distributeur}', function(Distributeur $distributeur) {
                    $validator = app(DeletionValidationService::class);
                    return response()->json($validator->validateDistributeurDeletion($distributeur));
                })->name('test-deletion-validation');

                Route::get('/phpinfo', function() {
                    if (Auth::user() && Auth::user()->hasRole('super_admin')) {
                        phpinfo();
                    } else {
                        abort(403);
                    }
                })->name('phpinfo');
            }
        });
    });

// ===== ROUTES FALLBACK =====
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});