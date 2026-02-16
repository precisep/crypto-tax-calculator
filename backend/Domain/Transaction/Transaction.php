<?php

namespace App\Domain\Transaction;

use Carbon\Carbon;

class Transaction
{
    public function __construct(
        public int $id,
        public string $coin,
        public string $type,
        public float $amount,
        public float $price,
        public Carbon $date,
        public float $fee = 0.0,
        public ?string $feeCoin = null,
        public string $wallet = 'default',
        public ?string $fromCoin = null,
        public ?string $toCoin = null,
        public ?string $fromWallet = null,
        public ?string $toWallet = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'coin' => $this->coin,
            'type' => $this->type,
            'amount' => $this->amount,
            'price' => $this->price,
            'date' => $this->date->format('Y-m-d'),
            'fee' => $this->fee,
            'fee_coin' => $this->feeCoin,
            'wallet' => $this->wallet,
            'from_coin' => $this->fromCoin,
            'to_coin' => $this->toCoin,
            'from_wallet' => $this->fromWallet,
            'to_wallet' => $this->toWallet
        ];
    }
}