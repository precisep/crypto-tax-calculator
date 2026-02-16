<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
     
    }
    public function boot(): void
    {
        if (config('app.env') === 'local') {
            \Illuminate\Support\Facades\Route::middleware('api')->group(function () {
                \Illuminate\Support\Facades\Route::match(['options', 'post'], '/api/calculate', function (Request $request) {
                    return response()->json(['message' => 'CORS preflight']);
                })->middleware('cors');
            });
        }
    }
}