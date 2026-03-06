<?php

declare(strict_types=1);

namespace Constructly\Domain\Strategy;

use Constructly\Domain\DTO\EstimateInput;
use Constructly\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class DrywallNormativeStrategy implements CalculationStrategyInterface
{
    private const MATERIAL_MULTIPLIER_PER_M2 = 1.05;

    public function calculate(EstimateInput $input): EstimateResult
    {
        if ($input->area === null || $input->area <= 0) {
            throw new InvalidArgumentException('Field "area" must be greater than 0 for drywall.');
        }

        $calculatedVolume = $input->area;
        $calculatedMaterialAmount = $input->area * self::MATERIAL_MULTIPLIER_PER_M2;

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $calculatedVolume,
            calculatedMaterialAmount: $calculatedMaterialAmount
        );
    }
}
