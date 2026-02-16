<?php

namespace App\Http\Transformers;

class CryptoCalculationTransformer
{
    private const TAX_PARAMETERS = [
        'annual_exclusion' => 40000,
        'short_term_rate' => 0.18,
        'long_term_rate' => 0.10,
        'long_term_threshold_years' => 3,
        'tax_year_start' => '1 March',
        'tax_year_end' => '28/29 February'
    ];

    public static function transform(array $serviceResults): array
    {
       
        $sellTransactions = [];
        if (isset($serviceResults['results'])) {
            $sellTransactions = array_filter($serviceResults['results'], function($r) { 
                return isset($r['type']) && in_array($r['type'], ['SELL', 'TRADE']); 
            });
        }
        
      
        $allCoins = [];
        $allWallets = [];
        if (isset($serviceResults['balances'])) {
            foreach ($serviceResults['balances'] as $balance) {
                if (isset($balance['coin'])) {
                    $allCoins[] = $balance['coin'];
                }
                if (isset($balance['wallet'])) {
                    $allWallets[] = $balance['wallet'];
                }
            }
        }
        
      
        $yearlySummary = [];
        if (isset($serviceResults['yearlyCapitalGains'])) {
            foreach ($serviceResults['yearlyCapitalGains'] as $year => $data) {
              
                if (is_numeric($data)) {
                
                    $yearlySummary[] = [
                        'year' => (int)$year,
                        'total_gains' => (float)$data,
                        'total_tax' => 0, 
                        'transactions' => 0
                    ];
                } elseif (is_array($data)) {
                 
                    $yearlySummary[] = [
                        'year' => (int)$year,
                        'total_gains' => $data['totalGain'] ?? $data['gain'] ?? 0,
                        'total_tax' => $data['totalTax'] ?? $data['tax'] ?? 0,
                        'transactions' => $data['transactionCount'] ?? $data['count'] ?? 0
                    ];
                }
            }
        }

        return [
            'success' => true,
            'data' => [
                'results' => $serviceResults['results'] ?? [],
                'balances' => $serviceResults['balances'] ?? [],
                'yearlySummary' => $yearlySummary,
                'totalCapitalGain' => $serviceResults['totalCapitalGain'] ?? 0,
                'totalTax' => $serviceResults['totalTax'] ?? 0,
                'summary' => [
                    'transactions_processed' => count($serviceResults['transactions'] ?? []),
                    'sell_transactions' => count($sellTransactions),
                    'years_covered' => count($yearlySummary),
                    'unique_coins' => count(array_unique($allCoins)),
                    'unique_wallets' => count(array_unique($allWallets))
                ]
            ],
            'tax_parameters' => [
                'annual_exclusion' => self::TAX_PARAMETERS['annual_exclusion'],
                'short_term_rate' => self::TAX_PARAMETERS['short_term_rate'] * 100,
                'long_term_rate' => self::TAX_PARAMETERS['long_term_rate'] * 100,
                'long_term_threshold_years' => self::TAX_PARAMETERS['long_term_threshold_years'],
                'tax_year_start' => self::TAX_PARAMETERS['tax_year_start'],
                'tax_year_end' => self::TAX_PARAMETERS['tax_year_end']
            ]
        ];
    }
}