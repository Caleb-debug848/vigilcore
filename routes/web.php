<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Route vers le Dashboard Volt
Volt::route('/dashboard', 'incident-dashboard');