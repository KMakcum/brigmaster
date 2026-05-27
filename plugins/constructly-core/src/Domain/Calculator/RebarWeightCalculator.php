<?php

declare(strict_types=1);

namespace Brigmaster\Domain\Calculator;

final class RebarWeightCalculator
{
    private const FORMULA_DIVISOR = 162.0;

    public static function resolveUnitWeightKgPerM(float $diameterMm): float
    {
        // Удельный вес арматуры: m(кг/м) = d^2 / 162, где d — диаметр в мм.
        return ($diameterMm * $diameterMm) / self::FORMULA_DIVISOR;
    }
}
