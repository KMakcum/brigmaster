<?php

declare(strict_types=1);

namespace Brigmaster\Domain\DTO;

use InvalidArgumentException;

final class EstimateInput
{
    public const MODE_NORMATIVE = 'normative';
    public const MODE_RESERVE = 'reserve';
    public const MODE_BEGINNER = 'beginner';

    private const ALLOWED_MODES = [
        self::MODE_NORMATIVE,
        self::MODE_RESERVE,
        self::MODE_BEGINNER,
    ];

    public function __construct(
        public readonly string $mode,
        public readonly ?float $area = null,
        public readonly ?float $thickness = null,
        public readonly ?string $subType = null,
        public readonly ?float $tileLengthCm = null,
        public readonly ?float $tileWidthCm = null,
        public readonly ?float $length = null,
        public readonly ?float $width = null,
        public readonly ?float $height = null
    ) {
        if ($this->mode === '') {
            throw new InvalidArgumentException('Field "mode" is required.');
        }

        if (!in_array($this->mode, self::ALLOWED_MODES, true)) {
            throw new InvalidArgumentException('Field "mode" must be one of: normative, reserve, beginner.');
        }

    }
}
