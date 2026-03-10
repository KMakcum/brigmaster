<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use Brigmaster\Domain\Strategy\CalculationStrategyInterface;

final class TileCalculator implements CalculatorInterface
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
