<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Incident;
use App\Services\StatuspageService;
use App\Services\IncidentScenarioService;

class Dashboard extends Component
{
    use WithPagination;

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
        $this->resetPage();
    }

    public function switchLocale(string $locale)
    {
        if (in_array($locale, ['fr', 'en'])) {
            session(['locale' => $locale]);
            session()->save();
            app()->setLocale($locale);
        }
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
            $raw = $incident->raw_payload;

            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $raw = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : ['raw' => $raw];
            }

            if (!is_array($raw)) {
                $raw = [];
            }

            $createdAtDouala = $incident->created_at 
                ? $incident->created_at->timezone('Africa/Douala')->format('Y-m-d\TH:i:sP') 
                : now()->timezone('Africa/Douala')->format('Y-m-d\TH:i:sP');
            $createdAtHuman = $incident->created_at 
                ? $incident->created_at->timezone('Africa/Douala')->format('d/m/Y H:i:s') . ' (WAT - Douala)'
                : now()->timezone('Africa/Douala')->format('d/m/Y H:i:s') . ' (WAT - Douala)';

            $updatedAtDouala = $incident->updated_at 
                ? $incident->updated_at->timezone('Africa/Douala')->format('Y-m-d\TH:i:sP') 
                : null;
            $updatedAtHuman = ($incident->status === 'resolved' && $incident->updated_at)
                ? $incident->updated_at->timezone('Africa/Douala')->format('d/m/Y H:i:s') . ' (WAT - Douala)'
                : 'En cours (Non résolu)';

            $mttrSec = null;
            $mttrHuman = 'En cours (Non résolu)';
            if ($incident->status === 'resolved' && $incident->created_at && $incident->updated_at) {
                $mttrSec = $incident->created_at->diffInSeconds($incident->updated_at);
                $mttrHuman = ($mttrSec >= 60) 
                    ? (floor($mttrSec / 60) . 'm ' . ($mttrSec % 60) . 's') 
                    : ($mttrSec . 's');
            }

            // Construction du JSON d'audit exhaustif avec diagnostic technique
            $payload = array_merge([
                'id'                    => $incident->id,
                'title'                 => $incident->title ?? 'Alerte Système',
                'alert_name'            => $raw['alert_name'] ?? $incident->title,
                'service_name'          => $raw['service_name'] ?? $incident->title,
                'component'             => $raw['component'] ?? 'Infrastructure',
                'severity'              => strtoupper($incident->severity ?? 'INFO'),
                'status'                => $incident->status ?? 'open',
                'root_cause'            => $raw['root_cause'] ?? 'Délai d\'attente ou anomalie de flux réseau détecté par les sondes de surveillance.',
                'error_code'            => $raw['error_code'] ?? 'ERR_GATEWAY_TIMEOUT_504',
                'http_status'           => $raw['http_status'] ?? 504,
                'business_impact'       => $raw['business_impact'] ?? 'Ralentissement ou échec temporaire des validations de transactions usagers.',
                'recommended_action'    => $raw['recommended_action'] ?? 'Vérifier la liaison API partenaire et relancer le microservice de synchronisation.',
                'affected_endpoints'    => $raw['affected_endpoints'] ?? ['/api/v2/payment/validate', '/api/v2/status'],
                'server'                => $raw['server'] ?? $raw['host'] ?? 'srv901529',
                'source'                => $incident->source ?? $raw['source'] ?? 'Kibana Logs Engine',
                'environment'           => 'Production (srv901529)',
                'datacenter'            => 'Douala Datacenter (Cameroun) • Cloudflare Edge',
                'timezone'              => 'Africa/Douala (UTC+1 / WAT - Cameroun)',
                'triggered_at_wat'      => $createdAtHuman,
                'resolved_at_wat'       => $updatedAtHuman,
                'duration_mttr'         => $mttrHuman,
                'started_at_iso'        => $createdAtDouala,
                'ended_at_iso'          => ($incident->status === 'resolved') ? $updatedAtDouala : null,
                'message'               => $incident->description ?? $raw['message'] ?? 'Aucun détail fourni',
                'description'           => $incident->description ?? $raw['description'] ?? $raw['message'] ?? '',
                'message_investigating' => $raw['message_investigating'] ?? ($incident->description ?? ''),
                'message_identified'    => $raw['message_identified'] ?? 'Analyse de cause racine effectuée par les équipes.',
                'message_monitoring'    => $raw['message_monitoring'] ?? 'Rétablissement en cours de surveillance active.',
                'message_resolved'      => $raw['message_resolved'] ?? 'Le service est entièrement rétabli et opérationnel.',
            ], $raw);

            // Garantir la cohérence des indicateurs critiques
            $payload['id']               = $incident->id;
            $payload['status']           = $incident->status;
            $payload['severity']         = strtoupper($incident->severity ?? 'INFO');
            $payload['timezone']         = 'Africa/Douala (UTC+1 / WAT - Cameroun)';
            $payload['triggered_at_wat'] = $createdAtHuman;
            $payload['resolved_at_wat']  = $updatedAtHuman;
            $payload['duration_mttr']    = $mttrHuman;

            $this->activeJsonPayload = $payload;
            $this->selectedIncidentTitle = $incident->title ?? 'Incident #' . $incident->id;
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
            } else {
                $statuspage->resolveMatchingIncident($incident->title);
            }

            $incident->update(['status' => 'resolved']);
            \Illuminate\Support\Facades\Cache::forget('vigilcore_dashboard_counts');
            \Illuminate\Support\Facades\Cache::forget('vigilcore_active_counts');
            \Illuminate\Support\Facades\Cache::forget('statuspage_components_cache');
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

        $incidents = $query->latest()->paginate(10);

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

        // Calcul dynamique du SLA Uptime en temps réel (rolling window 7j = 10080 min)
        $downtimeMinutes = 0;
        $allIncidents = Incident::where('created_at', '>=', now()->subDays(7))->get();
        foreach ($allIncidents as $inc) {
            $sev = strtoupper($inc->severity ?? 'INFO');
            if (!in_array($sev, ['CRITICAL', 'WARNING'])) {
                continue;
            }
            $weight = ($sev === 'CRITICAL') ? 1.0 : 0.3;

            if ($inc->status === 'resolved' && $inc->created_at && $inc->updated_at) {
                $diffMin = min(120, $inc->created_at->diffInMinutes($inc->updated_at));
                $downtimeMinutes += ($diffMin * $weight);
            } elseif ($inc->status !== 'resolved' && $inc->created_at) {
                $diffMin = min(180, $inc->created_at->diffInMinutes(now()));
                $downtimeMinutes += ($diffMin * $weight);
            }
        }

        $uptimePct = 100.00;
        if ($downtimeMinutes > 0) {
            $calculatedUptime = 100.0 - (($downtimeMinutes / 10080) * 100);
            $uptimePct = round(max(95.00, min(99.99, $calculatedUptime)), 2);
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

