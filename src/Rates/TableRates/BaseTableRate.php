<?php
declare(strict_types=1);

namespace Lendable\Interview\Rates\TableRates;

use Lendable\Interview\Exceptions\LoanAmountException;
use Lendable\Interview\Rates\RateInterface;
use Lendable\Interview\Utils\NumberHelper;

abstract class BaseTableRate implements RateInterface
{
    public function __construct()
    {
        // Check if the constant is defined in the late static binding context (the child class)
        if (!defined('static::BREAKPOINTS_TABLE')) {
            throw new \Exception('Constant BREAKPOINTS_TABLE is not defined on subclass ' . get_class($this));
        }
    }

    /**
     * Calculate Fee
     *
     * @throws LoanAmountException
     */
    public function calculateFee(int $amount): int
    {
        // Check if the loan amount is valid
        $this->validLoadAmount($amount);

        // If the amount is exactly the band
        if (array_key_exists($amount, static::BREAKPOINTS_TABLE)) {
            return static::BREAKPOINTS_TABLE[$amount];
        }

        // If the loan amount is between the bands
        $fee = $this->linearInterpolationCalculator($amount, $this->getLower($amount), $this->getUpper($amount));

        // Check if the fee can be divided by 5. If not, round up to the closer value that can be divided by 5
        return (int)ceil($fee / 500) * 500;
    }

    /**
     * Check if load amount within the range
     *
     * @param int $amount
     * @return void
     * @throws LoanAmountException
     */
    public function validLoadAmount(int $amount): void
    {
        $feeBands = array_keys(static::BREAKPOINTS_TABLE);
        $min = min($feeBands);
        $max = max($feeBands);

        if ($amount < $min || $amount > $max) {
            throw new LoanAmountException('The minimum amount for a loan is ' . NumberHelper::convertToOutputAmount($min) . ', and the maximum is ' . NumberHelper::convertToOutputAmount($max) . '.');
        }
    }

    /**
     * Calculator Linear Interpolation Value
     *
     * Linear interpolation equation: y = y1 + (x - x1) * ((y2 - y1) / (x2 - x1))
     *
     * @param int $amount
     * @param array $lower
     * @param array $upper
     * @return int
     */
    private function linearInterpolationCalculator(int $amount, array $lower, array $upper): int
    {
        // Works out the ratio of the fee
        $ratio = ($upper[1] - $lower[1]) / ($upper[0] - $lower[0]);

        // Works out the fee between two band
        $fee = ($amount - $lower[0]) * $ratio;

        return (int)($lower[1] + $fee);
    }

    /**
     * Get the lower end of the band
     *
     * @param int $amount
     * @return array
     */
    private function getLower(int $amount): array
    {
        $revertFee = array_reverse(static::BREAKPOINTS_TABLE, true);
        foreach ($revertFee as $loanAmountBand => $fee) {
            if ($amount > $loanAmountBand) {
                return [$loanAmountBand, $fee];
            }
        }
    }

    /**
     * Get the higher end of the band
     *
     * @param int $amount
     * @return array
     */
    private function getUpper(int $amount): array
    {
        foreach (static::BREAKPOINTS_TABLE as $loanAmountBand => $fee) {
            if ($loanAmountBand > $amount) {
                return [$loanAmountBand, $fee];
            }
        }
    }
}