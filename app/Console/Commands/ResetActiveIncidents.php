<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Incident;
use Illuminate\Support\Facades\Cache;

class ResetActiveIncidents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vigilcore:reset-active-incidents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clôture et résout tous les incidents actifs pour remettre les 20 services à 100% opérationnels (20/20 OK)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $activeCount = Incident::where('status', '!=', 'resolved')->count();

        if ($activeCount === 0) {
            $this->info("✅ Aucun incident actif. Tous les 20 services sont déjà 100% opérationnels !");
            return Command::SUCCESS;
        }

        // Marque tous les incidents actifs comme résolus
        Incident::where('status', '!=', 'resolved')->update([
            'status'      => 'resolved',
            'is_resolved' => true,
            'resolved_at' => now(),
            'updated_at'  => now(),
        ]);

        // Purge tous les caches de métriques
        Cache::forget('vigilcore_dashboard_counts');
        Cache::forget('vigilcore_active_counts');
        Cache::forget('vigilcore_reports_kpis_today');
        Cache::forget('vigilcore_reports_kpis_week');
        Cache::forget('vigilcore_reports_kpis_month');
        Cache::forget('vigilcore_reports_kpis_all');
        Cache::forget('statuspage_components_cache');

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("🧹 {$activeCount} incident(s) actif(s) clôturé(s) et résolu(s) avec succès !");
        $this->info("🟢 Votre Dashboard VigilCore est remis à 20/20 Services Opérationnels (0 incident actif).");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return Command::SUCCESS;
    }
}
