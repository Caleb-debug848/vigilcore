<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Route vers le composant Livewire Dashboard
Route::get('/dashboard', Dashboard::class);