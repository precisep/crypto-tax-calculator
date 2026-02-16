// src/Domain/Tax/CapitalGainCalculator.php
namespace App\Domain\Tax;

use App\Domain\Balance\BalanceLot;
use Carbon\Carbon;

class CapitalGainCalculator
{
    private array $yearlyGains = [];
    private float $feeRate = 0.0025;

    public function calculateSale(
        float $sellable,
        BalanceLot $lot,
        float $salePrice,
        Carbon $saleDate
    ): array {
        $cost = $sellable * $lot->price;
        $proceeds = $sellable * $salePrice;
        $fee = $proceeds * $this->feeRate;
        $netProceeds = $proceeds - $fee;
        $gain = $netProceeds - $cost;

        return [
            'buy_date' => $lot->date->format('Y-m-d'),
            'buy_price' => $lot->price,
            'amount_sold' => $sellable,
            'cost' => $cost,
            'proceeds' => $proceeds,
            'net_proceeds' => $netProceeds,
            'fee' => $fee,
            'gain' => $gain,
            'holding_days' => $lot->date->diffInDays($saleDate)
        ];
    }

    public function addYearlyGain(int $taxYear, float $gain): void
    {
        if (!isset($this->yearlyGains[$taxYear])) {
            $this->yearlyGains[$taxYear] = 0;
        }
        $this->yearlyGains[$taxYear] += $gain;
    }

    public function getYearlyGains(): array
    {
        ksort($this->yearlyGains);
        return $this->yearlyGains;
    }

    public function getTotalGain(): float
    {
        return array_sum($this->yearlyGains);
    }
}