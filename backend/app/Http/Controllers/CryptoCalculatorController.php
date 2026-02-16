<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CryptoCalculatorService;
use App\Http\Requests\CalculateTaxRequest;
use App\Http\Transformers\CryptoCalculationTransformer;

class CryptoCalculatorController extends Controller
{

    public function calculate(CalculateTaxRequest $request)
    {
        try {
       
            
            $calculator = new CryptoCalculatorService($request->transactions);
            $results = $calculator->calculate();
            
        
            $responseData = CryptoCalculationTransformer::transform($results);
            
            return response()->json($responseData);
            
        } catch (\Exception $e) {
            \Log::error('Calculation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Calculation error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}