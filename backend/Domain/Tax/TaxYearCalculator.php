<?php

namespace App\Domain\Tax;

use Carbon\Carbon;

class TaxYearCalculator
{
    public function getTaxYear(Carbon $date): int
    {
        // South African tax year: 1 March to 28/29 February
        if ($date->month < 3) {
            return $date->year;
        }
        return $date->year + 1;
    }

    public function isTaxYearBoundary(Carbon $date): bool
    {
        // Take snapshot on 1st March each year (tax year boundary)
        return $date->month == 3 && $date->day == 1;
    }
}