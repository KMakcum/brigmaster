<?php

declare(strict_types=1);

use Brigmaster\Application\EstimateService;

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

$runErrorCase = static function (
    string $title,
    callable $callback
) use ($print): void {
    try {
        $callback();
        $print('FAIL', $title . ' should throw InvalidArgumentException');
    } catch (InvalidArgumentException) {
        $print('PASS', $title);
    } catch (Throwable $exception) {
        $print('FAIL', $title . ' threw unexpected exception: ' . $exception::class);
    }
};

$runSuccessCase(
    'brick dimensions works',
    90.00,
    14538.46,
    static fn () => $service->calculate(
        calculator: 'brick',
        mode: 'dimensions',
        brickFormat: 'single_nf',
        brickLengthMm: 250.0,
        brickWidthMm: 120.0,
        brickHeightMm: 65.0,
        jointThicknessMm: 10.0,
        wallThicknessType: 'one_and_half_bricks',
        wallLengthM: 30.0,
        wallHeightM: 3.0,
        reservePercent: 5.0,
        cementBagWeightKg: 50.0
    )
);

$runSuccessCase(
    'screed area works',
    2.00,
    2.00,
    static fn () => $service->calculate(
        calculator: 'screed',
        mode: 'area',
        area: 10.0,
        height: 0.2,
        includeReinforcement: false,
        mixture: [
            'type' => 'ready',
            'readyConcretePricePerM3' => 7000.0,
        ]
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
    'slab_foundation dimensions concrete volume works',
    20.00,
    20.00,
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'dimensions',
        length: 10.0,
        width: 8.0,
        height: 0.25
    )
);

$runSuccessCase(
    'slab_foundation area concrete volume works',
    10.00,
    10.00,
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'area',
        area: 50.0,
        height: 0.2
    )
);

$runSuccessCase(
    'slab_foundation dimensions with explicit area is ignored',
    20.00,
    20.00,
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'dimensions',
        length: 10.0,
        width: 8.0,
        area: 1.0,
        height: 0.25
    )
);

try {
    $result = $service->calculate(
        calculator: 'slab_foundation',
        mode: 'area',
        area: 50.0,
        height: 0.2,
        includeReinforcement: false,
        includeFormwork: false
    );

    $hasReinforcement = array_key_exists('reinforcement', $result->details);
    $hasFormwork = array_key_exists('formwork', $result->details);

    if (!$hasReinforcement && !$hasFormwork) {
        $print('PASS', 'slab_foundation area without sections returns only concrete block');
    } else {
        $print('FAIL', 'slab_foundation area without sections should not include reinforcement/formwork blocks');
    }
} catch (Throwable $exception) {
    $print('FAIL', 'slab_foundation area without sections threw exception: ' . $exception->getMessage());
}

try {
    $result = $service->calculate(
        calculator: 'slab_foundation',
        mode: 'dimensions',
        length: 10.0,
        width: 8.0,
        height: 0.25,
        includeReinforcement: true,
        includeFormwork: true
    );

    $reinforcementMass = $result->details['reinforcement']['massKg'] ?? null;
    $formworkArea = $result->details['formwork']['areaM2'] ?? null;

    if ($isClose((float) $reinforcementMass, 1599.64, $epsilon) && $isClose((float) $formworkArea, 11.88, $epsilon)) {
        $print('PASS', 'slab_foundation reinforcement and formwork details work');
    } else {
        $print(
            'FAIL',
            sprintf(
                'slab_foundation details mismatch: expected mass=1599.64 area=11.88, got mass=%.2f area=%.2f',
                (float) $reinforcementMass,
                (float) $formworkArea
            )
        );
    }
} catch (Throwable $exception) {
    $print('FAIL', 'slab_foundation details case threw exception: ' . $exception->getMessage());
}

$runErrorCase(
    'slab_foundation area with reinforcement without dimensions throws',
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'area',
        area: 30.0,
        height: 0.2,
        includeReinforcement: true
    )
);

$runErrorCase(
    'slab_foundation area with formwork without dimensions throws',
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'area',
        area: 30.0,
        height: 0.2,
        includeFormwork: true
    )
);

$runErrorCase(
    'slab_foundation invalid rebar layers throws',
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'dimensions',
        length: 6.0,
        width: 5.0,
        height: 0.2,
        includeReinforcement: true,
        rebarLayers: 3
    )
);

