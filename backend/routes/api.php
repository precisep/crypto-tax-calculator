<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoCalculatorController;


Route::get('/test', function () {
    return response()->json(['message' => 'API test route is working']);
});


Route::post('/calculate-public', [CryptoCalculatorController::class, 'calculate']);