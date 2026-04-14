Lendable Interview Test - Fee Calculation
=========================================

## Background

This test is designed to evaluate your problem-solving approach and engineering ability. Design your solution to
demonstrate your knowledge of OOP concepts, SOLID principles, design patterns, domain-driven design and clean and
extensible architecture.

A strong submission will demonstrate a solid grasp of these fundamentals as a set of well-designed classes and will be
documented and executable with elegant tests.

Please note that the main interface of this test is a single console script entrypoint and it is used to review your
solution programmatically. The `bin/calculate-fee` command **SHOULD** only be used for bootstrapping and running your
solution, therefore is expected most of your code will live in the `src` and `tests` folders.

You **MAY** use any libraries that add value to your solution but please **DO NOT** include a whole web framework (AKA
Symfony, Laravel, etc) into the test as it is not needed. We don't expect you to use any infrastructure,
like a database, for this test.

You **MAY** provide us with a development docker (compose) setup if you are comfortable writing your code in a
containerized environment and if you want to showcase your docker abilities; but please note this is for your own use
as a dev environment, so let this not distract you from the main goal of this test.

Please note that your solution will be run with PHP 8.4. You *MAY* use any version you want to develop your solution as
long as it is backward compatible with the aforementioned version, but please note you are encouraged to use the
latest features of the language.

You also **MAY** consider including a README that provides a high-level overview of your approach to the problem and
your solution.

## The Test

The requirement is to build a fee calculator that given a monetary **amount** and a **term** (the contractual duration
of the loan, expressed as a number of months) will produce an appropriate **fee** for a loan based on a fee structure
and a set of rules described below.

The calculator **MUST** be implemented as a CLI tool using the provided `bin/calculate-fee` script. It must take the
mentioned **amount** and **term** as the only arguments and in that order. (Ex: `bin/calculate-fee 20,000.00 24`).

Upon successful execution, the script **MUST** print the resulting **fee** to `stdout` followed by a line feed (`\n`) and
exit with status code `zero`. The fee must be formatted numerically, with two decimal places and with no currency
identifiers or symbols (Ex: `1,223.44`). Supporting different currencies is not required as we only care about monetary
amounts.

Upon failure, the script must print any errors to `stderr` and exit with status code `non-zero`.

In terms of the business logic, implement your solution such that it fulfils the following requirements / premises:

- The fee structure does not follow a formula.
- Values in between the breakpoints should be interpolated linearly between the lower bound and upper bound that they fall between.
- The number of breakpoints, their values, or storage might change.
- The term can be either 12 or 24 (the number of months). You can also assume values will always be within this set.
- The fee should be rounded up such that the sum of the fee and the loan amount is exactly divisible by £5.
- The minimum amount for a loan is £1,000, and the maximum is £20,000.
- You can assume values will always be within this range but **there may be any values up to 2 decimal places**.

Example inputs/outputs:

| Loan Amount (in GBP) | Term (in Months) | Fee (in GBP) |
|----------------------|------------------|--------------|
| 11,500.00            | 24               | 460.00       |
| 19,250.00            | 12               | 385.00       |

# Fee Structure

The fee structure doesn't follow particular algorithm and it is possible that same fee will be applicable for different
amounts.

You can assume the fee structure is in Pounds Stirling (GBP), although this is of little importance for the test.

### Term 12 Breakpoints

| Amount | Fee |
|--------|-----|
| 1,000  | 50  |
| 2,000  | 90  |
| 3,000  | 90  |
| 4,000  | 115 |
| 5,000  | 100 |
| 6,000  | 120 |
| 7,000  | 140 |
| 8,000  | 160 |
| 9,000  | 180 |
| 10,000 | 200 |
| 11,000 | 220 |
| 12,000 | 240 |
| 13,000 | 260 |
| 14,000 | 280 |
| 15,000 | 300 |
| 16,000 | 320 |
| 17,000 | 340 |
| 18,000 | 360 |
| 19,000 | 380 |
| 20,000 | 400 |


### Term 24 Breakpoints

| Amount | Fee |
|--------|-----|
| 1,000  | 70  |
| 2,000  | 100 |
| 3,000  | 120 |
| 4,000  | 160 |
| 5,000  | 200 |
| 6,000  | 240 |
| 7,000  | 280 |
| 8,000  | 320 |
| 9,000  | 360 |
| 10,000 | 400 |
| 11,000 | 440 |
| 12,000 | 480 |
| 13,000 | 520 |
| 14,000 | 560 |
| 15,000 | 600 |
| 16,000 | 640 |
| 17,000 | 680 |
| 18,000 | 720 |
| 19,000 | 760 |
| 20,000 | 800 |

# Submitting Your Solution

You **SHOULD NOT** unnecessarily modify the directory structure of your test. Specially, **DO NOT** move the
`bin/calculate-fee` command nor the `composer.json` from the root directory of your submission, as they are used to
test your submission automatically.

If you need to include other files (like docker setup, fixtures, etc) and you feel they would clutter the root
directory, then you can place those files in a `.dev` folder inside the root directory.

If your solution ends up not being runnable by our automated system due to not following these instructions then you
risk failing your test.

Please **DO NOT** make a public repository for your solution as **we will instantly fail you**. Instead, when you are
done working with your solution, simply run the `bin/submit` script provided. This will pack your solution into a
tarball that you must send to us. You risk failing your test if you send your solution in a different way.
