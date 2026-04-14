<?php
declare(strict_types=1);

namespace Lendable\Interview\Classes;

use Lendable\Interview\Exceptions\InvalidFeeStructure;
use Lendable\Interview\Rates\RateInterface;
use Lendable\Interview\Rates\TableRates\TwelveMonth;
use Lendable\Interview\Rates\TableRates\TwentyFourMonth;

class Calculator
{
    /**
     * Calculate Fee
     *
     * @param int $amount Amount in the smallest currency unit
     * @param string $rateName Name of the loan rate
     * @return int
     * @throws \Exception
     */
    public function calculate(int $amount, string $rateName): int
    {
        $fee = $this->fetchRate($rateName);

        return $fee->calculateFee($amount);
    }

    /**
     * Fetch Loan Rate
     *
     * @param string $rateName Name of the loan rate
     * @return RateInterface
     * @throws InvalidFeeStructure
     */
    private function fetchRate(string $rateName): RateInterface
    {
        switch ($rateName) {
            case "12":
                return new TwelveMonth();
            case "24":
                return new TwentyFourMonth();
        }

        throw new InvalidFeeStructure();
    }
}