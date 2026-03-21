<?php

declare(strict_types=1);

namespace Brigmaster\Domain\DTO;

final class EstimateResult
{
    public readonly string $mode;
    public readonly float $calculatedVolume;
    public readonly float $calculatedMaterialAmount;
    /** @var array<string, mixed> */
    public readonly array $details;

    public function __construct(
        string $mode,
        float $calculatedVolume,
        float $calculatedMaterialAmount,
        array $details = []
    ) {
        $this->mode = $mode;
        $this->calculatedVolume = round($calculatedVolume, 2);
        $this->calculatedMaterialAmount = round($calculatedMaterialAmount, 2);
        $this->details = $this->roundDetails($details);
    }

    /**
     * @param array<string, mixed> $details
     * @return array<string, mixed>
     */
    private function roundDetails(array $details): array
    {
        $rounded = [];

        foreach ($details as $key => $value) {
            if (is_int($value) || is_float($value)) {
                $rounded[$key] = round((float) $value, 2);
                continue;
            }

            if (is_array($value)) {
                $rounded[$key] = $this->roundDetails($value);
                continue;
            }

            $rounded[$key] = $value;
        }

        return $rounded;
    }
}
