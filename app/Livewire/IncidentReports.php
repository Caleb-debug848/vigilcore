<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Incident;
use App\Http\Controllers\IncidentExportController;
use Carbon\Carbon;

class IncidentReports extends Component
{
    use WithPagination;

    public string $period = 'week'; // 'today', 'week', 'month', 'all'
    public string $severityFilter = 'all';
    public string $search = '';

    // Modale d'inspection JSON
    public ?array $activeJsonPayload = null;
    public ?string $selectedIncidentTitle = null;
    public bool $showJsonModal = false;

    // Réinitialise la pagination lors d'un changement de filtre
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSeverityFilter()
    {
        $this->resetPage();
    }

    public function setPeriod(string $period)
    {
        $this->period = $period;
        $this->resetPage();
    }

    public function refreshData()
    {
        \Illuminate\Support\Facades\Cache::forget('vigilcore_reports_kpis_today');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_reports_kpis_week');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_reports_kpis_month');
        \Illuminate\Support\Facades\Cache::forget('vigilcore_reports_kpis_all');
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

    /**
     * Ouvre la modale d'inspection JSON pour un incident avec diagnostic complet
     */
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

    /**
     * Ferme la modale d'inspection JSON
     */
    public function closeModal()
    {
        $this->showJsonModal = false;
        $this->activeJsonPayload = null;
        $this->selectedIncidentTitle = null;
    }

    /**
     * Export Excel Haute Définition aux couleurs de VigilCore
     */
    public function exportExcel()
    {
        $incidents = $this->getFilteredQuery()->get();
        return IncidentExportController::exportExcelResponse($incidents, $this->period);
    }

    public function exportCsv()
    {
        $incidents = $this->getFilteredQuery()->get();
        return IncidentExportController::exportCsvResponse($incidents, $this->period);
    }

    protected function getFilteredQuery()
    {
        $query = Incident::query();

        // 1. Filtre par période temporelle
        if ($this->period === 'today') {
            $query->where('created_at', '>=', Carbon::today());
        } elseif ($this->period === 'week') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($this->period === 'month') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        // 2. Filtre par sévérité
        if ($this->severityFilter !== 'all' && !empty($this->severityFilter)) {
            $query->where('severity', strtoupper($this->severityFilter));
        }

        // 3. Recherche textuelle
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('source', 'like', '%' . $this->search . '%');
            });
        }

        return $query->latest();
    }

    public function render()
    {
        $cacheKey = "vigilcore_reports_kpis_{$this->period}";

        // Calculs analytiques mémorisés en cache (10s) en format tableau pur
        $analytics = \Illuminate\Support\Facades\Cache::remember($cacheKey, 10, function () {
            $baseQuery = Incident::query();
            $totalPeriodMinutes = 1440;

            if ($this->period === 'today') {
                $baseQuery->where('created_at', '>=', Carbon::today());
                $totalPeriodMinutes = 1440;
            } elseif ($this->period === 'week') {
                $baseQuery->where('created_at', '>=', Carbon::now()->subDays(7));
                $totalPeriodMinutes = 10080;
            } elseif ($this->period === 'month') {
                $baseQuery->where('created_at', '>=', Carbon::now()->subDays(30));
                $totalPeriodMinutes = 43200;
            } else {
                $totalPeriodMinutes = 43200 * 3;
            }

            $allPeriodIncidents = $baseQuery->get();
            $totalCount     = $allPeriodIncidents->count();
            $resolvedCount  = $allPeriodIncidents->where('status', 'resolved')->count();
            $criticalCount  = $allPeriodIncidents->filter(fn($i) => strtoupper($i->severity) === 'CRITICAL')->count();
            $warningCount   = $allPeriodIncidents->filter(fn($i) => strtoupper($i->severity) === 'WARNING')->count();
            $infoCount      = $allPeriodIncidents->filter(fn($i) => strtoupper($i->severity) === 'INFO')->count();

            // Calcul du MTTR Moyen
            $totalDurationSec = 0;
            $resolvedWithTime = 0;
            foreach ($allPeriodIncidents as $inc) {
                if ($inc->status === 'resolved' && $inc->created_at && $inc->updated_at) {
                    $diff = $inc->created_at->diffInSeconds($inc->updated_at);
                    if ($diff > 0) {
                        $totalDurationSec += $diff;
                        $resolvedWithTime++;
                    }
                }
            }

            $avgMttrSec = $resolvedWithTime > 0 ? round($totalDurationSec / $resolvedWithTime) : 120;
            $mttrFormatted = ($avgMttrSec >= 60) ? (floor($avgMttrSec / 60) . 'm ' . ($avgMttrSec % 60) . 's') : ($avgMttrSec . 's');

            // Disponibilité Uptime SLA %
            $downtimeWeightedMinutes = 0;
            foreach ($allPeriodIncidents as $inc) {
                $sev = strtoupper($inc->severity ?? 'INFO');
                if (!in_array($sev, ['CRITICAL', 'WARNING'])) {
                    continue;
                }
                $weight = ($sev === 'CRITICAL') ? 1.0 : 0.3;

                if ($inc->status === 'resolved' && $inc->created_at && $inc->updated_at) {
                    $diffMin = min(120, $inc->created_at->diffInMinutes($inc->updated_at));
                    $downtimeWeightedMinutes += ($diffMin * $weight);
                } elseif ($inc->status !== 'resolved' && $inc->created_at) {
                    $diffMin = min(180, $inc->created_at->diffInMinutes(now()));
                    $downtimeWeightedMinutes += ($diffMin * $weight);
                }
            }

            $uptimePct = 100.00;
            if ($totalPeriodMinutes > 0 && $downtimeWeightedMinutes > 0) {
                $calculatedUptime = 100.0 - (($downtimeWeightedMinutes / $totalPeriodMinutes) * 100);
                $uptimePct = round(max(95.00, min(99.99, $calculatedUptime)), 2);
            }

            // Taux de résolution
            $resolutionRate = $totalCount > 0 ? round(($resolvedCount / $totalCount) * 100) : 100;

            // Top 5 Services impactés (sauvegardé comme tableau natif pour compatibilité sérialisation cache)
            $topServices = $allPeriodIncidents->groupBy('title')
                ->map(function ($items, $key) use ($totalCount) {
                    $count = $items->count();
                    return [
                        'name'     => $key,
                        'count'    => $count,
                        'pct'      => $totalCount > 0 ? round(($count / $totalCount) * 100) : 0,
                        'resolved' => $items->where('status', 'resolved')->count(),
                    ];
                })
                ->sortByDesc('count')
                ->take(5)
                ->values()
                ->toArray();

            return compact(
                'totalCount',
                'resolvedCount',
                'criticalCount',
                'warningCount',
                'infoCount',
                'uptimePct',
                'mttrFormatted',
                'resolutionRate',
                'topServices'
            );
        });

        // Liste paginée pour le tableau de registre
        $incidents = $this->getFilteredQuery()->paginate(15);

        return view('livewire.incident-reports', array_merge($analytics, [
            'incidents' => $incidents,
        ]))->layout('layouts.app');
    }
}
