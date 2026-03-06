<?php

declare(strict_types=1);

return [
    'slug' => 'tile',
    'name' => 'Tile Calculator',
    'modes' => [
        'normative',
        'reserve',
        'beginner',
    ],
    'requires' => [
        'tileLengthCm',
        'tileWidthCm',
    ],
];
