<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Legacy_Content_Migration
{
    /**
     * @return list<int>
     */
    public static function migrate_legacy_content(): array
    {
        $updated_ids = [];

        $pages = get_posts([
            'post_type' => 'page',
            'post_status' => ['publish', 'draft', 'private'],
            'numberposts' => -1,
        ]);

        foreach ($pages as $page) {
            if (!$page instanceof WP_Post) {
                continue;
            }

            $original = (string) $page->post_content;
            $next = self::transform_legacy_content($original);

            if ($next !== $original) {
                wp_update_post([
                    'ID' => $page->ID,
                    'post_content' => $next,
                ]);
                $updated_ids[] = (int) $page->ID;
            }
        }

        return $updated_ids;
    }

    public static function transform_legacy_content(string $content): string
    {
        $content = self::replace_foundation_hub_shortcode_with_block($content);

        return self::inject_estimator_shortcode_titles($content);
    }

    private static function replace_foundation_hub_shortcode_with_block(string $content): string
    {
        $block = Constructly_Migration_Helpers::block('constructly/foundation-hub', []);

        $wrapped = [
            "<!-- wp:shortcode -->\n[brigmaster_foundation_hub]\n<!-- /wp:shortcode -->",
            "<!-- wp:shortcode -->\r\n[brigmaster_foundation_hub]\r\n<!-- /wp:shortcode -->",
        ];

        foreach ($wrapped as $needle) {
            $content = str_replace($needle, $block, $content);
        }

        return str_replace('[brigmaster_foundation_hub]', $block, $content);
    }

    /**
     * @return array<string, string>
     */
    private static function estimator_title_map(): array
    {
        return [
            'brigmaster_concrete_estimator' => 'Калькулятор плитного фундамента',
            'brigmaster_strip_foundation_estimator' => 'Калькулятор ленточного фундамента',
            'brigmaster_pile_foundation_estimator' => 'Калькулятор свайного фундамента',
            'brigmaster_brick_estimator' => 'Калькулятор кирпича',
            'brigmaster_screed_estimator' => 'Калькулятор стяжки',
            'brigmaster_drywall_estimator' => 'Калькулятор гипсокартона',
            'brigmaster_tile_estimator' => 'Калькулятор плитки',
        ];
    }

    private static function inject_estimator_shortcode_titles(string $content): string
    {
        foreach (self::estimator_title_map() as $tag => $title) {
            $pattern = '/\[' . preg_quote($tag, '/') . '(\s[^]]*)?\]/';
            $replaced = preg_replace_callback(
                $pattern,
                static function (array $matches) use ($tag, $title): string {
                    $inner_raw = $matches[1] ?? '';

                    if (is_string($inner_raw) && preg_match('/\btitle\s*=/', $inner_raw)) {
                        return $matches[0];
                    }

                    $attr = ' title="' . esc_attr($title) . '"';

                    if (!is_string($inner_raw) || trim($inner_raw) === '') {
                        return '[' . $tag . $attr . ']';
                    }

                    return '[' . $tag . $inner_raw . $attr . ']';
                },
                $content
            );

            if (is_string($replaced)) {
                $content = $replaced;
            }
        }

        return $content;
    }
}
