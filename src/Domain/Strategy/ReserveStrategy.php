<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Strategy;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;

final class ReserveStrategy implements CalculationStrategyInterface
{
    public const RESERVE_PERCENT = 10.0;
    private const PERCENT_BASE = 100;

    public function __construct(
        private readonly NormativeStrategy $baseStrategy = new NormativeStrategy()
    ) {
    }

    public function calculate(EstimateInput $input): EstimateResult
    {
        $baseResult = $this->baseStrategy->calculate($input);
        $coefficient = (self::PERCENT_BASE + self::RESERVE_PERCENT) / self::PERCENT_BASE;

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $baseResult->calculatedVolume,
            calculatedMaterialAmount: $baseResult->calculatedMaterialAmount * $coefficient
        );
    }
}

