<?php

declare(strict_types=1);

namespace Constructly\Domain\Calculator;

use Constructly\Domain\DTO\EstimateInput;
use Constructly\Domain\DTO\EstimateResult;
use Constructly\Domain\Strategy\CalculationStrategyInterface;

final class BrickCalculator implements CalculatorInterface
{
    public function __construct(
        private readonly CalculationStrategyInterface $strategy
    ) {
    }

    public function calculate(EstimateInput $input): EstimateResult
    {
        return $this->strategy->calculate($input);
    }
}
