<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Theme_Setup
{
    public static function init(): void
    {
        add_action('after_setup_theme', [self::class, 'setup']);
    }

    public static function setup(): void
    {
        load_theme_textdomain('brigmaster-theme', BM_THEME_PATH . '/languages');

        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ]);
        add_theme_support('post-thumbnails');
        add_theme_support('wp-block-styles');
        add_theme_support('responsive-embeds');
        add_theme_support('editor-styles');
        add_theme_support('align-wide');

        register_nav_menus([
            'primary' => __('Primary navigation', 'brigmaster-theme'),
            'footer-column-1' => __('Footer column 1 (calculators)', 'brigmaster-theme'),
            'footer-column-2' => __('Footer column 2 (information)', 'brigmaster-theme'),
            'footer-column-3' => __('Footer column 3 (about)', 'brigmaster-theme'),
        ]);
    }
}
