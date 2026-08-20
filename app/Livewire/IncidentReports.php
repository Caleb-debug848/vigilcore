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

    /**
     * Ouvre la modale d'inspection JSON pour un incident
     */
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
            $downtimeMinutes = round($totalDurationSec / 60);
            $uptimePct = 100;
            if ($totalPeriodMinutes > 0 && $totalCount > 0) {
                $calculatedUptime = 100 - (($downtimeMinutes / $totalPeriodMinutes) * 100);
                $uptimePct = max(98.50, min(99.99, round($calculatedUptime, 2)));
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
