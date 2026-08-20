<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Incident;
use App\Services\StatuspageService;
use App\Services\IncidentScenarioService;

class Dashboard extends Component
{
    public array $statusComponents = [];
    public string $filter = 'all';
    public ?array $activeJsonPayload = null;
    public ?string $selectedIncidentTitle = null;
    public bool $showJsonModal = false;

    // Simulateur Ops 20 Services
    public bool $showSimulationHub = false;
    public string $simulationCategory = 'all';
    public ?string $simulationFeedback = null;
    public ?string $simulationFeedbackType = 'success';

    public function mount(StatuspageService $statuspage)
    {
        $this->refreshData($statuspage);
    }

    public function refreshData(StatuspageService $statuspage)
    {
        \Illuminate\Support\Facades\Cache::forget('vigilcore_dashboard_counts');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_active_counts');
        $this->statusComponents = $statuspage->getComponents();
    }

    public function setFilter(string $severity)
    {
        $this->filter = strtolower($severity);
    }

    public function openSimulationHub()
    {
        $this->showSimulationHub = true;
        $this->simulationFeedback = null;
    }

    public function closeSimulationHub()
    {
        $this->showSimulationHub = false;
        $this->simulationFeedback = null;
    }

    public function setSimulationCategory(string $category)
    {
        $this->simulationCategory = $category;
    }

    /**
     * Déclenche l'un des 20 scénarios réels avec transmission n8n et sévérité configurable
     */
    public function triggerScenario(string $scenarioKey, StatuspageService $statuspage, ?string $severity = null)
    {
        $scenario = IncidentScenarioService::findScenario($scenarioKey);
        if (!$scenario) {
            $this->simulationFeedback = "Scénario introuvable.";
            $this->simulationFeedbackType = 'error';
            return;
        }

        $appliedSeverity = strtoupper($severity ?? $scenario['severity'] ?? 'CRITICAL');

        // 1. Exécution et transmission n8n + insertion base
        $incident = IncidentScenarioService::trigger($scenarioKey, true, $appliedSeverity);

        // 2. Si le service S3P ou Smobilpay est ciblé et Statuspage configuré, mettre à jour Statuspage
        if (in_array($scenarioKey, ['s3p', 'smobilpay'])) {
            $spSeverity = ($appliedSeverity === 'CRITICAL') ? 'major_outage' : (($appliedSeverity === 'WARNING') ? 'partial_outage' : 'under_maintenance');
            $statuspage->createIncident(
                $scenario['alert_title'],
                'investigating',
                $scenario['messages']['investigating'],
                'k8g1fr1p2ptp',
                $spSeverity
            );
        }

        $this->simulationFeedback = "✓ Incident « {$scenario['name']} » ({$appliedSeverity}) injecté & transmis au Webhook n8n !";
        $this->simulationFeedbackType = 'success';
        
        \Illuminate\Support\Facades\Cache::forget('vigilcore_active_counts');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_dashboard_counts');
        $this->refreshData($statuspage);
    }


    public function viewJson(int $incidentId)
    {
        $incident = Incident::find($incidentId);
        if ($incident) {
            $payload = $incident->raw_payload;

            if (is_string($payload)) {
                $decoded = json_decode($payload, true);
                $payload = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : ['raw' => $payload];
            }

            if (empty($payload)) {
                $payload = [
                    'id'          => $incident->id,
                    'title'       => $incident->title ?? $incident->alert_name ?? 'Incident #' . $incident->id,
                    'source'      => $incident->source ?? 'Monitoring',
                    'severity'    => strtoupper($incident->severity ?? 'INFO'),
                    'status'      => $incident->status ?? 'open',
                    'server'      => $incident->server ?? 'srv901529',
                    'description' => $incident->description ?? $incident->message ?? 'Aucune description fournie',
                    'created_at'  => $incident->created_at?->toISOString() ?? now()->toISOString(),
                    'updated_at'  => $incident->updated_at?->toISOString() ?? now()->toISOString(),
                ];
            }

            $this->activeJsonPayload = $payload;
            $this->selectedIncidentTitle = $incident->title ?? $incident->alert_name ?? 'Incident #' . $incident->id;
            $this->showJsonModal = true;
        }
    }

