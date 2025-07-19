<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AchatController;
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


Route::middleware(['auth', 'verified', 'check_admin_role'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // ... autres routes admin (dashboard, distributors) ...

        // Route pour la liste des achats
        Route::get('achats', [AchatController::class, 'index'])->name('achats.index');

    });
