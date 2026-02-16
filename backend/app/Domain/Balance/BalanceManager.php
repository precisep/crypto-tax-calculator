<?php

namespace App\Domain\Balance;

class BalanceManager
{
    private array $balances = [];

    public function getBalance(string $coin, string $wallet): Balance
    {
        $key = $coin . '_' . $wallet;
        
        if (!isset($this->balances[$key])) {
            $this->balances[$key] = new Balance($coin, $wallet);
        }
        
        return $this->balances[$key];
    }

    public function getBalances(): array
    {
        return $this->balances;
    }

    public function getFormattedBalances(): array
    {
        $formatted = [];
        
        foreach ($this->balances as $balance) {
            if (!$balance->isEmpty()) {
                $formatted[] = [
                    'coin' => $balance->coin,
                    'wallet' => $balance->wallet,
                    'total_amount' => round($balance->getTotalAmount(), 8),
                    'base_cost' => round($balance->getBaseCost(), 2),
                    'average_price' => round($balance->getAveragePrice(), 2),
                ];
            }
        }
        
        return $formatted;
    }
}