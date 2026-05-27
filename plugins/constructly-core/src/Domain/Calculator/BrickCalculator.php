<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class BrickCalculator implements CalculatorInterface
{
    private const PURCHASE_UNIT_BAG = 'bag';
    private const PURCHASE_UNIT_TONNE = 'tonne';
    private const DEFAULT_CEMENT_SHARE = 1.0;
    private const DEFAULT_SAND_SHARE = 4.0;
    private const DEFAULT_CEMENT_BAG_WEIGHT_KG = 50.0;
    private const DEFAULT_CEMENT_PURCHASE_UNIT = self::PURCHASE_UNIT_BAG;
    private const DEFAULT_SAND_PURCHASE_UNIT = self::PURCHASE_UNIT_TONNE;
    private const DEFAULT_BRICK_DENSITY_KG_PER_M3 = 1800.0;
    private const CEMENT_DENSITY_KG_PER_M3 = 1300.0;
    private const SAND_DENSITY_KG_PER_M3 = 1600.0;
    private const MORTAR_DENSITY_KG_PER_M3 = 1800.0;
    private const MORTAR_DRY_VOLUME_FACTOR = 1.33;
    private const WATER_CEMENT_RATIO = 0.5;
    private const LINTEL_SUPPORT_LENGTH_M = 0.25;

    private const BRICK_FORMAT_LABELS = [
        'single_nf' => '1 НФ (250×120×65 мм)',
        'one_and_half_nf' => '1.4 НФ (250×120×88 мм)',
        'double_nf' => '2.1 НФ (250×120×140 мм)',
        'euro_nf' => 'Евро (250×85×65 мм)',
        'custom' => 'Свой размер',
    ];

    private const WALL_THICKNESS_LABELS = [
        'half_brick' => 'В 0.5 кирпича',
        'one_brick' => 'В 1 кирпич',
        'one_and_half_bricks' => 'В 1.5 кирпича',
        'two_bricks' => 'В 2 кирпича',
        'two_and_half_bricks' => 'В 2.5 кирпича',
    ];

    public function calculate(EstimateInput $input): EstimateResult
    {
        $mode = $input->mode;
        if (!in_array($mode, [EstimateInput::MODE_DIMENSIONS, EstimateInput::MODE_AREA], true)) {
            throw new InvalidArgumentException('Field "mode" must be one of: dimensions, area for brick.');
        }

        $brickLengthMm = $this->requirePositiveNumber($input->brickLengthMm, 'brickLengthMm');
        $brickWidthMm = $this->requirePositiveNumber($input->brickWidthMm, 'brickWidthMm');
        $brickHeightMm = $this->requirePositiveNumber($input->brickHeightMm, 'brickHeightMm');
        $jointThicknessMm = $this->requirePositiveNumber($input->jointThicknessMm, 'jointThicknessMm');
        $wallHeightM = $this->requirePositiveNumber($input->wallHeightM, 'wallHeightM');
        $reservePercent = $this->requirePositiveNumber($input->reservePercent, 'reservePercent');
        $wallThicknessType = $this->requireNonEmptyString($input->wallThicknessType, 'wallThicknessType');

        $grossAreaM2 = $mode === EstimateInput::MODE_DIMENSIONS
            ? $this->requirePositiveNumber($input->wallLengthM, 'wallLengthM') * $wallHeightM
            : $this->requirePositiveNumber($input->area, 'area');

        $effectiveWallLengthM = $mode === EstimateInput::MODE_DIMENSIONS
            ? $this->requirePositiveNumber($input->wallLengthM, 'wallLengthM')
            : $grossAreaM2 / $wallHeightM;

        $openings = $this->normalizeElements($input->windows, $input->doors);
        $gables = $this->normalizeGables($input->gables);
        $openingsAreaM2 = $input->includeOpenings ? $this->sumElementsArea($openings) : 0.0;
        $gablesAreaM2 = $input->includeGables ? $this->sumGablesArea($gables) : 0.0;
        $netAreaM2 = $grossAreaM2 + $gablesAreaM2 - $openingsAreaM2;

        if ($netAreaM2 <= 0) {
            throw new InvalidArgumentException('Net masonry area must be greater than 0 after subtracting openings.');
        }

        $displayWallThicknessMm = $this->resolveDisplayWallThicknessMm(
            $wallThicknessType,
            $brickLengthMm,
            $brickWidthMm,
            $jointThicknessMm
        );
        $calculationThicknessMm = $this->resolveCalculationWallThicknessMm(
            $wallThicknessType,
            $brickLengthMm,
            $brickWidthMm,
            $jointThicknessMm
        );

        $brickVolumeM3 = ($brickLengthMm / 1000) * ($brickWidthMm / 1000) * ($brickHeightMm / 1000);
        $moduleVolumeM3 = (($brickLengthMm + $jointThicknessMm) / 1000)
            * (($brickWidthMm + $jointThicknessMm) / 1000)
            * (($brickHeightMm + $jointThicknessMm) / 1000);

        if ($brickVolumeM3 <= 0 || $moduleVolumeM3 <= 0) {
            throw new InvalidArgumentException('Brick dimensions must result in a positive volume.');
        }

        $wallVolumeM3 = $netAreaM2 * ($displayWallThicknessMm / 1000);
        $brickCountExact = ($netAreaM2 * ($calculationThicknessMm / 1000)) / $moduleVolumeM3;
        $brickCountWithReserve = $brickCountExact * (1 + ($reservePercent / 100));
        $brickCountToBuy = (float) ceil($brickCountWithReserve);

        $brickWeightKg = $input->brickWeightKg !== null && $input->brickWeightKg > 0
            ? $input->brickWeightKg
            : $brickVolumeM3 * self::DEFAULT_BRICK_DENSITY_KG_PER_M3;
        $brickMassExactKg = $brickCountExact * $brickWeightKg;
        $brickMassWithReserveKg = $brickCountWithReserve * $brickWeightKg;
        $brickMassToBuyKg = $brickCountToBuy * $brickWeightKg;

        $mortarVolumeM3 = max(0.0, $wallVolumeM3 - ($brickCountExact * $brickVolumeM3));
        $mortar = $this->calculateMortar($input, $mortarVolumeM3);
        $mesh = $this->calculateMesh($input, $wallHeightM, $effectiveWallLengthM, $displayWallThicknessMm, $gablesAreaM2);
        $lintels = $this->calculateLintels($openings);
        $costs = $this->calculateCosts($input, $brickCountWithReserve, $brickCountToBuy, $mortar);

        $rowHeightM = ($brickHeightMm + $jointThicknessMm) / 1000;
        $rowsCount = $rowHeightM > 0 ? ceil($wallHeightM / $rowHeightM) : 0.0;
        $loadKg = $brickMassWithReserveKg + ($mortarVolumeM3 * self::MORTAR_DENSITY_KG_PER_M3);

        $details = [
            'summary' => [
                'mode' => $mode,
                'grossAreaM2' => $grossAreaM2,
                'openingsAreaM2' => $openingsAreaM2,
                'gablesAreaM2' => $gablesAreaM2,
                'netAreaM2' => $netAreaM2,
                'wallLengthM' => $effectiveWallLengthM,
                'wallHeightM' => $wallHeightM,
                'wallVolumeM3' => $wallVolumeM3,
                'wallThicknessMm' => $displayWallThicknessMm,
                'wallThicknessLabel' => self::WALL_THICKNESS_LABELS[$wallThicknessType] ?? $wallThicknessType,
                'brickFormat' => self::BRICK_FORMAT_LABELS[$input->brickFormat ?? 'custom'] ?? 'Свой размер',
                'jointThicknessMm' => $jointThicknessMm,
                'rowsCount' => $rowsCount,
                'reservePercent' => $reservePercent,
                'estimatedFoundationLoadKg' => $loadKg,
                'estimatedFoundationLoadTonnes' => $loadKg / 1000,
            ],
            'brick' => [
                'countExact' => $brickCountExact,
                'countWithReserve' => $brickCountWithReserve,
                'countToBuy' => $brickCountToBuy,
                'weightPerUnitKg' => $brickWeightKg,
                'massExactKg' => $brickMassExactKg,
                'massWithReserveKg' => $brickMassWithReserveKg,
                'massToBuyKg' => $brickMassToBuyKg,
                'materialVolumeM3' => $brickCountExact * $brickVolumeM3,
                'pricePerUnit' => $input->brickPricePerUnit,
            ],
            'mortar' => $mortar,
            'openings' => [
                'items' => $openings,
            ],
            'gables' => [
                'items' => $gables,
            ],
            'mesh' => $mesh,
            'lintels' => $lintels,
            'costs' => $costs,
            'armopoyasGuide' => [
                'stripFoundationUrl' => '/kalkulyator-lentochnogo-fundamenta/',
                'note' => 'Армопояс удобнее считать в калькуляторе ленточного фундамента: укажите периметр стен, ширину по толщине стены и высоту пояса 0.2-0.3 м.',
            ],
        ];

        return new EstimateResult(
            mode: $mode,
            calculatedVolume: $netAreaM2,
            calculatedMaterialAmount: $brickCountWithReserve,
            details: $details
        );
    }

    private function requirePositiveNumber(?float $value, string $field): float
    {
        if ($value === null || $value <= 0) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be greater than 0 for brick.', $field));
        }

        return $value;
    }

    private function requireNonEmptyString(?string $value, string $field): string
    {
        if ($value === null || trim($value) === '') {
            throw new InvalidArgumentException(sprintf('Field "%s" is required for brick.', $field));
        }

        return trim($value);
    }

    /**
     * @param array<int, array<string, mixed>>|null $windows
     * @param array<int, array<string, mixed>>|null $doors
     * @return array<int, array<string, mixed>>
     */
    private function normalizeElements(?array $windows, ?array $doors): array
    {
        $items = [];

        foreach ($windows ?? [] as $item) {
            $items[] = $this->normalizeElement($item, 'window', 'Окно');
        }

        foreach ($doors ?? [] as $item) {
            $items[] = $this->normalizeElement($item, 'door', 'Дверь');
        }

        return array_values(array_filter($items, static fn (array $item): bool => $item['count'] > 0));
    }

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private function normalizeElement(array $item, string $type, string $label): array
    {
        $widthM = isset($item['widthM']) ? (float) $item['widthM'] : 0.0;
        $heightM = isset($item['heightM']) ? (float) $item['heightM'] : 0.0;
        $count = isset($item['count']) ? (int) $item['count'] : 0;

        return [
            'type' => $type,
            'label' => $label,
            'widthM' => $widthM,
            'heightM' => $heightM,
            'count' => max(0, $count),
            'areaM2' => max(0.0, $widthM * $heightM * max(0, $count)),
        ];
    }

    /**
     * @param array<int, array<string, mixed>>|null $gables
     * @return array<int, array<string, mixed>>
     */
    private function normalizeGables(?array $gables): array
    {
        $items = [];

        foreach ($gables ?? [] as $item) {
            $widthM = isset($item['widthM']) ? (float) $item['widthM'] : 0.0;
            $heightM = isset($item['heightM']) ? (float) $item['heightM'] : 0.0;
            $count = isset($item['count']) ? (int) $item['count'] : 0;
            $items[] = [
                'label' => 'Фронтон',
                'widthM' => $widthM,
                'heightM' => $heightM,
                'count' => max(0, $count),
                'areaM2' => max(0.0, 0.5 * $widthM * $heightM * max(0, $count)),
            ];
        }

        return array_values(array_filter($items, static fn (array $item): bool => $item['count'] > 0));
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function sumElementsArea(array $items): float
    {
        return array_reduce(
            $items,
            static fn (float $sum, array $item): float => $sum + (float) ($item['areaM2'] ?? 0.0),
            0.0
        );
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function sumGablesArea(array $items): float
    {
        return array_reduce(
            $items,
            static fn (float $sum, array $item): float => $sum + (float) ($item['areaM2'] ?? 0.0),
            0.0
        );
    }

    private function resolveDisplayWallThicknessMm(
        string $wallThicknessType,
        float $brickLengthMm,
        float $brickWidthMm,
        float $jointThicknessMm
    ): float {
        return match ($wallThicknessType) {
            'half_brick' => $brickWidthMm,
            'one_brick' => $brickLengthMm,
            'one_and_half_bricks' => $brickLengthMm + $brickWidthMm + $jointThicknessMm,
            'two_bricks' => ($brickLengthMm * 2) + $jointThicknessMm,
            'two_and_half_bricks' => ($brickLengthMm * 2) + $brickWidthMm + ($jointThicknessMm * 2),
            default => throw new InvalidArgumentException('Field "wallThicknessType" is not supported for brick.'),
        };
    }

    private function resolveCalculationWallThicknessMm(
        string $wallThicknessType,
        float $brickLengthMm,
        float $brickWidthMm,
        float $jointThicknessMm
    ): float {
        return match ($wallThicknessType) {
            'half_brick' => $brickWidthMm + $jointThicknessMm,
            'one_brick' => $brickLengthMm + $jointThicknessMm,
            'one_and_half_bricks' => $brickLengthMm + $brickWidthMm + ($jointThicknessMm * 2),
            'two_bricks' => ($brickLengthMm * 2) + ($jointThicknessMm * 2),
            'two_and_half_bricks' => ($brickLengthMm * 2) + $brickWidthMm + ($jointThicknessMm * 3),
            default => throw new InvalidArgumentException('Field "wallThicknessType" is not supported for brick.'),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function calculateMortar(EstimateInput $input, float $mortarVolumeM3): array
    {
        $cementShare = $input->cementShare !== null && $input->cementShare > 0
            ? $input->cementShare
            : self::DEFAULT_CEMENT_SHARE;
        $sandShare = $input->sandShare !== null && $input->sandShare > 0
            ? $input->sandShare
            : self::DEFAULT_SAND_SHARE;
        $parts = $cementShare + $sandShare;
        $dryVolumeM3 = $mortarVolumeM3 * self::MORTAR_DRY_VOLUME_FACTOR;
        $cementVolumeM3 = $parts > 0 ? $dryVolumeM3 * ($cementShare / $parts) : 0.0;
        $sandVolumeM3 = $parts > 0 ? $dryVolumeM3 * ($sandShare / $parts) : 0.0;
        $cementWeightKg = $cementVolumeM3 * self::CEMENT_DENSITY_KG_PER_M3;
        $sandWeightKg = $sandVolumeM3 * self::SAND_DENSITY_KG_PER_M3;
        $waterLiters = $cementWeightKg * self::WATER_CEMENT_RATIO;

        $cementPurchaseUnit = $this->normalizePurchaseUnit($input->cementPurchaseUnit, self::DEFAULT_CEMENT_PURCHASE_UNIT);
        $cementUnitWeightKg = $this->resolveUnitWeightKg(
            $input->cementUnitWeightKg,
            $input->cementBagWeightKg,
            $cementPurchaseUnit,
            self::DEFAULT_CEMENT_BAG_WEIGHT_KG
        );
        $cementUnitPrice = $this->resolveUnitPrice($input->cementUnitPrice, $input->cementBagPrice);

        $sandPurchaseUnit = $this->normalizePurchaseUnit($input->sandPurchaseUnit, self::DEFAULT_SAND_PURCHASE_UNIT);
        $sandUnitWeightKg = $this->resolveUnitWeightKg(
            $input->sandUnitWeightKg,
            $sandPurchaseUnit === self::PURCHASE_UNIT_TONNE ? 1000.0 : null,
            $sandPurchaseUnit,
            $sandPurchaseUnit === self::PURCHASE_UNIT_TONNE ? 1000.0 : 50.0
        );
        $sandUnitPrice = $this->resolveUnitPrice($input->sandUnitPrice, $input->sandPricePerTonne);

        $cementComponent = $this->buildMortarComponent(
            label: 'Цемент',
            share: $cementShare,
            volumeM3: $cementVolumeM3,
            weightKg: $cementWeightKg,
            purchaseUnit: $cementPurchaseUnit,
            unitWeightKg: $cementUnitWeightKg,
            unitPrice: $cementUnitPrice
        );
        $sandComponent = $this->buildMortarComponent(
            label: 'Песок',
            share: $sandShare,
            volumeM3: $sandVolumeM3,
            weightKg: $sandWeightKg,
            purchaseUnit: $sandPurchaseUnit,
            unitWeightKg: $sandUnitWeightKg,
            unitPrice: $sandUnitPrice
        );

        return [
            'volumeM3' => $mortarVolumeM3,
            'dryVolumeM3' => $dryVolumeM3,
            'ratio' => [
                'cementShare' => $cementShare,
                'sandShare' => $sandShare,
                'display' => sprintf('%s:%s', $this->formatRatioPart($cementShare), $this->formatRatioPart($sandShare)),
                'isCustom' => true,
            ],
            'cement' => $cementComponent,
            'sand' => $sandComponent,
            'waterLiters' => $waterLiters,
            'note' => 'Раствор рассчитан ориентировочно. Для закупки цемента и песка сверяйтесь с проектом и требованиями производителя.',
        ];
    }

    /**
     * @return array<string, float|string>
     */
    private function buildMortarComponent(
        string $label,
        float $share,
        float $volumeM3,
        float $weightKg,
        string $purchaseUnit,
        float $unitWeightKg,
        ?float $unitPrice
    ): array {
        $displayUnitWeight = $purchaseUnit === self::PURCHASE_UNIT_TONNE
            ? $unitWeightKg / 1000.0
            : $unitWeightKg;
        $requiredUnits = $unitWeightKg > 0 ? $weightKg / $unitWeightKg : 0.0;
        $roundedUnits = (float) ceil($requiredUnits);

        return [
            'label' => $label,
            'share' => $share,
            'volumeM3' => $volumeM3,
            'weightKg' => $weightKg,
            'purchaseUnit' => $purchaseUnit,
            'purchaseUnitLabel' => $purchaseUnit === self::PURCHASE_UNIT_TONNE ? 'т' : 'мешок',
            'displayUnitWeight' => $displayUnitWeight,
            'unitWeightKg' => $unitWeightKg,
            'unitPrice' => $unitPrice,
            'requiredUnits' => $requiredUnits,
            'roundedUnits' => $roundedUnits,
            'totalCostExact' => $unitPrice !== null && $unitPrice > 0 ? $requiredUnits * $unitPrice : null,
            'totalCostRounded' => $unitPrice !== null && $unitPrice > 0 ? $roundedUnits * $unitPrice : null,
        ];
    }

    /**
     * @return array<string, mixed>|array{}
     */
    private function calculateMesh(
        EstimateInput $input,
        float $wallHeightM,
        float $effectiveWallLengthM,
        float $wallThicknessMm,
        float $gablesAreaM2
    ): array {
        if (!$input->includeMasonryMesh) {
            return [];
        }

        $frequencyRows = $input->masonryMeshFrequencyRows ?? 3;
        if ($frequencyRows <= 0) {
            throw new InvalidArgumentException('Field "masonryMeshFrequencyRows" must be greater than 0 for brick.');
        }

        $rowPitchM = (($input->brickHeightMm ?? 0.0) + ($input->jointThicknessMm ?? 0.0)) / 1000;
        if ($rowPitchM <= 0) {
            return [];
        }

        $rowsCount = (int) ceil($wallHeightM / $rowPitchM);
        $reinforcedRows = (int) ceil($rowsCount / $frequencyRows);
        $gablesEquivalentLengthM = $wallHeightM > 0 ? $gablesAreaM2 / $wallHeightM : 0.0;
        $meshLengthM = ($effectiveWallLengthM + $gablesEquivalentLengthM) * $reinforcedRows;
        $meshWidthM = $wallThicknessMm / 1000;
        $meshAreaM2 = $meshLengthM * $meshWidthM;

        return [
            'rowsCount' => $rowsCount,
            'frequencyRows' => $frequencyRows,
            'reinforcedRows' => $reinforcedRows,
            'meshLengthM' => $meshLengthM,
            'meshWidthM' => $meshWidthM,
            'meshAreaM2' => $meshAreaM2,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $openings
     * @return array<string, mixed>|array{}
     */
    private function calculateLintels(array $openings): array
    {
        if ($openings === []) {
            return [];
        }

        $grouped = [];
        $totalCount = 0;

        foreach ($openings as $opening) {
            $count = (int) ($opening['count'] ?? 0);
            if ($count <= 0) {
                continue;
            }

            $widthM = (float) ($opening['widthM'] ?? 0.0);
            $key = sprintf('%s-%s', $opening['type'] ?? 'opening', number_format($widthM, 3, '.', ''));
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'label' => $opening['label'] ?? 'Проём',
                    'widthM' => $widthM,
                    'count' => 0,
                    'recommendedLengthM' => $widthM + (self::LINTEL_SUPPORT_LENGTH_M * 2),
                ];
            }

            $grouped[$key]['count'] += $count;
            $totalCount += $count;
        }

        return [
            'totalCount' => $totalCount,
            'recommendedSupportLengthM' => self::LINTEL_SUPPORT_LENGTH_M,
            'items' => array_values($grouped),
            'note' => 'Перемычки показаны как ориентир по количеству и минимальной длине. Тип изделия и схему армирования выбирайте по проекту.',
        ];
    }

    /**
     * @param array<string, mixed> $mortar
     * @return array<string, mixed>|array{}
     */
    private function calculateCosts(
        EstimateInput $input,
        float $brickCountWithReserve,
        float $brickCountToBuy,
        array $mortar
    ): array {
        $brickCostExact = $input->brickPricePerUnit !== null && $input->brickPricePerUnit > 0
            ? $brickCountWithReserve * $input->brickPricePerUnit
            : null;
        $brickCostRounded = $input->brickPricePerUnit !== null && $input->brickPricePerUnit > 0
            ? $brickCountToBuy * $input->brickPricePerUnit
            : null;

        $cement = $mortar['cement'] ?? [];
        $cementCostExact = isset($cement['totalCostExact']) ? (float) $cement['totalCostExact'] : null;
        $cementCostRounded = isset($cement['totalCostRounded']) ? (float) $cement['totalCostRounded'] : null;

        $sand = $mortar['sand'] ?? [];
        $sandCostExact = isset($sand['totalCostExact']) ? (float) $sand['totalCostExact'] : null;
        $sandCostRounded = isset($sand['totalCostRounded']) ? (float) $sand['totalCostRounded'] : null;

        $totalExact = $this->sumNullable([$brickCostExact, $cementCostExact, $sandCostExact]);
        $totalRounded = $this->sumNullable([$brickCostRounded, $cementCostRounded, $sandCostRounded]);

        if ($totalExact === null && $totalRounded === null) {
            return [];
        }

        return [
            'brickExact' => $brickCostExact,
            'brickRounded' => $brickCostRounded,
            'cementExact' => $cementCostExact,
            'cementRounded' => $cementCostRounded,
            'sandExact' => $sandCostExact,
            'sandRounded' => $sandCostRounded,
            'totalExact' => $totalExact,
            'totalRounded' => $totalRounded,
        ];
    }

    /**
     * @param array<int, float|null> $values
     */
    private function sumNullable(array $values): ?float
    {
        $sum = 0.0;
        $hasValue = false;

        foreach ($values as $value) {
            if ($value === null) {
                continue;
            }

            $sum += $value;
            $hasValue = true;
        }

        return $hasValue ? $sum : null;
    }

    private function formatRatioPart(float $value): string
    {
        $rounded = round($value, 2);
        if ($rounded === (float) (int) $rounded) {
            return (string) (int) $rounded;
        }

        return rtrim(rtrim(sprintf('%.2f', $rounded), '0'), '.');
    }

    private function normalizePurchaseUnit(?string $value, string $default): string
    {
        if ($value !== null && in_array($value, [self::PURCHASE_UNIT_BAG, self::PURCHASE_UNIT_TONNE], true)) {
            return $value;
        }

        return $default;
    }

    private function resolveUnitWeightKg(?float $primaryValue, ?float $fallbackValue, string $purchaseUnit, float $defaultValue): float
    {
        $resolved = $primaryValue !== null && $primaryValue > 0
            ? $primaryValue
            : (($fallbackValue !== null && $fallbackValue > 0) ? $fallbackValue : $defaultValue);

        if ($purchaseUnit === self::PURCHASE_UNIT_TONNE && $resolved <= 10.0) {
            return $resolved * 1000.0;
        }

        return $resolved;
    }

    private function resolveUnitPrice(?float $primaryValue, ?float $fallbackValue): ?float
    {
        if ($primaryValue !== null && $primaryValue > 0) {
            return $primaryValue;
        }

        if ($fallbackValue !== null && $fallbackValue > 0) {
            return $fallbackValue;
        }

        return null;
    }
}
