<?php

namespace App\Console\Commands;

use App\Models\StockPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-stock-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch real-time stock prices from Alpha Vantage API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stocks = config('services.alphavantage.stocks');

        $apiKey = config('services.alphavantage.api_key');

        $baseEndpoint = config('services.alphavantage.endpoints.base');

        foreach ($stocks as $symbol => $stock) {

            sleep(1); // Pause for 1 second between each request to avoid rate limits

            try {
                $response = Http::retry(3, 1000)->get($baseEndpoint, [
                'function' => 'GLOBAL_QUOTE',
                'symbol' => $symbol,
                'apikey' => $apiKey
            ]);

            if ($response->status() === 200) {
                $stockData = $response->json();

                // Save the data to your database here
                if(isset($stockData['Global Quote'])){

                    if(!empty($stockData['Global Quote'])){
                        $price = $stockData['Global Quote']['05. price'] ?? null;

                        $save = [
                            'name' => $stock,
                            'symbol' => $symbol,
                            'price' => $price,
                        ];

                        if ($price) {

                            $exists = StockPrice::where('symbol', $symbol)->first();

                            if($exists !== null){
                                $save['previous_price'] = $exists->price;

                                if ($save['previous_price']) {
                                    $percentageChange = (($price - $save['previous_price']) / $save['previous_price']) * 100;
                                    $save['percentage_change'] = round($percentageChange, 2);
                                }

                                StockPrice::where('symbol', $symbol)->update($save);
                            }else{
                                StockPrice::create($save);
                            }

                            Cache::put($symbol, $save, now()->addMinute());

                            Log::info('Fetched stock data', ['symbol' => $symbol, 'price' => $price]);
                        } else {
                            Log::warning('Price not available for stock', ['symbol' => $symbol]);
                        }
                    }

                }else{
                    Log::warning('Unexpected response structure for stock', ['symbol' => $symbol, 'response' => $stockData]);
                }
            }elseif ($response->status() == 429) {
                // Handle rate limiting
                Log::error('API rate limit reached. Retrying after some delay.', ['symbol' => $symbol]);
                sleep(60); // Pause for 60 seconds to prevent further rate limit issues
            } else {
                Log::error('Failed to fetch stock data', ['symbol' => $symbol, 'status' => $response->status()]);
            }
            }catch (\Exception $e){
                // Handle connection exceptions
                Log::error('Exception occurred while fetching stock data', [
                    'symbol' => $symbol,
                    'message' => $e->getMessage()
                ]);
            }
        }

        return 0;
    }
}
