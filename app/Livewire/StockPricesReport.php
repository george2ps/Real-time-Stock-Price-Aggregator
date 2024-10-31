<?php

namespace App\Livewire;

use App\Models\StockPrice;
use Livewire\Component;

class StockPricesReport extends Component
{
    public function render()
    {
        $stocks = StockPrice::all();

        return view('livewire.stock-prices-report', ['stocks' => $stocks]);
    }
}
