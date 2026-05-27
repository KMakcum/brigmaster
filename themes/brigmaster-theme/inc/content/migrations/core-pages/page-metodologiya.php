<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Methodology_Core_Page_Migration implements Constructly_Core_Page_Migration_Interface
{
    use Constructly_Core_Page_Blocks;

    public static function page_key(): string { return 'methodology'; }

    public static function resolve_target_page(): ?WP_Post
    {
        $page = get_page_by_path('metodologiya') ?? get_page_by_path('metodologiya-raschetov');
        return $page instanceof WP_Post ? $page : null;
    }

    public static function migration_version(): string { return 'core-page-methodology-v4'; }

    public static function seo_meta(): array
    {
        return [
            'title' => 'Методология расчетов Brigmaster',
            'description' => 'Методология расчетов Brigmaster: какие данные учитывают калькуляторы, какие формулы используются, где проходят ограничения и как интерпретировать результат.',
        ];
    }

    public static function build_content(): string
    {
        $links = self::site_links();
        return implode("\n\n", [
            self::paragraph_block('На этой странице описано, как работают калькуляторы Brigmaster: какие входные данные используются в расчетах, какие формулы применяются и где проходят границы результата.'),
            self::heading_block('Как использовать эту страницу'),
            self::list_block([
                'Проверяйте, что набор полей в форме совпадает с тем, что вы закладываете в расчет.',
                'Сверяйте единицы измерения перед вводом.',
                'Используйте результат как предварительный ориентир и уточняйте его перед финальной закупкой.',
            ]),
            self::heading_block('Режимы расчета'),
            self::list_block([
                '<strong>Норматив</strong> - базовый расчет без дополнительного резерва.',
                '<strong>С запасом</strong> - увеличение базового результата на 10%.',
                '<strong>Для новичка</strong> - увеличение базового результата на 15%.',
            ]),
            self::heading_block('Фундаментные калькуляторы'),
            self::list_block([
                'Плитный, ленточный и свайный калькуляторы используют отдельные модели и разные наборы полей.',
                'Результаты подходят для предварительной оценки материалов, но не заменяют проект и проверку несущей способности.',
                'Для сравнения сценариев используйте хаб: <a href="' . esc_url($links['foundation']) . '">Калькулятор фундамента</a>.',
            ]),
            self::heading_block('Обратная связь по методологии'),
            self::list_block([
                'Если вы нашли спорную формулировку или расхождение в логике, используйте страницу <a href="' . esc_url($links['contacts']) . '">Контакты</a>.',
                'В сообщении укажите страницу калькулятора, входные данные и полученный результат.',
            ]),
        ]);
    }
}
