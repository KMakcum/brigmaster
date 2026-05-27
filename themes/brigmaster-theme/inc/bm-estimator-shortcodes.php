<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registered estimator shortcode tags (constructly-core plugin).
 * Single source for conditional asset loading and content checks.
 *
 * @return list<string>
 */
function bm_estimator_shortcode_tags(): array
{
    return [
        'brigmaster_concrete_estimator',
        'brigmaster_strip_foundation_estimator',
        'brigmaster_pile_foundation_estimator',
        'brigmaster_brick_estimator',
        'brigmaster_screed_estimator',
        'brigmaster_drywall_estimator',
        'brigmaster_tile_estimator',
    ];
}