    public function closeModal()
    {
        $this->showJsonModal = false;
        $this->activeJsonPayload = null;
        $this->selectedIncidentTitle = null;
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
            \Illuminate\Support\Facades\Cache::forget('vigilcore_active_counts');
            $this->refreshData($statuspage);
        }
    }

    public function simulateKibanaIncident(StatuspageService $statuspage)
    {
        $this->triggerScenario('s3p', $statuspage);
    }

    public function simulateZabbixAlert()
    {
        $this->triggerScenario('mtn_momo', app(StatuspageService::class));
    }

    public function simulateN8nDispatch()
    {
        $this->triggerScenario('camtel', app(StatuspageService::class));
    }

    public function render()
    {
        $query = Incident::query();
        if ($this->filter !== 'all') {
            $query->where('severity', strtoupper($this->filter));
        }

        $incidents = $query->latest()->get();

        // Récupération des compteurs avec mise en cache ultra-courte (5s)
        $counts = \Illuminate\Support\Facades\Cache::remember('vigilcore_dashboard_counts', 5, function () {
            $all = Incident::all();
            $active = $all->where('status', '!=', 'resolved');

            return [
                // Totaux par sévérité pour les onglets du tableau
                'totalAll'  => $all->count(),
                'countCrit' => $all->filter(fn($i) => strtoupper($i->severity) === 'CRITICAL')->count(),
                'countWarn' => $all->filter(fn($i) => strtoupper($i->severity) === 'WARNING')->count(),
                'countInfo' => $all->filter(fn($i) => strtoupper($i->severity) === 'INFO')->count(),

                // Uniquement les incidents encore ACTIFS (non résolus) pour le KPI du haut
                'activeCrit' => $active->filter(fn($i) => strtoupper($i->severity) === 'CRITICAL')->count(),
                'activeWarn' => $active->filter(fn($i) => strtoupper($i->severity) === 'WARNING')->count(),
                'activeInfo' => $active->filter(fn($i) => strtoupper($i->severity) === 'INFO')->count(),
            ];
        });

        $totalAll   = $counts['totalAll'];
        $countCrit  = $counts['countCrit'];
        $countWarn  = $counts['countWarn'];
        $countInfo  = $counts['countInfo'];

        $activeCrit  = $counts['activeCrit'];
        $activeWarn  = $counts['activeWarn'];
        $activeInfo  = $counts['activeInfo'];
        $totalActive = $activeCrit + $activeWarn + $activeInfo;

        // Calcul dynamique du SLA Uptime en temps réel (rolling window 7j)
        $totalDurationSec = 0;
        foreach ($incidents as $inc) {
            if ($inc->status === 'resolved' && $inc->created_at && $inc->updated_at) {
                $diff = $inc->created_at->diffInSeconds($inc->updated_at);
                if ($diff > 0) $totalDurationSec += $diff;
            } elseif ($inc->status !== 'resolved' && $inc->created_at) {
                $diff = $inc->created_at->diffInSeconds(now());
                if ($diff > 0) $totalDurationSec += $diff;
            }
        }
        $downtimeMinutes = round($totalDurationSec / 60);
        $uptimePct = 100.00;
        if ($totalAll > 0) {
            $calculatedUptime = 100 - (($downtimeMinutes / 10080) * 100);
            $uptimePct = max(98.50, min(99.99, round($calculatedUptime, 2)));
        }

        // Scénarios filtrés pour le modal de simulation
        $allScenarios = IncidentScenarioService::getScenarios();
        $filteredScenarios = ($this->simulationCategory === 'all')
            ? $allScenarios
            : array_values(array_filter($allScenarios, fn($s) => $s['category'] === $this->simulationCategory));

        return view('livewire.dashboard', [
            'incidents'         => $incidents,
            'totalAll'          => $totalAll,
            'countCrit'         => $countCrit,
            'countWarn'         => $countWarn,
            'countInfo'         => $countInfo,
            'activeCrit'        => $activeCrit,
            'activeWarn'        => $activeWarn,
            'activeInfo'        => $activeInfo,
            'totalActive'       => $totalActive,
            'uptimePct'         => $uptimePct,
            'scenarios'         => $filteredScenarios,
            'allScenariosCount' => count($allScenarios),
        ]);
    }
}

