<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Brick_Core_Page_Migration implements Constructly_Core_Page_Migration_Interface
{
    use Constructly_Core_Page_Blocks;

    public static function page_key(): string { return 'brick'; }

    public static function resolve_target_page(): ?WP_Post
    {
        $page = get_page_by_path('kalkulyatory/kirpich') ?? get_page_by_path('kalkulyator-kirpicha');
        return $page instanceof WP_Post ? $page : null;
    }

    public static function migration_version(): string { return 'core-page-brick-v4'; }

    public static function seo_meta(): array
    {
        return [
            'title' => 'Калькулятор кирпича Brigmaster: кирпич, раствор, сетка',
            'description' => 'Онлайн-калькулятор кирпича Brigmaster считает кирпич, раствор, кладочную сетку, проёмы, фронтоны и ориентировочную стоимость материалов для кладки стен.',
        ];
    }

    public static function build_content(): string
    {
        $links = self::site_links();
        return implode("\n\n", [
            self::shortcode_block('[brigmaster_brick_estimator title="Калькулятор кирпича"]'),
            self::paragraph_block('Калькулятор кирпича Brigmaster помогает ориентировочно рассчитать кирпич, раствор, кладочную сетку и базовую геометрию кладки.'),
            self::heading_block('Как пользоваться калькулятором'),
            self::list_block([
                'Выберите режим расчета: по длине и высоте стен или по площади стен.',
                'Укажите формат кирпича, толщину стены, размеры кирпича, толщину шва и запас.',
                'При необходимости учтите окна, двери, фронтоны, раствор, кладочную сетку и цены.',
                'Нажмите Рассчитать, чтобы получить ориентир по материалам и стоимости.',
            ], true),
            self::heading_block('Ограничения и методика'),
            self::list_block([
                'Результат подходит для предварительной закупки и оценки, но не заменяет проект и спецификацию.',
                'Перемычки и нагрузка на фундамент выводятся как ориентиры, а не как полный конструктивный расчет.',
            ]),
            self::heading_block('Полезные ссылки'),
            self::list_block([
                '<a href="' . esc_url($links['screed']) . '">Калькулятор стяжки</a>.',
                '<a href="' . esc_url($links['tile']) . '">Калькулятор плитки</a>.',
                '<a href="' . esc_url($links['methodology']) . '">Методология расчетов</a>.',
            ]),
        ]);
    }
}
