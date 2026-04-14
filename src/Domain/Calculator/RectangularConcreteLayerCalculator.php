<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class RectangularConcreteLayerCalculator
{
    private const DEFAULT_FORMWORK_HEIGHT_M = 0.30;
    private const DEFAULT_FORMWORK_RESERVE_PERCENT = 10.0;

    public function __construct(
        private readonly string $calculatorType,
        private readonly bool $supportsFormwork = false,
        private readonly ConcreteMixtureCalculator $mixtureCalculator = new ConcreteMixtureCalculator()
    ) {
    }

    public function calculate(EstimateInput $input): EstimateResult
    {
        if ($input->mode !== EstimateInput::MODE_DIMENSIONS && $input->mode !== EstimateInput::MODE_AREA) {
            throw new InvalidArgumentException(sprintf('Field "mode" must be one of: dimensions, area for %s.', $this->calculatorType));
        }

        if ($input->height === null || $input->height <= 0) {
            throw new InvalidArgumentException(sprintf('Field "height" must be greater than 0 for %s.', $this->calculatorType));
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

            $details['reinforcement'] = GridRebarCalculator::calculate($input, $length, $width);
        }

        if ($this->supportsFormwork && ($input->includeFormwork ?? false) === true) {
            if ($length === null || $width === null) {
                throw new InvalidArgumentException('Fields "length" and "width" are required for formwork when mode="area".');
            }

            $details['formwork'] = $this->calculateFormwork($input, $length, $width);
        }

        $mixture = $this->mixtureCalculator->calculate($this->calculatorType, $volume, $input->mixture);
        if ($mixture !== null) {
            $details['mixture'] = $mixture;
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
                throw new InvalidArgumentException(sprintf('Field "length" must be greater than 0 for %s in dimensions mode.', $this->calculatorType));
            }

            if ($input->width === null || $input->width <= 0) {
                throw new InvalidArgumentException(sprintf('Field "width" must be greater than 0 for %s in dimensions mode.', $this->calculatorType));
            }

            return [$input->length, $input->width, $input->length * $input->width];
        }

        if ($input->area === null || $input->area <= 0) {
            throw new InvalidArgumentException(sprintf('Field "area" must be greater than 0 for %s in area mode.', $this->calculatorType));
        }

        $length = ($input->length !== null && $input->length > 0) ? $input->length : null;
        $width = ($input->width !== null && $input->width > 0) ? $input->width : null;

        return [$length, $width, $input->area];
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
