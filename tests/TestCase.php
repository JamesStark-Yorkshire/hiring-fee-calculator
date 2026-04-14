<?php

namespace Lendable\Interview;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param string $args
     * @return (array|int)[]
     */
    public function runCalculateFeeInstance(string $args)
    {
        $output = [];
        exec("php ./bin/calculate-fee $args", $output[], $output[]);
        return $output;
    }
}