$runErrorCase(
    'slab_foundation invalid mode throws',
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'normative',
        area: 30.0,
        height: 0.2
    )
);

$runErrorCase(
    'slab_foundation with zero height throws',
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'area',
        area: 30.0,
        height: 0.0
    )
);

$runErrorCase(
    'slab_foundation with invalid rebar step throws',
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'dimensions',
        length: 6.0,
        width: 5.0,
        height: 0.2,
        includeReinforcement: true,
        rebarStepMm: 0.0
    )
);

$runErrorCase(
    'slab_foundation with invalid formwork height throws',
    static fn () => $service->calculate(
        calculator: 'slab_foundation',
        mode: 'dimensions',
        length: 6.0,
        width: 5.0,
        height: 0.2,
        includeFormwork: true,
        formworkHeightM: -0.3
    )
);

$runSuccessCase(
    'strip_foundation perimeter volume works',
    16.00,
    16.00,
    static fn () => $service->calculate(
        calculator: 'strip_foundation',
        mode: 'perimeter',
        totalLengthM: 40.0,
        widthM: 0.4,
        heightM: 1.0
    )
);

$runSuccessCase(
    'strip_foundation house volume works',
    14.40,
    14.40,
    static fn () => $service->calculate(
        calculator: 'strip_foundation',
        mode: 'house',
        houseLengthM: 10.0,
        houseWidthM: 8.0,
        widthM: 0.4,
        heightM: 1.0
    )
);

$runSuccessCase(
    'strip_foundation segments volume works',
    8.40,
    8.40,
    static fn () => $service->calculate(
        calculator: 'strip_foundation',
        mode: 'segments',
        segments: [
            [
                'segmentLengthM' => 10.0,
                'segmentWidthM' => 0.4,
                'segmentHeightM' => 1.0,
            ],
            [
                'segmentLengthM' => 8.0,
                'segmentWidthM' => 0.5,
                'segmentHeightM' => 1.1,
            ],
        ]
    )
);

try {
    $result = $service->calculate(
        calculator: 'strip_foundation',
        mode: 'perimeter',
        totalLengthM: 40.0,
        widthM: 0.4,
        heightM: 1.0,
        includeReinforcement: true,
        longitudinalBarsCount: 4,
        longitudinalDiameterMm: 12.0,
        longitudinalReservePercent: 10.0,
        transverseDiameterMm: 8.0,
        transverseStepMm: 300.0,
        transverseReservePercent: 10.0,
        includeFormwork: true,
        formworkHeightM: 0.8,
        formworkReservePercent: 10.0
    );

    $volume = $result->details['concrete']['volumeM3'] ?? null;
    $rebarMass = $result->details['reinforcement']['totalMassKg'] ?? null;
    $formworkArea = $result->details['formwork']['totalFormworkAreaWithReserveM2'] ?? null;
    $byDiameter = $result->details['reinforcement']['byDiameter'] ?? [];
    $byDiameterMap = [];
    if (is_array($byDiameter)) {
        foreach ($byDiameter as $entry) {
            if (!is_array($entry) || !isset($entry['diameterMm'])) {
                continue;
            }
            $byDiameterMap[(string) $entry['diameterMm']] = $entry;
        }
    }
    $d12 = $byDiameterMap['12'] ?? null;
    $d8 = $byDiameterMap['8'] ?? null;

    if (
        $isClose((float) $volume, 16.00, $epsilon)
        && $isClose((float) $rebarMass, 319.49, $epsilon)
        && $isClose((float) $formworkArea, 70.40, $epsilon)
        && is_array($d12)
        && is_array($d8)
        && $isClose((float) ($d12['totalLengthM'] ?? 0.0), 160.00, $epsilon)
        && $isClose((float) ($d12['totalLengthWithReserveM'] ?? 0.0), 176.00, $epsilon)
        && $isClose((float) ($d12['massKg'] ?? 0.0), 156.44, $epsilon)
        && $isClose((float) ($d8['totalLengthM'] ?? 0.0), 375.20, $epsilon)
        && $isClose((float) ($d8['totalLengthWithReserveM'] ?? 0.0), 412.72, $epsilon)
        && $isClose((float) ($d8['massKg'] ?? 0.0), 163.05, $epsilon)
    ) {
        $print('PASS', 'strip_foundation perimeter reinforcement and formwork details work');
    } else {
        $print(
            'FAIL',
            sprintf(
                'strip_foundation details mismatch: expected volume=16.00 rebar=319.49 area=70.40 with byDiameter[12,8], got volume=%.2f rebar=%.2f area=%.2f',
                (float) $volume,
                (float) $rebarMass,
                (float) $formworkArea
            )
        );
    }
} catch (Throwable $exception) {
    $print('FAIL', 'strip_foundation details case threw exception: ' . $exception->getMessage());
}

