<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Screed_Core_Page_Migration implements Constructly_Core_Page_Migration_Interface
{
    use Constructly_Core_Page_Blocks;

    public static function page_key(): string { return 'screed'; }

    public static function resolve_target_page(): ?WP_Post
    {
        $page = get_page_by_path('kalkulyatory/styazhka') ?? get_page_by_path('kalkulyator-styazhki');
        return $page instanceof WP_Post ? $page : null;
    }

    public static function migration_version(): string { return 'core-page-screed-v4'; }

    public static function seo_meta(): array
    {
        return [
            'title' => 'Калькулятор стяжки пола Brigmaster: объём, смесь, арматура',
            'description' => 'Онлайн-калькулятор стяжки Brigmaster считает объём смеси, ориентир по арматуре и состав материалов для готовой, сухой или самомесной стяжки.',
        ];
    }

    public static function build_content(): string
    {
        $links = self::site_links();
        return implode("\n\n", [
            self::shortcode_block('[brigmaster_screed_estimator title="Калькулятор стяжки"]'),
            self::paragraph_block('Калькулятор стяжки Brigmaster считает объем смеси, ориентир по арматуре и состав материалов в зависимости от выбранного типа смеси.'),
            self::heading_block('Как пользоваться калькулятором'),
            self::list_block([
                'Выберите режим: по длине и ширине или по площади.',
                'Укажите геометрию пола и среднюю толщину стяжки.',
                'При необходимости включите арматуру и выберите тип смеси.',
                'Заполните цены, если нужен ориентир по стоимости.',
            ], true),
            self::heading_block('Ограничения и методика'),
            self::list_block([
                'Толщина задается как средняя по всей площади.',
                'Арматура и расход материалов считаются ориентировочно и требуют проверки перед закупкой.',
            ]),
            self::heading_block('Полезные ссылки'),
            self::list_block([
                '<a href="' . esc_url($links['tile']) . '">Калькулятор плитки</a>.',
                '<a href="' . esc_url($links['drywall']) . '">Калькулятор гипсокартона</a>.',
                '<a href="' . esc_url($links['methodology']) . '">Методология расчетов</a>.',
            ]),
        ]);
    }
}
