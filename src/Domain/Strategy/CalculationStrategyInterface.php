<?php

declare(strict_types=1);

namespace Constructly\Domain\Strategy;

use Constructly\Domain\DTO\EstimateInput;
use Constructly\Domain\DTO\EstimateResult;

interface CalculationStrategyInterface
{
    public function calculate(EstimateInput $input): EstimateResult;
}

