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
        $title     = $data['alert_name'] ?? $data['title'] ?? $data['component'] ?? 'Alerte Système';
        $message   = $data['message'] ?? ($data['description'] ?? ($data['message_investigating'] ?? ''));
        $severity  = strtoupper($data['severity'] ?? 'INFO');
        $source    = $data['source'] ?? 'Kibana Logs Engine';

        // 1. CAS DE RÉSOLUTION (status = resolved ou ok)
        if (in_array($rawStatus, ['resolved', 'ok'])) {
            $incident = Incident::where('title', 'LIKE', '%' . $title . '%')
                ->where('status', '!=', 'resolved')
                ->latest()
                ->first();

            if ($incident) {
                $incident->update([
                    'status' => 'resolved',
                ]);

                // Clôture Statuspage si ID associé
                if ($incident->statuspage_incident_id) {
                    app(StatuspageService::class)->resolveIncident(
                        $incident->statuspage_incident_id,
                        'Rétablissement confirmé et clôturé automatiquement.'
                    );
                }

                return response()->json([
                    'success'     => true,
                    'message'     => 'Incident résolu avec succès dans le dashboard.',
                    'incident_id' => $incident->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Aucun incident actif trouvé pour cette alerte.',
            ]);
        }

        // 2. CAS D'OUVERTURE D'INCIDENT (Création)
        $incident = Incident::create([
            'title'                  => $title,
            'description'            => $message,
            'severity'               => $severity,
            'status'                 => 'open',
            'source'                 => $source,
            'statuspage_incident_id' => $data['statuspage_incident_id'] ?? null,
            'raw_payload'            => is_array($data) ? $data : json_decode($data, true),
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Incident enregistré avec succès dans le dashboard.',
            'incident_id' => $incident->id,
        ]);
    }
}
