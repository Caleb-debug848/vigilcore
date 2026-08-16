<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Services\StatuspageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlertWebhookController extends Controller
{
    /**
     * Reçoit et traite les alertes entrantes depuis n8n, Zabbix ou Kibana
     */
    public function handle(Request $request, StatuspageService $statuspage)
    {
        try {
            $data = $request->all();

            $title = $data['alert_name'] ?? $data['title'] ?? 'Alerte Système VigilCore';
            $source = $data['source'] ?? 'Monitoring Hub';
            $severity = strtolower($data['severity'] ?? 'info');
            $status = strtolower($data['status'] ?? 'investigating');
            $description = $data['message'] ?? $data['description'] ?? 'Anomalie détectée par les sondes.';
            $component = $data['component'] ?? null;
            $statuspageId = $data['statuspage_incident_id'] ?? null;

            // 1. Si l'alerte indique un retour à la normale (RESOLVED / OK)
            if (in_array($status, ['resolved', 'ok'])) {
                $incident = Incident::where('title', $title)
                    ->where('status', '!=', 'resolved')
                    ->latest()
                    ->first();

                if ($incident) {
                    $incident->update([
                        'status' => 'resolved',
                        'raw_payload' => array_merge($incident->raw_payload ?? [], ['resolved_payload' => $data]),
                    ]);

                    if ($incident->statuspage_incident_id) {
                        $statuspage->resolveIncident($incident->statuspage_incident_id, 'Rétablissement validé par sonde automatique.');
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Incident clôturé avec succès.',
                        'incident_id' => $incident->id
                    ], 200);
                }
            }

            // 2. Si c'est une nouvelle panne critique ou alerte, création en base
            $newIncident = Incident::create([
                'title' => $title,
                'source' => $source,
                'severity' => in_array($severity, ['critical', 'warning', 'info']) ? $severity : 'info',
                'status' => in_array($status, ['resolved', 'identified', 'monitoring']) ? $status : 'investigating',
                'description' => $description,
                'statuspage_incident_id' => $statuspageId,
                'raw_payload' => $data,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alerte ingérée avec succès.',
                'incident_id' => $newIncident->id
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur Ingestion Webhook VigilCore : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
