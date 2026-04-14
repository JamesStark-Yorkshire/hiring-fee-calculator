<?php
declare(strict_types=1);

namespace Lendable\Interview\Utils;

class NumberHelper
{
    /**
     * Convert to display format
     *
     * @param float $amount
     * @return string
     */
    public static function convertToOutputAmount(float $amount): string
    {
        return number_format($amount / 100, 2);
    }

    /**
     * Sanitised the input amount and convert it to Smallest Currency Unit (Pence)
     *
     * @param string $amountString
     * @return int
     */
    public static function sanitisedInputAmount(string $amountString): int
    {
        // Remove all non-numeric value
        $amountString = (float)preg_replace("/[^0-9.]/", "", $amountString);

        // Format amount to 2 decimal place
        $amount = round($amountString, 2);

        return (int)($amount * 100);
    }
}