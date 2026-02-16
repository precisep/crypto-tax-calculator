<?php

namespace App\Services\Transaction;

use App\Domain\Transaction\Transaction;
use App\Domain\Balance\BalanceManager;
use App\Domain\Tax\CapitalGainCalculator;
use App\Domain\Tax\TaxYearCalculator;

interface TransactionProcessorInterface
{
    public function process(
        Transaction $transaction,
        BalanceManager $balanceManager,
        CapitalGainCalculator $capitalGainCalculator,
        TaxYearCalculator $taxYearCalculator
    ): array;
}