<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_About_Core_Page_Migration implements Constructly_Core_Page_Migration_Interface
{
    use Constructly_Core_Page_Blocks;

    public static function page_key(): string { return 'about'; }

    public static function resolve_target_page(): ?WP_Post
    {
        $page = get_page_by_path('o-proekte');
        return $page instanceof WP_Post ? $page : null;
    }

    public static function migration_version(): string { return 'core-page-about-v4'; }

    public static function seo_meta(): array
    {
        return [
            'title' => 'О проекте Brigmaster: методика и ограничения',
            'description' => 'О проекте Brigmaster: как работают калькуляторы, кто отвечает за контент, как устроена редакционная проверка и где проходят границы результата.',
        ];
    }

    public static function build_content(): string
    {
        $links = self::site_links();
        return implode("\n\n", [
            self::paragraph_block('Brigmaster - это сервис строительных калькуляторов для предварительной оценки материалов. Он помогает быстро понять порядок объемов, сравнить сценарии и подготовиться к обсуждению работ.'),
            self::heading_block('Кто отвечает за проект'),
            self::list_block([
                'За расчетную логику, тексты и структуру ключевых страниц отвечает команда Brigmaster.',
                'Brigmaster не является проектной организацией и не заменяет проектировщика, сметчика или технический надзор.',
                'Замечания по расчетам и контенту можно отправить через страницу контактов.',
            ]),
            self::heading_block('Ограничения сервиса'),
            self::list_block([
                'Калькуляторы дают предварительный ориентир, а не финальную смету или рабочую документацию.',
                'Для ответственных конструкций и закупки нужна проверка по проекту и спецификации.',
            ]),
            self::heading_block('Куда перейти дальше'),
            self::list_block([
                '<a href="' . esc_url($links['foundation']) . '">Калькулятор фундамента</a>.',
                '<a href="' . esc_url($links['brick']) . '">Калькулятор кирпича</a>.',
                '<a href="' . esc_url($links['methodology']) . '">Методология расчетов</a>.',
                '<a href="' . esc_url($links['contacts']) . '">Контакты</a>.',
            ]),
        ]);
    }
}
