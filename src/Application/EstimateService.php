<?php

declare(strict_types=1);

namespace Brigmaster\Application;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class EstimateService
{
    public const CALCULATOR_BRICK = 'brick';
    public const CALCULATOR_SCREED = 'screed';
    public const CALCULATOR_DRYWALL = 'drywall';
    public const CALCULATOR_TILE = 'tile';
    public const CALCULATOR_SLAB_FOUNDATION = 'slab_foundation';
    public const CALCULATOR_STRIP_FOUNDATION = 'strip_foundation';
    public const CALCULATOR_PILE_FOUNDATION = 'pile_foundation';

    private const ALLOWED_CALCULATORS = [
        self::CALCULATOR_BRICK,
        self::CALCULATOR_SCREED,
        self::CALCULATOR_DRYWALL,
        self::CALCULATOR_TILE,
        self::CALCULATOR_SLAB_FOUNDATION,
        self::CALCULATOR_STRIP_FOUNDATION,
        self::CALCULATOR_PILE_FOUNDATION,
    ];

    public function __construct(
        private readonly CalculatorRegistry $calculatorRegistry = new CalculatorRegistry()
    ) {
    }

    public function calculate(
        string $calculator,
        string $mode,
        ?float $area = null,
        ?float $thickness = null,
        ?string $subType = null,
        ?float $tileLengthCm = null,
        ?float $tileWidthCm = null,
        ?float $length = null,
        ?float $width = null,
        ?float $height = null,
        ?bool $includeReinforcement = null,
        ?bool $includeFormwork = null,
        ?float $rebarDiameterMm = null,
        ?float $rebarStepMm = null,
        ?int $rebarLayers = null,
        ?float $rebarReservePercent = null,
        ?float $formworkHeightM = null,
        ?float $formworkReservePercent = null,
        ?float $totalLengthM = null,
        ?float $widthM = null,
        ?float $heightM = null,
        ?float $houseLengthM = null,
        ?float $houseWidthM = null,
        ?array $segments = null,
        ?int $longitudinalBarsCount = null,
        ?float $longitudinalDiameterMm = null,
        ?float $longitudinalReservePercent = null,
        ?float $transverseDiameterMm = null,
        ?float $transverseStepMm = null,
        ?float $transverseReservePercent = null,
        ?string $pileType = null,
        ?bool $includePiles = null,
        ?int $pilesCount = null,
        ?float $pileShaftDiameterM = null,
        ?float $pileShaftHeightM = null,
        ?bool $includePileBase = null,
        ?float $pileBaseDiameterM = null,
        ?float $pileBaseHeightM = null,
        ?bool $includeGrillage = null,
        ?bool $includePileReinforcement = null,
        ?int $pileReinforcementBarsCount = null,
        ?float $pileReinforcementDiameterMm = null,
        ?float $pileReinforcementReservePercent = null
    ): EstimateResult
    {
        if (!in_array($calculator, self::ALLOWED_CALCULATORS, true)) {
            throw new InvalidArgumentException('Field "calculator" must be one of: brick, screed, drywall, tile, slab_foundation, strip_foundation, pile_foundation.');
        }

        $input = new EstimateInput(
            mode: $mode,
            area: $area,
            thickness: $thickness,
            subType: $subType,
            tileLengthCm: $tileLengthCm,
            tileWidthCm: $tileWidthCm,
            length: $length,
            width: $width,
            height: $height,
            includeReinforcement: $includeReinforcement,
            includeFormwork: $includeFormwork,
            rebarDiameterMm: $rebarDiameterMm,
            rebarStepMm: $rebarStepMm,
            rebarLayers: $rebarLayers,
            rebarReservePercent: $rebarReservePercent,
            formworkHeightM: $formworkHeightM,
            formworkReservePercent: $formworkReservePercent,
            totalLengthM: $totalLengthM,
            widthM: $widthM,
            heightM: $heightM,
            houseLengthM: $houseLengthM,
            houseWidthM: $houseWidthM,
            segments: $segments,
            longitudinalBarsCount: $longitudinalBarsCount,
            longitudinalDiameterMm: $longitudinalDiameterMm,
            longitudinalReservePercent: $longitudinalReservePercent,
            transverseDiameterMm: $transverseDiameterMm,
            transverseStepMm: $transverseStepMm,
            transverseReservePercent: $transverseReservePercent,
            pileType: $pileType,
            includePiles: $includePiles,
            pilesCount: $pilesCount,
            pileShaftDiameterM: $pileShaftDiameterM,
            pileShaftHeightM: $pileShaftHeightM,
            includePileBase: $includePileBase,
            pileBaseDiameterM: $pileBaseDiameterM,
            pileBaseHeightM: $pileBaseHeightM,
            includeGrillage: $includeGrillage,
            includePileReinforcement: $includePileReinforcement,
            pileReinforcementBarsCount: $pileReinforcementBarsCount,
            pileReinforcementDiameterMm: $pileReinforcementDiameterMm,
            pileReinforcementReservePercent: $pileReinforcementReservePercent
        );

        $calculatorService = $this->calculatorRegistry->resolve($calculator, $mode);

        return $calculatorService->calculate($input);
    }
}
