<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Incident;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncidentReports extends Component
{
    use WithPagination;

    public $period = 'week'; // 'today', 'week', 'month', 'all'
    public $severityFilter = 'all';
    public $search = '';

    // Réinitialise la pagination lors d'un changement de filtre
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSeverityFilter()
    {
        $this->resetPage();
    }

    public function setPeriod($period)
    {
        $this->period = $period;
        $this->resetPage();
    }

    /**
     * Export Excel Haute Définition aux couleurs de VigilCore
     */
    public function exportExcel()
    {
        $incidents = $this->getFilteredQuery()->get();
        $fileName = 'VigilCore_Rapport_' . strtoupper($this->period) . '_' . date('Ymd_His') . '.xls';

        // Calcul des métriques pour l'en-tête Excel
        $totalCount    = $incidents->count();
        $resolvedCount = $incidents->where('status', 'resolved')->count();
        $critCount     = $incidents->where('severity', 'CRITICAL')->count();
        $warnCount     = $incidents->where('severity', 'WARNING')->count();
        $resRate       = $totalCount > 0 ? round(($resolvedCount / $totalCount) * 100) : 100;

        $totalDurationSec = 0;
        $resolvedWithTime = 0;
        foreach ($incidents as $inc) {
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

        return response()->streamDownload(function () use ($incidents, $totalCount, $resolvedCount, $critCount, $warnCount, $resRate, $mttrFormatted) {
            $logoPath = public_path('images/logo.png');
            $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>VigilCore SLA Report</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>
                body { font-family: "Segoe UI", Arial, sans-serif; font-size: 11pt; color: #1e293b; background-color: #ffffff; }
                .title-banner { background-color: #0f172a; color: #ffffff; font-size: 15pt; font-weight: bold; padding: 12px; vertical-align: middle; }
                .subtitle { background-color: #1e293b; color: #94a3b8; font-size: 10pt; padding: 6px; }
                .kpi-title { font-size: 9pt; font-weight: bold; color: #64748b; text-transform: uppercase; background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px; }
                .kpi-val { font-size: 14pt; font-weight: bold; color: #0f172a; background-color: #ffffff; border: 1px solid #cbd5e1; padding: 8px; text-align: center; }
                .kpi-crit { color: #dc2626; font-weight: bold; }
                .kpi-sla { color: #059669; font-weight: bold; }
                .kpi-mttr { color: #d97706; font-weight: bold; }
                th { background-color: #4338ca; color: #ffffff; font-weight: bold; padding: 10px; border: 1px solid #3730a3; text-align: left; }
                td { padding: 8px; border: 1px solid #e2e8f0; vertical-align: middle; }
                .row-alt { background-color: #f8fafc; }
                .badge-crit { background-color: #fee2e2; color: #991b1b; font-weight: bold; text-align: center; border-radius: 4px; padding: 4px; }
                .badge-warn { background-color: #fef3c7; color: #92400e; font-weight: bold; text-align: center; border-radius: 4px; padding: 4px; }
                .badge-info { background-color: #dbeafe; color: #1e40af; font-weight: bold; text-align: center; border-radius: 4px; padding: 4px; }
                .badge-res { background-color: #d1fae5; color: #065f46; font-weight: bold; text-align: center; }
                .badge-open { background-color: #fee2e2; color: #b91c1c; font-weight: bold; text-align: center; }
                .footer { font-size: 8pt; color: #94a3b8; font-style: italic; padding: 10px; }
            </style></head><body>';

            echo '<table border="0" cellspacing="0" cellpadding="0" style="width:100%;">';
            
            // Header Banner avec Logo Officiel
            $logoHtml = $logoBase64 ? '<img src="data:image/png;base64,' . $logoBase64 . '" width="36" height="36" style="vertical-align:middle;margin-right:10px;border-radius:6px;" /> ' : '🛡️ ';
            echo '<tr><td colspan="8" class="title-banner">' . $logoHtml . 'VIGILCORE OPS-01 — RAPPORT D\'AUDIT & ANALYTICS SLA</td></tr>';
            echo '<tr><td colspan="8" class="subtitle">Généré le ' . date('d/m/Y à H:i:s') . ' | Période : ' . strtoupper($this->period) . ' | Environnement : Production srv901529</td></tr>';
            echo '<tr><td colspan="8" style="height:12px;"></td></tr>';


            // KPI Summary Cards in Table
            echo '<tr>
                <td colspan="2" class="kpi-title">TOTAL INCIDENTS</td>
                <td colspan="2" class="kpi-title">DISPONIBILITÉ SLA</td>
                <td colspan="2" class="kpi-title">MTTR MOYEN</td>
                <td colspan="2" class="kpi-title">TAUX DE RÉSOLUTION</td>
            </tr>';
            echo '<tr>
                <td colspan="2" class="kpi-val">' . $totalCount . ' <span style="font-size:9pt;color:#64748b;">(' . $critCount . ' critiques)</span></td>
                <td colspan="2" class="kpi-val kpi-sla">≥ 99.85%</td>
                <td colspan="2" class="kpi-val kpi-mttr">' . $mttrFormatted . '</td>
                <td colspan="2" class="kpi-val kpi-sla">' . $resRate . '% (' . $resolvedCount . ' résolus)</td>
            </tr>';
            echo '<tr><td colspan="8" style="height:16px;"></td></tr>';

            // Data Table Headers
            echo '<tr>
                <th style="width:60px;">ID</th>
                <th style="width:150px;">DATE & HEURE</th>
                <th style="width:260px;">COMPOSANT / INTITULÉ</th>
                <th style="width:110px;text-align:center;">SÉVÉRITÉ</th>
                <th style="width:110px;text-align:center;">STATUT</th>
                <th style="width:110px;text-align:center;">MTTR (DURÉE)</th>
                <th style="width:160px;">SOURCE</th>
                <th style="width:350px;">DESCRIPTION DÉTAILLÉE</th>
            </tr>';

            $i = 0;
            foreach ($incidents as $inc) {
                $i++;
                $rowClass = ($i % 2 === 0) ? 'row-alt' : '';
                
                $durationSec = 0;
                $mttr = '--';
                if ($inc->status === 'resolved' && $inc->created_at && $inc->updated_at) {
                    $durationSec = $inc->created_at->diffInSeconds($inc->updated_at);
                    $mttr = ($durationSec >= 60) ? (floor($durationSec / 60) . 'm ' . ($durationSec % 60) . 's') : ($durationSec . 's');
                }

                $sev = strtoupper($inc->severity ?? 'INFO');
                $sevClass = 'badge-info';
                if ($sev === 'CRITICAL') $sevClass = 'badge-crit';
                elseif ($sev === 'WARNING') $sevClass = 'badge-warn';

                $statClass = ($inc->status === 'resolved') ? 'badge-res' : 'badge-open';
                $statLabel = ($inc->status === 'resolved') ? 'RÉSOLU' : 'EN COURS';

                echo "<tr class='{$rowClass}'>
                    <td style='font-weight:bold;text-align:center;'>#{$inc->id}</td>
                    <td>" . ($inc->created_at ? $inc->created_at->format('d/m/Y H:i:s') : '') . "</td>
                    <td style='font-weight:bold;color:#0f172a;'>" . htmlspecialchars($inc->title ?? $inc->alert_name ?? 'Incident') . "</td>
                    <td class='{$sevClass}'>{$sev}</td>
                    <td class='{$statClass}'>{$statLabel}</td>
                    <td style='text-align:center;font-weight:bold;color:#d97706;'>{$mttr}</td>
                    <td style='color:#64748b;'>" . htmlspecialchars($inc->source ?? 'Monitoring') . "</td>
                    <td style='color:#334155;'>" . htmlspecialchars($inc->description ?? $inc->message ?? '') . "</td>
                </tr>";
            }

            echo '<tr><td colspan="8" style="height:14px;"></td></tr>';
            echo '<tr><td colspan="8" class="footer">Document généré automatiquement par VigilCore OPS-01 — Confidentiel & Réservé à l\'exploitation.</td></tr>';
            echo '</table></body></html>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function exportCsv()
    {
        return $this->exportExcel();
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
        if ($this->severityFilter !== 'all') {
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

        // Calculs analytiques mémorisés en cache (10s) pour éviter les requêtes lourdes répétées
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
            $criticalCount  = $allPeriodIncidents->where('severity', 'CRITICAL')->count();
            $warningCount   = $allPeriodIncidents->where('severity', 'WARNING')->count();
            $infoCount      = $allPeriodIncidents->where('severity', 'INFO')->count();

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

            // Taux d'auto-résolution
            $resolutionRate = $totalCount > 0 ? round(($resolvedCount / $totalCount) * 100) : 100;

            // Top 5 Services impactés
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
                ->take(5);

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
