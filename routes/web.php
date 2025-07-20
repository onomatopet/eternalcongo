<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AchatController;
use App\Http\Controllers\Admin\DistributeurController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes admin avec vrais contrôleurs
Route::middleware(['auth', 'verified', 'check_admin_role'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Route de recherche AJAX pour distributeurs (DOIT être AVANT resource)
        Route::get('distributeurs/search', [DistributeurController::class, 'search'])
            ->name('distributeurs.search');

        // Routes complètes pour distributeurs
        Route::resource('distributeurs', DistributeurController::class);

        // Routes complètes pour achats
        Route::resource('achats', AchatController::class);

        // Routes complètes pour produits
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);

        // Routes pour les bonus
        Route::resource('bonuses', App\Http\Controllers\Admin\BonusController::class);
        Route::get('bonuses/{bonus}/pdf', [App\Http\Controllers\Admin\BonusController::class, 'generatePdf'])
            ->name('bonuses.pdf');

        // Routes de test pour vérifier les contrôleurs
        Route::get('test-distributeurs', [DistributeurController::class, 'index'])->name('test.distributeurs');
        Route::get('test-achats', [AchatController::class, 'index'])->name('test.achats');
    });
