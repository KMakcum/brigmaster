<?php

declare(strict_types=1);

namespace Constructly\Domain\Strategy;

use Constructly\Domain\DTO\EstimateInput;
use Constructly\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class ScreedNormativeStrategy implements CalculationStrategyInterface
{
    private const MATERIAL_MULTIPLIER = 0.95;

    public function calculate(EstimateInput $input): EstimateResult
    {
        if ($input->area === null || $input->area <= 0) {
            throw new InvalidArgumentException('Field "area" must be greater than 0 for screed.');
        }

        if ($input->thickness === null || $input->thickness <= 0) {
            throw new InvalidArgumentException('Field "thickness" must be greater than 0 for screed.');
        }

        $calculatedVolume = $input->area * $input->thickness;
        $calculatedMaterialAmount = $calculatedVolume * self::MATERIAL_MULTIPLIER;

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $calculatedVolume,
            calculatedMaterialAmount: $calculatedMaterialAmount
        );
    }
}
