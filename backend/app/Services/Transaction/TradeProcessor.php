<?php

namespace App\Services\Transaction;

use App\Domain\Transaction\Transaction;
use App\Domain\Balance\BalanceManager;
use App\Domain\Tax\CapitalGainCalculator;
use App\Domain\Tax\TaxYearCalculator;

class TradeProcessor implements TransactionProcessorInterface
{
    private const TAX_PARAMETERS = [
        'short_term_rate' => 0.18,
        'long_term_rate' => 0.10,
        'annual_exclusion' => 40000,
        'long_term_threshold_years' => 3,
    ];

    public function process(
        Transaction $transaction,
        BalanceManager $balanceManager,
        CapitalGainCalculator $capitalGainCalculator,
        TaxYearCalculator $taxYearCalculator
    ): array {
        $fromBalance = $balanceManager->getBalance($transaction->fromCoin, $transaction->wallet);
        $toBalance = $balanceManager->getBalance($transaction->toCoin, $transaction->wallet);
        $fromBalanceKey = "{$transaction->fromCoin}_{$transaction->wallet}";
        $toBalanceKey = "{$transaction->toCoin}_{$transaction->wallet}";

        if ($fromBalance->isEmpty() || $fromBalance->getTotalAmount() < $transaction->amount) {
            return $this->insufficientBalanceResponse($transaction, $fromBalance);
        }

        $taxYear = $taxYearCalculator->getTaxYear($transaction->date);

        $matchedBuys = $fromBalance->removeLots(
            $transaction->amount,
            fn($partialLot, $originalLot) => $this->processLot($partialLot, $originalLot, $transaction, $capitalGainCalculator, $taxYear)
        );

        return $this->buildResponse($transaction, $fromBalance, $toBalance, $matchedBuys, $capitalGainCalculator, $taxYear);
    }

    private function processLot($partialLot, $originalLot, $transaction, $capitalGainCalculator, $taxYear): array
    {
        
        $proceeds = $partialLot * $transaction->price;
        
        
        $feeRate = $transaction->fee;
        
        
        $feeAmount = $proceeds * $feeRate;
        
       
        $result = $capitalGainCalculator->calculateSale(
            $partialLot,
            $originalLot,
            $transaction->price,
            $transaction->date,
            $feeAmount,  
            $feeRate    
        );

        $holdingYears = abs($originalLot->date->floatDiffInYears($transaction->date));
        $isLongTerm = $holdingYears >= self::TAX_PARAMETERS['long_term_threshold_years'];
        $taxRate = $isLongTerm ? self::TAX_PARAMETERS['long_term_rate'] : self::TAX_PARAMETERS['short_term_rate'];

        $exclusionResult = $capitalGainCalculator->applyAnnualExclusion($taxYear, $result['gain']);
        $taxableGain = $exclusionResult['taxable_gain'];
        $taxAmount = $taxableGain * $taxRate;

        $capitalGainCalculator->addYearlyTax($taxYear, $taxAmount);

        return array_merge($result, [
            'holding_years' => round($holdingYears, 2),
            'is_long_term' => $isLongTerm,
            'tax_rate' => $taxRate * 100,
            'taxable_gain' => $taxableGain,
            'remaining_exclusion' => $exclusionResult['remaining_exclusion'],
            'tax_amount' => $taxAmount,
        ]);
    }

    private function buildResponse($transaction, $fromBalance, $toBalance, $matchedBuys, $capitalGainCalculator, $taxYear): array
    {
        $totalGain = $totalTax = $totalProceeds = $totalTaxableGain = $totalFee = 0;

        foreach ($matchedBuys as $buy) {
            $totalGain += $buy['gain'];
            $totalTax += $buy['tax_amount'];
            $totalProceeds += $buy['proceeds'];
            $totalTaxableGain += $buy['taxable_gain'];
            $totalFee += $buy['fee'] ?? 0;
        }

        $receivedAmount = $transaction->price > 0 ? $totalProceeds / $transaction->price : 0;

        $toBalance->addLot($receivedAmount, $transaction->price, $transaction->date);
        $capitalGainCalculator->addYearlyGain($taxYear, $totalGain);

        return [
            'type' => 'TRADE',
            'from_coin' => $transaction->fromCoin,
            'to_coin' => $transaction->toCoin,
            'amount' => $transaction->amount,
            'price' => $transaction->price,
            'date' => $transaction->date->toDateString(),
            'wallet' => $transaction->wallet,
            'capital_gain' => round($totalGain, 2),
            'taxable_gain' => round($totalTaxableGain, 2),
            'received_amount' => round($receivedAmount, 8),
            'received_coin' => $transaction->toCoin,
            'matched_buys' => $matchedBuys,
            'tax_year' => $taxYear,
            'remaining_balance_from' => round($fromBalance->getTotalAmount(), 8),
            'remaining_balance_to' => round($toBalance->getTotalAmount(), 8),
            'remaining_annual_exclusion' => $capitalGainCalculator->getRemainingExclusion($taxYear),
            'total_tax' => round($totalTax, 2),
            'total_fee' => round($totalFee, 2),
            'fee_rate' => $transaction->fee * 100, 
            'fee_coin' => $transaction->feeCoin, 
            'success' => true
        ];
    }

    private function insufficientBalanceResponse($transaction, $fromBalance): array
    {
        return [
            'type' => 'TRADE',
            'from_coin' => $transaction->fromCoin,
            'to_coin' => $transaction->toCoin,
            'amount' => $transaction->amount,
            'price' => $transaction->price,
            'date' => $transaction->date->toDateString(),
            'wallet' => $transaction->wallet,
            'error' => "Insufficient {$transaction->fromCoin} balance for trade",
            'capital_gain' => 0,
            'total_tax' => 0,
            'total_fee' => 0,
            'remaining_balance_from' => round($fromBalance->getTotalAmount(), 8),
            'success' => false
        ];
    }
}