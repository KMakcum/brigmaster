<?php

declare(strict_types=1);

namespace Constructly\Domain\Strategy;

use Constructly\Domain\DTO\EstimateInput;
use Constructly\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class TileNormativeStrategy implements CalculationStrategyInterface
{
    private const CENTIMETERS_IN_METER = 100.0;

    public function calculate(EstimateInput $input): EstimateResult
    {
        if ($input->area === null || $input->area <= 0) {
            throw new InvalidArgumentException('Field "area" must be greater than 0 for tile.');
        }

        if ($input->tileLengthCm === null || $input->tileLengthCm <= 0) {
            throw new InvalidArgumentException('Field "tileLengthCm" must be greater than 0.');
        }

        if ($input->tileWidthCm === null || $input->tileWidthCm <= 0) {
            throw new InvalidArgumentException('Field "tileWidthCm" must be greater than 0.');
        }

        $calculatedVolume = $input->area;
        $tileAreaM2 = ($input->tileLengthCm / self::CENTIMETERS_IN_METER) * ($input->tileWidthCm / self::CENTIMETERS_IN_METER);
        $calculatedMaterialAmount = $input->area / $tileAreaM2;

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $calculatedVolume,
            calculatedMaterialAmount: $calculatedMaterialAmount
        );
    }
}
