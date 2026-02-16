<?php

namespace App\Services;

use App\Domain\Transaction\TransactionFactory;
use App\Domain\Balance\BalanceManager;
use App\Domain\Tax\CapitalGainCalculator;
use App\Domain\Tax\TaxYearCalculator;
use App\Services\Snapshot\SnapshotService;
use App\Services\Transaction\TransactionProcessorFactory;

class CryptoCalculatorService
{
    private array $transactions = [];
    private array $results = [];
    private float $totalCapitalGain = 0;
    private float $totalTax = 0;
    private array $yearlyCapitalGains = [];

    public function __construct(array $transactions)
    {
        $this->transactions = TransactionFactory::createSorted($transactions);
    }

    public function calculate(): array
    {
        $balanceManager = new BalanceManager();
        $capitalGainCalculator = new CapitalGainCalculator();
        $taxYearCalculator = new TaxYearCalculator();
        $snapshotService = new SnapshotService();
        $processorFactory = new TransactionProcessorFactory();

        foreach ($this->transactions as $transaction) {
            $processor = $processorFactory->getProcessor($transaction);
            
            $result = $processor->process(
                $transaction,
                $balanceManager,
                $capitalGainCalculator,
                $taxYearCalculator
            );

       
            $this->results[] = array_merge(
                ['transaction_id' => $transaction->id],
                $result
            );

          
            if (isset($result['capital_gain'])) {
                $this->totalCapitalGain += $result['capital_gain'];
            }
            if (isset($result['total_tax'])) {
                $this->totalTax += $result['total_tax'];
            }

        
            if ($taxYearCalculator->isTaxYearBoundary($transaction->date)) {
                $snapshotYear = $taxYearCalculator->getTaxYear($transaction->date);
                $snapshotService->takeSnapshot($balanceManager, $transaction->date, $snapshotYear);
            }
        }

   
        $this->yearlyCapitalGains = $capitalGainCalculator->getYearlyGains();

        return $this->formatResults(
            $balanceManager,
            $snapshotService
        );
    }

    private function formatResults(
        BalanceManager $balanceManager,
        SnapshotService $snapshotService
    ): array {
        return [
            'transactions' => array_map(fn($tx) => $tx->toArray(), $this->transactions),
            'results' => $this->results,
            'balances' => $balanceManager->getFormattedBalances(),
            'yearlyCapitalGains' => $this->yearlyCapitalGains,
            'totalCapitalGain' => round($this->totalCapitalGain, 2),
            'totalTax' => round($this->totalTax, 2),
            'totalFees' => $this->calculateTotalFees(),
            'baseCostSnapshots' => $snapshotService->getSnapshots(),
        ];
    }

    private function calculateTotalFees(): float
    {
        $totalFees = 0;

        foreach ($this->results as $result) {
            if (isset($result['fee'])) {
                $totalFees += $result['fee'];
            }
            if (isset($result['total_fee'])) {
                $totalFees += $result['total_fee'];
            }
        }

        return round($totalFees, 2);
    }
}