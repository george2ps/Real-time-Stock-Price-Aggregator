<?php

namespace App\Api;

use App\Models\StockPrice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait Stocks
{
    public function getLatestStockPrices()
    {

        $stocks = config('services.alphavantage.stocks');

        // Get Stock data from cache if available
        $getStockData = Cache::get('latest-stock-prices');

        if($getStockData == null) {
            try {
                $getStockData = StockPrice::whereIn('symbol', array_keys($stocks))->get()->toArray();

                // Store data in Cache for 1 minute
                Cache::put('latest-stock-prices', $getStockData, 60);
            }catch (\Exception $e){
                Log::error('Failed to fetch stock data from Database', [ 'message' => $e->getMessage() ]);

                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                    'data' => []
                ]);
            }

        }

       return response()->json([
           'status' => 200,
           'data' => $getStockData
       ]);
    }
}
