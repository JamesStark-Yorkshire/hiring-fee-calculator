<?php
declare(strict_types=1);

namespace Lendable\Interview\Rates\TableRates;

use Lendable\Interview\Rates\RateInterface;

class TwentyFourMonth extends BaseTableRate implements RateInterface
{
    /**
     * Fee Breakpoints for 24 Months Term Loan
     */
    const array BREAKPOINTS_TABLE = [
        100000 => 7000,
        200000 => 10000,
        300000 => 12000,
        400000 => 16000,
        500000 => 20000,
        600000 => 24000,
        700000 => 28000,
        800000 => 32000,
        900000 => 36000,
        1000000 => 40000,
        1100000 => 44000,
        1200000 => 48000,
        1300000 => 52000,
        1400000 => 56000,
        1500000 => 60000,
        1600000 => 64000,
        1700000 => 68000,
        1800000 => 72000,
        1900000 => 76000,
        2000000 => 80000
    ];
}