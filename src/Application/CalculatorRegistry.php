<?php

declare(strict_types=1);

namespace Brigmaster\Application;

use Brigmaster\Domain\Calculator\CalculatorInterface;
use Brigmaster\Domain\Calculator\BrickCalculator;
use Brigmaster\Domain\Calculator\DrywallCalculator;
use Brigmaster\Domain\Calculator\PileFoundationCalculator;
use Brigmaster\Domain\Calculator\ScreedCalculator;
use Brigmaster\Domain\Calculator\SlabFoundationCalculator;
use Brigmaster\Domain\Calculator\StripFoundationCalculator;
use Brigmaster\Domain\Calculator\TileCalculator;
use Brigmaster\Domain\DTO\EstimateInput;
use InvalidArgumentException;

final class CalculatorRegistry
{
    /** @var array<string, array<string, callable(): CalculatorInterface>> */
    private array $factories;

    public function __construct()
    {
        $this->factories = [
            EstimateService::CALCULATOR_SCREED => [
                EstimateInput::MODE_DIMENSIONS => static fn (): CalculatorInterface => new ScreedCalculator(),
                EstimateInput::MODE_AREA => static fn (): CalculatorInterface => new ScreedCalculator(),
            ],
            EstimateService::CALCULATOR_BRICK => [
                EstimateInput::MODE_DIMENSIONS => static fn (): CalculatorInterface => new BrickCalculator(),
                EstimateInput::MODE_AREA => static fn (): CalculatorInterface => new BrickCalculator(),
            ],
            EstimateService::CALCULATOR_DRYWALL => [
                EstimateInput::MODE_DIMENSIONS => static fn (): CalculatorInterface => new DrywallCalculator(),
                EstimateInput::MODE_AREA => static fn (): CalculatorInterface => new DrywallCalculator(),
            ],
            EstimateService::CALCULATOR_TILE => [
                EstimateInput::MODE_DIMENSIONS => static fn (): CalculatorInterface => new TileCalculator(),
                EstimateInput::MODE_AREA => static fn (): CalculatorInterface => new TileCalculator(),
            ],
            EstimateService::CALCULATOR_SLAB_FOUNDATION => [
                EstimateInput::MODE_DIMENSIONS => static fn (): CalculatorInterface => new SlabFoundationCalculator(),
                EstimateInput::MODE_AREA => static fn (): CalculatorInterface => new SlabFoundationCalculator(),
            ],
            EstimateService::CALCULATOR_STRIP_FOUNDATION => [
                EstimateInput::MODE_PERIMETER => static fn (): CalculatorInterface => new StripFoundationCalculator(),
                EstimateInput::MODE_HOUSE => static fn (): CalculatorInterface => new StripFoundationCalculator(),
                EstimateInput::MODE_SEGMENTS => static fn (): CalculatorInterface => new StripFoundationCalculator(),
            ],
            EstimateService::CALCULATOR_PILE_FOUNDATION => [
                EstimateInput::MODE_PERIMETER => static fn (): CalculatorInterface => new PileFoundationCalculator(),
                EstimateInput::MODE_HOUSE => static fn (): CalculatorInterface => new PileFoundationCalculator(),
                EstimateInput::MODE_SEGMENTS => static fn (): CalculatorInterface => new PileFoundationCalculator(),
            ],
        ];
    }

    public function resolve(string $calculator, string $mode): CalculatorInterface
    {
        if (!isset($this->factories[$calculator])) {
            throw new InvalidArgumentException('Field "calculator" must be one of: brick, screed, drywall, tile, slab_foundation, strip_foundation, pile_foundation.');
        }

        if (!isset($this->factories[$calculator][$mode])) {
            throw new InvalidArgumentException(sprintf('Field "mode" value "%s" is not supported for calculator "%s".', $mode, $calculator));
        }

        return ($this->factories[$calculator][$mode])();
    }
}
