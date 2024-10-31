<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GetLatestStockPricesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_latest_stock_prices_from_database_when_cache_is_empty(): void
    {
        // Ensure cache is empty
        Cache::forget('latest-stock-prices');

        $response = $this->getJson('/api/latest-stock-prices');

        // Assert: Verify that the data was fetched from the database
        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
            ]);

        // Assert: Verify that data is now cached
        $this->assertNotNull(Cache::get('latest-stock-prices'));
    }
}
