<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Contacts_Core_Page_Migration implements Constructly_Core_Page_Migration_Interface
{
    use Constructly_Core_Page_Blocks;

    public static function page_key(): string { return 'contacts'; }

    public static function resolve_target_page(): ?WP_Post
    {
        $page = get_page_by_path('kontakty');
        return $page instanceof WP_Post ? $page : null;
    }

    public static function migration_version(): string { return 'core-page-contacts-v4'; }

    public static function seo_meta(): array
    {
        return [
            'title' => 'Контакты Brigmaster',
            'description' => 'Контакты Brigmaster: форма обратной связи для вопросов по калькуляторам, замечаний по расчетам, контенту и обращениям по персональным данным.',
        ];
    }

    public static function build_content(): string
    {
        $links = self::site_links();
        $contact_form_shortcode = self::contact_form_shortcode();
        $blocks = [
            self::paragraph_block('На этой странице можно отправить сообщение команде Brigmaster через форму обратной связи. Она подходит для вопросов по работе калькуляторов, сообщений о спорных результатах и предложений по улучшению сервиса.'),
        ];

        $blocks[] = $contact_form_shortcode !== ''
            ? self::shortcode_block($contact_form_shortcode)
            : self::paragraph_block('Контактная форма в текущем окружении не найдена. Проверьте наличие опубликованной формы Contact Form 7 перед повторным запуском миграции.');

        $blocks[] = self::heading_block('По каким вопросам писать');
        $blocks[] = self::list_block([
            'Не сходится результат калькулятора с вашими исходными данными.',
            'На странице есть обещание функции, которой нет в форме или в результате.',
            'Вы хотите предложить новый калькулятор, поясняющую статью или улучшение интерфейса.',
        ]);
        $blocks[] = self::heading_block('Полезные ссылки');
        $blocks[] = self::list_block([
            '<a href="' . esc_url($links['about']) . '">О проекте Brigmaster</a>.',
            '<a href="' . esc_url($links['methodology']) . '">Методология расчетов</a>.',
            '<a href="' . esc_url($links['privacy']) . '">Политика конфиденциальности</a>.',
        ]);

        return implode("\n\n", $blocks);
    }
}
