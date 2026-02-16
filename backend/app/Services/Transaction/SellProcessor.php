<?php

namespace App\Services\Transaction;

use App\Domain\Transaction\Transaction;
use App\Domain\Balance\BalanceManager;
use App\Domain\Tax\CapitalGainCalculator;
use App\Domain\Tax\TaxYearCalculator;

class SellProcessor implements TransactionProcessorInterface
{
    private const TAX_PARAMETERS = [
        'short_term_rate' => 0.18,
        'long_term_rate' => 0.10,
        'annual_exclusion' => 40000,
    ];

    private array $remainingExclusionByYear = [];

    public function process(
        Transaction $transaction,
        BalanceManager $balanceManager,
        CapitalGainCalculator $capitalGainCalculator,
        TaxYearCalculator $taxYearCalculator
    ): array {
        $balance = $balanceManager->getBalance($transaction->coin, $transaction->wallet);
        $balanceKey = "{$transaction->coin}_{$transaction->wallet}";

        if ($balance->isEmpty() || $balance->getTotalAmount() < $transaction->amount) {
            return $this->insufficientBalanceResponse($transaction, $balance, $balanceKey);
        }

        $taxYear = $taxYearCalculator->getTaxYear($transaction->date);
        $this->initializeExclusion($taxYear);

        $matchedBuys = $balance->removeLots(
            $transaction->amount,
            fn($partialLot, $originalLot) => $this->processLot($partialLot, $originalLot, $transaction, $capitalGainCalculator, $taxYear)
        );

        return $this->buildResponse($transaction, $balance, $matchedBuys, $capitalGainCalculator, $taxYear, $balanceKey);
    }

    private function processLot($partialLot, $originalLot, $transaction, $capitalGainCalculator, $taxYear): array
    {
        // Calculate proceeds for this lot
        $proceeds = $partialLot * $transaction->price;
        
        // The fee from transaction is a percentage rate (e.g., 0.015 for 1.5%)
        $feeRate = $transaction->fee;
        
        // Calculate the actual fee amount in the fee coin currency
        $feeAmount = $proceeds * $feeRate;
        
        // Calculate the sale with fee
        $result = $capitalGainCalculator->calculateSale(
            $partialLot,
            $originalLot,
            $transaction->price,
            $transaction->date,
            $feeAmount,  // The actual fee amount in ZAR (or whatever fee_coin is)
            $feeRate     // The percentage rate (e.g., 0.015)
        );

        // Calculate holding period and tax rate
        $holdingYears = abs($originalLot->date->floatDiffInYears($transaction->date));
        $isLongTerm = $holdingYears >= 3;
        $taxRate = $isLongTerm ? self::TAX_PARAMETERS['long_term_rate'] : self::TAX_PARAMETERS['short_term_rate'];

        // Apply annual exclusion to the gain
        $exclusionResult = $capitalGainCalculator->applyAnnualExclusion($taxYear, $result['gain']);
        $taxableGain = $exclusionResult['taxable_gain'];
        $taxAmount = $taxableGain * $taxRate;

        $capitalGainCalculator->addYearlyTax($taxYear, $taxAmount);

        return array_merge($result, [
            'buy_date' => $originalLot->date->toDateString(),
            'buy_price' => $originalLot->price,
            'amount_sold' => $partialLot,
            'cost' => $partialLot * $originalLot->price,
            'proceeds' => $proceeds,
            'holding_years' => round($holdingYears, 2),
            'is_long_term' => $isLongTerm,
            'tax_rate' => $taxRate * 100,
            'taxable_gain' => $taxableGain,
            'remaining_exclusion' => $this->remainingExclusionByYear[$taxYear],
            'tax_amount' => $taxAmount,
            'original_lot_amount' => $originalLot->amount,
        ]);
    }

    private function buildResponse($transaction, $balance, $matchedBuys, $capitalGainCalculator, $taxYear, $balanceKey): array
    {
        $totalGain = $totalTax = $totalFee = $totalTaxableGain = 0;

        foreach ($matchedBuys as $buy) {
            $totalGain += $buy['gain'];
            $totalTax += $buy['tax_amount'];
            $totalFee += $buy['fee'] ?? 0;
            $totalTaxableGain += $buy['taxable_gain'];
        }

        $capitalGainCalculator->addYearlyGain($taxYear, $totalGain);

        return [
            'type' => 'SELL',
            'coin' => $transaction->coin,
            'amount' => $transaction->amount,
            'price' => $transaction->price,
            'date' => $transaction->date->toDateString(),
            'wallet' => $transaction->wallet,
            'capital_gain' => round($totalGain, 2),
            'taxable_gain' => round($totalTaxableGain, 2),
            'remaining_to_sell' => 0,
            'matched_buys' => $matchedBuys,
            'total_tax' => round($totalTax, 2),
            'tax_year' => $taxYear,
            'balance_key' => $balanceKey,
            'remaining_balance' => round($balance->getTotalAmount(), 8),
            'remaining_annual_exclusion' => $this->remainingExclusionByYear[$taxYear],
            'details' => "Sold {$transaction->amount} {$transaction->coin} at R" . number_format($transaction->price, 2),
            'success' => true,
            'total_fee' => round($totalFee, 2),
            'fee_rate' => $transaction->fee * 100, // Show as percentage
            'fee_coin' => $transaction->feeCoin, // Use feeCoin property
        ];
    }

    private function insufficientBalanceResponse($transaction, $balance, $balanceKey): array
    {
        $remainingAmount = $balance->getTotalAmount();
        return [
            'type' => 'SELL',
            'coin' => $transaction->coin,
            'amount' => $transaction->amount,
            'price' => $transaction->price,
            'date' => $transaction->date->toDateString(),
            'wallet' => $transaction->wallet,
            'error' => "Insufficient {$transaction->coin} balance for sale. Available: {$remainingAmount}",
            'capital_gain' => 0,
            'remaining_to_sell' => $transaction->amount - $remainingAmount,
            'remaining_balance' => round($remainingAmount, 8),
            'total_tax' => 0,
            'balance_key' => $balanceKey,
            'success' => false
        ];
    }

    private function initializeExclusion(int $taxYear): void
    {
        if (!isset($this->remainingExclusionByYear[$taxYear])) {
            $this->remainingExclusionByYear[$taxYear] = self::TAX_PARAMETERS['annual_exclusion'];
        }
    }
}