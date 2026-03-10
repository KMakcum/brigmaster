<?php

declare(strict_types=1);

namespace Brigmaster\Http\Rest;

use Brigmaster\Application\EstimateService;
use InvalidArgumentException;
use WP_REST_Request;
use WP_REST_Response;

final class EstimateController
{
    private const ALLOWED_CALCULATORS = [
        EstimateService::CALCULATOR_CONCRETE,
        EstimateService::CALCULATOR_BRICK,
        EstimateService::CALCULATOR_SCREED,
        EstimateService::CALCULATOR_DRYWALL,
        EstimateService::CALCULATOR_TILE,
    ];

    private const ALLOWED_MODES = [
        'normative',
        'reserve',
        'beginner',
    ];

    private const CONCRETE_SUBTYPES = [
        'slab',
        'strip',
    ];

    private const BRICK_SUBTYPES = [
        'bricks',
        'mortar',
    ];

    public function __construct(
        private readonly EstimateService $estimateService
    ) {
    }

    public function registerRoutes(): void
    {
        \register_rest_route(
            'brigmaster/v1',
            '/estimate',
            [
                'methods' => 'POST',
                'callback' => [$this, 'handleEstimate'],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function handleEstimate(WP_REST_Request $request): WP_REST_Response
    {
        $calculatorRaw = $request->get_param('calculator');
        $modeRaw = $request->get_param('mode');
        $areaRaw = $request->get_param('area');
        $thicknessRaw = $request->get_param('thickness');
        $subTypeRaw = $request->get_param('subType');
        $tileLengthCmRaw = $request->get_param('tileLengthCm');
        $tileWidthCmRaw = $request->get_param('tileWidthCm');
        $lengthRaw = $request->get_param('length');
        $widthRaw = $request->get_param('width');
        $heightRaw = $request->get_param('height');

        $errors = $this->validateRequest(
            calculator: $calculatorRaw,
            mode: $modeRaw,
            area: $areaRaw,
            thickness: $thicknessRaw,
            subType: $subTypeRaw,
            tileLengthCm: $tileLengthCmRaw,
            tileWidthCm: $tileWidthCmRaw,
            length: $lengthRaw,
            width: $widthRaw,
            height: $heightRaw
        );

        if ($errors !== []) {
            return new WP_REST_Response(
                [
                    'code' => 'validation_error',
                    'message' => 'Validation failed.',
                    'errors' => $errors,
                ],
                400
            );
        }

        try {
            $calculator = (string) $calculatorRaw;
            $mode = (string) $modeRaw;
            $area = (float) $areaRaw;
            $thickness = (float) $thicknessRaw;
            $subType = $this->isNonEmptyString($subTypeRaw) ? (string) $subTypeRaw : null;
            if ($calculator === EstimateService::CALCULATOR_CONCRETE && $subType === null) {
                $subType = 'slab';
            }
            $tileLengthCm = $this->isNumericValue($tileLengthCmRaw) ? (float) $tileLengthCmRaw : null;
            $tileWidthCm = $this->isNumericValue($tileWidthCmRaw) ? (float) $tileWidthCmRaw : null;
            $length = $this->isNumericValue($lengthRaw) ? (float) $lengthRaw : null;
            $width = $this->isNumericValue($widthRaw) ? (float) $widthRaw : null;
            $height = $this->isNumericValue($heightRaw) ? (float) $heightRaw : null;

            $result = $this->estimateService->calculate(
                calculator: $calculator,
                mode: $mode,
                area: $area,
                thickness: $thickness,
                subType: $subType,
                tileLengthCm: $tileLengthCm,
                tileWidthCm: $tileWidthCm,
                length: $length,
                width: $width,
                height: $height
            );

            return new WP_REST_Response(
                [
                    'calculator' => $calculator,
                    'mode' => $result->mode,
                    'calculatedVolume' => $result->calculatedVolume,
                    'calculatedMaterialAmount' => $result->calculatedMaterialAmount,
                ],
                200
            );
        } catch (InvalidArgumentException $exception) {
            return new WP_REST_Response(
                [
                    'code' => 'validation_error',
                    'message' => 'Validation failed.',
                    'errors' => [
                        'general' => [$exception->getMessage()],
                    ],
                ],
                400
            );
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function validateRequest(
        mixed $calculator,
        mixed $mode,
        mixed $area,
        mixed $thickness,
        mixed $subType,
        mixed $tileLengthCm,
        mixed $tileWidthCm,
        mixed $length,
        mixed $width,
        mixed $height
    ): array
    {
        $errors = [];

        if (!$this->isNonEmptyString($calculator)) {
            $errors['calculator'][] = 'The calculator field is required and must be a string.';
        } elseif (!in_array($calculator, self::ALLOWED_CALCULATORS, true)) {
            $errors['calculator'][] = 'The calculator field must be one of: concrete, brick, screed, drywall, tile.';
        }

        if (!$this->isNonEmptyString($mode)) {
            $errors['mode'][] = 'The mode field is required and must be a string.';
        } elseif (!in_array($mode, self::ALLOWED_MODES, true)) {
            $errors['mode'][] = 'The mode field must be one of: normative, reserve, beginner.';
        }

        if ($calculator === EstimateService::CALCULATOR_CONCRETE) {
            $normalizedSubType = $this->isNonEmptyString($subType) ? (string) $subType : 'slab';

            if (!in_array($normalizedSubType, self::CONCRETE_SUBTYPES, true)) {
                $errors['subType'][] = 'The subType field for concrete must be one of: slab, strip.';
            }

            if ($normalizedSubType === 'slab') {
                $this->validatePositiveNumericField($errors, 'area', $area, 'The area field is required and must be numeric for concrete slab.');
                $this->validatePositiveNumericField($errors, 'thickness', $thickness, 'The thickness field is required and must be numeric for concrete slab.');
            }

            if ($normalizedSubType === 'strip') {
                $this->validatePositiveNumericField($errors, 'length', $length, 'The length field is required and must be numeric for concrete strip.');
                $this->validatePositiveNumericField($errors, 'width', $width, 'The width field is required and must be numeric for concrete strip.');
                $this->validatePositiveNumericField($errors, 'height', $height, 'The height field is required and must be numeric for concrete strip.');
            }
        }

        if ($calculator === EstimateService::CALCULATOR_SCREED) {
            $this->validatePositiveNumericField($errors, 'area', $area, 'The area field is required and must be numeric for screed.');
            $this->validatePositiveNumericField($errors, 'thickness', $thickness, 'The thickness field is required and must be numeric for screed.');
        }

        if ($calculator === EstimateService::CALCULATOR_BRICK) {
            $this->validatePositiveNumericField($errors, 'area', $area, 'The area field is required and must be numeric for brick.');

            if (!$this->isNonEmptyString($subType)) {
                $errors['subType'][] = 'The subType field is required for brick.';
            } elseif (!in_array($subType, self::BRICK_SUBTYPES, true)) {
                $errors['subType'][] = 'The subType field for brick must be one of: bricks, mortar.';
            }
        }

        if ($calculator === EstimateService::CALCULATOR_DRYWALL) {
            $this->validatePositiveNumericField($errors, 'area', $area, 'The area field is required and must be numeric for drywall.');
        }

        if ($calculator === EstimateService::CALCULATOR_TILE) {
            $this->validatePositiveNumericField($errors, 'area', $area, 'The area field is required and must be numeric for tile.');
            $this->validatePositiveNumericField($errors, 'tileLengthCm', $tileLengthCm, 'The tileLengthCm field is required and must be numeric for tile.');
            $this->validatePositiveNumericField($errors, 'tileWidthCm', $tileWidthCm, 'The tileWidthCm field is required and must be numeric for tile.');
        }

        return $errors;
    }

    private function isNonEmptyString(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '';
    }

    private function isNumericValue(mixed $value): bool
    {
        return (is_string($value) && trim($value) !== '' && is_numeric($value)) || is_int($value) || is_float($value);
    }

    /**
     * @param array<string, array<int, string>> $errors
     */
    private function validatePositiveNumericField(array &$errors, string $field, mixed $value, string $requiredMessage): void
    {
        if (!$this->isNumericValue($value)) {
            $errors[$field][] = $requiredMessage;

            return;
        }

        if ((float) $value <= 0) {
            $errors[$field][] = sprintf('The %s field must be greater than 0.', $field);
        }
    }
}
