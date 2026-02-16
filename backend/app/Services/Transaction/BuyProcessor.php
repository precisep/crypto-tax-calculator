<?php

namespace App\Services\Transaction;

use App\Domain\Transaction\Transaction;
use App\Domain\Balance\BalanceManager;
use App\Domain\Tax\CapitalGainCalculator;
use App\Domain\Tax\TaxYearCalculator;

class BuyProcessor implements TransactionProcessorInterface
{
    public function process(
        Transaction $transaction,
        BalanceManager $balanceManager,
        CapitalGainCalculator $capitalGainCalculator,
        TaxYearCalculator $taxYearCalculator
    ): array {
        $balance = $balanceManager->getBalance($transaction->coin, $transaction->wallet);
        $balanceKey = "{$transaction->coin}_{$transaction->wallet}";
        
        
        $balance->addLot(
            $transaction->amount,
            $transaction->price,
            $transaction->date
        );

        return [
            'type' => 'BUY',
            'coin' => $transaction->coin,
            'amount' => $transaction->amount,
            'price' => $transaction->price,
            'date' => $transaction->date->toDateString(),
            'wallet' => $transaction->wallet,
            'details' => "Bought {$transaction->amount} {$transaction->coin} at R" . number_format($transaction->price, 2),
            'remaining_balance' => round($balance->getTotalAmount(), 8),
            'balance_key' => $balanceKey,
            'capital_gain' => 0,
            'total_tax' => 0,
            'success' => true
        ];
    }
}