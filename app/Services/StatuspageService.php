<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatuspageService
{
    protected string $apiKey;
    protected string $pageId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.statuspage.key');
        $this->pageId = (string) config('services.statuspage.page_id');
        $this->baseUrl = "https://api.statuspage.io/v1/pages/{$this->pageId}/";
    }

    /**
     * Client HTTP avec authentification OAuth Statuspage
     */
    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => "OAuth {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    /**
     * Récupérer tous les composants de opsca.statuspage.io
     */
    public function getComponents(): array
    {
        try {
            $response = $this->client()->get('components');
            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Statuspage Get Components Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Déclarer un incident public
     */
    public function createIncident(string $name, string $status, string $message, ?string $componentId = null, string $componentStatus = 'major_outage'): ?array
    {
        try {
            $payload = [
                'incident' => [
                    'name' => $name,
                    'status' => $status,
                    'body' => $message,
                ]
            ];

            if ($componentId) {
                $payload['incident']['components'] = [
                    $componentId => $componentStatus
                ];
                $payload['incident']['component_ids'] = [$componentId];
            }

            $response = $this->client()->post('incidents', $payload);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Statuspage Create Incident Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Résoudre un incident existant
     */
    public function resolveIncident(string $incidentId, string $message = 'Incident entièrement résolu.'): ?array
    {
        try {
            $response = $this->client()->patch("incidents/{$incidentId}", [
                'incident' => [
                    'status' => 'resolved',
                    'body' => $message,
                ]
            ]);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Statuspage Resolve Incident Error: ' . $e->getMessage());
            return null;
        }
    }
}
