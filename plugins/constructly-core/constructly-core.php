<?php
/**
 * Plugin Name: Brigmaster Core
 * Description: Core plugin for brigmaster construction calculators.
 * Version: 0.1.0
 * Author: brigmaster
 * Requires PHP: 8.2
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (is_readable($autoloadPath)) {
    require_once $autoloadPath;
}

add_action('rest_api_init', static function (): void {
    $controller = new \Brigmaster\Http\Rest\EstimateController(
        new \Brigmaster\Application\EstimateService()
    );

    $controller->registerRoutes();
});

add_action('init', static function (): void {
    $estimateShortcode = new \Brigmaster\Http\Shortcode\EstimateShortcode(__FILE__);
    $estimateShortcode->registerShortcodes();
});

