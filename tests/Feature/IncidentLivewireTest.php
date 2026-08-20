<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Incident;
use App\Livewire\Dashboard;
use App\Livewire\IncidentReports;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncidentLivewireTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_view_json_opens_modal_and_displays_payload()
    {
        $user = User::factory()->create();

        $incident = Incident::create([
            'title' => 'Test Incident Webhook',
            'description' => 'Test Description',
            'severity' => 'critical',
            'source' => 'Kibana Logs',
            'status' => 'open',
            'raw_payload' => [
                'service' => 's3p',
                'error_code' => 500,
                'details' => 'Simulated test spike',
            ]
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('Test Incident Webhook')
            ->call('viewJson', $incident->id)
            ->assertSet('showJsonModal', true)
            ->assertSet('activeJsonPayload', [
                'service' => 's3p',
                'error_code' => 500,
                'details' => 'Simulated test spike',
            ])
            ->assertSee('Payload JSON')
            ->assertSee('Simulated test spike')
            ->call('closeModal')
            ->assertSet('showJsonModal', false)
            ->assertSet('activeJsonPayload', null);
    }

    public function test_dashboard_manual_refresh_action_refreshes_components_and_counts()
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertStatus(200)
            ->call('refreshData');

        $this->assertNotEmpty($component->get('statusComponents'));
    }

    public function test_reports_page_renders_and_exports_excel_without_errors()
    {
        config(['cache.default' => 'database']);
        $user = User::factory()->create();

        Incident::create([
            'title' => 'Service Outage Test',
            'description' => 'Service Outage Details',
            'severity' => 'critical',
            'source' => 'Zabbix',
            'status' => 'resolved',
        ]);

        // Premier rendu
        Livewire::actingAs($user)
            ->test(IncidentReports::class)
            ->assertStatus(200)
            ->assertSee('Service Outage Test');

        // Deuxième appel vérifiant la désérialisation du cache database + export Excel
        Livewire::actingAs($user)
            ->test(IncidentReports::class)
            ->assertStatus(200)
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    public function test_reports_direct_export_route_downloads_excel()
    {
        $user = User::factory()->create();

        Incident::create([
            'title' => 'S3P Gateway 500 Spike',
            'description' => '14 erreurs 500 en 60s',
            'severity' => 'CRITICAL',
            'source' => 'Kibana Logs',
            'status' => 'resolved',
        ]);

        $response = $this->actingAs($user)->get('/reports/export?period=week&severity=all');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');
    }

    public function test_dashboard_simulation_hub_triggers_scenarios()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('openSimulationHub')
            ->assertSet('showSimulationHub', true)
            ->assertSee('Smobilpay Platform & APIs')
            ->call('setSimulationCategory', 'momo')
            ->assertSet('simulationCategory', 'momo')
            ->assertSee('MTN Mobile Money')
            ->call('triggerScenario', 'eneo', app(\App\Services\StatuspageService::class), 'WARNING')
            ->assertSee('injecté')
            ->call('closeSimulationHub')
            ->assertSet('showSimulationHub', false);

        $this->assertDatabaseHas('incidents', [
            'component' => 'eneo',
            'title'     => 'Perturbation Paiement Factures ENEO',
            'severity'  => 'WARNING',
        ]);
    }

    public function test_language_switch_route_and_translation()
    {
        $user = User::factory()->create();

        // 1. Bascule vers l'anglais
        $response = $this->actingAs($user)->get('/lang/en');
        $response->assertSessionHas('locale', 'en');

        // 2. Vérification que le Dashboard s'affiche en anglais avec session active
        $dashResponse = $this->actingAs($user)->withSession(['locale' => 'en'])->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Active Incidents');
        $dashResponse->assertSee('Refresh');
        $dashResponse->assertSee('Reports');

        // 4. Vérification que la page Reports s'affiche en anglais avec session active
        $reportsResponseEn = $this->actingAs($user)->withSession(['locale' => 'en'])->get('/reports');
        $reportsResponseEn->assertStatus(200);
        $reportsResponseEn->assertSee('Total Incidents');
        $reportsResponseEn->assertSee('Global SLA Availability');
        $reportsResponseEn->assertSee('Mean Time To Resolution (MTTR)');
        $reportsResponseEn->assertSee('Resolution Rate');
        $reportsResponseEn->assertSee('Live Monitoring');
        $reportsResponseEn->assertSee('TOP IMPACTED SERVICES &amp; STABILITY', false);
        $reportsResponseEn->assertSee('BREAKDOWN BY SEVERITY');

        // 5. Vérification que la page Reports s'affiche en français avec session active
        $reportsResponseFr = $this->actingAs($user)->withSession(['locale' => 'fr'])->get('/reports');
        $reportsResponseFr->assertStatus(200);
        $reportsResponseFr->assertSee('Total Incidents');
        $reportsResponseFr->assertSee('Disponibilité SLA Globale');
        $reportsResponseFr->assertSee('Temps Moyen de Résolution (MTTR)');
        $reportsResponseFr->assertSee('Taux de Résolution');
        $reportsResponseFr->assertSee('Monitoring Live');
        $reportsResponseFr->assertSee('TOP SERVICES IMPACTÉS &amp; STABILITÉ', false);
        $reportsResponseFr->assertSee('RÉPARTITION PAR CRITICITÉ');
    }
}


