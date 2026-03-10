<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Strategy;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;

interface CalculationStrategyInterface
{
    public function calculate(EstimateInput $input): EstimateResult;
}