$runErrorCase(
    'strip_foundation perimeter without widthM throws',
    static fn () => $service->calculate(
        calculator: 'strip_foundation',
        mode: 'perimeter',
        totalLengthM: 40.0,
        heightM: 1.0
    )
);

$runErrorCase(
    'strip_foundation segments with invalid segment size throws',
    static fn () => $service->calculate(
        calculator: 'strip_foundation',
        mode: 'segments',
        segments: [
            [
                'segmentLengthM' => -10.0,
                'segmentWidthM' => 0.4,
                'segmentHeightM' => 1.0,
            ],
        ]
    )
);

$runErrorCase(
    'strip_foundation with invalid segment rebar override throws',
    static fn () => $service->calculate(
        calculator: 'strip_foundation',
        mode: 'segments',
        segments: [
            [
                'segmentLengthM' => 10.0,
                'segmentWidthM' => 0.4,
                'segmentHeightM' => 1.0,
                'segmentIncludeReinforcement' => true,
                'segmentUseGlobalRebarParams' => false,
                'segmentLongitudinalBarsCount' => 4,
                'segmentLongitudinalDiameterMm' => 12.0,
                'segmentTransverseDiameterMm' => 8.0,
                'segmentTransverseStepMm' => 0.0,
            ],
        ],
        includeReinforcement: true,
        longitudinalBarsCount: 4,
        longitudinalDiameterMm: 12.0,
        longitudinalReservePercent: 10.0,
        transverseDiameterMm: 8.0,
        transverseStepMm: 300.0,
        transverseReservePercent: 10.0
    )
);

$runSuccessCase(
    'pile_foundation bored with base and grillage works',
    11.60,
    11.60,
    static fn () => $service->calculate(
        calculator: 'pile_foundation',
        mode: 'perimeter',
        includePiles: true,
        pileType: 'bored',
        pilesCount: 10,
        pileShaftDiameterM: 0.3,
        pileShaftHeightM: 2.0,
        includePileBase: true,
        pileBaseDiameterM: 0.5,
        pileBaseHeightM: 0.3,
        includeGrillage: true,
        totalLengthM: 40.0,
        widthM: 0.4,
        heightM: 0.6
    )
);

try {
    $result = $service->calculate(
        calculator: 'pile_foundation',
        mode: 'perimeter',
        includePiles: true,
        pileType: 'bored',
        pilesCount: 10,
        pileShaftDiameterM: 0.3,
        pileShaftHeightM: 2.0,
        includePileBase: true,
        pileBaseDiameterM: 0.5,
        pileBaseHeightM: 0.3,
        includePileReinforcement: true,
        pileReinforcementBarsCount: 4,
        pileReinforcementDiameterMm: 12.0,
        pileReinforcementReservePercent: 10.0,
        includeGrillage: false
    );

    $pileReinforcement = $result->details['piles']['reinforcement'] ?? null;
    $byDiameter = is_array($pileReinforcement) ? ($pileReinforcement['byDiameter'] ?? null) : null;
    $firstDiameter = (is_array($byDiameter) && isset($byDiameter[0]) && is_array($byDiameter[0])) ? $byDiameter[0] : null;
    if (
        is_array($pileReinforcement)
        && is_array($firstDiameter)
        && (float) ($pileReinforcement['massKg'] ?? 0.0) > 0.0
        && $isClose((float) ($firstDiameter['diameterMm'] ?? 0.0), 12.0, $epsilon)
        && $isClose((float) ($firstDiameter['totalLengthM'] ?? 0.0), 80.0, $epsilon)
        && $isClose((float) ($firstDiameter['totalLengthWithReserveM'] ?? 0.0), 88.0, $epsilon)
        && (float) ($firstDiameter['massKg'] ?? 0.0) > 0.0
    ) {
        $print('PASS', 'pile_foundation bored with pile reinforcement returns reinforcement byDiameter');
    } else {
        $print('FAIL', 'pile_foundation bored with pile reinforcement has invalid reinforcement block');
    }
} catch (Throwable $exception) {
    $print('FAIL', 'pile_foundation bored reinforcement case threw exception: ' . $exception->getMessage());
}

