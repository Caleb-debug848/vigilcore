<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatuspageService
{
    protected ?string $apiKey;
    protected ?string $pageId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.statuspage.key');
        $this->pageId = config('services.statuspage.page_id');
        $this->baseUrl = $this->pageId ? "https://api.statuspage.io/v1/pages/{$this->pageId}/" : "https://api.statuspage.io/v1/pages/";
    }

    /**
     * Client HTTP avec authentification OAuth Statuspage
     */
    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => "OAuth {$this->apiKey}",
            'Content-Type'  => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    /**
     * Récupérer tous les composants de opsca.statuspage.io (avec mise en cache 15s)
     */
    public function getComponents(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('statuspage_components_cache', 15, function () {
            try {
                if (empty($this->apiKey) || empty($this->pageId)) {
                    return $this->getDefaultComponents();
                }

                $response = $this->client()->get('components');
                if ($response->successful()) {
                    $data = $response->json();
                    if (is_array($data) && !isset($data['error'])) {
                        return array_values(array_filter($data, fn($item) => is_array($item) && isset($item['name'])));
                    }
                }

                Log::warning('Statuspage Get Components non-OK: ' . $response->body());
                return $this->getDefaultComponents();
            } catch (\Exception $e) {
                Log::error('Statuspage Get Components Error: ' . $e->getMessage());
                return $this->getDefaultComponents();
            }
        });
    }


    /**
     * Composants par défaut si l'API Statuspage n'est pas encore configurée dans .env
     */
    public function getDefaultComponents(): array
    {
        return [
            ['name' => 'Smobilpay Payment Platform and APIs', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'MTN Mobile Money', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'Orange Money', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'Third Party Merchant API (S3P)', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'Camwater Bills', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'Canal +', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'ENEO Bills', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'DSTV', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'SABC Payment', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'Agent and Merchant Payment Portal', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'StarTimes', 'status' => 'operational', 'group' => false, 'showcase' => true],
            ['name' => 'Smobilpay for e-commerce', 'status' => 'operational', 'group' => false, 'showcase' => true],
        ];
    }

    /**
     * Déclarer un incident public
     */
    public function createIncident(string $name, string $status, string $message, ?string $componentId = null, string $componentStatus = 'major_outage'): ?array
    {
        if (empty($this->apiKey) || empty($this->pageId)) {
            return ['id' => 'mock_inc_' . bin2hex(random_bytes(4)), 'name' => $name, 'status' => $status];
        }

        try {
            $payload = [
                'incident' => [
                    'name'   => $name,
                    'status' => $status,
                    'body'   => $message,
                ]
            ];

            if ($componentId) {
                $payload['incident']['components'] = [
                    $componentId => $componentStatus
                ];
                $payload['incident']['component_ids'] = [$componentId];
            }

            $response = $this->client()->post('incidents', $payload);
            \Illuminate\Support\Facades\Cache::forget('statuspage_components_cache');
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
        if (empty($this->apiKey) || empty($this->pageId)) {
            return ['id' => $incidentId, 'status' => 'resolved'];
        }

        try {
            $response = $this->client()->patch("incidents/{$incidentId}", [
                'incident' => [
                    'status' => 'resolved',
                    'body'   => $message,
                ]
            ]);
            \Illuminate\Support\Facades\Cache::forget('statuspage_components_cache');
            return $response->json();

        } catch (\Exception $e) {
            Log::error('Statuspage Resolve Incident Error: ' . $e->getMessage());
            return null;
        }
    }
}
