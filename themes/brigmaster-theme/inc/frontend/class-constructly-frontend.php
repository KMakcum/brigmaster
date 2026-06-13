<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Frontend
{
    public static function init(): void
    {
        add_action('init', [self::class, 'register_assets']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_global_assets'], 20);
        add_action('after_setup_theme', [self::class, 'register_theme_supports']);
    }

    public static function register_theme_supports(): void
    {
        add_theme_support('editor-styles');
        add_theme_support('wp-block-styles');
        add_theme_support('responsive-embeds');
    }

    public static function register_assets(): void
    {
        wp_register_style(
            'bm-base',
            false,
            [],
            CONSTRUCTLY_THEME_VERSION
        );

        wp_register_style(
            'bm-core-estimator',
            false,
            ['bm-base'],
            CONSTRUCTLY_THEME_VERSION
        );

        wp_register_style(
            'bm-core-hub',
            false,
            ['bm-base'],
            CONSTRUCTLY_THEME_VERSION
        );
    }

    public static function enqueue_global_assets(): void
    {
        wp_enqueue_style('bm-base');
        Constructly_Assets::enqueue_main_bundle();

        if (is_singular()) {
            $post = get_post();
            if ($post instanceof WP_Post) {
                $content = (string) $post->post_content;

                if (self::post_content_has_estimator_shortcode($content)) {
                    wp_enqueue_style('bm-core-estimator');
                }

                if (self::post_content_needs_foundation_hub_styles($content)) {
                    wp_enqueue_style('bm-core-hub');
                }
            }
        }
    }

    private static function post_content_needs_foundation_hub_styles(string $content): bool
    {
        if (has_shortcode($content, 'brigmaster_foundation_hub')) {
            return true;
        }

        $blocks = [
            'constructly/foundation-hub-hero',
            'constructly/foundation-hub-type-cards',
        ];

        foreach ($blocks as $block_name) {
            if (has_block($block_name, $content)) {
                return true;
            }
        }

        return false;
    }

    private static function post_content_has_estimator_shortcode(string $content): bool
    {
        foreach (bm_estimator_shortcode_tags() as $tag) {
            if (has_shortcode($content, $tag)) {
                return true;
            }
        }

        return false;
    }
}
