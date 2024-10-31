<?php

namespace App\Http\Controllers;

use App\Api\Stocks;
use Illuminate\Http\Request;

class StockPricesController extends Controller
{
    use Stocks;

    public function query($function)
    {
        switch ($function){
            case 'getLatestStockPrices': return $this->getLatestStockPrices();
            default: return $this->getLatestStockPrices();
        }
    }
}
