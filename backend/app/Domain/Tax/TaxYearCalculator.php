<?php

namespace App\Domain\Tax;

use Carbon\Carbon;

class TaxYearCalculator
{
    public function getTaxYear(Carbon $date): int
    {
       
        if ($date->month < 3) {
            return $date->year;
        }
        return $date->year + 1;
    }

    public function isTaxYearBoundary(Carbon $date): bool
    {
        
        return $date->month == 3 && $date->day == 1;
    }
}