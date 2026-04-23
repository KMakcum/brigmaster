<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class TileCalculator implements CalculatorInterface
{
    private const MILLIMETERS_IN_METER = 1000.0;
    private const DEFAULT_TILE_THICKNESS_WALL_MM = 8.0;
    private const DEFAULT_TILE_THICKNESS_FLOOR_MM = 9.0;
    private const DEFAULT_JOINT_MM = 2.0;
    private const DEFAULT_DIRECT_RESERVE_PERCENT = 5.0;
    private const DEFAULT_OFFSET_RESERVE_PERCENT = 7.0;
    private const DEFAULT_DIAGONAL_RESERVE_PERCENT = 10.0;
    private const DEFAULT_OFFSET_PERCENT = 50.0;
    private const DEFAULT_ADHESIVE_CONSUMPTION_KG_PER_M2 = 3.5;
    private const DEFAULT_ADHESIVE_LAYER_MM = 3.0;
    private const DEFAULT_ADHESIVE_BAG_WEIGHT_KG = 25.0;
    private const DEFAULT_GROUT_DENSITY_KG_PER_M3 = 1600.0;
    private const DEFAULT_GROUT_PACK_WEIGHT_KG = 2.0;
    private const EDGE_TRIM_WARNING_MM = 50.0;
    private const SCHEME_PREVIEW_COLUMNS = 8;
    private const SCHEME_PREVIEW_ROWS = 6;

    public function calculate(EstimateInput $input): EstimateResult
    {
        $target = $this->normalizeTarget($input->tileTarget);
        $pattern = $this->normalizePattern($input->tileLayingPattern);
        $jointMm = $this->resolvePositiveOrDefault($input->tileJointMm, self::DEFAULT_JOINT_MM);
        $tileLengthMm = $this->requirePositive($input->tileLengthMm, 'tileLengthMm');
        $tileWidthMm = $this->requirePositive($input->tileWidthMm, 'tileWidthMm');
        $tileThicknessMm = $this->resolvePositiveOrDefault(
            $input->tileThicknessMm,
            $target === 'wall' ? self::DEFAULT_TILE_THICKNESS_WALL_MM : self::DEFAULT_TILE_THICKNESS_FLOOR_MM
        );
        $reservePercent = $this->resolvePositiveOrDefault($input->reservePercent, $this->defaultReservePercent($pattern));
        $offsetPercent = $pattern === 'offset'
            ? $this->resolvePositiveOrDefault($input->tileOffsetPercent, self::DEFAULT_OFFSET_PERCENT)
            : 0.0;

        $grossAreaM2 = $this->resolveGrossArea($input, $target);
        $openingsAreaM2 = $this->sumOpeningsArea($input, $target);
        $cutouts = $this->calculateCutouts($input);
        $netAreaM2 = max(0.0, $grossAreaM2 - $openingsAreaM2 - $cutouts['totalAreaM2']);

        $tileAreaM2 = ($tileLengthMm / self::MILLIMETERS_IN_METER) * ($tileWidthMm / self::MILLIMETERS_IN_METER);
        $moduleLengthM = ($tileLengthMm + $jointMm) / self::MILLIMETERS_IN_METER;
        $moduleWidthM = ($tileWidthMm + $jointMm) / self::MILLIMETERS_IN_METER;
        $moduleAreaM2 = $moduleLengthM * $moduleWidthM;

        if ($tileAreaM2 <= 0 || $moduleAreaM2 <= 0) {
            throw new InvalidArgumentException('Tile dimensions must be greater than 0.');
        }

        $baseTileCount = $netAreaM2 / $moduleAreaM2;
        $cutoutWasteTiles = (float) $cutouts['count'];
        $tilesWithoutReserve = $baseTileCount + $cutoutWasteTiles;
        $tilesWithReserve = $tilesWithoutReserve * (1.0 + ($reservePercent / 100.0));
        $tilesToBuy = (float) ceil($tilesWithReserve);

        $layout = $this->calculateLayout(
            $input,
            $target,
            $pattern,
            $tileLengthMm,
            $tileWidthMm,
            $jointMm,
            $offsetPercent
        );

        $adhesive = $this->calculateAdhesive($input, $netAreaM2);
        $grout = $this->calculateGrout($input, $netAreaM2, $tileLengthMm, $tileWidthMm, $tileThicknessMm, $jointMm);
        $costs = $this->calculateCosts($input, $tilesToBuy, $netAreaM2, $adhesive, $grout);

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $netAreaM2,
            calculatedMaterialAmount: $tilesWithReserve,
            details: [
                'geometry' => [
                    'target' => $target,
                    'inputMode' => $input->mode,
                    'grossAreaM2' => $grossAreaM2,
                    'openingsAreaM2' => $openingsAreaM2,
                    'cutoutsAreaM2' => $cutouts['totalAreaM2'],
                    'netAreaM2' => $netAreaM2,
                    'isLayoutApproximate' => !$layout['canRender'],
                ],
                'tile' => [
                    'lengthMm' => $tileLengthMm,
                    'widthMm' => $tileWidthMm,
                    'thicknessMm' => $tileThicknessMm,
                    'jointMm' => $jointMm,
                    'pattern' => $pattern,
                    'offsetPercent' => $offsetPercent,
                    'reservePercent' => $reservePercent,
                    'tileAreaM2' => $tileAreaM2,
                    'moduleAreaM2' => $moduleAreaM2,
                    'countBase' => $baseTileCount,
                    'countCutoutWaste' => $cutoutWasteTiles,
                    'countWithoutReserve' => $tilesWithoutReserve,
                    'countWithReserve' => $tilesWithReserve,
                    'countToBuy' => $tilesToBuy,
                ],
                'openings' => [
                    'count' => $this->countItems($input->tileOpenings),
                ],
                'cutouts' => $cutouts,
                'layout' => $layout,
                'adhesive' => $adhesive,
                'grout' => $grout,
                'costs' => $costs,
            ]
        );
    }

    private function resolveGrossArea(EstimateInput $input, string $target): float
    {
        if ($input->mode === EstimateInput::MODE_AREA) {
            return $this->requirePositive($input->area, 'area');
        }

        $lengthM = $this->requirePositive($input->length, 'length');
        $widthM = $this->requirePositive($input->width, 'width');

        if ($target === 'floor') {
            return $lengthM * $widthM;
        }

        $heightM = $this->requirePositive($input->height, 'height');

        return (2.0 * ($lengthM + $widthM)) * $heightM;
    }

    private function sumOpeningsArea(EstimateInput $input, string $target): float
    {
        if ($target !== 'wall' || $input->tileIncludeOpenings !== true || !is_array($input->tileOpenings)) {
            return 0.0;
        }

        $total = 0.0;
        foreach ($input->tileOpenings as $item) {
            if (!is_array($item)) {
                continue;
            }

            $widthM = isset($item['widthM']) ? (float) $item['widthM'] : 0.0;
            $heightM = isset($item['heightM']) ? (float) $item['heightM'] : 0.0;
            $count = isset($item['count']) ? (int) $item['count'] : 0;
            if ($widthM <= 0 || $heightM <= 0 || $count <= 0) {
                continue;
            }

            $total += $widthM * $heightM * $count;
        }

        return $total;
    }

    /**
     * @return array{totalAreaM2: float, count: int, items: array<int, array<string, mixed>>}
     */
    private function calculateCutouts(EstimateInput $input): array
    {
        if ($input->tileIncludeCutouts !== true || !is_array($input->tileCutouts)) {
            return [
                'totalAreaM2' => 0.0,
                'count' => 0,
                'items' => [],
            ];
        }

        $items = [];
        $totalArea = 0.0;
        $totalCount = 0;

        foreach ($input->tileCutouts as $item) {
            if (!is_array($item)) {
                continue;
            }

            $shape = isset($item['shape']) ? (string) $item['shape'] : 'rect';
            $count = isset($item['count']) ? max(0, (int) $item['count']) : 0;
            if ($count <= 0) {
                continue;
            }

            $area = 0.0;
            if ($shape === 'circle') {
                $diameterMm = isset($item['diameterMm']) ? (float) $item['diameterMm'] : 0.0;
                if ($diameterMm <= 0) {
                    continue;
                }

                $radiusM = ($diameterMm / self::MILLIMETERS_IN_METER) / 2.0;
                $area = M_PI * $radiusM * $radiusM;
            } else {
                $widthMm = isset($item['widthMm']) ? (float) $item['widthMm'] : 0.0;
                $heightMm = isset($item['heightMm']) ? (float) $item['heightMm'] : 0.0;
                if ($widthMm <= 0 || $heightMm <= 0) {
                    continue;
                }

                $area = ($widthMm / self::MILLIMETERS_IN_METER) * ($heightMm / self::MILLIMETERS_IN_METER);
                $shape = 'rect';
            }

            $lineArea = $area * $count;
            $totalArea += $lineArea;
            $totalCount += $count;
            $items[] = [
                'shape' => $shape,
                'count' => $count,
                'areaM2' => $lineArea,
            ];
        }

        return [
            'totalAreaM2' => $totalArea,
            'count' => $totalCount,
            'items' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function calculateLayout(
        EstimateInput $input,
        string $target,
        string $pattern,
        float $tileLengthMm,
        float $tileWidthMm,
        float $jointMm,
        float $offsetPercent
    ): array {
        if ($input->mode !== EstimateInput::MODE_DIMENSIONS) {
            return [
                'canRender' => false,
                'note' => 'Схема раскладки строится только для прямоугольной зоны, когда заданы размеры. В режиме по площади показываем только ориентир по материалам.',
            ];
        }

        $mainLengthM = $this->requirePositive($input->length, 'length');
        $mainWidthM = $target === 'floor'
            ? $this->requirePositive($input->width, 'width')
            : $this->requirePositive($input->height, 'height');

        $tileLengthM = $tileLengthMm / self::MILLIMETERS_IN_METER;
        $tileWidthM = $tileWidthMm / self::MILLIMETERS_IN_METER;
        $jointM = $jointMm / self::MILLIMETERS_IN_METER;

        $tilesAlongLength = $this->countFullTilesForDimension($mainLengthM, $tileLengthM, $jointM);
        $tilesAlongWidth = $this->countFullTilesForDimension($mainWidthM, $tileWidthM, $jointM);
        $remainderLengthM = $this->calculateRemainder($mainLengthM, $tilesAlongLength, $tileLengthM, $jointM);
        $remainderWidthM = $this->calculateRemainder($mainWidthM, $tilesAlongWidth, $tileWidthM, $jointM);
        $edgeTrimLengthMm = ($remainderLengthM * self::MILLIMETERS_IN_METER) / 2.0;
        $edgeTrimWidthMm = ($remainderWidthM * self::MILLIMETERS_IN_METER) / 2.0;
        $hasNarrowCutWarning = ($edgeTrimLengthMm > 0 && $edgeTrimLengthMm < self::EDGE_TRIM_WARNING_MM)
            || ($edgeTrimWidthMm > 0 && $edgeTrimWidthMm < self::EDGE_TRIM_WARNING_MM);

        return [
            'canRender' => true,
            'target' => $target,
            'pattern' => $pattern,
            'offsetPercent' => $offsetPercent,
            'areaLengthM' => $mainLengthM,
            'areaWidthM' => $mainWidthM,
            'tilesAlongLength' => $tilesAlongLength,
            'tilesAlongWidth' => $tilesAlongWidth,
            'rowsCount' => $tilesAlongWidth,
            'columnsCount' => $tilesAlongLength,
            'remainderLengthM' => $remainderLengthM,
            'remainderWidthM' => $remainderWidthM,
            'edgeTrimLengthMm' => $edgeTrimLengthMm,
            'edgeTrimWidthMm' => $edgeTrimWidthMm,
            'hasNarrowCutWarning' => $hasNarrowCutWarning,
            'warningText' => $hasNarrowCutWarning
                ? 'По одному из краёв получается узкая подрезка. На практике лучше сместить старт укладки или разложить плитку симметрично.'
                : null,
            'scheme' => [
                'previewColumns' => min(self::SCHEME_PREVIEW_COLUMNS, max(1, $tilesAlongLength + ($edgeTrimLengthMm > 0 ? 2 : 0))),
                'previewRows' => min(self::SCHEME_PREVIEW_ROWS, max(1, $tilesAlongWidth + ($edgeTrimWidthMm > 0 ? 2 : 0))),
                'hasEdgeTrimLength' => $edgeTrimLengthMm > 0,
                'hasEdgeTrimWidth' => $edgeTrimWidthMm > 0,
                'isApproximate' => $pattern === 'diagonal',
            ],
            'note' => $pattern === 'diagonal'
                ? 'Для диагональной укладки схема остаётся упрощённой: она показывает общий принцип и риск подрезки, но не заменяет раскладку по месту.'
                : 'Схема ориентирована на прямоугольную зону. Проёмы и отверстия учтены в числах, но не раскладываются по координатам.',
        ];
    }

    /**
     * @return array<string, float|int|null|bool>
     */
    private function calculateAdhesive(EstimateInput $input, float $netAreaM2): array
    {
        if ($input->tileIncludeAdhesive !== true) {
            return [
                'enabled' => false,
            ];
        }

        $consumption = $this->resolvePositiveOrDefault($input->tileAdhesiveConsumptionKgPerM2, self::DEFAULT_ADHESIVE_CONSUMPTION_KG_PER_M2);
        $layerMm = $this->resolvePositiveOrDefault($input->tileAdhesiveLayerMm, self::DEFAULT_ADHESIVE_LAYER_MM);
        $bagWeightKg = $this->resolvePositiveOrDefault($input->tileAdhesiveBagWeightKg, self::DEFAULT_ADHESIVE_BAG_WEIGHT_KG);
        $requiredKg = $netAreaM2 * $consumption * ($layerMm / self::DEFAULT_ADHESIVE_LAYER_MM);
        $requiredBags = $bagWeightKg > 0 ? $requiredKg / $bagWeightKg : 0.0;
        $bagsToBuy = (float) ceil($requiredBags);
        $bagPrice = $input->tileAdhesiveBagPrice !== null && $input->tileAdhesiveBagPrice > 0
            ? $input->tileAdhesiveBagPrice
            : null;

        return [
            'enabled' => true,
            'consumptionKgPerM2' => $consumption,
            'layerMm' => $layerMm,
            'bagWeightKg' => $bagWeightKg,
            'requiredKg' => $requiredKg,
            'requiredBags' => $requiredBags,
            'bagsToBuy' => $bagsToBuy,
            'bagPrice' => $bagPrice,
            'costExact' => $bagPrice !== null ? $requiredBags * $bagPrice : null,
            'costRounded' => $bagPrice !== null ? $bagsToBuy * $bagPrice : null,
        ];
    }

    /**
     * @return array<string, float|int|null|bool>
     */
    private function calculateGrout(
        EstimateInput $input,
        float $netAreaM2,
        float $tileLengthMm,
        float $tileWidthMm,
        float $tileThicknessMm,
        float $jointMm
    ): array {
        if ($input->tileIncludeGrout !== true) {
            return [
                'enabled' => false,
            ];
        }

        $density = $this->resolvePositiveOrDefault($input->tileGroutDensityKgPerM3, self::DEFAULT_GROUT_DENSITY_KG_PER_M3);
        $packWeightKg = $this->resolvePositiveOrDefault($input->tileGroutPackWeightKg, self::DEFAULT_GROUT_PACK_WEIGHT_KG);

        $tileLengthM = $tileLengthMm / self::MILLIMETERS_IN_METER;
        $tileWidthM = $tileWidthMm / self::MILLIMETERS_IN_METER;
        $jointM = $jointMm / self::MILLIMETERS_IN_METER;
        $depthM = $tileThicknessMm / self::MILLIMETERS_IN_METER;

        $volumeM3 = (($tileLengthM + $tileWidthM) / ($tileLengthM * $tileWidthM)) * $jointM * $depthM * $netAreaM2;
        $requiredKg = $volumeM3 * $density;
        $requiredPacks = $packWeightKg > 0 ? $requiredKg / $packWeightKg : 0.0;
        $packsToBuy = (float) ceil($requiredPacks);
        $packPrice = $input->tileGroutPackPrice !== null && $input->tileGroutPackPrice > 0
            ? $input->tileGroutPackPrice
            : null;

        return [
            'enabled' => true,
            'densityKgPerM3' => $density,
            'packWeightKg' => $packWeightKg,
            'requiredKg' => $requiredKg,
            'requiredPacks' => $requiredPacks,
            'packsToBuy' => $packsToBuy,
            'packPrice' => $packPrice,
            'costExact' => $packPrice !== null ? $requiredPacks * $packPrice : null,
            'costRounded' => $packPrice !== null ? $packsToBuy * $packPrice : null,
        ];
    }

    /**
     * @param array<string, mixed> $adhesive
     * @param array<string, mixed> $grout
     * @return array<string, float|null>
     */
    private function calculateCosts(
        EstimateInput $input,
        float $tilesToBuy,
        float $netAreaM2,
        array $adhesive,
        array $grout
    ): array {
        $tilePricePerM2 = $input->tilePricePerM2 !== null && $input->tilePricePerM2 > 0
            ? $input->tilePricePerM2
            : null;
        $tileCostExact = $tilePricePerM2 !== null ? $netAreaM2 * $tilePricePerM2 : null;
        $tileCostRounded = $tilePricePerM2 !== null ? $tileCostExact : null;
        $totalExact = 0.0;
        $totalRounded = 0.0;
        $hasAnyCost = false;

        foreach ([$tileCostExact, $adhesive['costExact'] ?? null, $grout['costExact'] ?? null] as $value) {
            if ($value !== null) {
                $totalExact += (float) $value;
                $hasAnyCost = true;
            }
        }

        foreach ([$tileCostRounded, $adhesive['costRounded'] ?? null, $grout['costRounded'] ?? null] as $value) {
            if ($value !== null) {
                $totalRounded += (float) $value;
            }
        }

        return [
            'tilePricePerM2' => $tilePricePerM2,
            'tileCostExact' => $tileCostExact,
            'tileCostRounded' => $tileCostRounded,
            'adhesiveCostExact' => isset($adhesive['costExact']) ? (float) $adhesive['costExact'] : null,
            'adhesiveCostRounded' => isset($adhesive['costRounded']) ? (float) $adhesive['costRounded'] : null,
            'groutCostExact' => isset($grout['costExact']) ? (float) $grout['costExact'] : null,
            'groutCostRounded' => isset($grout['costRounded']) ? (float) $grout['costRounded'] : null,
            'totalExact' => $hasAnyCost ? $totalExact : null,
            'totalRounded' => $hasAnyCost ? $totalRounded : null,
            'tilesToBuy' => $tilesToBuy,
        ];
    }

    private function normalizeTarget(?string $target): string
    {
        return $target === 'wall' ? 'wall' : 'floor';
    }

    private function normalizePattern(?string $pattern): string
    {
        return in_array($pattern, ['direct', 'offset', 'diagonal'], true) ? (string) $pattern : 'direct';
    }

    private function defaultReservePercent(string $pattern): float
    {
        return match ($pattern) {
            'offset' => self::DEFAULT_OFFSET_RESERVE_PERCENT,
            'diagonal' => self::DEFAULT_DIAGONAL_RESERVE_PERCENT,
            default => self::DEFAULT_DIRECT_RESERVE_PERCENT,
        };
    }

    private function requirePositive(?float $value, string $field): float
    {
        if ($value === null || $value <= 0) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be greater than 0.', $field));
        }

        return $value;
    }

    private function resolvePositiveOrDefault(?float $value, float $default): float
    {
        if ($value === null || $value <= 0) {
            return $default;
        }

        return $value;
    }

    private function countFullTilesForDimension(float $dimensionM, float $tileM, float $jointM): int
    {
        return max(1, (int) floor(($dimensionM + $jointM) / ($tileM + $jointM)));
    }

    private function calculateRemainder(float $dimensionM, int $tilesCount, float $tileM, float $jointM): float
    {
        $occupied = ($tilesCount * $tileM) + (max(0, $tilesCount - 1) * $jointM);
        return max(0.0, $dimensionM - $occupied);
    }

    /**
     * @param array<int, mixed>|null $items
     */
    private function countItems(?array $items): int
    {
        if (!is_array($items)) {
            return 0;
        }

        $count = 0;
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $count += isset($item['count']) ? max(0, (int) $item['count']) : 0;
        }

        return $count;
    }
}
