<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;

interface CalculatorInterface
{
    public function calculate(EstimateInput $input): EstimateResult;
}

