<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Incident;
use App\Services\StatuspageService;

class Dashboard extends Component
{
    public array $statusComponents = [];
    public string $filter = 'all';
    public ?array $activeJsonPayload = null;
    public bool $showJsonModal = false;

    public function mount(StatuspageService $statuspage)
    {
        $this->refreshData($statuspage);
    }

    public function refreshData(StatuspageService $statuspage)
    {
        $this->statusComponents = $statuspage->getComponents();
    }

    public function setFilter(string $severity)
    {
        $this->filter = $severity;
    }

    public function viewJson(int $incidentId)
    {
        $incident = Incident::find($incidentId);
        if ($incident) {
            $this->activeJsonPayload = $incident->raw_payload ?? [
                'id' => $incident->id,
                'title' => $incident->title,
                'source' => $incident->source,
                'severity' => $incident->severity,
                'status' => $incident->status,
                'description' => $incident->description,
                'created_at' => $incident->created_at->toISOString(),
            ];
            $this->showJsonModal = true;
        }
    }

    public function closeModal()
    {
        $this->showJsonModal = false;
        $this->activeJsonPayload = null;
    }

    public function resolveIncident(int $id, StatuspageService $statuspage)
    {
        $incident = Incident::find($id);
        if ($incident) {
            if ($incident->statuspage_incident_id) {
                $statuspage->resolveIncident(
                    $incident->statuspage_incident_id,
                    'Incident résolu et validé par les équipes opérationnelles VigilCore.'
                );
            }

            $incident->update(['status' => 'resolved']);
            $this->refreshData($statuspage);
        }
    }

    public function simulateKibanaIncident(StatuspageService $statuspage)
    {
        $componentId = 'k8g1fr1p2ptp'; // S3P Gateway ID
        $result = $statuspage->createIncident(
            'HTTP 500 Spike // API S3P Gateway',
            'investigating',
            'Détection de 14 erreurs 500 en 60s sur la passerelle de paiement.',
            $componentId,
            'major_outage'
        );

        Incident::create([
            'title' => 'HTTP 500 Spike // API S3P Gateway',
            'source' => 'Kibana Logs',
            'severity' => 'critical',
            'status' => 'investigating',
            'description' => 'Détection de 14 erreurs 500 en 60s sur la route /api/v1/payments/process.',
            'statuspage_incident_id' => $result['id'] ?? null,
            'raw_payload' => [
                'event_type' => 'HTTP_ERROR_THRESHOLD',
                'service' => 's3p',
                'route' => '/api/v1/payments/process',
                'errors_count' => 14,
                'window' => '60s',
                'server' => 'srv901529'
            ]
        ]);

        $this->refreshData($statuspage);
    }

    public function simulateZabbixAlert()
    {
        Incident::create([
            'title' => 'High Memory Usage Warning (>88%)',
            'source' => 'Zabbix Agent 2',
            'severity' => 'warning',
            'status' => 'open',
            'description' => 'La mémoire vive du serveur srv901529 a dépassé 88% d\'occupation.',
            'raw_payload' => [
                'metric' => 'vm.memory.util',
                'threshold' => '88%',
                'current_value' => '89.4%',
                'host' => 'srv901529'
            ]
        ]);
    }

    public function simulateN8nDispatch()
    {
        Incident::create([
            'title' => 'Camtel Webhook Queue Latency',
            'source' => 'n8n Alert Hub',
            'severity' => 'info',
            'status' => 'open',
            'description' => 'Temporisation observée dans la transmission des callbacks transactionnels.',
            'raw_payload' => [
                'workflow' => 'vigilcore-alert',
                'connector' => 'camtel-gateway',
                'queue_delay_ms' => 450
            ]
        ]);
    }

    public function render()
    {
        $query = Incident::query();
        if ($this->filter !== 'all') {
            $query->where('severity', $this->filter);
        }

        $incidents = $query->latest()->get();

        $activeCrit = Incident::where('status', '!=', 'resolved')->where('severity', 'critical')->count();
        $activeWarn = Incident::where('status', '!=', 'resolved')->where('severity', 'warning')->count();
        $activeInfo = Incident::where('status', '!=', 'resolved')->where('severity', 'info')->count();
        $totalActive = $activeCrit + $activeWarn + $activeInfo;

        return view('livewire.dashboard', [
            'incidents' => $incidents,
            'activeCrit' => $activeCrit,
            'activeWarn' => $activeWarn,
            'activeInfo' => $activeInfo,
            'totalActive' => $totalActive,
        ]);
    }
}
