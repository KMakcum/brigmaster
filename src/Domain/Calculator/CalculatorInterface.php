<?php

declare(strict_types=1);

namespace Constructly\Domain\Calculator;

use Constructly\Domain\DTO\EstimateInput;
use Constructly\Domain\DTO\EstimateResult;

interface CalculatorInterface
{
    public function calculate(EstimateInput $input): EstimateResult;
}

