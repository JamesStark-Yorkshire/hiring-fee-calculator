<?php
declare(strict_types=1);

namespace Lendable\Interview\Rates\TableRates;

use Lendable\Interview\Rates\RateInterface;

class TwelveMonth extends BaseTableRate implements RateInterface
{
    /**
     * Fee Breakpoints for 12 Months Term Loan
     */
    const array BREAKPOINTS_TABLE = [
        100000 => 5000,
        200000 => 9000,
        300000 => 9000,
        400000 => 11500,
        500000 => 10000,
        600000 => 12000,
        700000 => 14000,
        800000 => 16000,
        900000 => 18000,
        1000000 => 20000,
        1100000 => 22000,
        1200000 => 24000,
        1300000 => 26000,
        1400000 => 28000,
        1500000 => 30000,
        1600000 => 32000,
        1700000 => 34000,
        1800000 => 36000,
        1900000 => 38000,
        2000000 => 40000
    ];
}