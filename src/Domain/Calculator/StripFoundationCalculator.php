<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class StripFoundationCalculator implements CalculatorInterface
{
    public function __construct(
        private readonly ConcreteMixtureCalculator $mixtureCalculator = new ConcreteMixtureCalculator()
    ) {
    }

    public function calculate(EstimateInput $input): EstimateResult
    {
        if (!in_array($input->mode, [EstimateInput::MODE_PERIMETER, EstimateInput::MODE_HOUSE, EstimateInput::MODE_SEGMENTS], true)) {
            throw new InvalidArgumentException('Field "mode" must be one of: perimeter, house, segments for strip foundation.');
        }

        $segments = $this->resolveSegments($input);
        $totalLengthM = 0.0;
        $volumeM3 = 0.0;

        foreach ($segments as $segment) {
            $segmentLengthM = $segment['lengthM'];
            $segmentWidthM = $segment['widthM'];
            $segmentHeightM = $segment['heightM'];

            // Объем участка: V = L * B * H, где L — длина ленты, B — ширина, H — высота.
            $volumeM3 += $segmentLengthM * $segmentWidthM * $segmentHeightM;
            $totalLengthM += $segmentLengthM;
        }

        $details = [
            'concrete' => [
                'totalLengthM' => $totalLengthM,
                'volumeM3' => $volumeM3,
            ],
        ];

        if (($input->includeReinforcement ?? false) === true) {
            $details['reinforcement'] = $this->calculateReinforcement($input, $segments, $totalLengthM);
        }

        if (($input->includeFormwork ?? false) === true) {
            $details['formwork'] = $this->calculateFormwork($input, $segments);
        }

        $mixture = $this->mixtureCalculator->calculate('strip_foundation', $volumeM3, $input->mixture);
        if ($mixture !== null) {
            $details['mixture'] = $mixture;
        }

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $volumeM3,
            calculatedMaterialAmount: $volumeM3,
            details: $details
        );
    }

    /**
     * @return array<int, array{lengthM: float, widthM: float, heightM: float, sourceIndex: int}>
     */
    private function resolveSegments(EstimateInput $input): array
    {
        if ($input->mode === EstimateInput::MODE_PERIMETER) {
            if ($input->totalLengthM === null || $input->totalLengthM <= 0) {
                throw new InvalidArgumentException('Field "totalLengthM" must be greater than 0 for strip foundation in perimeter mode.');
            }

            if ($input->widthM === null || $input->widthM <= 0) {
                throw new InvalidArgumentException('Field "widthM" must be greater than 0 for strip foundation in perimeter mode.');
            }

            if ($input->heightM === null || $input->heightM <= 0) {
                throw new InvalidArgumentException('Field "heightM" must be greater than 0 for strip foundation in perimeter mode.');
            }

            return [[
                'lengthM' => $input->totalLengthM,
                'widthM' => $input->widthM,
                'heightM' => $input->heightM,
                'sourceIndex' => 0,
            ]];
        }

        if ($input->mode === EstimateInput::MODE_HOUSE) {
            if ($input->houseLengthM === null || $input->houseLengthM <= 0) {
                throw new InvalidArgumentException('Field "houseLengthM" must be greater than 0 for strip foundation in house mode.');
            }

            if ($input->houseWidthM === null || $input->houseWidthM <= 0) {
                throw new InvalidArgumentException('Field "houseWidthM" must be greater than 0 for strip foundation in house mode.');
            }

            if ($input->widthM === null || $input->widthM <= 0) {
                throw new InvalidArgumentException('Field "widthM" must be greater than 0 for strip foundation in house mode.');
            }

            if ($input->heightM === null || $input->heightM <= 0) {
                throw new InvalidArgumentException('Field "heightM" must be greater than 0 for strip foundation in house mode.');
            }

            // Общая длина ленты вокруг дома: Lобщ = 2 * (Lдома + Wдома).
            $totalLengthM = 2 * ($input->houseLengthM + $input->houseWidthM);

            return [[
                'lengthM' => $totalLengthM,
                'widthM' => $input->widthM,
                'heightM' => $input->heightM,
                'sourceIndex' => 0,
            ]];
        }

        if ($input->segments === null || $input->segments === []) {
            throw new InvalidArgumentException('Field "segments" must contain at least one segment for strip foundation in segments mode.');
        }

        $normalized = [];
        foreach ($input->segments as $index => $segment) {
            if (!is_array($segment)) {
                throw new InvalidArgumentException(sprintf('Segment at index %d must be an object.', $index));
            }

            $length = $segment['segmentLengthM'] ?? null;
            $width = $segment['segmentWidthM'] ?? null;
            $height = $segment['segmentHeightM'] ?? null;

            if (!is_numeric($length) || (float) $length <= 0) {
                throw new InvalidArgumentException(sprintf('Field "segmentLengthM" must be greater than 0 in segments[%d].', $index));
            }

            if (!is_numeric($width) || (float) $width <= 0) {
                throw new InvalidArgumentException(sprintf('Field "segmentWidthM" must be greater than 0 in segments[%d].', $index));
            }

            if (!is_numeric($height) || (float) $height <= 0) {
                throw new InvalidArgumentException(sprintf('Field "segmentHeightM" must be greater than 0 in segments[%d].', $index));
            }

            $normalized[] = [
                'lengthM' => (float) $length,
                'widthM' => (float) $width,
                'heightM' => (float) $height,
                'sourceIndex' => (int) $index,
            ];
        }

        return $normalized;
    }

    /**
     * @param array<int, array{lengthM: float, widthM: float, heightM: float, sourceIndex: int}> $segments
     * @return array<string, mixed>
     */
    private function calculateReinforcement(EstimateInput $input, array $segments, float $totalLengthM): array
    {
        if ($input->longitudinalBarsCount === null || $input->longitudinalBarsCount <= 0) {
            throw new InvalidArgumentException('Field "longitudinalBarsCount" must be greater than 0.');
        }

        if ($input->longitudinalDiameterMm === null || $input->longitudinalDiameterMm <= 0) {
            throw new InvalidArgumentException('Field "longitudinalDiameterMm" must be greater than 0.');
        }

        if ($input->longitudinalReservePercent === null || $input->longitudinalReservePercent <= 0) {
            throw new InvalidArgumentException('Field "longitudinalReservePercent" must be greater than 0.');
        }

        if ($input->transverseDiameterMm === null || $input->transverseDiameterMm <= 0) {
            throw new InvalidArgumentException('Field "transverseDiameterMm" must be greater than 0.');
        }

        if ($input->transverseStepMm === null || $input->transverseStepMm <= 0) {
            throw new InvalidArgumentException('Field "transverseStepMm" must be greater than 0.');
        }

        if ($input->transverseReservePercent === null || $input->transverseReservePercent <= 0) {
            throw new InvalidArgumentException('Field "transverseReservePercent" must be greater than 0.');
        }

        $totalLongitudinalLengthM = 0.0;
        $totalTransverseLengthM = 0.0;
        $longitudinalMassKg = 0.0;
        $transverseMassKg = 0.0;
        /** @var array<string, array{diameterMm: float, totalLengthM: float, totalLengthWithReserveM: float, massKg: float}> $byDiameterMap */
        $byDiameterMap = [];
        $transverseStepM = $input->transverseStepMm / 1000.0;

        foreach ($segments as $segment) {
            $index = $segment['sourceIndex'];
            $segmentLengthM = $segment['lengthM'];
            $segmentWidthM = $segment['widthM'];
            $segmentHeightM = $segment['heightM'];

            $segmentIncludeReinforcement = $this->resolveSegmentBoolean($input, $index, 'segmentIncludeReinforcement', true);
            if (!$segmentIncludeReinforcement) {
                continue;
            }

            $useGlobalParams = $this->resolveSegmentBoolean($input, $index, 'segmentUseGlobalRebarParams', true);
            $barsCount = $input->longitudinalBarsCount;
            $longitudinalDiameterMm = $input->longitudinalDiameterMm;
            $transverseDiameterMm = $input->transverseDiameterMm;
            $transverseStepMForSegment = $transverseStepM;

            if (!$useGlobalParams) {
                $barsCount = $this->resolveSegmentInt($input, $index, 'segmentLongitudinalBarsCount');
                $longitudinalDiameterMm = $this->resolveSegmentFloat($input, $index, 'segmentLongitudinalDiameterMm');
                $transverseDiameterMm = $this->resolveSegmentFloat($input, $index, 'segmentTransverseDiameterMm');
                $transverseStepMForSegment = $this->resolveSegmentFloat($input, $index, 'segmentTransverseStepMm') / 1000.0;
            }

            // Продольная арматура участка: Lпрод = Nстержней * Lучастка.
            $longitudinalLengthSegmentM = $barsCount * $segmentLengthM;
            $totalLongitudinalLengthM += $longitudinalLengthSegmentM;
            $longitudinalWithReserveSegmentM = $longitudinalLengthSegmentM * (1 + ($input->longitudinalReservePercent / 100.0));
            $longitudinalMassSegmentKg = $longitudinalWithReserveSegmentM * RebarWeightCalculator::resolveUnitWeightKgPerM($longitudinalDiameterMm);
            $longitudinalMassKg += $longitudinalMassSegmentKg;
            $this->addToDiameterSummary(
                $byDiameterMap,
                $longitudinalDiameterMm,
                $longitudinalLengthSegmentM,
                $longitudinalWithReserveSegmentM,
                $longitudinalMassSegmentKg
            );

            // Периметр хомута: Pхом = 2 * (B + H), где B — ширина ленты, H — высота ленты.
            $stirrupPerimeterM = 2 * ($segmentWidthM + $segmentHeightM);
            // Количество хомутов: Nхом = floor(Lучастка / шаг) + 1.
            $stirrupsCount = floor($segmentLengthM / $transverseStepMForSegment) + 1;
            // Длина поперечной арматуры: Lпоп = Nхом * Pхом.
            $transverseLengthSegmentM = $stirrupsCount * $stirrupPerimeterM;
            $totalTransverseLengthM += $transverseLengthSegmentM;
            $transverseWithReserveSegmentM = $transverseLengthSegmentM * (1 + ($input->transverseReservePercent / 100.0));
            $transverseMassSegmentKg = $transverseWithReserveSegmentM * RebarWeightCalculator::resolveUnitWeightKgPerM($transverseDiameterMm);
            $transverseMassKg += $transverseMassSegmentKg;
            $this->addToDiameterSummary(
                $byDiameterMap,
                $transverseDiameterMm,
                $transverseLengthSegmentM,
                $transverseWithReserveSegmentM,
                $transverseMassSegmentKg
            );
        }

        // Длина с запасом: Lсзапасом = L * (1 + reserve/100).
        $longitudinalWithReserveM = $totalLongitudinalLengthM * (1 + ($input->longitudinalReservePercent / 100.0));
        $transverseWithReserveM = $totalTransverseLengthM * (1 + ($input->transverseReservePercent / 100.0));

        return [
            'totalLengthM' => $totalLengthM,
            'longitudinal' => [
                'barsCount' => $input->longitudinalBarsCount,
                'diameterMm' => $input->longitudinalDiameterMm,
                'reservePercent' => $input->longitudinalReservePercent,
                'totalLengthM' => $totalLongitudinalLengthM,
                'totalLengthWithReserveM' => $longitudinalWithReserveM,
                'globalDiameterMm' => $input->longitudinalDiameterMm,
                'massKg' => $longitudinalMassKg,
            ],
            'transverse' => [
                'globalDiameterMm' => $input->transverseDiameterMm,
                'stepMm' => $input->transverseStepMm,
                'reservePercent' => $input->transverseReservePercent,
                'totalLengthM' => $totalTransverseLengthM,
                'totalLengthWithReserveM' => $transverseWithReserveM,
                'massKg' => $transverseMassKg,
            ],
            // Сводка по диаметрам: агрегируем длину/длину с запасом/массу
            // по всем продольным и поперечным стержням независимо от режима расчета.
            'byDiameter' => $this->buildDiameterSummary($byDiameterMap),
            'totalMassKg' => $longitudinalMassKg + $transverseMassKg,
        ];
    }

    /**
     * @param array<int, array{lengthM: float, widthM: float, heightM: float, sourceIndex: int}> $segments
     * @return array<string, float>
     */
    private function calculateFormwork(EstimateInput $input, array $segments): array
    {
        if ($input->formworkHeightM === null || $input->formworkHeightM <= 0) {
            throw new InvalidArgumentException('Field "formworkHeightM" must be greater than 0.');
        }

        if ($input->formworkReservePercent === null || $input->formworkReservePercent <= 0) {
            throw new InvalidArgumentException('Field "formworkReservePercent" must be greater than 0.');
        }

        $totalFormworkAreaM2 = 0.0;
        $totalFormworkLinearM = 0.0;

        foreach ($segments as $segment) {
            $index = $segment['sourceIndex'];
            $segmentLengthM = $segment['lengthM'];

            $segmentIncludeFormwork = $this->resolveSegmentBoolean($input, $index, 'segmentIncludeFormwork', true);
            if (!$segmentIncludeFormwork) {
                continue;
            }

            $useGlobalParams = $this->resolveSegmentBoolean($input, $index, 'segmentUseGlobalFormworkParams', true);
            $localHeightM = $input->formworkHeightM;
            if (!$useGlobalParams) {
                $localHeightM = $this->resolveSegmentFloat($input, $index, 'segmentFormworkHeightM');
            }

            // Площадь боковых щитов опалубки: Sуч = 2 * Lучастка * Hщита.
            // В расчет не входят внутренние элементы (распорки, откосины и т.п.).
            $totalFormworkAreaM2 += 2 * $segmentLengthM * $localHeightM;
            // Линейные метры щитов по двум сторонам: Lлин = 2 * Lучастка.
            $totalFormworkLinearM += 2 * $segmentLengthM;
        }

        // Итог с запасом: Xсзапасом = X * (1 + reserve/100).
        $coefficient = 1 + ($input->formworkReservePercent / 100.0);

        return [
            'heightM' => $input->formworkHeightM,
            'reservePercent' => $input->formworkReservePercent,
            'totalFormworkAreaM2' => $totalFormworkAreaM2,
            'totalFormworkAreaWithReserveM2' => $totalFormworkAreaM2 * $coefficient,
            'totalFormworkLinearM' => $totalFormworkLinearM * $coefficient,
        ];
    }

    /**
     * @param array<string, array{diameterMm: float, totalLengthM: float, totalLengthWithReserveM: float, massKg: float}> $byDiameterMap
     */
    private function addToDiameterSummary(
        array &$byDiameterMap,
        float $diameterMm,
        float $lengthM,
        float $lengthWithReserveM,
        float $massKg
    ): void {
        $diameterKey = (string) round($diameterMm, 6);
        if (!isset($byDiameterMap[$diameterKey])) {
            $byDiameterMap[$diameterKey] = [
                'diameterMm' => $diameterMm,
                'totalLengthM' => 0.0,
                'totalLengthWithReserveM' => 0.0,
                'massKg' => 0.0,
            ];
        }

        // Для каждого диаметра накапливаем "чистую" длину, длину с запасом и массу.
        $byDiameterMap[$diameterKey]['totalLengthM'] += $lengthM;
        $byDiameterMap[$diameterKey]['totalLengthWithReserveM'] += $lengthWithReserveM;
        $byDiameterMap[$diameterKey]['massKg'] += $massKg;
    }

    /**
     * @param array<string, array{diameterMm: float, totalLengthM: float, totalLengthWithReserveM: float, massKg: float}> $byDiameterMap
     * @return array<int, array{diameterMm: float, totalLengthM: float, totalLengthWithReserveM: float, massKg: float}>
     */
    private function buildDiameterSummary(array $byDiameterMap): array
    {
        $summary = array_values($byDiameterMap);
        usort(
            $summary,
            static fn (array $left, array $right): int => $left['diameterMm'] <=> $right['diameterMm']
        );

        return $summary;
    }

    private function resolveSegmentBoolean(EstimateInput $input, int $segmentIndex, string $field, bool $default): bool
    {
        $segment = $input->segments[$segmentIndex] ?? null;
        if (!is_array($segment) || !array_key_exists($field, $segment)) {
            return $default;
        }

        $value = $segment[$field];
        if (!is_bool($value)) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be a boolean in segments[%d].', $field, $segmentIndex));
        }

        return $value;
    }

    private function resolveSegmentInt(EstimateInput $input, int $segmentIndex, string $field): int
    {
        $value = $input->segments[$segmentIndex][$field] ?? null;
        if (!is_numeric($value) || (float) $value <= 0) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be greater than 0 in segments[%d].', $field, $segmentIndex));
        }

        if ((float) $value !== (float) (int) $value) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be an integer in segments[%d].', $field, $segmentIndex));
        }

        return (int) $value;
    }

    private function resolveSegmentFloat(EstimateInput $input, int $segmentIndex, string $field): float
    {
        $value = $input->segments[$segmentIndex][$field] ?? null;
        if (!is_numeric($value) || (float) $value <= 0) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be greater than 0 in segments[%d].', $field, $segmentIndex));
        }

        return (float) $value;
    }
}
