<?php

declare(strict_types=1);

namespace Brigmaster\Application;

use Brigmaster\Domain\Calculator\CalculatorInterface;
use Brigmaster\Domain\Calculator\BrickCalculator;
use Brigmaster\Domain\Calculator\ConcreteCalculator;
use Brigmaster\Domain\Calculator\DrywallCalculator;
use Brigmaster\Domain\Calculator\ScreedCalculator;
use Brigmaster\Domain\Calculator\TileCalculator;
use Brigmaster\Domain\DTO\EstimateInput;
use Brigmaster\Domain\Strategy\BrickBeginnerStrategy;
use Brigmaster\Domain\Strategy\BrickNormativeStrategy;
use Brigmaster\Domain\Strategy\BrickReserveStrategy;
use Brigmaster\Domain\Strategy\BeginnerStrategy;
use Brigmaster\Domain\Strategy\DrywallBeginnerStrategy;
use Brigmaster\Domain\Strategy\DrywallNormativeStrategy;
use Brigmaster\Domain\Strategy\DrywallReserveStrategy;
use Brigmaster\Domain\Strategy\NormativeStrategy;
use Brigmaster\Domain\Strategy\ReserveStrategy;
use Brigmaster\Domain\Strategy\ScreedBeginnerStrategy;
use Brigmaster\Domain\Strategy\ScreedNormativeStrategy;
use Brigmaster\Domain\Strategy\ScreedReserveStrategy;
use Brigmaster\Domain\Strategy\TileBeginnerStrategy;
use Brigmaster\Domain\Strategy\TileNormativeStrategy;
use Brigmaster\Domain\Strategy\TileReserveStrategy;
use InvalidArgumentException;

final class CalculatorRegistry
{
    /** @var array<string, array<string, callable(): CalculatorInterface>> */
    private array $factories;

    public function __construct()
    {
        $this->factories = [
            EstimateService::CALCULATOR_CONCRETE => [
                EstimateInput::MODE_NORMATIVE => static fn (): CalculatorInterface => new ConcreteCalculator(new NormativeStrategy()),
                EstimateInput::MODE_RESERVE => static fn (): CalculatorInterface => new ConcreteCalculator(new ReserveStrategy()),
                EstimateInput::MODE_BEGINNER => static fn (): CalculatorInterface => new ConcreteCalculator(new BeginnerStrategy()),
            ],
            EstimateService::CALCULATOR_SCREED => [
                EstimateInput::MODE_NORMATIVE => static fn (): CalculatorInterface => new ScreedCalculator(new ScreedNormativeStrategy()),
                EstimateInput::MODE_RESERVE => static fn (): CalculatorInterface => new ScreedCalculator(new ScreedReserveStrategy()),
                EstimateInput::MODE_BEGINNER => static fn (): CalculatorInterface => new ScreedCalculator(new ScreedBeginnerStrategy()),
            ],
            EstimateService::CALCULATOR_BRICK => [
                EstimateInput::MODE_NORMATIVE => static fn (): CalculatorInterface => new BrickCalculator(new BrickNormativeStrategy()),
                EstimateInput::MODE_RESERVE => static fn (): CalculatorInterface => new BrickCalculator(new BrickReserveStrategy()),
                EstimateInput::MODE_BEGINNER => static fn (): CalculatorInterface => new BrickCalculator(new BrickBeginnerStrategy()),
            ],
            EstimateService::CALCULATOR_DRYWALL => [
                EstimateInput::MODE_NORMATIVE => static fn (): CalculatorInterface => new DrywallCalculator(new DrywallNormativeStrategy()),
                EstimateInput::MODE_RESERVE => static fn (): CalculatorInterface => new DrywallCalculator(new DrywallReserveStrategy()),
                EstimateInput::MODE_BEGINNER => static fn (): CalculatorInterface => new DrywallCalculator(new DrywallBeginnerStrategy()),
            ],
            EstimateService::CALCULATOR_TILE => [
                EstimateInput::MODE_NORMATIVE => static fn (): CalculatorInterface => new TileCalculator(new TileNormativeStrategy()),
                EstimateInput::MODE_RESERVE => static fn (): CalculatorInterface => new TileCalculator(new TileReserveStrategy()),
                EstimateInput::MODE_BEGINNER => static fn (): CalculatorInterface => new TileCalculator(new TileBeginnerStrategy()),
            ],
        ];
    }

    public function resolve(string $calculator, string $mode): CalculatorInterface
    {
        if (!isset($this->factories[$calculator])) {
            throw new InvalidArgumentException('Field "calculator" must be one of: concrete, brick, screed, drywall, tile.');
        }

        if (!isset($this->factories[$calculator][$mode])) {
            throw new InvalidArgumentException('Field "mode" must be one of: normative, reserve, beginner.');
        }

        return ($this->factories[$calculator][$mode])();
    }
}
