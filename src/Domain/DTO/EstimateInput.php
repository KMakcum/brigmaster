<?php

declare(strict_types=1);

namespace Brigmaster\Domain\DTO;

use InvalidArgumentException;

final class EstimateInput
{
    public const MODE_NORMATIVE = 'normative';
    public const MODE_RESERVE = 'reserve';
    public const MODE_BEGINNER = 'beginner';
    public const MODE_DIMENSIONS = 'dimensions';
    public const MODE_AREA = 'area';
    public const MODE_PERIMETER = 'perimeter';
    public const MODE_HOUSE = 'house';
    public const MODE_SEGMENTS = 'segments';

    private const ALLOWED_MODES = [
        self::MODE_NORMATIVE,
        self::MODE_RESERVE,
        self::MODE_BEGINNER,
        self::MODE_DIMENSIONS,
        self::MODE_AREA,
        self::MODE_PERIMETER,
        self::MODE_HOUSE,
        self::MODE_SEGMENTS,
    ];

    public function __construct(
        public readonly string $mode,
        public readonly ?float $area = null,
        public readonly ?float $thickness = null,
        public readonly ?string $subType = null,
        public readonly ?float $tileLengthCm = null,
        public readonly ?float $tileWidthCm = null,
        public readonly ?float $length = null,
        public readonly ?float $width = null,
        public readonly ?float $height = null,
        public readonly ?bool $includeReinforcement = null,
        public readonly ?bool $includeFormwork = null,
        public readonly ?float $rebarDiameterMm = null,
        public readonly ?float $rebarStepMm = null,
        public readonly ?int $rebarLayers = null,
        public readonly ?float $rebarReservePercent = null,
        public readonly ?float $formworkHeightM = null,
        public readonly ?float $formworkReservePercent = null,
        public readonly ?float $totalLengthM = null,
        public readonly ?float $widthM = null,
        public readonly ?float $heightM = null,
        public readonly ?float $houseLengthM = null,
        public readonly ?float $houseWidthM = null,
        /** @var array<int, array<string, mixed>>|null */
        public readonly ?array $segments = null,
        public readonly ?int $longitudinalBarsCount = null,
        public readonly ?float $longitudinalDiameterMm = null,
        public readonly ?float $longitudinalReservePercent = null,
        public readonly ?float $transverseDiameterMm = null,
        public readonly ?float $transverseStepMm = null,
        public readonly ?float $transverseReservePercent = null,
        public readonly ?string $pileType = null,
        public readonly ?bool $includePiles = null,
        public readonly ?int $pilesCount = null,
        public readonly ?float $pileShaftDiameterM = null,
        public readonly ?float $pileShaftHeightM = null,
        public readonly ?bool $includePileBase = null,
        public readonly ?float $pileBaseDiameterM = null,
        public readonly ?float $pileBaseHeightM = null,
        public readonly ?bool $includeGrillage = null,
        public readonly ?bool $includePileReinforcement = null,
        public readonly ?int $pileReinforcementBarsCount = null,
        public readonly ?float $pileReinforcementDiameterMm = null,
        public readonly ?float $pileReinforcementReservePercent = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $mixture = null,
        public readonly ?bool $useUnifiedConcreteMixtureSettings = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $pileMixture = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $grillageMixture = null
    ) {
        if ($this->mode === '') {
            throw new InvalidArgumentException('Field "mode" is required.');
        }

        if (!in_array($this->mode, self::ALLOWED_MODES, true)) {
            throw new InvalidArgumentException('Field "mode" must be one of: normative, reserve, beginner, dimensions, area, perimeter, house, segments.');
        }

    }
}
