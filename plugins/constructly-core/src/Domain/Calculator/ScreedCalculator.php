<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;

final class ScreedCalculator implements CalculatorInterface
{
    public function __construct(
        private readonly RectangularConcreteLayerCalculator $calculator = new RectangularConcreteLayerCalculator('screed')
    ) {
    }

    public function calculate(EstimateInput $input): EstimateResult
    {
        return $this->calculator->calculate($input);
    }
}
