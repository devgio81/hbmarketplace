<?php

namespace Tests\Unit;

use App\Services\WeclappService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeclappServiceTest extends TestCase
{
    protected $weclappService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->weclappService = new WeclappService();
    }

    /** @test */
    public function it_can_update_rate_limit()
    {
        $newLimit = 50;
        $this->weclappService->updateRateLimit($newLimit);

        $this->assertEquals($newLimit, $this->weclappService->getCurrentRateLimit());
        $this->assertEquals($newLimit, Cache::get('weclapp_rate_limit_value'));
    }

    /** @test */
    public function it_can_get_contract_by_id()
    {
        $entityId = '556515';
        $fakeResponse = [
            'id' => '556515',
            'description' => 'Test-Vertrag',
            'createdDate' => '2025-04-13',
        ];

        Http::fake([
            config('weclapp.base_url') . "/contract/id/{$entityId}" => Http::response($fakeResponse, 200),
        ]);

        $result = $this->weclappService->getContractById($entityId);
        $this->assertEquals($fakeResponse, $result);
    }

    /** @test */
    public function it_can_update_contract()
    {
        $entityId = '556515';
        $contractData = [
            'id' => '556515',
            'description' => 'Martin Tomczak',
            'createdDate' => '2025-04-14',
        ];

        Http::fake([
            config('weclapp.base_url') . "/contract/id/{$entityId}" => Http::response($contractData, 200),
        ]);

        $result = $this->weclappService->updateContract($entityId, $contractData);
        $this->assertEquals($contractData, $result);
    }

    /** @test */
    public function it_respects_rate_limit()
    {
        $this->weclappService->updateRateLimit(2);

        Cache::put('weclapp_api_rate_limit', 1, now()->addMinute());

        $entityId = '556515';
        $fakeResponse = ['id' => '556515'];

        Http::fake([
            config('weclapp.base_url') . "/contract/id/{$entityId}" => Http::response($fakeResponse, 200),
        ]);

        // Erste Anfrage sollte funktionieren
        $result = $this->weclappService->getContractById($entityId);
        $this->assertEquals($fakeResponse, $result);

        // Rate-Limit auf maximale Anzahl setzen
        Cache::put('weclapp_api_rate_limit', 2, now()->addMinute());


        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Weclapp API rate limit exceeded');

        $this->weclappService->getContractById($entityId);
    }
}
