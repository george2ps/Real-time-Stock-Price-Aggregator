<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    protected $table = 'stock_prices';

    protected $fillable = [
        'name',
        'symbol',
        'price',
        'fetched_at'
    ];
}
