<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IncidentScenarioService;
use App\Services\StatuspageService;
use Illuminate\Support\Facades\Cache;

class DispatchPeriodicIncident extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vigilcore:dispatch-periodic-incident 
                            {--service= : Spécifier la clé d\'un service précis (ex: eneo, s3p, mtn_momo)}
                            {--severity= : Forcer la sévérité (CRITICAL, WARNING, INFO)}
                            {--no-n8n : Ne pas envoyer la requête HTTP au webhook n8n}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Déclenche un incident périodique rotatif parmi les 20 services critiques et synchronise n8n / Statuspage / VigilCore';

    /**
     * Execute the console command.
     */
    public function handle(StatuspageService $statuspage): int
    {
        $scenarios = IncidentScenarioService::getScenarios();
        $totalScenarios = count($scenarios);

        if ($totalScenarios === 0) {
            $this->error('Aucun scénario disponible.');
            return Command::FAILURE;
        }

        $specificKey = $this->option('service');
        $customSeverity = $this->option('severity');
        $sendToN8n = !$this->option('no-n8n');

        $selectedScenario = null;

        if ($specificKey) {
            $selectedScenario = IncidentScenarioService::findScenario($specificKey);
            if (!$selectedScenario) {
                $this->error("Service introuvable pour la clé : {$specificKey}");
                $this->line("Clés disponibles : " . implode(', ', array_column($scenarios, 'key')));
                return Command::FAILURE;
            }
        } else {
            // Rotation séquentielle automatique (0 -> 19 -> 0)
            $currentIndex = (int) Cache::get('vigilcore_periodic_scenario_index', 0);
            $selectedScenario = $scenarios[$currentIndex % $totalScenarios];

            // Incrémente le curseur pour le prochain déclenchement
            Cache::put('vigilcore_periodic_scenario_index', ($currentIndex + 1) % $totalScenarios, now()->addDays(30));
        }

        $key = $selectedScenario['key'];
        $name = $selectedScenario['name'];
        $category = $selectedScenario['category_label'] ?? 'Général';
        $severity = strtoupper($customSeverity ?? $selectedScenario['severity'] ?? 'CRITICAL');

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("🛡️  VIGILCORE SRE ENGINE — DÉCLENCHEMENT PÉRIODIQUE DES 20 SERVICES");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("🔹 Service ciblé : <fg=cyan>{$name}</> ({$key})");
        $this->line("🔹 Catégorie     : <fg=yellow>{$category}</>");
        $this->line("🔹 Criticité     : <fg=red>{$severity}</>");
        $this->line("🔹 Horodatage WAT: <fg=green>" . now()->timezone('Africa/Douala')->format('d/m/Y H:i:s') . " (Douala)</>");

        // 1. Déclenchement via le service VigilCore (création incident DB + envoi n8n)
        $incident = IncidentScenarioService::trigger($key, $sendToN8n, $severity);

        if (!$incident) {
            $this->error("Échec lors du déclenchement du scénario.");
            return Command::FAILURE;
        }

        $this->info("✅ Incident VigilCore #{$incident->id} créé avec succès !");

        // 2. Synchronisation optionnelle Atlassian Statuspage
        try {
            $statuspage->createIncident(
                $selectedScenario['alert_title'],
                'investigating',
                $selectedScenario['messages']['investigating'] ?? 'Incident en cours d\'investigation.'
            );
            $this->info("✅ Atlassian Statuspage synchronisée (Investigating).");
        } catch (\Exception $e) {
            $this->warn("⚠️  Statuspage sync ignorée : " . $e->getMessage());
        }

        // 3. Purge des caches de métriques
        Cache::forget('vigilcore_dashboard_counts');
        Cache::forget('vigilcore_active_counts');
        Cache::forget('vigilcore_reports_kpis_today');
        Cache::forget('vigilcore_reports_kpis_week');
        Cache::forget('vigilcore_reports_kpis_month');
        Cache::forget('vigilcore_reports_kpis_all');

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("🚀 Notification n8n / WhatsApp envoyée et Dashboard mis à jour !");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return Command::SUCCESS;
    }
}
