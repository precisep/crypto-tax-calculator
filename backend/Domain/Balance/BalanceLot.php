// src/Domain/Balance/BalanceLot.php
namespace App\Domain\Balance;

use Carbon\Carbon;

class BalanceLot
{
    public function __construct(
        public float $amount,
        public float $price,
        public Carbon $date
    ) {}
}