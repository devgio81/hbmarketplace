<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class WeclappService
{
    private string $baseUrl;
    private string $apiToken;
    private int $rateLimitPerMinute;
    private string $rateLimitCacheKey = 'weclapp_api_rate_limit';

    public function __construct()
    {
        $this->baseUrl = config('weclapp.base_url');
        $this->apiToken = config('weclapp.api_token');
        $this->rateLimitPerMinute = config('weclapp.rate_limit_per_minute', 30);
    }

    /**
     * Sendet eine Anfrage an die MarketplaceAPI
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array|null
     * @throws RequestException
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): ?array
    {
          // Rate-Limit-Prüfung
        if (!$this->checkRateLimit()) {
            Log::warning('Weclapp API rate limit exceeded');
            throw new \Exception('Weclapp API rate limit exceeded');
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'AuthenticationToken' => $this->apiToken,
            ])->$method("{$this->baseUrl}/{$endpoint}", $data);


            $this->incrementRequestCount();

            $response->throw();
            return $response->json();
        } catch (RequestException $e) {
            Log::error('Weclapp API error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ruft den Vertrag mit der VertragsID ab
     *
     * @param string $entityId
     * @return array|null
     */
    public function getContractById(string $entityId): ?array
    {
        $endpoint = "contract/id/{$entityId}";
        return $this->makeRequest('get', $endpoint);
    }

    /**
     * Aktualisiert den Vertrag mit einer individuellen Beschreibung
     *
     * @param string $entityId
     * @param array $data
     * @return array|null
     */
    public function updateContract(string $entityId, array $data): ?array
    {
        $endpoint = "contract/id/{$entityId}";
        return $this->makeRequest('put', $endpoint, $data);
    }

    /**
     * Überprüft das Rate-Limiting
     *
     * @return bool
     */
    private function checkRateLimit(): bool
    {
        $requestCount = Cache::get($this->rateLimitCacheKey, 0);
        return $requestCount < $this->rateLimitPerMinute;
    }

    /**
     * Erhöht den Anfragezähler für das Rate-Limiting
     */
    private function incrementRequestCount(): void
    {
        $requestCount = Cache::get($this->rateLimitCacheKey, 0);
        Cache::put($this->rateLimitCacheKey, $requestCount + 1, now()->addMinute());
    }

    /**
     * Aktualisiert das Rate-Limiting zur Laufzeit
     *
     * @param int $newLimit
     */
    public function updateRateLimit(int $newLimit): void
    {
        $this->rateLimitPerMinute = $newLimit;
        Cache::put('weclapp_rate_limit_value', $newLimit, now()->addDays(30));
    }

    /**
     * Gibt das aktuelle Rate-Limiting zurück
     *
     * @return int
     */
    public function getCurrentRateLimit(): int
    {
        return Cache::get('weclapp_rate_limit_value', $this->rateLimitPerMinute);
    }
}
