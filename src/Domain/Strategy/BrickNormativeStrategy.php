<?php

declare(strict_types=1);

namespace Constructly\Domain\Strategy;

use Constructly\Domain\DTO\EstimateInput;
use Constructly\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class BrickNormativeStrategy implements CalculationStrategyInterface
{
    public const SUBTYPE_BRICKS = 'bricks';
    public const SUBTYPE_MORTAR = 'mortar';

    private const MATERIAL_PER_M2 = [
        self::SUBTYPE_BRICKS => 50.0,
        self::SUBTYPE_MORTAR => 18.0,
    ];

    public function calculate(EstimateInput $input): EstimateResult
    {
        if ($input->area === null || $input->area <= 0) {
            throw new InvalidArgumentException('Field "area" must be greater than 0 for brick.');
        }

        if ($input->subType === null || !isset(self::MATERIAL_PER_M2[$input->subType])) {
            throw new InvalidArgumentException('Field "subType" must be one of: bricks, mortar.');
        }

        $calculatedVolume = $input->area;
        $calculatedMaterialAmount = $input->area * self::MATERIAL_PER_M2[$input->subType];

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $calculatedVolume,
            calculatedMaterialAmount: $calculatedMaterialAmount
        );
    }
}
