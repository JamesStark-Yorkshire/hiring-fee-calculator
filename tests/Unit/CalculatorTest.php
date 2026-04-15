<?php

/**
 * Fee Calculator Test Suite
 *
 * All monetary amounts are expressed in pence (integer) to avoid floating-point
 * ambiguity.  £11,500.00 → 1_150_000, £460.00 → 46_000.
 *
 * Rounding rule: the fee is rounded UP so that (amount + fee) is exactly
 * divisible by £5 (500 pence).
 *
 * Interpolation: linear between the two enclosing breakpoints.
 */

// ---------------------------------------------------------------------------
// 1. README EXAMPLES  (the canonical acceptance criteria)
// ---------------------------------------------------------------------------

test('readme example: £11,500 / 24 months → £460 fee', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate(1_150_000, '24'))->toBe(46_000);
});

test('readme example: £19,250 / 12 months → £385 fee', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate(1_925_000, '12'))->toBe(38_500);
});

// ---------------------------------------------------------------------------
// 2. EXACT BREAKPOINT AMOUNTS  (no interpolation required)
// ---------------------------------------------------------------------------

test('exact breakpoints return the defined fee for 12-month term', function (int $amount, int $expectedFee) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate($amount, '12'))->toBe($expectedFee);
})->with(array_map(
    fn(int $amount, int $fee) => [$amount, $fee],
    array_keys(\Lendable\Interview\Rates\TableRates\TwelveMonth::BREAKPOINTS_TABLE),
    array_values(\Lendable\Interview\Rates\TableRates\TwelveMonth::BREAKPOINTS_TABLE),
));

test('exact breakpoints return the defined fee for 24-month term', function (int $amount, int $expectedFee) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate($amount, '24'))->toBe($expectedFee);
})->with(array_map(
    fn(int $amount, int $fee) => [$amount, $fee],
    array_keys(\Lendable\Interview\Rates\TableRates\TwentyFourMonth::BREAKPOINTS_TABLE),
    array_values(\Lendable\Interview\Rates\TableRates\TwentyFourMonth::BREAKPOINTS_TABLE),
));

// ---------------------------------------------------------------------------
// 3. LINEAR INTERPOLATION  (midpoints and off-centre values)
// ---------------------------------------------------------------------------

/**
 * Midpoint between £1,000 (fee 50) and £2,000 (fee 90) for 12 months:
 *   interpolated = 50 + 0.5 * (90 - 50) = 70
 *   £1,500 + £70 = £1,570 → not divisible by £5 (1570/5 = 314 ✓) → 70_000
 */
test('interpolation: midpoint £1,500 / 12 months → £70 fee', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate(150_000, '12'))->toBe(7_000);
});

/**
 * Midpoint between £1,000 (fee 70) and £2,000 (fee 100) for 24 months:
 *   interpolated = 70 + 0.5 * (100 - 70) = 85
 *   £1,500 + £85 = £1,585 → divisible by £5 (1585/5 = 317 ✓) → fee = £85
 */
test('interpolation: midpoint £1,500 / 24 months → £85 fee (rounding applied)', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate(150_000, '24'))->toBe(8_500);
});

/**
 * £2,500 / 12 months — between £2,000 (90) and £3,000 (90):
 *   interpolated = 90 (flat segment)
 *   £2,500 + £90 = £2,590 → next £5 multiple: £2,590 → already divisible → fee = £90
 */
test('interpolation: flat fee segment £2,500 / 12 months → £90 fee', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate(250_000, '12'))->toBe(9_000);
});

/**
 * £3,500 / 12 months — between £3,000 (90) and £4,000 (115):
 *   interpolated = 90 + 0.5 * (115 - 90) = 102.5
 *   £3,500 + £102.50 = £3,602.50 → next £5 multiple: £3,605 → fee = £105
 */
test('interpolation: £3,500 / 12 months → £105 fee', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate(350_000, '12'))->toBe(10_500);
});

