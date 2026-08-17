<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\IncidentReports;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Route vers le composant Livewire Dashboard
Route::get('/dashboard', Dashboard::class)->name('dashboard');

// Route vers le module Rapports & SLA
Route::get('/reports', IncidentReports::class)->name('reports');