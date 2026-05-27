<?php

declare(strict_types=1);

use Brigmaster\Application\EstimateService;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$service = new EstimateService();
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

echo "Calculator: strip_foundation\n";
echo "Mode: {$result->mode}\n";
echo json_encode($result->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
