<?php

declare(strict_types=1);

namespace Brigmaster\Domain\DTO;

final class EstimateResult
{
    public readonly string $mode;
    public readonly float $calculatedVolume;
    public readonly float $calculatedMaterialAmount;

    public function __construct(
        string $mode,
        float $calculatedVolume,
        float $calculatedMaterialAmount
    ) {
        $this->mode = $mode;
        $this->calculatedVolume = round($calculatedVolume, 2);
        $this->calculatedMaterialAmount = round($calculatedMaterialAmount, 2);
    }
}
