<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Tile_Core_Page_Migration implements Constructly_Core_Page_Migration_Interface
{
    use Constructly_Core_Page_Blocks;

    public static function page_key(): string { return 'tile'; }

    public static function resolve_target_page(): ?WP_Post
    {
        $page = get_page_by_path('kalkulyatory/plitka') ?? get_page_by_path('kalkulyator-plitki');
        return $page instanceof WP_Post ? $page : null;
    }

    public static function migration_version(): string { return 'core-page-tile-v4'; }

    public static function seo_meta(): array
    {
        return [
            'title' => 'Калькулятор плитки Brigmaster для пола и стен',
            'description' => 'Онлайн-калькулятор плитки Brigmaster для пола и стен. Считает плитку, ориентировочную раскладку, подрезку, клей, затирку и итог по материалам для прямоугольной зоны.',
        ];
    }

    public static function build_content(): string
    {
        $links = self::site_links();
        return implode("\n\n", [
            self::shortcode_block('[brigmaster_tile_estimator title="Калькулятор плитки"]'),
            self::paragraph_block('Калькулятор плитки Brigmaster помогает рассчитать плитку, подрезку, клей и затирку для пола и стен.'),
            self::heading_block('Как пользоваться калькулятором'),
            self::list_block([
                'Выберите тип зоны: пол или стены.',
                'Укажите режим расчета, размеры зоны, размер плитки, шов и способ укладки.',
                'При необходимости включите проемы, вырезы, клей, затирку и цены.',
                'Проверьте результат перед финальной закупкой и раскладкой.',
            ], true),
            self::heading_block('Ограничения и методика'),
            self::list_block([
                'Раскладка рассчитана для прямоугольной зоны и служит ориентиром.',
                'Проемы уменьшают площадь, но не раскладываются по координатам.',
            ]),
            self::heading_block('Полезные ссылки'),
            self::list_block([
                '<a href="' . esc_url($links['screed']) . '">Калькулятор стяжки</a>.',
                '<a href="' . esc_url($links['drywall']) . '">Калькулятор гипсокартона</a>.',
                '<a href="' . esc_url($links['methodology']) . '">Методология расчетов</a>.',
            ]),
        ]);
    }
}
