<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Drywall_Core_Page_Migration implements Constructly_Core_Page_Migration_Interface
{
    use Constructly_Core_Page_Blocks;

    public static function page_key(): string { return 'drywall'; }

    public static function resolve_target_page(): ?WP_Post
    {
        $page = get_page_by_path('kalkulyatory/gipsokarton') ?? get_page_by_path('kalkulyator-gipsokartona');
        return $page instanceof WP_Post ? $page : null;
    }

    public static function migration_version(): string { return 'core-page-drywall-v4'; }

    public static function seo_meta(): array
    {
        return [
            'title' => 'Калькулятор гипсокартона Brigmaster: стены, потолок, перегородка',
            'description' => 'Онлайн-калькулятор гипсокартона Brigmaster для стен, потолков и перегородок. Считает листы ГКЛ, профиль, метизы, отделку и ориентировочную стоимость.',
        ];
    }

    public static function build_content(): string
    {
        $links = self::site_links();
        return implode("\n\n", [
            self::shortcode_block('[brigmaster_drywall_estimator title="Калькулятор гипсокартона"]'),
            self::paragraph_block('Калькулятор гипсокартона Brigmaster помогает ориентировочно рассчитать материалы для стен, потолков и перегородок.'),
            self::heading_block('Как пользоваться калькулятором'),
            self::list_block([
                'Выберите тип конструкции: стена, потолок или перегородка.',
                'Укажите режим расчета, геометрию, параметры листа и шаг профиля.',
                'При необходимости включите проемы, отделку и стоимость материалов.',
                'Используйте результат как предварительный ориентир для закупки.',
            ], true),
            self::heading_block('Ограничения и методика'),
            self::list_block([
                'Калькулятор считает прямоугольную геометрию и не заменяет проектный расчет узлов.',
                'В режиме по площади профиль и часть крепежа не считаются, потому что для них нужна геометрия.',
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
