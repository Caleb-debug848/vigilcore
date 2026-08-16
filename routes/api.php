<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\Api\IncidentWebhookController;
use App\Http\Controllers\Api\AlertWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route d'ingestion générale des alertes n8n / Zabbix / Kibana
Route::post('/v1/webhooks/alert', [AlertWebhookController::class, 'handle']);
Route::post('/webhooks/alerts', [AlertWebhookController::class, 'handle']);
Route::post('/webhook/incident', [AlertWebhookController::class, 'handle']);

// Route de clôture/résolution automatique depuis n8n
Route::post('/webhooks/incidents/resolve', [IncidentWebhookController::class, 'autoResolve']);