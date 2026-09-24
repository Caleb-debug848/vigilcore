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
        if (in_array($source, ['VigilCore Monitoring', 'Monitoring Hub', 'VigilCore Alert Hub'])) {
            $source = 'Kibana Logs Engine';
        }

        $component = $data['component'] ?? $data['service'] ?? null;

        // 1. CAS DE RÉSOLUTION (status = resolved ou ok)
        if (in_array($rawStatus, ['resolved', 'ok'])) {
            $incident = Incident::where('status', '!=', 'resolved')
                ->where(function ($q) use ($title, $component) {
                    if ($component) {
                        $q->where('component', $component)
                          ->orWhere('title', 'LIKE', '%' . $component . '%');
                    }
                    if ($title) {
                        $q->orWhere('title', 'LIKE', '%' . $title . '%');
                    }
                })
                ->latest()
                ->first();

            if ($incident) {
                $raw = $incident->raw_payload;
                if (is_string($raw)) {
                    $raw = json_decode($raw, true) ?? [];
                }
                if (!is_array($raw)) {
                    $raw = [];
                }

                $raw['resolved_at_iso'] = now()->timezone('Africa/Douala')->toIso8601String();
                $raw['resolved_at_wat'] = now()->timezone('Africa/Douala')->format('d/m/Y H:i:s') . ' (WAT - Douala)';
                $raw['resolution_note'] = $data['message_resolved'] ?? $data['message'] ?? 'Service rétabli et opérationnel.';

                $incident->update([
                    'status'      => 'resolved',
                    'is_resolved' => true,
                    'resolved_at' => now(),
                    'raw_payload' => $raw,
                ]);

                // Clôture Statuspage si ID associé
                if ($incident->statuspage_incident_id) {
                    app(StatuspageService::class)->resolveIncident(
                        $incident->statuspage_incident_id,
                        'Rétablissement confirmé et clôturé automatiquement.'
                    );
                }

                \Illuminate\Support\Facades\Cache::forget('vigilcore_active_counts');
                \Illuminate\Support\Facades\Cache::forget('vigilcore_dashboard_counts');
                \Illuminate\Support\Facades\Cache::forget('statuspage_components_cache');

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
        $spId = $data['statuspage_incident_id'] ?? null;
        if (empty($spId)) {
            $comp = strtolower($component ?? $title ?? 'inc');
            $prefix = match (true) {
                str_contains($comp, 'smobilpay') || str_contains($comp, 's3p') => 'sp_core_',
                str_contains($comp, 'mtn') => 'sp_mtn_',
                str_contains($comp, 'orange') => 'sp_ora_',
                str_contains($comp, 'camtel') => 'sp_camtel_',
                str_contains($comp, 'eneo') => 'sp_eneo_',
                str_contains($comp, 'camwater') => 'sp_camwater_',
                str_contains($comp, 'canal') || str_contains($comp, 'dstv') || str_contains($comp, 'startimes') => 'sp_tv_',
                str_contains($comp, 'sabc') => 'sp_sabc_',
                default => 'sp_inc_',
            };
            $spId = $prefix . substr(md5(($title ?? 'inc') . microtime()), 0, 8);
        }

        $incident = Incident::create([
            'title'                  => $title,
            'description'            => $message,
            'severity'               => $severity,
            'status'                 => 'open',
            'source'                 => $source,
            'component'              => $component,
            'statuspage_incident_id' => $spId,
            'raw_payload'            => is_array($data) ? $data : json_decode($data, true),
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Incident enregistré avec succès dans le dashboard.',
            'incident_id' => $incident->id,
        ]);
    }
}
