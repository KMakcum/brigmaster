<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Strategy;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class NormativeStrategy implements CalculationStrategyInterface
{
    public const SUBTYPE_SLAB = 'slab';
    public const SUBTYPE_STRIP = 'strip';

    public function calculate(EstimateInput $input): EstimateResult
    {
        $subType = $input->subType ?? self::SUBTYPE_SLAB;

        if ($subType === self::SUBTYPE_STRIP) {
            if ($input->length === null || $input->length <= 0) {
                throw new InvalidArgumentException('Field "length" must be greater than 0 for concrete strip.');
            }

            if ($input->width === null || $input->width <= 0) {
                throw new InvalidArgumentException('Field "width" must be greater than 0 for concrete strip.');
            }

            if ($input->height === null || $input->height <= 0) {
                throw new InvalidArgumentException('Field "height" must be greater than 0 for concrete strip.');
            }

            $calculatedVolume = $input->length * $input->width * $input->height;

            return new EstimateResult(
                mode: $input->mode,
                calculatedVolume: $calculatedVolume,
                calculatedMaterialAmount: $calculatedVolume
            );
        }

        if ($subType !== self::SUBTYPE_SLAB) {
            throw new InvalidArgumentException('Field "subType" for concrete must be one of: slab, strip.');
        }

        if ($input->area === null || $input->area <= 0) {
            throw new InvalidArgumentException('Field "area" must be greater than 0 for concrete slab.');
        }

        if ($input->thickness === null || $input->thickness <= 0) {
            throw new InvalidArgumentException('Field "thickness" must be greater than 0 for concrete slab.');
        }

        $calculatedVolume = $input->area * $input->thickness;

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $calculatedVolume,
            calculatedMaterialAmount: $calculatedVolume
        );
    }
}

