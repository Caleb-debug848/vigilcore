<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;
use App\Services\StatuspageService;

class AlertWebhookController extends Controller
{
    public function handle(Request $request)
    {
        return $this->handleWebhook($request);
    }

    public function handleAlert(Request $request)
    {
        return $this->handleWebhook($request);
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        $rawStatus = strtolower($data['status'] ?? $data['event_status'] ?? 'firing');
        $alertName = $data['alert_name'] ?? $data['event_name'] ?? $data['title'] ?? null;
        $component = $data['component'] ?? $data['service'] ?? null;

        // 1. CAS DE RÉSOLUTION (fermeture automatique)
        if (in_array($rawStatus, ['resolved', 'ok'])) {
            $incident = Incident::where(function ($query) use ($alertName, $component) {
                    if ($alertName) {
                        $query->where('alert_name', $alertName)
                              ->orWhere('title', $alertName);
                    }
                    if ($component) {
                        $query->orWhere('component', $component);
                    }
                })
                ->where(function ($q) {
                    $q->where('status', '!=', 'resolved')
                      ->orWhereNull('resolved_at');
                })
                ->latest()
                ->first();

            if ($incident) {
                $incident->update([
                    'status'      => 'resolved',
                    'is_resolved' => true,
                    'resolved_at' => now(),
                    'raw_payload' => array_merge($incident->raw_payload ?? [], ['resolved_payload' => $data]),
                ]);

                if ($incident->statuspage_incident_id) {
                    app(StatuspageService::class)->resolveIncident(
                        $incident->statuspage_incident_id,
                        'Rétablissement confirmé et clôturé automatiquement.'
                    );
                }

                return response()->json([
                    'status'      => 'success',
                    'message'     => 'Incident résolu dans le Dashboard.',
                    'incident_id' => $incident->id
                ]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Aucun incident actif trouvé pour cette alerte.'
            ]);
        }

        // 2. CAS D'OUVERTURE D'INCIDENT (création)
        $newIncident = Incident::create([
            'component'              => $component ?? 'Système',
            'alert_name'             => $alertName ?? 'Alerte Détectée',
            'title'                  => $alertName ?? ($component ? "Alerte Composant [{$component}]" : 'Alerte Détectée'),
            'severity'               => strtoupper($data['severity'] ?? 'INFO'),
            'status'                 => 'open',
            'is_resolved'            => false,
            'message'                => $data['message'] ?? $data['description'] ?? '',
            'description'            => $data['message'] ?? $data['description'] ?? 'Anomalie détectée par les sondes.',
            'source'                 => $data['source'] ?? 'Kibana Logs Engine',
            'server'                 => $data['server'] ?? $data['host'] ?? 'srv901529',
            'statuspage_incident_id' => $data['statuspage_incident_id'] ?? null,
            'raw_payload'            => $data,
        ]);

        return response()->json([
            'status'      => 'success',
            'message'     => 'Incident créé.',
            'incident_id' => $newIncident->id
        ]);
    }
}
