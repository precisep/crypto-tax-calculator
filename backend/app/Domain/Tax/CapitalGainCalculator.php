<?php

namespace App\Domain\Tax;

use App\Domain\Balance\Lot;
use Carbon\Carbon;

class CapitalGainCalculator
{
    private array $yearlyGains = [];
    private array $yearlyTax = [];
    private array $remainingExclusionByYear = [];

    private float $annualExclusion = 40000.0;

    public function calculateSale(
        float $sellable,
        Lot $lot,
        float $salePrice,
        Carbon $saleDate,
        float $fee,
        ?float $feeRate = null
    ): array {
        \Log::info("CapitalGainCalculator::calculateSale called", [
            'sellable' => $sellable,
            'lot_price' => $lot->price,
            'salePrice' => $salePrice,
            'fee_param' => $fee,
            'feeRate_param' => $feeRate,
        ]);
        
        $cost = $sellable * $lot->price;
        
        $proceeds = $sellable * $salePrice;
        
        \Log::info("Before fee calculation", [
            'cost' => $cost,
            'proceeds' => $proceeds,
            'feeRate' => $feeRate,
            'fee' => $fee,
        ]);
        
        // If fee rate is provided, calculate fee from proceeds
        if ($feeRate !== null) {
            $fee = $proceeds * $feeRate;
            \Log::info("Fee recalculated from feeRate: {$proceeds} * {$feeRate} = {$fee}");
        }
    
        $netProceeds = $proceeds - $fee;
        $gain = $netProceeds - $cost;
        
        \Log::info("After fee calculation", [
            'fee' => $fee,
            'netProceeds' => $netProceeds,
            'gain' => $gain,
        ]);
    
        return [
            'buy_date' => $lot->date->format('Y-m-d'),
            'buy_price' => $lot->price,
            'amount_sold' => $sellable,
            'cost' => $cost,
            'proceeds' => $proceeds,
            'fee_rate' => $feeRate,
            'fee' => $fee,
            'net_proceeds' => $netProceeds,
            'gain' => $gain,
            'holding_days' => $lot->date->diffInDays($saleDate),
        ];
    }

    public function applyAnnualExclusion(int $taxYear, float $gain): array
    {
        $this->initializeYear($taxYear);

        $remaining = $this->remainingExclusionByYear[$taxYear];
        $taxableGain = max(0.0, $gain - $remaining);

        $this->remainingExclusionByYear[$taxYear] = max(0.0, $remaining - $gain);

        return [
            'taxable_gain' => $taxableGain,
            'remaining_exclusion' => $this->remainingExclusionByYear[$taxYear],
        ];
    }

    public function addYearlyGain(int $taxYear, float $gain): void
    {
        $this->initializeYear($taxYear);
        $this->yearlyGains[$taxYear] += $gain;
    }

    public function addYearlyTax(int $taxYear, float $taxAmount): void
    {
        $this->initializeYear($taxYear);
        $this->yearlyTax[$taxYear] += $taxAmount;
    }

    public function getYearlyGains(): array
    {
        ksort($this->yearlyGains);
        return $this->yearlyGains;
    }

    public function getYearlyTax(): array
    {
        ksort($this->yearlyTax);
        return $this->yearlyTax;
    }

    public function getTotalGain(): float
    {
        return array_sum($this->yearlyGains);
    }

    public function getTotalTax(): float
    {
        return array_sum($this->yearlyTax);
    }

    public function getRemainingExclusion(int $taxYear): float
    {
        return $this->remainingExclusionByYear[$taxYear] ?? $this->annualExclusion;
    }
    
    public function setAnnualExclusion(float $annualExclusion): void
    {
        if ($annualExclusion < 0) {
            throw new \InvalidArgumentException('Annual exclusion cannot be negative');
        }
        $this->annualExclusion = $annualExclusion;
    }
    
    public function getAnnualExclusion(): float
    {
        return $this->annualExclusion;
    }
    
    public function reset(): void
    {
        $this->yearlyGains = [];
        $this->yearlyTax = [];
        $this->remainingExclusionByYear = [];
    }

    private function initializeYear(int $taxYear): void
    {
        $this->yearlyGains[$taxYear] ??= 0.0;
        $this->yearlyTax[$taxYear] ??= 0.0;
        $this->remainingExclusionByYear[$taxYear] ??= $this->annualExclusion;
    }
}