<?php

test('The expected output from Readme', function (string $args, string $expected) {
    [$output, $exitCode] = $this->runCalculateFeeInstance($args);
    expect($output)->toBe([$expected]);
    expect($exitCode)->toBe(0);
})->with([
    ["11,500.00 24", "460.00"],
    ["19,250.00 12", "385.00"]
])->setRunClassInSeparateProcess(true);

test('Invalid Input', function () {
    [$output, $exitCode] = $this->runCalculateFeeInstance("11500.00 36");
    expect($output)->toBe(["Error: Invalid Fee Structure."]);
    expect($exitCode)->toBe(101);
});
