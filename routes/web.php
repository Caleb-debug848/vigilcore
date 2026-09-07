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
        session()->save();
        app()->setLocale($locale);
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


// Route pour le statut et le QR Code WhatsApp en direct
Route::get('/whatsapp-live-status', function () {
    $apiKey = env('EVOLUTION_API_KEY', 'B6D711FCDE4D4FD5936544120E713976');
    try {
        $stateResp = \Illuminate\Support\Facades\Http::withHeaders(['apikey' => $apiKey])
            ->timeout(3)
            ->get('http://127.0.0.1:8090/instance/connectionState/vigilcore-ops');
        
        $state = $stateResp->json('instance.state');
        if ($state === 'open') {
            return response()->json(['status' => 'connected', 'message' => 'WhatsApp VigilCore est connecté et opérationnel !']);
        }

        $connectResp = \Illuminate\Support\Facades\Http::withHeaders(['apikey' => $apiKey])
            ->timeout(5)
            ->get('http://127.0.0.1:8090/instance/connect/vigilcore-ops');

        return response()->json([
            'status' => 'disconnected',
            'data' => $connectResp->json()
        ]);
    } catch (\Throwable $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

require __DIR__.'/auth.php';
