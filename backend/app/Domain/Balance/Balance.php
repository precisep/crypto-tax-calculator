<?php

namespace App\Domain\Balance;

use Carbon\Carbon;
use App\Domain\Transaction\Transaction;
use App\Domain\Tax\CapitalGainCalculator;
use App\Domain\Tax\TaxYearCalculator;

class Balance
{
    public string $coin;
    public string $wallet;
    private array $lots = [];

    public function __construct(string $coin, string $wallet)
    {
        $this->coin = $coin;
        $this->wallet = $wallet;
    }

    public function addLot(float $amount, float $price, Carbon $date): void
    {
        $this->lots[] = new Lot($amount, $price, $date);
    }

    public function getTotalAmount(): float
    {
        $total = 0;
        foreach ($this->lots as $lot) {
            $total += $lot->amount;
        }
        return $total;
    }

    public function getBaseCost(): float
    {
        $total = 0;
        foreach ($this->lots as $lot) {
            $total += $lot->amount * $lot->price;
        }
        return $total;
    }

    public function getAveragePrice(): float
    {
        $totalAmount = $this->getTotalAmount();
        if ($totalAmount == 0) {
            return 0;
        }
        return $this->getBaseCost() / $totalAmount;
    }

    public function getLots(): array
    {
        return $this->lots;
    }

    public function isEmpty(): bool
    {
        return empty($this->lots);
    }
    public function transferLots(float $amount, Balance $destinationBalance): array
    {
        $transferredLots = [];
        $remainingAmount = $amount;
        
        usort($this->lots, function ($a, $b) {
            return $a->date->timestamp <=> $b->date->timestamp;
        });
        
        foreach ($this->lots as $index => $lot) {
            if ($remainingAmount <= 0) {
                break;
            }
            
            $lotAmount = $lot->amount;
            
            if ($lotAmount <= $remainingAmount) {
                $destinationBalance->addLot($lot->amount, $lot->price, $lot->date);
                $transferredLots[] = $lot;
                
                $remainingAmount -= $lotAmount;

                unset($this->lots[$index]);
                
            } else {
                $transferredLot = new Lot($remainingAmount, $lot->price, $lot->date);
                $destinationBalance->addLot($remainingAmount, $lot->price, $lot->date);
                $transferredLots[] = $transferredLot;
                $lot->amount = $lotAmount - $remainingAmount;
                $remainingAmount = 0;
            }
        }
        $this->lots = array_values($this->lots);
        if ($remainingAmount > 0) {
            throw new \RuntimeException("Insufficient balance to transfer {$amount} {$this->coin}");
        }
        
        return $transferredLots;
    }

    public function removeLots(float $amount, callable $callback): array
    {
        $matchedBuys = [];
        $remaining = $amount;

        foreach ($this->lots as $index => $lot) {
            if ($remaining <= 0) {
                break;
            }

            $amountToTake = min($lot->amount, $remaining);
            $remaining -= $amountToTake;
            $matchedBuys[] = $callback($amountToTake, $lot);

            if ($amountToTake == $lot->amount) {
                unset($this->lots[$index]);
            } else {
                $lot->amount -= $amountToTake;
            }
        }
        $this->lots = array_values($this->lots);

        return $matchedBuys;
    }
}