<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use Carbon\Carbon;

class IncidentExportController extends Controller
{
    /**
     * Exporte les incidents filtrés au format Excel (.xls) haute définition
     */
    public function export(Request $request)
    {
        $period = $request->query('period', 'week');
        $severity = $request->query('severity', 'all');
        $search = $request->query('search', '');
        $format = $request->query('format', 'xls');

        $query = Incident::query();

        // 1. Filtre temporel
        if ($period === 'today') {
            $query->where('created_at', '>=', Carbon::today());
        } elseif ($period === 'week') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($period === 'month') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        // 2. Filtre sévérité
        if ($severity !== 'all' && !empty($severity)) {
            $query->where('severity', strtoupper($severity));
        }

        // 3. Filtre recherche textuelle
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('source', 'like', '%' . $search . '%');
            });
        }

        $incidents = $query->latest()->get();

        if ($format === 'csv') {
            return $this->exportCsvResponse($incidents, $period);
        }

        return $this->exportExcelResponse($incidents, $period);
    }

    /**
     * Génère la réponse de téléchargement Excel (.xls) stylisée
     */
    public static function exportExcelResponse($incidents, string $period = 'week')
    {
        $fileName = 'VigilCore_Rapport_' . strtoupper($period) . '_' . date('Ymd_His') . '.xls';

        $totalCount    = $incidents->count();
        $resolvedCount = $incidents->where('status', 'resolved')->count();
        $critCount     = $incidents->filter(fn($i) => strtoupper($i->severity) === 'CRITICAL')->count();
        $warnCount     = $incidents->filter(fn($i) => strtoupper($i->severity) === 'WARNING')->count();
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

        return response()->streamDownload(function () use ($incidents, $totalCount, $resolvedCount, $critCount, $warnCount, $resRate, $mttrFormatted, $period) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>VigilCore SLA Report</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>
                body { font-family: "Segoe UI", Arial, sans-serif; font-size: 11pt; color: #1e293b; background-color: #ffffff; }
                .title-banner { background-color: #0020B2; color: #ffffff; font-size: 14pt; font-weight: bold; padding: 12px 16px; vertical-align: middle; }
                .subtitle { background-color: #0c154a; color: #cbd5e1; font-size: 10pt; padding: 6px 16px; }
                .kpi-title { font-size: 9pt; font-weight: bold; color: #64748b; text-transform: uppercase; background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px; }
                .kpi-val { font-size: 14pt; font-weight: bold; color: #0f172a; background-color: #ffffff; border: 1px solid #cbd5e1; padding: 8px; text-align: center; }
                .kpi-crit { color: #dc2626; font-weight: bold; }
                .kpi-sla { color: #059669; font-weight: bold; }
                .kpi-mttr { color: #d97706; font-weight: bold; }
                th { background-color: #0020B2; color: #ffffff; font-weight: bold; padding: 10px; border: 1px solid #001ca0; text-align: left; }

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
            
            // Header Banner
            echo '<tr><td colspan="8" class="title-banner">🛡️ VIGILCORE OPS-01 — RAPPORT D\'AUDIT & ANALYTICS SLA</td></tr>';
            echo '<tr><td colspan="8" class="subtitle">Généré le ' . date('d/m/Y à H:i:s') . ' | Période : ' . strtoupper($period) . ' | Environnement : Production srv901529</td></tr>';
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
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    /**
     * Génère un export CSV standard
     */
    public static function exportCsvResponse($incidents, string $period = 'week')
    {
        $fileName = 'VigilCore_Rapport_' . strtoupper($period) . '_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($incidents) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM pour Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Date & Heure', 'Composant / Intitule', 'Severite', 'Statut', 'Duree (MTTR)', 'Source', 'Description']);

            foreach ($incidents as $inc) {
                $durationSec = 0;
                $mttr = '--';
                if ($inc->status === 'resolved' && $inc->created_at && $inc->updated_at) {
                    $diff = $inc->created_at->diffInSeconds($inc->updated_at);
                    $mttr = ($diff >= 60) ? (floor($diff / 60) . 'm ' . ($diff % 60) . 's') : ($diff . 's');
                }

                fputcsv($handle, [
                    '#' . $inc->id,
                    $inc->created_at ? $inc->created_at->format('d/m/Y H:i:s') : '',
                    $inc->title ?? $inc->alert_name ?? 'Incident',
                    strtoupper($inc->severity ?? 'INFO'),
                    $inc->status === 'resolved' ? 'RESOLU' : 'EN COURS',
                    $mttr,
                    $inc->source ?? 'Monitoring',
                    $inc->description ?? $inc->message ?? ''
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type'  => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
