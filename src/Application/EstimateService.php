<?php

declare(strict_types=1);

namespace Brigmaster\Application;

use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\DTO\EstimateResult;
use InvalidArgumentException;

final class EstimateService
{
    public const CALCULATOR_CONCRETE = 'concrete';
    public const CALCULATOR_BRICK = 'brick';
    public const CALCULATOR_SCREED = 'screed';
    public const CALCULATOR_DRYWALL = 'drywall';
    public const CALCULATOR_TILE = 'tile';

    private const ALLOWED_CALCULATORS = [
        self::CALCULATOR_CONCRETE,
        self::CALCULATOR_BRICK,
        self::CALCULATOR_SCREED,
        self::CALCULATOR_DRYWALL,
        self::CALCULATOR_TILE,
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
        ?float $height = null
    ): EstimateResult
    {
        if (!in_array($calculator, self::ALLOWED_CALCULATORS, true)) {
            throw new InvalidArgumentException('Field "calculator" must be one of: concrete, brick, screed, drywall, tile.');
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
            height: $height
        );

        $calculatorService = $this->calculatorRegistry->resolve($calculator, $mode);

        return $calculatorService->calculate($input);
    }
}
