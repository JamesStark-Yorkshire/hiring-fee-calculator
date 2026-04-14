Fee Calculation Implementation
=========================================

## Packages
- [league/climate](https://github.com/thephpleague/climate): Command Line Output Helper
- [pestphp/pest](https://github.com/pestphp/pest): PHP testing Framework

## Design Decision
- Amount is converted to values represent the smallest currency unit inside the code (Idea from Stripe), to prevent potential rounding issues
- Fee Structure is implemented in a way that can be easily swap and replace
- Strict typing enabled
- Separation between the CLI Main program and the calculator

## Terminologies
- Rate (instead of Term): More generic term for other rate structure that can be added in the future (such as other flat rates)
- Amount: Loan amount

## Development Environment
- PHP 8.5.4
- PhpStorm
- Windows 11 Enterprise 26H1 (ARM64)
- Windows Subsystem for Linux 2 (Ubuntu): Running the submit script

## Feedback from Lendable/Known issues
Thank you again for taking the time to complete the exercise. We appreciate the effort involved in working through the task and submitting a solution for review. It was clear that you aimed to solve the core problem directly and put together an implementation that covered the main flow end to end.

After reviewing the submission against the level required for this role, we will not be progressing further. The main issue was that the solution fell well short of the technical standard we were assessing for. In particular, a large number of internal tests failed, including due to incorrect rounding behaviour, which raised concerns around functional correctness. We also did not see the architectural approach expected for this exercise, with business logic, validation, interpolation, calculation, and rounding all combined into a small number of classes. In addition, the absence of value objects, limited use of composition, weak separation of responsibilities, and lack of code quality tooling reduced confidence in the overall design and engineering discipline of the submission.