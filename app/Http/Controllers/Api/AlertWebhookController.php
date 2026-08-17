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

        if (empty($data)) {
            return response()->json(['status' => 'ignored', 'message' => 'Payload vide.']);
        }

        $rawStatus = strtolower($data['status'] ?? $data['event_status'] ?? 'firing');
        $alertName = $data['alert_name'] ?? $data['event_name'] ?? 'Alerte Système';
        $component = $data['component'] ?? $data['service'] ?? 'Système';

        // 1. CAS DE RÉSOLUTION AUTOMATIQUE (status = resolved)
        if (in_array($rawStatus, ['resolved', 'ok'])) {
            $incident = Incident::where(function ($query) use ($alertName, $component) {
                    $query->where('alert_name', $alertName)
                          ->orWhere('component', $component)
                          ->orWhere('title', $alertName);
                })
                ->where('status', '!=', 'resolved')
                ->latest()
                ->first();

            if ($incident) {
                $incident->update([
                    'status' => 'resolved'
                ]);

                // Synchronisation Statuspage si ID présent
                if ($incident->statuspage_incident_id) {
                    app(StatuspageService::class)->resolveIncident(
                        $incident->statuspage_incident_id,
                        'Rétablissement confirmé et clôturé automatiquement.'
                    );
                }

                return response()->json([
                    'success'     => true,
                    'message'     => 'Incident résolu avec succès dans le dashboard.',
                    'incident_id' => $incident->id
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Aucun incident actif à clôturer.']);
        }

        // 2. CAS D'OUVERTURE D'INCIDENT (status = open)
        $incident = Incident::create([
            'component'              => $component,
            'alert_name'             => $alertName,
            'title'                  => $alertName,
            'severity'               => strtoupper($data['severity'] ?? 'INFO'),
            'status'                 => 'open',
            'message'                => $data['message'] ?? ($data['message_investigating'] ?? ''),
            'description'            => $data['message'] ?? ($data['message_investigating'] ?? 'Anomalie détectée par les sondes.'),
            'source'                 => $data['source'] ?? 'Kibana Logs Engine',
            'server'                 => $data['server'] ?? 'srv901529',
            'statuspage_incident_id' => $data['statuspage_incident_id'] ?? null,
            'raw_payload'            => $data,
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Incident enregistré avec succès dans le dashboard.',
            'incident_id' => $incident->id
        ]);
    }
}
