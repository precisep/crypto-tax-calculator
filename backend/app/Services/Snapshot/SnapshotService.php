<?php

namespace App\Services\Snapshot;

use App\Domain\Balance\BalanceManager;
use Carbon\Carbon;

class SnapshotService
{
    private array $snapshots = [];

    public function takeSnapshot(BalanceManager $balanceManager, Carbon $date, int $year): void
    {
        $snapshot = [];

        foreach ($balanceManager->getBalances() as $balance) {
            if (!$balance->isEmpty()) {
                $snapshot[] = [
                    'coin' => $balance->coin,
                    'wallet' => $balance->wallet,
                    'total_amount' => round($balance->getTotalAmount(), 8),
                    'base_cost' => round($balance->getBaseCost(), 2),
                    'average_price' => round($balance->getAveragePrice(), 2),
                    'lots' => array_map(function ($lot) {
                        return [
                            'amount' => round($lot->amount, 8),
                            'price' => round($lot->price, 2),
                            'date' => $lot->date->format('Y-m-d'),
                            'value' => round($lot->getValue(), 2)
                        ];
                    }, $balance->getLots())
                ];
            }
        }

        if (!empty($snapshot)) {
            $this->snapshots[$year] = $snapshot;
        }
    }

    public function getSnapshots(): array
    {
        ksort($this->snapshots);
        return $this->snapshots;
    }

    public function getSnapshotForYear(int $year): ?array
    {
        return $this->snapshots[$year] ?? null;
    }
}