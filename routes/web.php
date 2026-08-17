<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Dashboard;
use App\Livewire\IncidentReports;
use Illuminate\Support\Facades\Route;

// Redirection automatique de la racine vers le Dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Routes protégées par le middleware d'authentification VigilCore
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/reports', IncidentReports::class)->name('reports');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
