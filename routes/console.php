<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planification automatique de la rotation des 20 services toutes les 15 minutes
Schedule::command('vigilcore:dispatch-periodic-incident')->everyFifteenMinutes();
