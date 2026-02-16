// src/Domain/Balance/Balance.php
namespace App\Domain\Balance;

use Carbon\Carbon;

class Balance
{
    public function __construct(
        public string $coin,
        public string $wallet,
        public array $lots = []
    ) {}

    public function addLot(float $amount, float $price, Carbon $date): void
    {
        $this->lots[] = new BalanceLot($amount, $price, $date);
    }

    public function removeLots(float $amount, \Closure $processor): array
    {
        $remaining = $amount;
        $matchedLots = [];
        
        foreach ($this->lots as $index => $lot) {
            if ($remaining <= 0) break;
            
            $sellable = min($remaining, $lot->amount);
            $remaining -= $sellable;
            $lot->amount -= $sellable;
            
            $matchedLots[] = $processor($sellable, $lot, $index);
        }

        // Remove empty lots
        $this->lots = array_values(array_filter(
            $this->lots,
            fn($lot) => $lot->amount > 0
        ));

        return $matchedLots;
    }

    public function getTotalAmount(): float
    {
        return array_sum(array_map(fn($lot) => $lot->amount, $this->lots));
    }

    public function getBaseCost(): float
    {
        return array_sum(array_map(
            fn($lot) => $lot->amount * $lot->price,
            $this->lots
        ));
    }
}