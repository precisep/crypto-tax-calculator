<?php

namespace App\Services\Transaction;

use App\Domain\Transaction\Transaction;
use App\Domain\Balance\BalanceManager;
use App\Domain\Tax\CapitalGainCalculator;
use App\Domain\Tax\TaxYearCalculator;

class TransferProcessor implements TransactionProcessorInterface
{
    public function process(
        Transaction $transaction,
        BalanceManager $balanceManager,
        CapitalGainCalculator $capitalGainCalculator,
        TaxYearCalculator $taxYearCalculator
    ): array {
        $fromBalanceKey = "{$transaction->coin}_{$transaction->fromWallet}";
        $toBalanceKey = "{$transaction->coin}_{$transaction->toWallet}";
        
        $fromBalance = $balanceManager->getBalance($transaction->coin, $transaction->fromWallet);
        $toBalance = $balanceManager->getBalance($transaction->coin, $transaction->toWallet);

        if ($fromBalance->isEmpty() || $fromBalance->getTotalAmount() < $transaction->amount) {
            return [
                'type' => 'TRANSFER',
                'coin' => $transaction->coin,
                'amount' => $transaction->amount,
                'date' => $transaction->date->toDateString(),
                'from_wallet' => $transaction->fromWallet,
                'to_wallet' => $transaction->toWallet,
                'error' => "Insufficient balance in {$transaction->fromWallet} wallet",
                'remaining_balance_from' => round($fromBalance->getTotalAmount(), 8),
                'capital_gain' => 0,
                'total_tax' => 0,
                'success' => false
            ];
        }

 
        $transferredLots = $fromBalance->transferLots(
            $transaction->amount,
            $toBalance
        );

        return [
            'type' => 'TRANSFER',
            'coin' => $transaction->coin,
            'amount' => $transaction->amount,
            'date' => $transaction->date->toDateString(),
            'from_wallet' => $transaction->fromWallet,
            'to_wallet' => $transaction->toWallet,
            'transferred' => array_map(function($lot) {
                return [
                    'amount' => round($lot->amount, 8),
                    'price' => round($lot->price, 2),
                    'date' => $lot->date->toDateString()
                ];
            }, $transferredLots),
            'details' => "Transferred {$transaction->amount} {$transaction->coin} from {$transaction->fromWallet} to {$transaction->toWallet}",
            'remaining_balance_from' => round($fromBalance->getTotalAmount(), 8),
            'remaining_balance_to' => round($toBalance->getTotalAmount(), 8),
            'capital_gain' => 0,
            'total_tax' => 0,
            'success' => true
        ];
    }
}