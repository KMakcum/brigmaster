<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class PileFoundationCalculator implements CalculatorInterface
{
    private const PILE_TYPE_BORED = 'bored';
    private const PILE_TYPE_SCREW = 'screw';
    private const PILE_TYPE_DRIVEN = 'driven';
    private const PI = 3.141592653589793;

    public function __construct(
        private readonly StripFoundationCalculator $stripFoundationCalculator = new StripFoundationCalculator()
    ) {
    }

    public function calculate(EstimateInput $input): EstimateResult
    {
        $details = [];
        $totalVolumeM3 = 0.0;

        $includePiles = $input->includePiles ?? true;
        if ($includePiles) {
            $pilesDetails = $this->calculatePiles($input);
            $details['piles'] = $pilesDetails;
            $totalVolumeM3 += $pilesDetails['concreteVolumeM3'] ?? 0.0;
        }

        $includeGrillage = $input->includeGrillage ?? true;
        if ($includeGrillage) {
            $grillageResult = $this->stripFoundationCalculator->calculate($input);
            $grillageDetails = $grillageResult->details;

            if (isset($grillageDetails['concrete'])) {
                $details['concrete'] = $grillageDetails['concrete'];
                $totalVolumeM3 += (float) ($grillageDetails['concrete']['volumeM3'] ?? 0.0);
            }

            if (isset($grillageDetails['reinforcement'])) {
                $details['reinforcement'] = $grillageDetails['reinforcement'];
            }

            if (isset($grillageDetails['formwork'])) {
                $details['formwork'] = $grillageDetails['formwork'];
            }
        }

        return new EstimateResult(
            mode: $input->mode,
            calculatedVolume: $totalVolumeM3,
            calculatedMaterialAmount: $totalVolumeM3,
            details: $details
        );
    }

    /**
     * @return array<string, int|float|string|bool>
     */
    private function calculatePiles(EstimateInput $input): array
    {
        $pileType = $input->pileType ?? self::PILE_TYPE_BORED;

        if (!in_array($pileType, [self::PILE_TYPE_BORED, self::PILE_TYPE_SCREW, self::PILE_TYPE_DRIVEN], true)) {
            throw new InvalidArgumentException('Field "pileType" must be one of: bored, screw, driven.');
        }

        if ($input->pilesCount === null || $input->pilesCount <= 0) {
            throw new InvalidArgumentException('Field "pilesCount" must be greater than 0 when includePiles is true.');
        }

        if ($pileType === self::PILE_TYPE_BORED) {
            if ($input->pileShaftDiameterM === null || $input->pileShaftDiameterM <= 0) {
                throw new InvalidArgumentException('Field "pileShaftDiameterM" must be greater than 0 for bored piles.');
            }

            if ($input->pileShaftHeightM === null || $input->pileShaftHeightM <= 0) {
                throw new InvalidArgumentException('Field "pileShaftHeightM" must be greater than 0 for bored piles.');
            }

            $includePileBase = $input->includePileBase ?? true;
            $shaftRadiusM = $input->pileShaftDiameterM / 2.0;
            $shaftVolumeM3 = self::PI * $shaftRadiusM * $shaftRadiusM * $input->pileShaftHeightM;

            $baseVolumeM3 = 0.0;
            if ($includePileBase) {
                if ($input->pileBaseDiameterM === null || $input->pileBaseDiameterM <= 0) {
                    throw new InvalidArgumentException('Field "pileBaseDiameterM" must be greater than 0 when includePileBase is true.');
                }

                if ($input->pileBaseHeightM === null || $input->pileBaseHeightM <= 0) {
                    throw new InvalidArgumentException('Field "pileBaseHeightM" must be greater than 0 when includePileBase is true.');
                }

                $baseRadiusM = $input->pileBaseDiameterM / 2.0;
                $baseVolumeM3 = self::PI * $baseRadiusM * $baseRadiusM * $input->pileBaseHeightM;
            }

            $details = [
                'pileType' => $pileType,
                'count' => $input->pilesCount,
                'includePileBase' => $includePileBase,
                'shaftVolumePerPileM3' => $shaftVolumeM3,
                'baseVolumePerPileM3' => $baseVolumeM3,
                'concreteVolumePerPileM3' => $shaftVolumeM3 + $baseVolumeM3,
                'concreteVolumeM3' => ($shaftVolumeM3 + $baseVolumeM3) * $input->pilesCount,
            ];

            $includePileReinforcement = $input->includePileReinforcement ?? false;
            if ($includePileReinforcement) {
                if ($input->pileReinforcementBarsCount === null || $input->pileReinforcementBarsCount <= 0) {
                    throw new InvalidArgumentException('Field "pileReinforcementBarsCount" must be greater than 0 when pile reinforcement is enabled.');
                }

                if ($input->pileReinforcementDiameterMm === null || $input->pileReinforcementDiameterMm <= 0) {
                    throw new InvalidArgumentException('Field "pileReinforcementDiameterMm" must be greater than 0 when pile reinforcement is enabled.');
                }

                if ($input->pileReinforcementReservePercent === null || $input->pileReinforcementReservePercent <= 0) {
                    throw new InvalidArgumentException('Field "pileReinforcementReservePercent" must be greater than 0 when pile reinforcement is enabled.');
                }

                // Арматура свай считается только по стволу:
                // длина в одной свае = N стержней * высота ствола.
                $onePileRebarLengthM = $input->pileReinforcementBarsCount * $input->pileShaftHeightM;
                $totalLengthM = $onePileRebarLengthM * $input->pilesCount;
                $totalLengthWithReserveM = $totalLengthM * (1 + ($input->pileReinforcementReservePercent / 100.0));
                $massKg = $totalLengthWithReserveM * RebarWeightCalculator::resolveUnitWeightKgPerM($input->pileReinforcementDiameterMm);

                $details['reinforcement'] = [
                    'barsCount' => $input->pileReinforcementBarsCount,
                    'diameterMm' => $input->pileReinforcementDiameterMm,
                    'reservePercent' => $input->pileReinforcementReservePercent,
                    'totalLengthM' => $totalLengthM,
                    'totalLengthWithReserveM' => $totalLengthWithReserveM,
                    'massKg' => $massKg,
                    'byDiameter' => [[
                        'diameterMm' => $input->pileReinforcementDiameterMm,
                        'totalLengthM' => $totalLengthM,
                        'totalLengthWithReserveM' => $totalLengthWithReserveM,
                        'massKg' => $massKg,
                    ]],
                ];
            }

            return $details;
        }

        return [
            'pileType' => $pileType,
            'count' => $input->pilesCount,
            'concreteVolumeM3' => 0.0,
            'note' => 'Concrete for piles is not required for screw/driven pile types.',
        ];
    }
}
