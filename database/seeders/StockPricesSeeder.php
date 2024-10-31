<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockPrice;
use Faker\Factory as Faker;

class StockPricesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $stocks = [
            ["symbol" => "AAPL", "price" => "230.1000", "name" => "Apple Inc."],
            ["symbol" => "MSFT", "price" => "432.5300", "name" => "Microsoft Corporation"],
            ["symbol" => "GOOGL", "price" => "174.4600", "name" => "Alphabet Inc. (Google)"],
            ["symbol" => "AMZN", "price" => "192.7300", "name" => "Amazon.com Inc."],
            ["symbol" => "TSLA", "price" => "257.5500", "name" => "Tesla Inc."],
            ["symbol" => "META", "price" => "591.8000", "name" => "Meta Platforms Inc."],
            ["symbol" => "NVDA", "price" => "139.3350", "name" => "NVIDIA Corporation"],
            ["symbol" => "BRK.B", "price" => "454.9600", "name" => "Berkshire Hathaway Inc."],
            ["symbol" => "JNJ", "price" => "160.6100", "name" => "Johnson & Johnson"],
            ["symbol" => "V", "price" => "290.1600", "name" => "Visa Inc."],
        ];

        foreach ($stocks as $k => $stock) {
            // Generate a random previous price between 90% and 110% of the current price
            $previousPrice = $faker->randomFloat(4, $stock['price'] * 0.9, $stock['price'] * 1.1);

            // Calculate percentage change
            $percentageChange = (($stock['price'] - $previousPrice) / $previousPrice) * 100;

            $exists = StockPrice::where('symbol', $stock['symbol'])->exists();

            if(!$exists) {
                StockPrice::create([
                    'symbol' => $stock['symbol'],
                    'name' => $stock['name'],
                    'price' => $stock['price'],
                    'previous_price' => $previousPrice,
                    'percentage_change' => round($percentageChange, 2),
                ]);
            }
        }
    }
}
