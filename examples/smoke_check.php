<?php

declare(strict_types=1);

use Constructly\Application\EstimateService;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$service = new EstimateService();
$epsilon = 0.00001;

$print = static function (string $status, string $message): void {
    echo sprintf("[%s] %s\n", $status, $message);
};

$isClose = static function (float $actual, float $expected, float $epsilon): bool {
    return abs($actual - $expected) < $epsilon;
};

$runSuccessCase = static function (
    string $title,
    float $expectedVolume,
    float $expectedAmount,
    callable $callback
) use ($print, $isClose, $epsilon): void {
    try {
        $result = $callback();
        $volumeOk = $isClose($result->calculatedVolume, $expectedVolume, $epsilon);
        $amountOk = $isClose($result->calculatedMaterialAmount, $expectedAmount, $epsilon);

        if ($volumeOk && $amountOk) {
            $print('PASS', $title);

            return;
        }

        $print(
            'FAIL',
            sprintf(
                '%s: expected volume=%.2f amount=%.2f, got volume=%.2f amount=%.2f',
                $title,
                $expectedVolume,
                $expectedAmount,
                $result->calculatedVolume,
                $result->calculatedMaterialAmount
            )
        );
    } catch (Throwable $exception) {
        $print('FAIL', $title . ' threw exception: ' . $exception->getMessage());
    }
};

$runSuccessCase(
    'concrete slab normative works',
    2.00,
    2.00,
    static fn () => $service->calculate(
        calculator: 'concrete',
        mode: 'normative',
        area: 10.0,
        thickness: 0.2,
        subType: 'slab'
    )
);

$runSuccessCase(
    'brick normative (bricks) works',
    10.00,
    500.00,
    static fn () => $service->calculate(
        calculator: 'brick',
        mode: 'normative',
        area: 10.0,
        subType: 'bricks'
    )
);

$runSuccessCase(
    'screed normative works',
    2.00,
    1.90,
    static fn () => $service->calculate(
        calculator: 'screed',
        mode: 'normative',
        area: 10.0,
        thickness: 0.2
    )
);

$runSuccessCase(
    'drywall normative works',
    10.00,
    10.50,
    static fn () => $service->calculate(
        calculator: 'drywall',
        mode: 'normative',
        area: 10.0
    )
);

$runSuccessCase(
    'tile normative works',
    10.00,
    111.11,
    static fn () => $service->calculate(
        calculator: 'tile',
        mode: 'normative',
        area: 10.0,
        tileLengthCm: 30.0,
        tileWidthCm: 30.0
    )
);

$runSuccessCase(
    'concrete strip normative works',
    1.50,
    1.50,
    static fn () => $service->calculate(
        calculator: 'concrete',
        mode: 'normative',
        subType: 'strip',
        length: 10.0,
        width: 0.5,
        height: 0.3
    )
);

try {
    $service->calculate(calculator: 'brick', mode: 'normative', area: 10.0);
    $print('FAIL', 'brick without subType should throw InvalidArgumentException');
} catch (InvalidArgumentException) {
    $print('PASS', 'brick without subType throws InvalidArgumentException');
} catch (Throwable $exception) {
    $print('FAIL', 'brick without subType threw unexpected exception: ' . $exception::class);
}
