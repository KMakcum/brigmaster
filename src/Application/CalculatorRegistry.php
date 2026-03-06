<?php

declare(strict_types=1);

namespace Constructly\Application;

use Constructly\Domain\Calculator\CalculatorInterface;
use Constructly\Domain\Calculator\BrickCalculator;
use Constructly\Domain\Calculator\ConcreteCalculator;
use Constructly\Domain\Calculator\DrywallCalculator;
use Constructly\Domain\Calculator\ScreedCalculator;
use Constructly\Domain\Calculator\TileCalculator;
use Constructly\Domain\DTO\EstimateInput;
use Constructly\Domain\Strategy\BrickBeginnerStrategy;
use Constructly\Domain\Strategy\BrickNormativeStrategy;
use Constructly\Domain\Strategy\BrickReserveStrategy;
use Constructly\Domain\Strategy\BeginnerStrategy;
use Constructly\Domain\Strategy\DrywallBeginnerStrategy;
use Constructly\Domain\Strategy\DrywallNormativeStrategy;
use Constructly\Domain\Strategy\DrywallReserveStrategy;
use Constructly\Domain\Strategy\NormativeStrategy;
use Constructly\Domain\Strategy\ReserveStrategy;
use Constructly\Domain\Strategy\ScreedBeginnerStrategy;
use Constructly\Domain\Strategy\ScreedNormativeStrategy;
use Constructly\Domain\Strategy\ScreedReserveStrategy;
use Constructly\Domain\Strategy\TileBeginnerStrategy;
use Constructly\Domain\Strategy\TileNormativeStrategy;
use Constructly\Domain\Strategy\TileReserveStrategy;
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
