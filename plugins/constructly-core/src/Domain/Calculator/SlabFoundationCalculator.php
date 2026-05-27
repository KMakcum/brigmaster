<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;

final class SlabFoundationCalculator implements CalculatorInterface
{
    public function __construct(
        private readonly RectangularConcreteLayerCalculator $calculator = new RectangularConcreteLayerCalculator('slab_foundation', true)
    ) {
    }

    public function calculate(EstimateInput $input): EstimateResult
    {
        return $this->calculator->calculate($input);
    }
}
