<?php
declare(strict_types=1);

namespace Lendable\Interview;

use League\CLImate\CLImate;
use Lendable\Interview\Classes\Calculator;
use Lendable\Interview\Utils\NumberHelper;

class Main
{
    private CLImate $climate;
    private Calculator $calculator;

    public function __construct(CLImate $climate, Calculator $calculator)
    {
        $this->climate = $climate;
        $this->calculator = $calculator;
    }

    /**
     * Main Function
     *
     * @param string $amountString
     * @param string $rateName
     * @return void
     */
    public function main(string $amountString, string $rateName): void
    {
        try {
            // Convert amount to integer value (Smallest Currency Unit)
            $amount = NumberHelper::sanitisedInputAmount($amountString);

            $this->climate->out(NumberHelper::convertToOutputAmount($this->calculator->calculate($amount, $rateName)));
            exit(0);
        } catch (\Exception $exception) {
            $this->climate->error('Error: ' . $exception->getMessage());
            exit($exception->getCode());
        }
    }
}