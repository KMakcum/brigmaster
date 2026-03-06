<?php

declare(strict_types=1);

use Constructly\Application\EstimateService;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$service = new EstimateService();
$result = $service->calculate(
    calculator: 'concrete',
    mode: 'reserve',
    area: 10.0,
    thickness: 0.2,
    subType: 'slab'
);

echo "Calculator: concrete\n";
echo "Mode: {$result->mode}\n";
echo "Calculated volume: {$result->calculatedVolume}\n";
echo "Calculated material amount: {$result->calculatedMaterialAmount}\n";
