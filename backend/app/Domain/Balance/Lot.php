<?php

namespace App\Domain\Balance;

use Carbon\Carbon;

class Lot
{
    public float $amount;
    public float $price;
    public Carbon $date;

    public function __construct(float $amount, float $price, Carbon $date)
    {
        $this->amount = $amount;
        $this->price = $price;
        $this->date = $date;
    }

    public function getValue(): float
    {
        return $this->amount * $this->price;
    }
}