$runSuccessCase(
    'pile_foundation bored without base and without grillage works',
    1.41,
    1.41,
    static fn () => $service->calculate(
        calculator: 'pile_foundation',
        mode: 'perimeter',
        includePiles: true,
        pileType: 'bored',
        pilesCount: 10,
        pileShaftDiameterM: 0.3,
        pileShaftHeightM: 2.0,
        includePileBase: false,
        includePileReinforcement: false,
        includeGrillage: false
    )
);

try {
    $result = $service->calculate(
        calculator: 'pile_foundation',
        mode: 'perimeter',
        includePiles: true,
        pileType: 'bored',
        pilesCount: 10,
        pileShaftDiameterM: 0.3,
        pileShaftHeightM: 2.0,
        includePileBase: false,
        includePileReinforcement: false,
        includeGrillage: false
    );

    $hasPileReinforcement = isset($result->details['piles']['reinforcement']);
    if (!$hasPileReinforcement) {
        $print('PASS', 'pile_foundation bored without pile reinforcement does not return piles.reinforcement');
    } else {
        $print('FAIL', 'pile_foundation bored without pile reinforcement should not return piles.reinforcement');
    }
} catch (Throwable $exception) {
    $print('FAIL', 'pile_foundation bored no-reinforcement case threw exception: ' . $exception->getMessage());
}

$runSuccessCase(
    'pile_foundation screw with grillage works',
    9.60,
    9.60,
    static fn () => $service->calculate(
        calculator: 'pile_foundation',
        mode: 'perimeter',
        includePiles: true,
        pileType: 'screw',
        pilesCount: 12,
        includePileReinforcement: true,
        includeGrillage: true,
        totalLengthM: 40.0,
        widthM: 0.4,
        heightM: 0.6
    )
);

try {
    $result = $service->calculate(
        calculator: 'pile_foundation',
        mode: 'perimeter',
        includePiles: true,
        pileType: 'screw',
        pilesCount: 12,
        includePileReinforcement: true,
        includeGrillage: true,
        totalLengthM: 40.0,
        widthM: 0.4,
        heightM: 0.6
    );

    $note = $result->details['piles']['note'] ?? '';
    $pileConcrete = $result->details['piles']['concreteVolumeM3'] ?? null;
    $hasPileReinforcement = isset($result->details['piles']['reinforcement']);
    if (
        is_string($note)
        && $note !== ''
        && $isClose((float) $pileConcrete, 0.0, $epsilon)
        && !$hasPileReinforcement
    ) {
        $print('PASS', 'pile_foundation screw note returned and piles.reinforcement is not included');
    } else {
        $print('FAIL', 'pile_foundation screw response has unexpected piles reinforcement behavior');
    }
} catch (Throwable $exception) {
    $print('FAIL', 'pile_foundation screw response check threw exception: ' . $exception->getMessage());
}

$runErrorCase(
    'pile_foundation bored without shaft dimensions throws',
    static fn () => $service->calculate(
        calculator: 'pile_foundation',
        mode: 'perimeter',
        includePiles: true,
        pileType: 'bored',
        pilesCount: 10,
        includeGrillage: false
    )
);

$runErrorCase(
    'pile_foundation bored with invalid pile reinforcement fields throws',
    static fn () => $service->calculate(
        calculator: 'pile_foundation',
        mode: 'perimeter',
        includePiles: true,
        pileType: 'bored',
        pilesCount: 10,
        pileShaftDiameterM: 0.3,
        pileShaftHeightM: 2.0,
        includePileReinforcement: true,
        pileReinforcementBarsCount: 0,
        pileReinforcementDiameterMm: 12.0,
        pileReinforcementReservePercent: 10.0,
        includeGrillage: false
    )
);

$runErrorCase(
    'pile_foundation with invalid pile type throws',
    static fn () => $service->calculate(
        calculator: 'pile_foundation',
        mode: 'perimeter',
        includePiles: true,
        pileType: 'custom',
        pilesCount: 10,
        includeGrillage: false
    )
);
