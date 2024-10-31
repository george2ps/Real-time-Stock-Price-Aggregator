<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/{function}', [\App\Http\Controllers\StockPricesController::class, 'query'])->name('api.query');
