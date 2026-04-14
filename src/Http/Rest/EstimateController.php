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
        EstimateService::CALCULATOR_BRICK,
        EstimateService::CALCULATOR_SCREED,
        EstimateService::CALCULATOR_DRYWALL,
        EstimateService::CALCULATOR_TILE,
        EstimateService::CALCULATOR_SLAB_FOUNDATION,
        EstimateService::CALCULATOR_STRIP_FOUNDATION,
        EstimateService::CALCULATOR_PILE_FOUNDATION,
    ];

    private const ALLOWED_MODES = [
        'normative',
        'reserve',
        'beginner',
    ];

    private const ALLOWED_SLAB_FOUNDATION_MODES = [
        'dimensions',
        'area',
    ];

    private const ALLOWED_STRIP_FOUNDATION_MODES = [
        'perimeter',
        'house',
        'segments',
    ];

    private const ALLOWED_PILE_FOUNDATION_MODES = [
        'perimeter',
        'house',
        'segments',
    ];

    private const BRICK_SUBTYPES = [
        'bricks',
        'mortar',
    ];

    private const ALLOWED_MIXTURE_TYPES_FOUNDATION = [
        'ready',
        'self_mix',
    ];

    private const ALLOWED_MIXTURE_TYPES_SCREED = [
        'ready',
        'dry_ready',
        'self_mix',
    ];

    private const ALLOWED_PURCHASE_UNITS = [
        'bag',
        'tonne',
    ];

    /** @var array<int> */
    private const ALLOWED_REBAR_LAYERS = [1, 2];

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
        $includeReinforcementRaw = $request->get_param('includeReinforcement');
        $includeFormworkRaw = $request->get_param('includeFormwork');
        $rebarDiameterMmRaw = $request->get_param('rebarDiameterMm');
        $rebarStepMmRaw = $request->get_param('rebarStepMm');
        $rebarLayersRaw = $request->get_param('rebarLayers');
        $rebarReservePercentRaw = $request->get_param('rebarReservePercent');
        $formworkHeightMRaw = $request->get_param('formworkHeightM');
        $formworkReservePercentRaw = $request->get_param('formworkReservePercent');
        $totalLengthMRaw = $request->get_param('totalLengthM');
        $widthMRaw = $request->get_param('widthM');
        $heightMRaw = $request->get_param('heightM');
        $houseLengthMRaw = $request->get_param('houseLengthM');
        $houseWidthMRaw = $request->get_param('houseWidthM');
        $segmentsRaw = $request->get_param('segments');
        $longitudinalBarsCountRaw = $request->get_param('longitudinalBarsCount');
        $longitudinalDiameterMmRaw = $request->get_param('longitudinalDiameterMm');
        $longitudinalReservePercentRaw = $request->get_param('longitudinalReservePercent');
        $transverseDiameterMmRaw = $request->get_param('transverseDiameterMm');
        $transverseStepMmRaw = $request->get_param('transverseStepMm');
        $transverseReservePercentRaw = $request->get_param('transverseReservePercent');
        $pileTypeRaw = $request->get_param('pileType');
        $includePilesRaw = $request->get_param('includePiles');
        $pilesCountRaw = $request->get_param('pilesCount');
        $pileShaftDiameterMRaw = $request->get_param('pileShaftDiameterM');
        $pileShaftHeightMRaw = $request->get_param('pileShaftHeightM');
        $includePileBaseRaw = $request->get_param('includePileBase');
        $pileBaseDiameterMRaw = $request->get_param('pileBaseDiameterM');
        $pileBaseHeightMRaw = $request->get_param('pileBaseHeightM');
        $includeGrillageRaw = $request->get_param('includeGrillage');
        $includePileReinforcementRaw = $request->get_param('includePileReinforcement');
        $pileReinforcementBarsCountRaw = $request->get_param('pileReinforcementBarsCount');
        $pileReinforcementDiameterMmRaw = $request->get_param('pileReinforcementDiameterMm');
        $pileReinforcementReservePercentRaw = $request->get_param('pileReinforcementReservePercent');
        $mixtureRaw = $request->get_param('mixture');
        $useUnifiedConcreteMixtureSettingsRaw = $request->get_param('useUnifiedConcreteMixtureSettings');
        $pileMixtureRaw = $request->get_param('pileMixture');
        $grillageMixtureRaw = $request->get_param('grillageMixture');

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
            height: $heightRaw,
            includeReinforcement: $includeReinforcementRaw,
            includeFormwork: $includeFormworkRaw,
            rebarDiameterMm: $rebarDiameterMmRaw,
            rebarStepMm: $rebarStepMmRaw,
            rebarLayers: $rebarLayersRaw,
            rebarReservePercent: $rebarReservePercentRaw,
            formworkHeightM: $formworkHeightMRaw,
            formworkReservePercent: $formworkReservePercentRaw,
            totalLengthM: $totalLengthMRaw,
            widthM: $widthMRaw,
            heightM: $heightMRaw,
            houseLengthM: $houseLengthMRaw,
            houseWidthM: $houseWidthMRaw,
            segments: $segmentsRaw,
            longitudinalBarsCount: $longitudinalBarsCountRaw,
            longitudinalDiameterMm: $longitudinalDiameterMmRaw,
            longitudinalReservePercent: $longitudinalReservePercentRaw,
            transverseDiameterMm: $transverseDiameterMmRaw,
            transverseStepMm: $transverseStepMmRaw,
            transverseReservePercent: $transverseReservePercentRaw,
            pileType: $pileTypeRaw,
            includePiles: $includePilesRaw,
            pilesCount: $pilesCountRaw,
            pileShaftDiameterM: $pileShaftDiameterMRaw,
            pileShaftHeightM: $pileShaftHeightMRaw,
            includePileBase: $includePileBaseRaw,
            pileBaseDiameterM: $pileBaseDiameterMRaw,
            pileBaseHeightM: $pileBaseHeightMRaw,
            includeGrillage: $includeGrillageRaw,
            includePileReinforcement: $includePileReinforcementRaw,
            pileReinforcementBarsCount: $pileReinforcementBarsCountRaw,
            pileReinforcementDiameterMm: $pileReinforcementDiameterMmRaw,
            pileReinforcementReservePercent: $pileReinforcementReservePercentRaw,
            mixture: $mixtureRaw,
            useUnifiedConcreteMixtureSettings: $useUnifiedConcreteMixtureSettingsRaw,
            pileMixture: $pileMixtureRaw,
            grillageMixture: $grillageMixtureRaw
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
            $area = $this->isNumericValue($areaRaw) ? (float) $areaRaw : null;
            $thickness = $this->isNumericValue($thicknessRaw) ? (float) $thicknessRaw : null;
            $subType = $this->isNonEmptyString($subTypeRaw) ? (string) $subTypeRaw : null;
            $tileLengthCm = $this->isNumericValue($tileLengthCmRaw) ? (float) $tileLengthCmRaw : null;
            $tileWidthCm = $this->isNumericValue($tileWidthCmRaw) ? (float) $tileWidthCmRaw : null;
            $length = $this->isNumericValue($lengthRaw) ? (float) $lengthRaw : null;
            $width = $this->isNumericValue($widthRaw) ? (float) $widthRaw : null;
            $height = $this->isNumericValue($heightRaw) ? (float) $heightRaw : null;
            $includeReinforcement = is_bool($includeReinforcementRaw) ? $includeReinforcementRaw : null;
            $includeFormwork = is_bool($includeFormworkRaw) ? $includeFormworkRaw : null;
            $rebarDiameterMm = $this->isNumericValue($rebarDiameterMmRaw) ? (float) $rebarDiameterMmRaw : null;
            $rebarStepMm = $this->isNumericValue($rebarStepMmRaw) ? (float) $rebarStepMmRaw : null;
            $rebarLayers = $this->isNumericValue($rebarLayersRaw) ? (int) $rebarLayersRaw : null;
            $rebarReservePercent = $this->isNumericValue($rebarReservePercentRaw) ? (float) $rebarReservePercentRaw : null;
            $formworkHeightM = $this->isNumericValue($formworkHeightMRaw) ? (float) $formworkHeightMRaw : null;
            $formworkReservePercent = $this->isNumericValue($formworkReservePercentRaw) ? (float) $formworkReservePercentRaw : null;
            $totalLengthM = $this->isNumericValue($totalLengthMRaw) ? (float) $totalLengthMRaw : null;
            $widthM = $this->isNumericValue($widthMRaw) ? (float) $widthMRaw : null;
            $heightM = $this->isNumericValue($heightMRaw) ? (float) $heightMRaw : null;
            $houseLengthM = $this->isNumericValue($houseLengthMRaw) ? (float) $houseLengthMRaw : null;
            $houseWidthM = $this->isNumericValue($houseWidthMRaw) ? (float) $houseWidthMRaw : null;
            $segments = is_array($segmentsRaw) ? $segmentsRaw : null;
            $longitudinalBarsCount = $this->isNumericValue($longitudinalBarsCountRaw) ? (int) $longitudinalBarsCountRaw : null;
            $longitudinalDiameterMm = $this->isNumericValue($longitudinalDiameterMmRaw) ? (float) $longitudinalDiameterMmRaw : null;
            $longitudinalReservePercent = $this->isNumericValue($longitudinalReservePercentRaw) ? (float) $longitudinalReservePercentRaw : null;
            $transverseDiameterMm = $this->isNumericValue($transverseDiameterMmRaw) ? (float) $transverseDiameterMmRaw : null;
            $transverseStepMm = $this->isNumericValue($transverseStepMmRaw) ? (float) $transverseStepMmRaw : null;
            $transverseReservePercent = $this->isNumericValue($transverseReservePercentRaw) ? (float) $transverseReservePercentRaw : null;
            $pileType = $this->isNonEmptyString($pileTypeRaw) ? (string) $pileTypeRaw : null;
            $includePiles = is_bool($includePilesRaw) ? $includePilesRaw : null;
            $pilesCount = $this->isNumericValue($pilesCountRaw) ? (int) $pilesCountRaw : null;
            $pileShaftDiameterM = $this->isNumericValue($pileShaftDiameterMRaw) ? (float) $pileShaftDiameterMRaw : null;
            $pileShaftHeightM = $this->isNumericValue($pileShaftHeightMRaw) ? (float) $pileShaftHeightMRaw : null;
            $includePileBase = is_bool($includePileBaseRaw) ? $includePileBaseRaw : null;
            $pileBaseDiameterM = $this->isNumericValue($pileBaseDiameterMRaw) ? (float) $pileBaseDiameterMRaw : null;
            $pileBaseHeightM = $this->isNumericValue($pileBaseHeightMRaw) ? (float) $pileBaseHeightMRaw : null;
            $includeGrillage = is_bool($includeGrillageRaw) ? $includeGrillageRaw : null;
            $includePileReinforcement = is_bool($includePileReinforcementRaw) ? $includePileReinforcementRaw : null;
            $pileReinforcementBarsCount = $this->isNumericValue($pileReinforcementBarsCountRaw) ? (int) $pileReinforcementBarsCountRaw : null;
            $pileReinforcementDiameterMm = $this->isNumericValue($pileReinforcementDiameterMmRaw) ? (float) $pileReinforcementDiameterMmRaw : null;
            $pileReinforcementReservePercent = $this->isNumericValue($pileReinforcementReservePercentRaw) ? (float) $pileReinforcementReservePercentRaw : null;
            $mixture = is_array($mixtureRaw) ? $mixtureRaw : null;
            $useUnifiedConcreteMixtureSettings = is_bool($useUnifiedConcreteMixtureSettingsRaw) ? $useUnifiedConcreteMixtureSettingsRaw : null;
            $pileMixture = is_array($pileMixtureRaw) ? $pileMixtureRaw : null;
            $grillageMixture = is_array($grillageMixtureRaw) ? $grillageMixtureRaw : null;

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
                height: $height,
                includeReinforcement: $includeReinforcement,
                includeFormwork: $includeFormwork,
                rebarDiameterMm: $rebarDiameterMm,
                rebarStepMm: $rebarStepMm,
                rebarLayers: $rebarLayers,
                rebarReservePercent: $rebarReservePercent,
                formworkHeightM: $formworkHeightM,
                formworkReservePercent: $formworkReservePercent,
                totalLengthM: $totalLengthM,
                widthM: $widthM,
                heightM: $heightM,
                houseLengthM: $houseLengthM,
                houseWidthM: $houseWidthM,
                segments: $segments,
                longitudinalBarsCount: $longitudinalBarsCount,
                longitudinalDiameterMm: $longitudinalDiameterMm,
                longitudinalReservePercent: $longitudinalReservePercent,
                transverseDiameterMm: $transverseDiameterMm,
                transverseStepMm: $transverseStepMm,
                transverseReservePercent: $transverseReservePercent,
                pileType: $pileType,
                includePiles: $includePiles,
                pilesCount: $pilesCount,
                pileShaftDiameterM: $pileShaftDiameterM,
                pileShaftHeightM: $pileShaftHeightM,
                includePileBase: $includePileBase,
                pileBaseDiameterM: $pileBaseDiameterM,
                pileBaseHeightM: $pileBaseHeightM,
                includeGrillage: $includeGrillage,
                includePileReinforcement: $includePileReinforcement,
                pileReinforcementBarsCount: $pileReinforcementBarsCount,
                pileReinforcementDiameterMm: $pileReinforcementDiameterMm,
                pileReinforcementReservePercent: $pileReinforcementReservePercent,
                mixture: $mixture,
                useUnifiedConcreteMixtureSettings: $useUnifiedConcreteMixtureSettings,
                pileMixture: $pileMixture,
                grillageMixture: $grillageMixture
            );

            $response = [
                'calculator' => $calculator,
                'mode' => $result->mode,
            ];

            if (in_array($calculator, [EstimateService::CALCULATOR_SLAB_FOUNDATION, EstimateService::CALCULATOR_STRIP_FOUNDATION, EstimateService::CALCULATOR_PILE_FOUNDATION], true)) {
                $response = array_merge($response, $result->details);
            } else {
                $response['calculatedVolume'] = $result->calculatedVolume;
                $response['calculatedMaterialAmount'] = $result->calculatedMaterialAmount;

                if ($result->details !== []) {
                    $response = array_merge($response, $result->details);
                }
            }

            return new WP_REST_Response($response, 200);
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
        mixed $height,
        mixed $includeReinforcement,
        mixed $includeFormwork,
        mixed $rebarDiameterMm,
        mixed $rebarStepMm,
        mixed $rebarLayers,
        mixed $rebarReservePercent,
        mixed $formworkHeightM,
        mixed $formworkReservePercent,
        mixed $totalLengthM,
        mixed $widthM,
        mixed $heightM,
        mixed $houseLengthM,
        mixed $houseWidthM,
        mixed $segments,
        mixed $longitudinalBarsCount,
        mixed $longitudinalDiameterMm,
        mixed $longitudinalReservePercent,
        mixed $transverseDiameterMm,
        mixed $transverseStepMm,
        mixed $transverseReservePercent,
        mixed $pileType,
        mixed $includePiles,
        mixed $pilesCount,
        mixed $pileShaftDiameterM,
        mixed $pileShaftHeightM,
        mixed $includePileBase,
        mixed $pileBaseDiameterM,
        mixed $pileBaseHeightM,
        mixed $includeGrillage,
        mixed $includePileReinforcement,
        mixed $pileReinforcementBarsCount,
        mixed $pileReinforcementDiameterMm,
        mixed $pileReinforcementReservePercent,
        mixed $mixture,
        mixed $useUnifiedConcreteMixtureSettings,
        mixed $pileMixture,
        mixed $grillageMixture
    ): array
    {
        $errors = [];

        if (!$this->isNonEmptyString($calculator)) {
            $errors['calculator'][] = 'The calculator field is required and must be a string.';
        } elseif (!in_array($calculator, self::ALLOWED_CALCULATORS, true)) {
            $errors['calculator'][] = 'The calculator field must be one of: brick, screed, drywall, tile, slab_foundation, strip_foundation, pile_foundation.';
        }

        if (!$this->isNonEmptyString($mode)) {
            $errors['mode'][] = 'The mode field is required and must be a string.';
        } elseif ($calculator === EstimateService::CALCULATOR_SLAB_FOUNDATION || $calculator === EstimateService::CALCULATOR_SCREED) {
            if (!in_array($mode, self::ALLOWED_SLAB_FOUNDATION_MODES, true)) {
                $errors['mode'][] = sprintf('The mode field for %s must be one of: dimensions, area.', (string) $calculator);
            }
        } elseif ($calculator === EstimateService::CALCULATOR_STRIP_FOUNDATION) {
            if (!in_array($mode, self::ALLOWED_STRIP_FOUNDATION_MODES, true)) {
                $errors['mode'][] = 'The mode field for strip_foundation must be one of: perimeter, house, segments.';
            }
        } elseif ($calculator === EstimateService::CALCULATOR_PILE_FOUNDATION) {
            if (!in_array($mode, self::ALLOWED_PILE_FOUNDATION_MODES, true)) {
                $errors['mode'][] = 'The mode field for pile_foundation must be one of: perimeter, house, segments.';
            }
        } elseif (!in_array($mode, self::ALLOWED_MODES, true)) {
            $errors['mode'][] = 'The mode field must be one of: normative, reserve, beginner.';
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

        if ($calculator === EstimateService::CALCULATOR_SLAB_FOUNDATION) {
            if ($mode === 'dimensions') {
                $this->validatePositiveNumericField($errors, 'length', $length, 'The length field is required and must be numeric for slab_foundation in dimensions mode.');
                $this->validatePositiveNumericField($errors, 'width', $width, 'The width field is required and must be numeric for slab_foundation in dimensions mode.');
                $this->validatePositiveNumericField($errors, 'height', $height, 'The height field is required and must be numeric for slab_foundation.');
            }

            if ($mode === 'area') {
                $this->validatePositiveNumericField($errors, 'area', $area, 'The area field is required and must be numeric for slab_foundation in area mode.');
                $this->validatePositiveNumericField($errors, 'height', $height, 'The height field is required and must be numeric for slab_foundation.');
            }

            $this->validateStrictBooleanField($errors, 'includeReinforcement', $includeReinforcement);
            $this->validateStrictBooleanField($errors, 'includeFormwork', $includeFormwork);

            $includeReinforcementEnabled = is_bool($includeReinforcement) && $includeReinforcement;
            $includeFormworkEnabled = is_bool($includeFormwork) && $includeFormwork;
            $needsPerimeter = $mode === 'area' && ($includeReinforcementEnabled || $includeFormworkEnabled);

            if ($needsPerimeter) {
                if (!$this->isNumericValue($length) || (float) $length <= 0) {
                    $errors['length'][] = 'The length field is required and must be greater than 0 when includeReinforcement/includeFormwork is true and mode is area.';
                }

                if (!$this->isNumericValue($width) || (float) $width <= 0) {
                    $errors['width'][] = 'The width field is required and must be greater than 0 when includeReinforcement/includeFormwork is true and mode is area.';
                }
            }

            if ($includeReinforcementEnabled) {
                $this->validateOptionalPositiveNumericField($errors, 'rebarDiameterMm', $rebarDiameterMm);
                $this->validateOptionalPositiveNumericField($errors, 'rebarStepMm', $rebarStepMm);
                $this->validateOptionalPositiveNumericField($errors, 'rebarReservePercent', $rebarReservePercent);

                if ($rebarLayers !== null && !$this->isValidRebarLayers($rebarLayers)) {
                    $errors['rebarLayers'][] = 'The rebarLayers field must be one of: 1, 2.';
                }
            }

            if ($includeFormworkEnabled) {
                $this->validateOptionalPositiveNumericField($errors, 'formworkHeightM', $formworkHeightM);
                $this->validateOptionalPositiveNumericField($errors, 'formworkReservePercent', $formworkReservePercent);
            }

            $this->validateConcreteMixtureConfig($errors, 'mixture', $mixture, self::ALLOWED_MIXTURE_TYPES_FOUNDATION, true);
        }

        if ($calculator === EstimateService::CALCULATOR_SCREED) {
            if ($mode === 'dimensions') {
                $this->validatePositiveNumericField($errors, 'length', $length, 'The length field is required and must be numeric for screed in dimensions mode.');
                $this->validatePositiveNumericField($errors, 'width', $width, 'The width field is required and must be numeric for screed in dimensions mode.');
                $this->validatePositiveNumericField($errors, 'height', $height, 'The height field is required and must be numeric for screed.');
            }

            if ($mode === 'area') {
                $this->validatePositiveNumericField($errors, 'area', $area, 'The area field is required and must be numeric for screed in area mode.');
                $this->validatePositiveNumericField($errors, 'height', $height, 'The height field is required and must be numeric for screed.');
            }

            $this->validateStrictBooleanField($errors, 'includeReinforcement', $includeReinforcement);
            $includeReinforcementEnabled = is_bool($includeReinforcement) && $includeReinforcement;
            $needsGeometry = $mode === 'area' && $includeReinforcementEnabled;

            if ($needsGeometry) {
                if (!$this->isNumericValue($length) || (float) $length <= 0) {
                    $errors['length'][] = 'The length field is required and must be greater than 0 when includeReinforcement is true and mode is area.';
                }

                if (!$this->isNumericValue($width) || (float) $width <= 0) {
                    $errors['width'][] = 'The width field is required and must be greater than 0 when includeReinforcement is true and mode is area.';
                }
            }

            if ($includeReinforcementEnabled) {
                $this->validateOptionalPositiveNumericField($errors, 'rebarDiameterMm', $rebarDiameterMm);
                $this->validateOptionalPositiveNumericField($errors, 'rebarStepMm', $rebarStepMm);
                $this->validateOptionalPositiveNumericField($errors, 'rebarReservePercent', $rebarReservePercent);

                if ($rebarLayers !== null && !$this->isValidRebarLayers($rebarLayers)) {
                    $errors['rebarLayers'][] = 'The rebarLayers field must be one of: 1, 2.';
                }
            }

            $this->validateConcreteMixtureConfig($errors, 'mixture', $mixture, self::ALLOWED_MIXTURE_TYPES_SCREED, false);
        }

        if ($calculator === EstimateService::CALCULATOR_STRIP_FOUNDATION) {
            $this->validateStripFoundationPayload(
                errors: $errors,
                mode: $mode,
                totalLengthM: $totalLengthM,
                widthM: $widthM,
                heightM: $heightM,
                houseLengthM: $houseLengthM,
                houseWidthM: $houseWidthM,
                segments: $segments,
                includeReinforcement: $includeReinforcement,
                includeFormwork: $includeFormwork,
                longitudinalBarsCount: $longitudinalBarsCount,
                longitudinalDiameterMm: $longitudinalDiameterMm,
                longitudinalReservePercent: $longitudinalReservePercent,
                transverseDiameterMm: $transverseDiameterMm,
                transverseStepMm: $transverseStepMm,
                transverseReservePercent: $transverseReservePercent,
                formworkHeightM: $formworkHeightM,
                formworkReservePercent: $formworkReservePercent,
                contextLabel: 'strip_foundation'
            );

            $this->validateConcreteMixtureConfig($errors, 'mixture', $mixture, self::ALLOWED_MIXTURE_TYPES_FOUNDATION, true);
        }

        if ($calculator === EstimateService::CALCULATOR_PILE_FOUNDATION) {
            $this->validateStrictBooleanField($errors, 'includePiles', $includePiles);
            $this->validateStrictBooleanField($errors, 'includePileBase', $includePileBase);
            $this->validateStrictBooleanField($errors, 'includeGrillage', $includeGrillage);
            $this->validateStrictBooleanField($errors, 'includePileReinforcement', $includePileReinforcement);
            $this->validateStrictBooleanField($errors, 'useUnifiedConcreteMixtureSettings', $useUnifiedConcreteMixtureSettings);

            $includePilesEnabled = !is_bool($includePiles) || $includePiles;
            if ($includePilesEnabled) {
                $pileTypeNormalized = $this->isNonEmptyString($pileType) ? (string) $pileType : 'bored';
                if (!in_array($pileTypeNormalized, ['bored', 'screw', 'driven'], true)) {
                    $errors['pileType'][] = 'The pileType field must be one of: bored, screw, driven.';
                }

                $this->validatePositiveNumericField($errors, 'pilesCount', $pilesCount, 'The pilesCount field is required and must be numeric when includePiles is true.');
                if ($this->isNumericValue($pilesCount) && !$this->isIntegerNumber($pilesCount)) {
                    $errors['pilesCount'][] = 'The pilesCount field must be an integer.';
                }

                if ($pileTypeNormalized === 'bored') {
                    $this->validatePositiveNumericField($errors, 'pileShaftDiameterM', $pileShaftDiameterM, 'The pileShaftDiameterM field is required and must be numeric for bored piles.');
                    $this->validatePositiveNumericField($errors, 'pileShaftHeightM', $pileShaftHeightM, 'The pileShaftHeightM field is required and must be numeric for bored piles.');

                    $includePileBaseEnabled = !is_bool($includePileBase) || $includePileBase;
                    if ($includePileBaseEnabled) {
                        $this->validatePositiveNumericField($errors, 'pileBaseDiameterM', $pileBaseDiameterM, 'The pileBaseDiameterM field is required and must be numeric when includePileBase is true.');
                        $this->validatePositiveNumericField($errors, 'pileBaseHeightM', $pileBaseHeightM, 'The pileBaseHeightM field is required and must be numeric when includePileBase is true.');
                    }

                    $pileReinforcementEnabled = is_bool($includePileReinforcement) && $includePileReinforcement;
                    if ($pileReinforcementEnabled) {
                        $this->validatePositiveNumericField($errors, 'pileReinforcementBarsCount', $pileReinforcementBarsCount, 'The pileReinforcementBarsCount field is required and must be numeric when includePileReinforcement is true.');
                        if ($this->isNumericValue($pileReinforcementBarsCount) && !$this->isIntegerNumber($pileReinforcementBarsCount)) {
                            $errors['pileReinforcementBarsCount'][] = 'The pileReinforcementBarsCount field must be an integer.';
                        }

                        $this->validatePositiveNumericField($errors, 'pileReinforcementDiameterMm', $pileReinforcementDiameterMm, 'The pileReinforcementDiameterMm field is required and must be numeric when includePileReinforcement is true.');
                        $this->validatePositiveNumericField($errors, 'pileReinforcementReservePercent', $pileReinforcementReservePercent, 'The pileReinforcementReservePercent field is required and must be numeric when includePileReinforcement is true.');
                    }
                }
            }

            $includeGrillageEnabled = !is_bool($includeGrillage) || $includeGrillage;
            if ($includeGrillageEnabled) {
                $this->validateStripFoundationPayload(
                    errors: $errors,
                    mode: $mode,
                    totalLengthM: $totalLengthM,
                    widthM: $widthM,
                    heightM: $heightM,
                    houseLengthM: $houseLengthM,
                    houseWidthM: $houseWidthM,
                    segments: $segments,
                    includeReinforcement: $includeReinforcement,
                    includeFormwork: $includeFormwork,
                    longitudinalBarsCount: $longitudinalBarsCount,
                    longitudinalDiameterMm: $longitudinalDiameterMm,
                    longitudinalReservePercent: $longitudinalReservePercent,
                    transverseDiameterMm: $transverseDiameterMm,
                    transverseStepMm: $transverseStepMm,
                    transverseReservePercent: $transverseReservePercent,
                    formworkHeightM: $formworkHeightM,
                    formworkReservePercent: $formworkReservePercent,
                    contextLabel: 'pile_foundation'
                );
            }

            $useUnifiedMixture = !is_bool($useUnifiedConcreteMixtureSettings) || $useUnifiedConcreteMixtureSettings;
            $pileHasConcrete = $includePilesEnabled && $this->isBoredPileType($pileType);
            $grillageHasConcrete = $includeGrillageEnabled;
            $requiresAnyMixture = $pileHasConcrete || $grillageHasConcrete;

            if ($useUnifiedMixture && $requiresAnyMixture) {
                $this->validateConcreteMixtureConfig($errors, 'mixture', $mixture, self::ALLOWED_MIXTURE_TYPES_FOUNDATION, true);
            } else {
                if ($pileHasConcrete) {
                    $this->validateConcreteMixtureConfig($errors, 'pileMixture', $pileMixture, self::ALLOWED_MIXTURE_TYPES_FOUNDATION, true);
                }

                if ($grillageHasConcrete) {
                    $this->validateConcreteMixtureConfig($errors, 'grillageMixture', $grillageMixture, self::ALLOWED_MIXTURE_TYPES_FOUNDATION, true);
                }
            }
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

    /**
     * @param array<string, array<int, string>> $errors
     */
    private function validateOptionalPositiveNumericField(array &$errors, string $field, mixed $value): void
    {
        if ($value === null) {
            return;
        }

        if (!$this->isNumericValue($value)) {
            $errors[$field][] = sprintf('The %s field must be numeric.', $field);
            return;
        }

        if ((float) $value <= 0) {
            $errors[$field][] = sprintf('The %s field must be greater than 0.', $field);
        }
    }

    /**
     * @param array<string, array<int, string>> $errors
     */
    private function validateStrictBooleanField(array &$errors, string $field, mixed $value): void
    {
        if ($value === null) {
            return;
        }

        if (!is_bool($value)) {
            $errors[$field][] = sprintf('The %s field must be a boolean.', $field);
        }
    }

    /**
     * @param array<string, array<int, string>> $errors
     */
    private function validateStripFoundationPayload(
        array &$errors,
        mixed $mode,
        mixed $totalLengthM,
        mixed $widthM,
        mixed $heightM,
        mixed $houseLengthM,
        mixed $houseWidthM,
        mixed $segments,
        mixed $includeReinforcement,
        mixed $includeFormwork,
        mixed $longitudinalBarsCount,
        mixed $longitudinalDiameterMm,
        mixed $longitudinalReservePercent,
        mixed $transverseDiameterMm,
        mixed $transverseStepMm,
        mixed $transverseReservePercent,
        mixed $formworkHeightM,
        mixed $formworkReservePercent,
        string $contextLabel
    ): void {
        if ($mode === 'perimeter') {
            $this->validatePositiveNumericField($errors, 'totalLengthM', $totalLengthM, sprintf('The totalLengthM field is required and must be numeric for %s in perimeter mode.', $contextLabel));
            $this->validatePositiveNumericField($errors, 'widthM', $widthM, sprintf('The widthM field is required and must be numeric for %s in perimeter mode.', $contextLabel));
            $this->validatePositiveNumericField($errors, 'heightM', $heightM, sprintf('The heightM field is required and must be numeric for %s in perimeter mode.', $contextLabel));
        }

        if ($mode === 'house') {
            $this->validatePositiveNumericField($errors, 'houseLengthM', $houseLengthM, sprintf('The houseLengthM field is required and must be numeric for %s in house mode.', $contextLabel));
            $this->validatePositiveNumericField($errors, 'houseWidthM', $houseWidthM, sprintf('The houseWidthM field is required and must be numeric for %s in house mode.', $contextLabel));
            $this->validatePositiveNumericField($errors, 'widthM', $widthM, sprintf('The widthM field is required and must be numeric for %s in house mode.', $contextLabel));
            $this->validatePositiveNumericField($errors, 'heightM', $heightM, sprintf('The heightM field is required and must be numeric for %s in house mode.', $contextLabel));
        }

        if ($mode === 'segments') {
            $this->validateSegmentsForStripFoundation($errors, $segments);
        }

        $this->validateStrictBooleanField($errors, 'includeReinforcement', $includeReinforcement);
        $this->validateStrictBooleanField($errors, 'includeFormwork', $includeFormwork);

        $includeReinforcementEnabled = is_bool($includeReinforcement) && $includeReinforcement;
        if ($includeReinforcementEnabled) {
            $this->validatePositiveNumericField($errors, 'longitudinalBarsCount', $longitudinalBarsCount, 'The longitudinalBarsCount field is required and must be numeric when includeReinforcement is true.');
            $this->validatePositiveNumericField($errors, 'longitudinalDiameterMm', $longitudinalDiameterMm, 'The longitudinalDiameterMm field is required and must be numeric when includeReinforcement is true.');
            $this->validatePositiveNumericField($errors, 'longitudinalReservePercent', $longitudinalReservePercent, 'The longitudinalReservePercent field is required and must be numeric when includeReinforcement is true.');
            $this->validatePositiveNumericField($errors, 'transverseDiameterMm', $transverseDiameterMm, 'The transverseDiameterMm field is required and must be numeric when includeReinforcement is true.');
            $this->validatePositiveNumericField($errors, 'transverseStepMm', $transverseStepMm, 'The transverseStepMm field is required and must be numeric when includeReinforcement is true.');
            $this->validatePositiveNumericField($errors, 'transverseReservePercent', $transverseReservePercent, 'The transverseReservePercent field is required and must be numeric when includeReinforcement is true.');

            if ($this->isNumericValue($longitudinalBarsCount) && !$this->isIntegerNumber($longitudinalBarsCount)) {
                $errors['longitudinalBarsCount'][] = 'The longitudinalBarsCount field must be an integer.';
            }

            if ($mode === 'segments' && is_array($segments)) {
                $this->validateStripReinforcementSegments($errors, $segments);
            }
        }

        $includeFormworkEnabled = is_bool($includeFormwork) && $includeFormwork;
        if ($includeFormworkEnabled) {
            $this->validatePositiveNumericField($errors, 'formworkHeightM', $formworkHeightM, 'The formworkHeightM field is required and must be numeric when includeFormwork is true.');
            $this->validatePositiveNumericField($errors, 'formworkReservePercent', $formworkReservePercent, 'The formworkReservePercent field is required and must be numeric when includeFormwork is true.');

            if ($mode === 'segments' && is_array($segments)) {
                $this->validateStripFormworkSegments($errors, $segments);
            }
        }
    }

    /**
     * @param array<string, array<int, string>> $errors
     */
    private function validateSegmentsForStripFoundation(array &$errors, mixed $segments): void
    {
        if (!is_array($segments) || $segments === []) {
            $errors['segments'][] = 'The segments field is required and must contain at least one segment for strip_foundation in segments mode.';
            return;
        }

        foreach ($segments as $index => $segment) {
            if (!is_array($segment)) {
                $errors['segments'][] = sprintf('The segments[%d] value must be an object.', $index);
                continue;
            }

            $this->validatePositiveNumericField(
                $errors,
                sprintf('segments.%d.segmentLengthM', $index),
                $segment['segmentLengthM'] ?? null,
                sprintf('The segmentLengthM field is required and must be numeric in segments[%d].', $index)
            );
            $this->validatePositiveNumericField(
                $errors,
                sprintf('segments.%d.segmentWidthM', $index),
                $segment['segmentWidthM'] ?? null,
                sprintf('The segmentWidthM field is required and must be numeric in segments[%d].', $index)
            );
            $this->validatePositiveNumericField(
                $errors,
                sprintf('segments.%d.segmentHeightM', $index),
                $segment['segmentHeightM'] ?? null,
                sprintf('The segmentHeightM field is required and must be numeric in segments[%d].', $index)
            );
        }
    }

    /**
     * @param array<string, array<int, string>> $errors
     * @param array<int, mixed> $segments
     */
    private function validateStripReinforcementSegments(array &$errors, array $segments): void
    {
        foreach ($segments as $index => $segment) {
            if (!is_array($segment)) {
                continue;
            }

            if (array_key_exists('segmentIncludeReinforcement', $segment) && !is_bool($segment['segmentIncludeReinforcement'])) {
                $errors[sprintf('segments.%d.segmentIncludeReinforcement', $index)][] = 'The segmentIncludeReinforcement field must be a boolean.';
            }

            $include = !array_key_exists('segmentIncludeReinforcement', $segment) || $segment['segmentIncludeReinforcement'] === true;
            if (!$include) {
                continue;
            }

            if (array_key_exists('segmentUseGlobalRebarParams', $segment) && !is_bool($segment['segmentUseGlobalRebarParams'])) {
                $errors[sprintf('segments.%d.segmentUseGlobalRebarParams', $index)][] = 'The segmentUseGlobalRebarParams field must be a boolean.';
            }

            $useGlobal = !array_key_exists('segmentUseGlobalRebarParams', $segment) || $segment['segmentUseGlobalRebarParams'] === true;
            if ($useGlobal) {
                continue;
            }

            $this->validatePositiveNumericField(
                $errors,
                sprintf('segments.%d.segmentLongitudinalBarsCount', $index),
                $segment['segmentLongitudinalBarsCount'] ?? null,
                sprintf('The segmentLongitudinalBarsCount field is required and must be numeric in segments[%d] when segmentUseGlobalRebarParams is false.', $index)
            );
            if (isset($segment['segmentLongitudinalBarsCount']) && $this->isNumericValue($segment['segmentLongitudinalBarsCount']) && !$this->isIntegerNumber($segment['segmentLongitudinalBarsCount'])) {
                $errors[sprintf('segments.%d.segmentLongitudinalBarsCount', $index)][] = 'The segmentLongitudinalBarsCount field must be an integer.';
            }

            $this->validatePositiveNumericField(
                $errors,
                sprintf('segments.%d.segmentLongitudinalDiameterMm', $index),
                $segment['segmentLongitudinalDiameterMm'] ?? null,
                sprintf('The segmentLongitudinalDiameterMm field is required and must be numeric in segments[%d] when segmentUseGlobalRebarParams is false.', $index)
            );
            $this->validatePositiveNumericField(
                $errors,
                sprintf('segments.%d.segmentTransverseDiameterMm', $index),
                $segment['segmentTransverseDiameterMm'] ?? null,
                sprintf('The segmentTransverseDiameterMm field is required and must be numeric in segments[%d] when segmentUseGlobalRebarParams is false.', $index)
            );
            $this->validatePositiveNumericField(
                $errors,
                sprintf('segments.%d.segmentTransverseStepMm', $index),
                $segment['segmentTransverseStepMm'] ?? null,
                sprintf('The segmentTransverseStepMm field is required and must be numeric in segments[%d] when segmentUseGlobalRebarParams is false.', $index)
            );
        }
    }

    /**
     * @param array<string, array<int, string>> $errors
     * @param array<int, mixed> $segments
     */
    private function validateStripFormworkSegments(array &$errors, array $segments): void
    {
        foreach ($segments as $index => $segment) {
            if (!is_array($segment)) {
                continue;
            }

            if (array_key_exists('segmentIncludeFormwork', $segment) && !is_bool($segment['segmentIncludeFormwork'])) {
                $errors[sprintf('segments.%d.segmentIncludeFormwork', $index)][] = 'The segmentIncludeFormwork field must be a boolean.';
            }

            $include = !array_key_exists('segmentIncludeFormwork', $segment) || $segment['segmentIncludeFormwork'] === true;
            if (!$include) {
                continue;
            }

            if (array_key_exists('segmentUseGlobalFormworkParams', $segment) && !is_bool($segment['segmentUseGlobalFormworkParams'])) {
                $errors[sprintf('segments.%d.segmentUseGlobalFormworkParams', $index)][] = 'The segmentUseGlobalFormworkParams field must be a boolean.';
            }

            $useGlobal = !array_key_exists('segmentUseGlobalFormworkParams', $segment) || $segment['segmentUseGlobalFormworkParams'] === true;
            if ($useGlobal) {
                continue;
            }

            $this->validatePositiveNumericField(
                $errors,
                sprintf('segments.%d.segmentFormworkHeightM', $index),
                $segment['segmentFormworkHeightM'] ?? null,
                sprintf('The segmentFormworkHeightM field is required and must be numeric in segments[%d] when segmentUseGlobalFormworkParams is false.', $index)
            );
        }
    }

    /**
     * @param array<string, array<int, string>> $errors
     * @param array<string, mixed>|mixed $mixture
     * @param array<int, string> $allowedTypes
     */
    private function validateConcreteMixtureConfig(array &$errors, string $fieldPrefix, mixed $mixture, array $allowedTypes, bool $requiresGravel): void
    {
        if (!is_array($mixture)) {
            $errors[$fieldPrefix][] = sprintf('The %s field is required and must be an object.', $fieldPrefix);
            return;
        }

        $type = $mixture['type'] ?? null;
        if (!$this->isNonEmptyString($type)) {
            $errors[$fieldPrefix . '.type'][] = sprintf('The %s.type field is required.', $fieldPrefix);
            return;
        }

        $type = (string) $type;
        if (!in_array($type, $allowedTypes, true)) {
            $errors[$fieldPrefix . '.type'][] = sprintf(
                'The %s.type field must be one of: %s.',
                $fieldPrefix,
                implode(', ', $allowedTypes)
            );
            return;
        }

        if ($type === 'ready') {
            $this->validatePositiveNumericField(
                $errors,
                $fieldPrefix . '.readyConcretePricePerM3',
                $mixture['readyConcretePricePerM3'] ?? null,
                sprintf('The %s.readyConcretePricePerM3 field is required and must be numeric.', $fieldPrefix)
            );
            return;
        }

        if ($type === 'dry_ready') {
            $this->validatePositiveNumericField(
                $errors,
                $fieldPrefix . '.dryMixBagWeightKg',
                $mixture['dryMixBagWeightKg'] ?? null,
                sprintf('The %s.dryMixBagWeightKg field is required and must be numeric.', $fieldPrefix)
            );
            $this->validatePositiveNumericField(
                $errors,
                $fieldPrefix . '.dryMixBagPrice',
                $mixture['dryMixBagPrice'] ?? null,
                sprintf('The %s.dryMixBagPrice field is required and must be numeric.', $fieldPrefix)
            );
            return;
        }

        $this->validatePositiveNumericField(
            $errors,
            $fieldPrefix . '.cementShare',
            $mixture['cementShare'] ?? null,
            sprintf('The %s.cementShare field is required and must be numeric.', $fieldPrefix)
        );
        $this->validatePositiveNumericField(
            $errors,
            $fieldPrefix . '.sandShare',
            $mixture['sandShare'] ?? null,
            sprintf('The %s.sandShare field is required and must be numeric.', $fieldPrefix)
        );
        $this->validatePurchaseUnitField($errors, $fieldPrefix . '.cementPurchaseUnit', $mixture['cementPurchaseUnit'] ?? null);
        $this->validatePurchaseUnitField($errors, $fieldPrefix . '.sandPurchaseUnit', $mixture['sandPurchaseUnit'] ?? null);
        $this->validatePositiveNumericField(
            $errors,
            $fieldPrefix . '.cementUnitWeightKg',
            $mixture['cementUnitWeightKg'] ?? null,
            sprintf('The %s.cementUnitWeightKg field is required and must be numeric.', $fieldPrefix)
        );
        $this->validatePositiveNumericField(
            $errors,
            $fieldPrefix . '.cementUnitPrice',
            $mixture['cementUnitPrice'] ?? null,
            sprintf('The %s.cementUnitPrice field is required and must be numeric.', $fieldPrefix)
        );
        $this->validatePositiveNumericField(
            $errors,
            $fieldPrefix . '.sandUnitWeightKg',
            $mixture['sandUnitWeightKg'] ?? null,
            sprintf('The %s.sandUnitWeightKg field is required and must be numeric.', $fieldPrefix)
        );
        $this->validatePositiveNumericField(
            $errors,
            $fieldPrefix . '.sandUnitPrice',
            $mixture['sandUnitPrice'] ?? null,
            sprintf('The %s.sandUnitPrice field is required and must be numeric.', $fieldPrefix)
        );

        if ($requiresGravel) {
            $this->validatePositiveNumericField(
                $errors,
                $fieldPrefix . '.gravelShare',
                $mixture['gravelShare'] ?? null,
                sprintf('The %s.gravelShare field is required and must be numeric.', $fieldPrefix)
            );
            $this->validatePurchaseUnitField($errors, $fieldPrefix . '.gravelPurchaseUnit', $mixture['gravelPurchaseUnit'] ?? null);
            $this->validatePositiveNumericField(
                $errors,
                $fieldPrefix . '.gravelUnitWeightKg',
                $mixture['gravelUnitWeightKg'] ?? null,
                sprintf('The %s.gravelUnitWeightKg field is required and must be numeric.', $fieldPrefix)
            );
            $this->validatePositiveNumericField(
                $errors,
                $fieldPrefix . '.gravelUnitPrice',
                $mixture['gravelUnitPrice'] ?? null,
                sprintf('The %s.gravelUnitPrice field is required and must be numeric.', $fieldPrefix)
            );
        }
    }

    /**
     * @param array<string, array<int, string>> $errors
     */
    private function validatePurchaseUnitField(array &$errors, string $field, mixed $value): void
    {
        if (!$this->isNonEmptyString($value)) {
            $errors[$field][] = sprintf('The %s field is required.', $field);
            return;
        }

        if (!in_array((string) $value, self::ALLOWED_PURCHASE_UNITS, true)) {
            $errors[$field][] = sprintf('The %s field must be one of: bag, tonne.', $field);
        }
    }

    private function isBoredPileType(mixed $pileType): bool
    {
        if (!$this->isNonEmptyString($pileType)) {
            return true;
        }

        return (string) $pileType === 'bored';
    }

    private function isIntegerNumber(mixed $value): bool
    {
        if (!$this->isNumericValue($value)) {
            return false;
        }

        $number = (float) $value;
        return $number === (float) (int) $number;
    }

    private function isValidRebarLayers(mixed $value): bool
    {
        if (!$this->isNumericValue($value)) {
            return false;
        }

        $normalized = (float) $value;
        if ($normalized !== (float) (int) $normalized) {
            return false;
        }

        return in_array((int) $normalized, self::ALLOWED_REBAR_LAYERS, true);
    }
}
