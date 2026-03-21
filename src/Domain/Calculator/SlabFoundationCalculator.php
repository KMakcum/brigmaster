<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class SlabFoundationCalculator implements CalculatorInterface
{
    private const DEFAULT_REBAR_DIAMETER_MM = 12.0;
    private const DEFAULT_REBAR_STEP_MM = 200.0;
    private const DEFAULT_REBAR_LAYERS = 2;
    private const DEFAULT_REBAR_RESERVE_PERCENT = 10.0;
    private const DEFAULT_FORMWORK_HEIGHT_M = 0.30;
    private const DEFAULT_FORMWORK_RESERVE_PERCENT = 10.0;

    /** @var array<int> */
    private const ALLOWED_REBAR_LAYERS = [1, 2];

    public function calculate(EstimateInput $input): EstimateResult
    {
        if ($input->mode !== EstimateInput::MODE_DIMENSIONS && $input->mode !== EstimateInput::MODE_AREA) {
            throw new InvalidArgumentException('Field "mode" must be one of: dimensions, area for slab foundation.');
        }

        if ($input->height === null || $input->height <= 0) {
            throw new InvalidArgumentException('Field "height" must be greater than 0 for slab foundation.');
        }

        [$length, $width, $area] = $this->resolveGeometry($input);
        $volume = $area * $input->height;

        $details = [
            'concrete' => [
                'areaM2' => $area,
                'heightM' => $input->height,
                'volumeM3' => $volume,
            ],
        ];

        $includeReinforcement = $input->includeReinforcement ?? false;
        if ($includeReinforcement) {
            if ($length === null || $width === null) {
                throw new InvalidArgumentException('Fields "length" and "width" are required for reinforcement when mode="area".');
            }

            $details['reinforcement'] = $this->calculateReinforcement($input, $length, $width);
        }

        $includeFormwork = $input->includeFormwork ?? false;
        if ($includeFormwork) {
            if ($length === null || $width === null) {
                throw new InvalidArgumentException('Fields "length" and "width" are required for formwork when mode="area".');
            }

            $details['formwork'] = $this->calculateFormwork($input, $length, $width);
        }

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $volume,
            calculatedMaterialAmount: $volume,
            details: $details
        );
    }

    /**
     * @return array{0: float|null, 1: float|null, 2: float}
     */
    private function resolveGeometry(EstimateInput $input): array
    {
        if ($input->mode === EstimateInput::MODE_DIMENSIONS) {
            if ($input->length === null || $input->length <= 0) {
                throw new InvalidArgumentException('Field "length" must be greater than 0 for slab foundation in dimensions mode.');
            }

            if ($input->width === null || $input->width <= 0) {
                throw new InvalidArgumentException('Field "width" must be greater than 0 for slab foundation in dimensions mode.');
            }

            return [$input->length, $input->width, $input->length * $input->width];
        }

        if ($input->area === null || $input->area <= 0) {
            throw new InvalidArgumentException('Field "area" must be greater than 0 for slab foundation in area mode.');
        }

        $length = ($input->length !== null && $input->length > 0) ? $input->length : null;
        $width = ($input->width !== null && $input->width > 0) ? $input->width : null;

        return [$length, $width, $input->area];
    }

    /**
     * @return array<string, float|int>
     */
    private function calculateReinforcement(EstimateInput $input, float $length, float $width): array
    {
        $diameterMm = $input->rebarDiameterMm ?? self::DEFAULT_REBAR_DIAMETER_MM;
        $stepMm = $input->rebarStepMm ?? self::DEFAULT_REBAR_STEP_MM;
        $layers = $input->rebarLayers ?? self::DEFAULT_REBAR_LAYERS;
        $reservePercent = $input->rebarReservePercent ?? self::DEFAULT_REBAR_RESERVE_PERCENT;

        if ($diameterMm <= 0) {
            throw new InvalidArgumentException('Field "rebarDiameterMm" must be greater than 0.');
        }

        if ($stepMm <= 0) {
            throw new InvalidArgumentException('Field "rebarStepMm" must be greater than 0.');
        }

        if (!in_array($layers, self::ALLOWED_REBAR_LAYERS, true)) {
            throw new InvalidArgumentException('Field "rebarLayers" must be one of: 1, 2.');
        }

        if ($reservePercent <= 0) {
            throw new InvalidArgumentException('Field "rebarReservePercent" must be greater than 0.');
        }

        $stepM = $stepMm / 1000.0;
        $barsAlongLength = floor($width / $stepM) + 1;
        $barsAlongWidth = floor($length / $stepM) + 1;
        $totalLength = ($barsAlongLength * $length + $barsAlongWidth * $width) * $layers;
        $totalLengthWithReserve = $totalLength * (1 + ($reservePercent / 100.0));
        $unitWeightKgPerM = RebarWeightCalculator::resolveUnitWeightKgPerM($diameterMm);
        $massKg = $totalLengthWithReserve * $unitWeightKgPerM;

        return [
            'diameterMm' => $diameterMm,
            'stepMm' => $stepMm,
            'layers' => $layers,
            'reservePercent' => $reservePercent,
            'barsAlongLength' => $barsAlongLength,
            'barsAlongWidth' => $barsAlongWidth,
            'totalLengthM' => $totalLength,
            'totalLengthWithReserveM' => $totalLengthWithReserve,
            'unitWeightKgPerM' => $unitWeightKgPerM,
            'massKg' => $massKg,
        ];
    }

    /**
     * @return array<string, float>
     */
    private function calculateFormwork(EstimateInput $input, float $length, float $width): array
    {
        $formworkHeightM = $input->formworkHeightM ?? self::DEFAULT_FORMWORK_HEIGHT_M;
        $reservePercent = $input->formworkReservePercent ?? self::DEFAULT_FORMWORK_RESERVE_PERCENT;

        if ($formworkHeightM <= 0) {
            throw new InvalidArgumentException('Field "formworkHeightM" must be greater than 0.');
        }

        if ($reservePercent <= 0) {
            throw new InvalidArgumentException('Field "formworkReservePercent" must be greater than 0.');
        }

        $perimeter = 2 * ($length + $width);
        $coefficient = 1 + ($reservePercent / 100.0);

        return [
            'heightM' => $formworkHeightM,
            'reservePercent' => $reservePercent,
            'perimeterM' => $perimeter,
            'areaM2' => $perimeter * $formworkHeightM * $coefficient,
            'linearMeters' => $perimeter * $coefficient,
        ];
    }

}
