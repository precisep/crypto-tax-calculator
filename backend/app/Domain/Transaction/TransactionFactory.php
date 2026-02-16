<?php

namespace App\Domain\Transaction;

use Carbon\Carbon;

class TransactionFactory
{
    public static function createFromArray(array $data, int $index): Transaction
    {
        return new Transaction(
            id: $index + 1,
            coin: strtoupper($data['coin'] ?? 'BTC'),
            type: strtolower($data['type']),
            amount: floatval($data['amount']),
            price: floatval($data['price']),
            date: Carbon::parse($data['date']),
            fee: floatval($data['fee'] ?? 0),
            feeCoin: $data['fee_coin'] ?? null,
            wallet: $data['wallet'] ?? 'default',
            fromCoin: $data['from_coin'] ?? null,
            toCoin: $data['to_coin'] ?? null,
            fromWallet: $data['from_wallet'] ?? null,
            toWallet: $data['to_wallet'] ?? null
        );
    }

    public static function createSorted(array $transactions): array
    {
        usort($transactions, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return array_map(
            [self::class, 'createFromArray'],
            $transactions,
            array_keys($transactions)
        );
    }
}