/**
 * £5,500 / 24 months — between £5,000 (200) and £6,000 (240):
 *   interpolated = 200 + 0.5 * (240 - 200) = 220
 *   £5,500 + £220 = £5,720 → 5720/5 = 1144 ✓ → fee = £220
 */
test('interpolation: £5,500 / 24 months → £220 fee', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate(550_000, '24'))->toBe(22_000);
});

/**
 * £7,750 / 24 months — between £7,000 (280) and £8,000 (320):
 *   interpolated = 280 + 0.75 * (320 - 280) = 310
 *   £7,750 + £310 = £8,060 → next £5 multiple: £8,060 → 8060/5 = 1612 ✓ → fee = £310
 */
test('interpolation: £7,750 / 24 months → £310 fee', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate(775_000, '24'))->toBe(31_000);
});

// ---------------------------------------------------------------------------
// 4. ROUNDING BEHAVIOUR  (core correctness requirement)
// ---------------------------------------------------------------------------

/**
 * The rounding rule: round UP the fee so that (amount + fee) % 500 === 0.
 * If interpolation already lands on a £5-divisible total, no rounding is needed.
 */
test('rounding: fee is rounded UP, never down', function (int $amount, string $term, int $fee) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    $result = $calculator->calculate($amount, $term);

    // The returned fee must always be >= the raw interpolated fee
    expect($result)->toBeGreaterThanOrEqual($fee);
})->with([
    'above a breakpoint by £1' => [100_001, '12', 5_000],
    'one pence into a band' => [200_001, '12', 9_000],
]);

test('rounding: (amount + fee) is always exactly divisible by £5 (500 pence)', function (int $amount, string $term) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    $fee = $calculator->calculate($amount, $term);
    expect(($amount + $fee) % 500)->toBe(0);
})->with([
    [100_001, '12'],
    [123_456, '12'],
    [175_655, '12'],
    [303_540, '24'],
    [1_150_000, '24'],
    [1_323_456, '12'],
    [1_455_445, '24'],
    [1_525_000, '24'],
    [1_925_000, '12'],
    [1_999_999, '24'],
]);

test('rounding: fee is never negative', function (int $amount, string $term) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate($amount, $term))->toBeGreaterThan(0);
})->with([
    [100_001, '12'],
    [100_001, '24'],
    [200_000_0, '12'],
]);

// ---------------------------------------------------------------------------
// 5. BOUNDARY / EDGE AMOUNTS
// ---------------------------------------------------------------------------

test('minimum valid amount (£1,000.01) is accepted for both terms', function (string $term) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    $fee = $calculator->calculate(100_001, $term);
    expect($fee)->toBeInt()->toBeGreaterThan(0);
    expect((100_001 + $fee) % 500)->toBe(0);
})->with(['12', '24']);

test('maximum valid amount (£20,000.00) is accepted for both terms', function (string $term) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    $fee = $calculator->calculate(2_000_000, $term);
    expect($fee)->toBeInt()->toBeGreaterThan(0);
    expect((2_000_000 + $fee) % 500)->toBe(0);
})->with(['12', '24']);

test('amount one pence below the minimum boundary throws', function () {
    $this->expectExceptionCode(100);
    (new \Lendable\Interview\Classes\Calculator())->calculate(99_999, '12');
});

test('amount one pence above the maximum boundary throws', function () {
    $this->expectExceptionCode(100);
    (new \Lendable\Interview\Classes\Calculator())->calculate(2_000_001, '12');
});

// ---------------------------------------------------------------------------
// 6. AMOUNTS WITH SUB-POUND PRECISION (pence-level inputs)
// ---------------------------------------------------------------------------

test('pence-precision amounts produce correct rounded fees', function (int $amount, string $term, int $expectedFee) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate($amount, $term))->toBe($expectedFee);
})->with([
    [303_540, '24', 12_460],
    [1_323_456, '12', 26_544],
    [1_455_445, '24', 58_555],
    [175_655, '12', 8_345],
]);

// ---------------------------------------------------------------------------
// 7. INVALID INPUTS — LOAN AMOUNT
// ---------------------------------------------------------------------------

