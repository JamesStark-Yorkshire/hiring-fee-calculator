<?php
declare(strict_types=1);

namespace Lendable\Interview\Exceptions;

class LoanAmountException extends \Exception implements Exception
{
    protected $code = 100;

    protected $message = "Invalid Loan Amount.";

}