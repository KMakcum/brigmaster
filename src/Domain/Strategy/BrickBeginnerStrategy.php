<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Strategy;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;

final class BrickBeginnerStrategy implements CalculationStrategyInterface
{
    public const BEGINNER_PERCENT = 15.0;
    private const PERCENT_BASE = 100;

    public function __construct(
        private readonly BrickNormativeStrategy $baseStrategy = new BrickNormativeStrategy()
    ) {
    }

    public function calculate(EstimateInput $input): EstimateResult
    {
        $baseResult = $this->baseStrategy->calculate($input);
        $coefficient = (self::PERCENT_BASE + self::BEGINNER_PERCENT) / self::PERCENT_BASE;

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $baseResult->calculatedVolume,
            calculatedMaterialAmount: $baseResult->calculatedMaterialAmount * $coefficient
        );
    }
}
