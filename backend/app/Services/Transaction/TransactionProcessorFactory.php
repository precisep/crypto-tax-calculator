<?php

namespace App\Services\Transaction;

use App\Domain\Transaction\Transaction;

class TransactionProcessorFactory
{
    public function getProcessor(Transaction $transaction): TransactionProcessorInterface
    {
        return match ($transaction->type) {
            'buy' => new BuyProcessor(),
            'sell' => new SellProcessor(),
            'trade' => new TradeProcessor(),
            'transfer' => new TransferProcessor(),
            default => throw new \InvalidArgumentException("Unknown transaction type: {$transaction->type}"),
        };
    }
}