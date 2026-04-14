<?php
declare(strict_types=1);

namespace Lendable\Interview\Exceptions;

class InvalidFeeStructure extends \Exception implements Exception
{
    protected $code = 101;

    protected $message = "Invalid Fee Structure.";
}