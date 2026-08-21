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

        $component = Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('Test Incident Webhook')
            ->call('viewJson', $incident->id)
            ->assertSet('showJsonModal', true)
            ->assertSee('Payload JSON')
            ->assertSee('Simulated test spike');

        $payload = $component->get('activeJsonPayload');
        $this->assertIsArray($payload);
        $this->assertEquals('s3p', $payload['service']);
        $this->assertEquals(500, $payload['error_code']);
        $this->assertStringContainsString('WAT - Douala', $payload['triggered_at_wat']);

        $component->call('closeModal')
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

    public function test_incident_reports_refresh_data_clears_cache_and_refreshes()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(IncidentReports::class)
            ->call('refreshData')
            ->assertStatus(200);
    }

    public function test_incident_auto_sets_resolved_at_and_computes_audit_accessors()
    {
        $incident = Incident::create([
            'title'       => 'Test ENEO Payment Failure',
            'component'   => 'eneo',
            'severity'    => 'critical',
            'source'      => 'Kibana Logs',
            'status'      => 'open',
            'raw_payload' => [
                'error_code'         => 'ERR_GATEWAY_TIMEOUT_504',
                'root_cause'         => 'ENEO Partner Gateway Unresponsive',
                'resolution_note'    => 'Failover secondary link activated',
                'affected_endpoints' => ['/api/v2/eneo/bill/pay'],
            ],
        ]);

        $this->assertFalse($incident->is_resolved);
        $this->assertNull($incident->resolved_at);
        $this->assertEquals('--', $incident->mttr_formatted);
        $this->assertEquals('ERR_GATEWAY_TIMEOUT_504', $incident->error_code);
        $this->assertEquals('ENEO Partner Gateway Unresponsive', $incident->root_cause);
        $this->assertEquals('/api/v2/eneo/bill/pay', $incident->affected_endpoints_list);

        // Résolution de l'incident
        $incident->update(['status' => 'resolved']);
        $incident->refresh();

        $this->assertTrue($incident->is_resolved);
        $this->assertNotNull($incident->resolved_at);
        $this->assertNotEquals('--', $incident->mttr_formatted);
        $this->assertEquals('Failover secondary link activated', $incident->resolution_note);
        $this->assertStringContainsString('(WAT)', $incident->triggered_at_wat);
        $this->assertStringContainsString('(WAT)', $incident->resolved_at_wat);
    }

    public function test_reports_direct_export_csv_and_xls_support_12_columns()
    {
        $user = User::factory()->create();

        $incident = Incident::create([
            'title'       => 'MTN MoMo Collections Spike',
            'component'   => 'mtn_momo_collections',
            'severity'    => 'CRITICAL',
            'source'      => 'Kibana Logs',
            'status'      => 'resolved',
            'resolved_at' => now(),
            'raw_payload' => [
                'error_code'      => 'ERR_TIMEOUT_SOCKET',
                'root_cause'      => 'Telecom socket saturation',
                'resolution_note' => 'Socket pool cleared and restarted',
            ],
        ]);

        // Export CSV
        $responseCsv = $this->actingAs($user)->get('/reports/export?period=week&severity=all&format=csv');
        $responseCsv->assertStatus(200);
        $responseCsv->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Export XLS
        $responseXls = $this->actingAs($user)->get('/reports/export?period=week&severity=all&format=xls');
        $responseXls->assertStatus(200);
        $responseXls->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');
    }
}


