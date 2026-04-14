<?php
declare(strict_types=1);

namespace Lendable\Interview\Rates;

interface RateInterface
{
    /**
     * Calculate Fee
     *
     * @param int $amount
     * @return int
     */
    public function calculateFee(int $amount): int;
}