<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Services\StatuspageService;
use Illuminate\Http\Request;

class IncidentWebhookController extends Controller
{
    /**
     * Clôture automatique d'un incident via Webhook (n8n, Zabbix, Kibana) et synchronisation Statuspage
     */
    public function autoResolve(Request $request, StatuspageService $statuspage)
    {
        $serviceName = $request->input('service_name'); // ex: "Third Party Merchant API (S3P)"
        $incidentId = $request->input('statuspage_incident_id');
        $localIncidentId = $request->input('incident_id');

        // 1. Clôture sur l'API Atlassian Statuspage
        if ($incidentId) {
            $statuspage->resolveIncident($incidentId, 'Rétablissement automatique confirmé par les sondes.');
        }

        // 2. Clôture en base locale VigilCore
        $query = Incident::where('status', '!=', 'resolved');

        if ($localIncidentId) {
            $query->where('id', $localIncidentId);
        } elseif ($incidentId) {
            $query->where('statuspage_incident_id', $incidentId);
        } elseif ($serviceName) {
            $query->where(function ($q) use ($serviceName) {
                $q->where('title', 'like', "%{$serviceName}%")
                  ->orWhere('description', 'like', "%{$serviceName}%");
            });
        }

        $updatedCount = $query->update(['status' => 'resolved']);

        return response()->json([
            'success'       => true,
            'message'       => 'Incident auto-résolu.',
            'updated_count' => $updatedCount,
        ]);
    }
}
