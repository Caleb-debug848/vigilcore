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
        return $this->handleAlert($request, $statuspage);
    }

    /**
     * Méthode principale de traitement des alertes et résolutions
     */
    public function handleAlert(Request $request, ?StatuspageService $statuspage = null)
    {
        $statuspage = $statuspage ?? app(StatuspageService::class);

        try {
            $data = $request->all();

            $component   = $data['component'] ?? null;
            $title       = $data['alert_name'] ?? $data['title'] ?? ($component ? "Alerte Composant [{$component}]" : 'Alerte Système VigilCore');
            $source      = $data['source'] ?? 'Monitoring';
            $severity    = strtoupper($data['severity'] ?? 'INFO');
            $status      = strtolower($data['status'] ?? 'investigating');
            $description = $data['message'] ?? $data['description'] ?? 'Anomalie détectée par les sondes.';
            $server      = $data['server'] ?? $data['host'] ?? 'srv901529';
            $statuspageId= $data['statuspage_incident_id'] ?? null;

            // 1. Si le webhook annonce une résolution (RESOLVED / OK)
            if (in_array($status, ['resolved', 'ok'])) {
                // Recherche de l'incident actif correspondant
                $query = Incident::where('status', '!=', 'resolved');

                if ($statuspageId) {
                    $query->where('statuspage_incident_id', $statuspageId);
                } elseif ($component) {
                    $query->where(function ($q) use ($component, $title) {
                        $q->where('title', 'like', "%{$component}%")
                          ->orWhere('description', 'like', "%{$component}%")
                          ->orWhere('raw_payload->component', $component)
                          ->orWhere('raw_payload->service', $component)
                          ->orWhere('title', $title);
                    });
                } else {
                    $query->where('title', $title);
                }

                $incident = $query->latest()->first();

                if ($incident) {
                    $incident->update([
                        'status' => 'resolved',
                        'raw_payload' => array_merge($incident->raw_payload ?? [], [
                            'resolved_payload' => $data,
                            'resolved_at' => now()->toISOString()
                        ]),
                    ]);

                    // Clôture automatique sur Atlassian Statuspage si relié
                    $targetStatuspageId = $incident->statuspage_incident_id ?? $statuspageId;
                    if ($targetStatuspageId) {
                        $statuspage->resolveIncident(
                            $targetStatuspageId,
                            'Rétablissement validé et synchronisé par sonde automatique.'
                        );
                    }

                    return response()->json([
                        'success'     => true,
                        'message'     => 'Incident résolu avec succès dans le dashboard.',
                        'incident_id' => $incident->id
                    ], 200);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Aucun incident actif trouvé pour cette alerte (déjà résolu ou inexistant).'
                ], 200);
            }

            // 2. Sinon, création d'une nouvelle alerte / panne en base locale
            $newIncident = Incident::create([
                'title'                  => $title,
                'source'                 => $source,
                'severity'               => in_array(strtolower($severity), ['critical', 'warning', 'info']) ? strtolower($severity) : 'info',
                'status'                 => in_array($status, ['resolved', 'identified', 'monitoring']) ? $status : 'investigating',
                'description'            => $description,
                'statuspage_incident_id' => $statuspageId,
                'raw_payload'            => array_merge($data, [
                    'component' => $component,
                    'server'    => $server,
                ]),
            ]);

            return response()->json([
                'success'     => true,
                'message'     => 'Alerte enregistrée.',
                'incident_id' => $newIncident->id
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur Ingestion Webhook VigilCore : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