test('invalid loan amount throws exception with correct message', function (int $amount, string $term) {
    $this->expectExceptionMessage('The minimum amount for a loan is 1,000.00, and the maximum is 20,000.00.');
    $this->expectExceptionCode(100);
    (new \Lendable\Interview\Classes\Calculator())->calculate($amount, $term);
})->with([
    'far below minimum' => [100, '12'],
    'zero amount' => [0, '12'],
    'negative amount' => [-500, '24'],
    'one pence above maximum' => [2_000_001, '12'],
    'far above maximum' => [9_999_999, '12'],
]);

// ---------------------------------------------------------------------------
// 8. INVALID INPUTS — TERM
// ---------------------------------------------------------------------------

test('unsupported term throws exception with correct message and code', function (string $term) {
    $this->expectExceptionMessage('Invalid Fee Structure.');
    $this->expectExceptionCode(101);
    (new \Lendable\Interview\Classes\Calculator())->calculate(500_000, $term);
})->with([
    'term 6' => ['6'],
    'term 18' => ['18'],
    'term 36' => ['36'],
    'term 0' => ['0'],
    'term -12' => ['-12'],
    'term text' => ['monthly'],
]);

// ---------------------------------------------------------------------------
// 9. OUTPUT CONTRACT (format / type)
// ---------------------------------------------------------------------------

test('fee is always returned as an integer (pence)', function (int $amount, string $term) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    expect($calculator->calculate($amount, $term))->toBeInt();
})->with([
    [100_001, '12'],
    [150_000, '12'],
    [550_000, '24'],
    [1_150_000, '24'],
    [2_000_000, '12'],
]);

// ---------------------------------------------------------------------------
// 10. INTERPOLATION PROPERTY — fee grows monotonically within each band
// ---------------------------------------------------------------------------

/**
 * For both terms, a higher loan amount within the same fee band must never
 * produce a strictly lower fee than a lower amount in the same band.
 * (Fees may plateau but must not decrease within a band unless the fee
 *  structure explicitly defines a lower fee for a higher breakpoint.)
 */
test('fee is non-decreasing across consecutive 12-month breakpoints', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    $table = \Lendable\Interview\Rates\TableRates\TwelveMonth::BREAKPOINTS_TABLE;
    $amounts = array_keys($table);
    sort($amounts);

    for ($i = 0; $i < count($amounts) - 1; $i++) {
        $lower = $calculator->calculate($amounts[$i], '12');
        $upper = $calculator->calculate($amounts[$i + 1], '12');

        // The fee at the upper breakpoint must never be less than at the lower
        expect($upper)->toBeGreaterThanOrEqual($lower,
            "Fee at breakpoint {$amounts[$i+1]} ({$upper}) is less than at {$amounts[$i]} ({$lower})"
        );
    }
});

test('fee is non-decreasing across consecutive 24-month breakpoints', function () {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    $table = \Lendable\Interview\Rates\TableRates\TwentyFourMonth::BREAKPOINTS_TABLE;
    $amounts = array_keys($table);
    sort($amounts);

    for ($i = 0; $i < count($amounts) - 1; $i++) {
        $lower = $calculator->calculate($amounts[$i], '24');
        $upper = $calculator->calculate($amounts[$i + 1], '24');

        expect($upper)->toBeGreaterThanOrEqual($lower,
            "Fee at breakpoint {$amounts[$i+1]} ({$upper}) is less than at {$amounts[$i]} ({$lower})"
        );
    }
});

// ---------------------------------------------------------------------------
// 11. TERM SENSITIVITY — same amount produces different fees per term
// ---------------------------------------------------------------------------

test('same loan amount produces a different fee for 12 vs 24 month terms', function (int $amount) {
    $calculator = new \Lendable\Interview\Classes\Calculator();
    $fee12 = $calculator->calculate($amount, '12');
    $fee24 = $calculator->calculate($amount, '24');

    expect($fee12)->not->toBe($fee24,
        "Expected different fees for term 12 and 24 on amount {$amount}"
    );
})->with([
    [150_000],
    [500_000],
    [1_000_000],
    [1_500_000],
    [2_000_000],
]);