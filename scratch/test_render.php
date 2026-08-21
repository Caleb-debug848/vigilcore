<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = App\Models\User::first();
    echo "User: " . ($user ? $user->email : "None") . "\n";
    
    // Authenticate user
    auth()->login($user);
    
    $statuspage = app(App\Services\StatuspageService::class);
    $dashboard = app(App\Livewire\Dashboard::class);
    $dashboard->mount($statuspage);
    $view = $dashboard->render();
    $html = $view->render();
    echo "Dashboard render OK! Length: " . strlen($html) . " bytes\n";
    
    $reports = app(App\Livewire\IncidentReports::class);
    $viewRep = $reports->render();
    $htmlRep = $viewRep->render();
    echo "Reports render OK! Length: " . strlen($htmlRep) . " bytes\n";
} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
