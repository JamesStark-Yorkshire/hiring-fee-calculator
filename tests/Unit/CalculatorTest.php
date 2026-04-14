<?php

test('The expected output from Readme', function (int $amount, string $term, int $expectedOutput) {
    $calculator = new \Lendable\Interview\Classes\Calculator();

    $fee = $calculator->calculate($amount, $term);
    expect($fee)->toBe($expectedOutput);
})->with([
    [1150000, "24", 46000],
    [1925000, "12", 38500],
    [1525000, "24", 61000],
    [1425000, "12", 28500]
]);

test('Amounts exactly on fee band', function (int $amount, string $term, int $expectedOutput) {
    $calculator = new \Lendable\Interview\Classes\Calculator();

    $fee = $calculator->calculate($amount, $term);
    expect($fee)->toBe($expectedOutput);
})->with([
    'exact fee from 12 month term loan' =>
        function () {
            $band = array_rand(\Lendable\Interview\Rates\TableRates\TwelveMonth::BREAKPOINTS_TABLE);
            return [$band, "12", \Lendable\Interview\Rates\TableRates\TwelveMonth::BREAKPOINTS_TABLE[$band]];
        },
    'exact fee from 24 month term loan' =>
        function () {
            $band = array_rand(\Lendable\Interview\Rates\TableRates\TwentyFourMonth::BREAKPOINTS_TABLE);
            return [$band, "24", \Lendable\Interview\Rates\TableRates\TwentyFourMonth::BREAKPOINTS_TABLE[$band]];
        },
]);

test('Amount with decimal place', function (int $amount, string $term, int $expectedOutput) {
    $calculator = new \Lendable\Interview\Classes\Calculator();

    $fee = $calculator->calculate($amount, $term);
    expect($fee)->toBe($expectedOutput);
})->with([
    [303540, "24", 12500],
    [1323456, "12", 26500],
    [1455445, "24", 58500],
    [175655, "12", 8500]
]);

test('Invalid loan amount', function (int $amount, string $term) {
    $this->expectExceptionMessage('The minimum amount for a loan is 1,000.00, and the maximum is 20,000.00.');
    $this->expectExceptionCode(100);

    $calculator = new \Lendable\Interview\Classes\Calculator();
    $calculator->calculate($amount, $term);
})->with([
    'below minimum amount' => [1000, "24"],
    'higher than maximum amount' => [12000000, "12"]
]);

test('Invalid Fee Structure', function () {
    $this->expectExceptionMessage('Invalid Fee Structure.');
    $this->expectExceptionCode(101);

    $calculator = new \Lendable\Interview\Classes\Calculator();
    $calculator->calculate(1000, "36");
});