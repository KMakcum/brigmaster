<?php

declare(strict_types=1);

return [
    'slug' => 'tile',
    'name' => 'Tile Calculator',
    'modes' => [
        'dimensions',
        'area',
    ],
    'requires' => [
        'tileTarget',
        'tileLengthMm',
        'tileWidthMm',
    ],
];
