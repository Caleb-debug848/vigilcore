<?php

use App\Http\Controllers\IncidentExportController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Dashboard;
use App\Livewire\IncidentReports;
use Illuminate\Support\Facades\Route;

// Redirection automatique de la racine vers le Dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Changement de langue dynamique (FR / EN)
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['fr', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Routes protégées par le middleware d'authentification VigilCore
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/reports', IncidentReports::class)->name('reports');
    Route::get('/reports/export', [IncidentExportController::class, 'export'])->name('reports.export');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
