<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Blocks
{
    /**
     * @return array<string, array<string, string>>
     */
    private static function definitions(): array
    {
        return [
            'hero' => [
                'name' => 'constructly/home-hero',
                'title' => 'Constructly Hero',
                'style' => 'hero',
                'template' => 'hero',
            ],
            'how-it-works' => [
                'name' => 'constructly/how-it-works',
                'title' => 'Constructly How It Works',
                'style' => 'how-it-works',
                'template' => 'how-it-works',
            ],
            'popular-calculators' => [
                'name' => 'constructly/popular-calculators',
                'title' => 'Constructly Popular Calculators',
                'style' => 'popular-calculators',
                'template' => 'popular-calculators',
            ],
            'why-brigmaster' => [
                'name' => 'constructly/why-brigmaster',
                'title' => 'Constructly Why Brigmaster',
                'style' => 'why-brigmaster',
                'template' => 'why-brigmaster',
            ],
            'trust' => [
                'name' => 'constructly/trust',
                'title' => 'Constructly Trust',
                'style' => 'trust',
                'template' => 'trust',
            ],
            'who-its-for' => [
                'name' => 'constructly/who-its-for',
                'title' => 'Constructly Who It Is For',
                'style' => 'who-its-for',
                'template' => 'who-its-for',
            ],
            'how-calculations-work' => [
                'name' => 'constructly/how-calculations-work',
                'title' => 'Constructly How Calculations Work',
                'style' => 'how-calculations-work',
                'template' => 'how-calculations-work',
            ],
            'final-cta' => [
                'name' => 'constructly/final-cta',
                'title' => 'Constructly Final CTA',
                'style' => 'final-cta',
                'template' => 'final-cta',
            ],
            'foundation-hub-hero' => [
                'name' => 'constructly/foundation-hub-hero',
                'title' => 'Constructly Foundation Hub — Hero',
                'style' => 'foundation-hub',
                'template' => 'foundation-hub-hero',
            ],
            'foundation-hub-type-cards' => [
                'name' => 'constructly/foundation-hub-type-cards',
                'title' => 'Constructly Foundation Hub — Types',
                'style' => 'foundation-hub',
                'template' => 'foundation-hub-type-cards',
            ],
            'foundation-hub-criteria' => [
                'name' => 'constructly/foundation-hub-criteria',
                'title' => 'Constructly Foundation Hub — Criteria',
                'style' => 'foundation-hub',
                'template' => 'foundation-hub-criteria',
            ],
            'foundation-hub-links' => [
                'name' => 'constructly/foundation-hub-links',
                'title' => 'Constructly Foundation Hub — Links',
                'style' => 'foundation-hub',
                'template' => 'foundation-hub-links',
            ],
        ];
    }

    public static function init(): void
    {
        add_action('init', [self::class, 'register_assets']);
        add_action('init', [self::class, 'register_blocks']);
        add_filter('block_categories_all', [self::class, 'register_category']);
        add_filter('constructly_render_foundation_hub', [self::class, 'filter_render_foundation_hub'], 10, 1);
    }

    /**
     * @param array<int, array<string, mixed>> $categories
     * @return array<int, array<string, mixed>>
     */
    public static function register_category(array $categories): array
    {
        $categories[] = [
            'slug' => 'constructly',
            'title' => __('Constructly', 'brigmaster-theme'),
            'icon' => null,
        ];

        return $categories;
    }

    public static function register_assets(): void
    {
        wp_register_script(
            'bm-editor-blocks',
            CONSTRUCTLY_THEME_URL . '/assets/editor/js/blocks.js',
            ['wp-blocks', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-element', 'wp-i18n'],
            CONSTRUCTLY_THEME_VERSION,
            true
        );
    }

    public static function register_blocks(): void
    {
        foreach (self::definitions() as $definition) {
            register_block_type($definition['name'], [
                'api_version' => 3,
                'editor_script' => 'bm-editor-blocks',
                'style' => 'bm-theme',
                'editor_style' => 'bm-theme-editor',
                'supports' => [
                    'anchor' => false,
                    'customClassName' => false,
                    'html' => false,
                    'align' => false,
                ],
                'render_callback' => static function (array $attributes, string $content, WP_Block $block) use ($definition): string {
                    return self::render_template($definition['template'], $attributes, $content, $block);
                },
            ]);
        }

        register_block_type('constructly/foundation-hub', [
            'api_version' => 3,
            'title' => __('Constructly Foundation Hub', 'brigmaster-theme'),
            'category' => 'constructly',
            'editor_script' => 'bm-editor-blocks',
            'style' => 'bm-theme',
            'editor_style' => 'bm-theme-editor',
            'supports' => [
                'anchor' => false,
                'customClassName' => false,
                'html' => false,
                'align' => false,
            ],
            'render_callback' => static function (array $attributes, string $content, WP_Block $block): string {
                return self::render_foundation_hub_markup();
            },
        ]);
    }

    public static function render_foundation_hub_markup(): string
    {
        $template_path = CONSTRUCTLY_THEME_PATH . '/templates/blocks/foundation-hub.php';

        if (!is_readable($template_path)) {
            return '';
        }

        ob_start();
        require $template_path;

        return (string) ob_get_clean();
    }

    public static function filter_render_foundation_hub(string $html): string
    {
        if ($html !== '') {
            return $html;
        }

        return self::render_foundation_hub_markup();
    }

    public static function render_template(string $template, array $attributes, string $content, WP_Block $block): string
    {
        $template_path = CONSTRUCTLY_THEME_PATH . '/templates/blocks/' . $template . '.php';

        if (!is_readable($template_path)) {
            return $content;
        }

        ob_start();
        require $template_path;

        return (string) ob_get_clean();
    }
}
