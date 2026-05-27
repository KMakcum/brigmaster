<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

trait Constructly_Core_Page_Blocks
{
    /**
     * @return array<string, string>
     */
    protected static function site_links(): array
    {
        $calculator_links = Constructly_Migration_Helpers::resolve_links();

        return array_merge($calculator_links, [
            'about' => Constructly_Migration_Helpers::resolve_page_url(
                ['o-proekte'],
                ['о проекте', 'about'],
                home_url('/o-proekte/')
            ),
            'contacts' => Constructly_Migration_Helpers::resolve_page_url(
                ['kontakty'],
                ['контакты', 'contact'],
                home_url('/kontakty/')
            ),
            'methodology' => Constructly_Migration_Helpers::resolve_page_url(
                ['metodologiya', 'metodologiya-raschetov'],
                ['методология расчетов', 'methodology'],
                home_url('/metodologiya/')
            ),
            'privacy' => Constructly_Migration_Helpers::resolve_page_url(
                ['privacy-policy'],
                ['политика конфиденциальности', 'privacy'],
                home_url('/privacy-policy/')
            ),
        ]);
    }

    protected static function contact_form_shortcode(): string
    {
        $form = get_page_by_path('kontaktnaya-forma-1', OBJECT, 'wpcf7_contact_form');
        if (!$form instanceof WP_Post) {
            $forms = get_posts([
                'post_type' => 'wpcf7_contact_form',
                'post_status' => ['publish', 'draft', 'private'],
                'numberposts' => 1,
            ]);

            $form = !empty($forms) && $forms[0] instanceof WP_Post ? $forms[0] : null;
        }

        if (!$form instanceof WP_Post) {
            return '';
        }

        return sprintf(
            '[contact-form-7 id="%d" title="%s"]',
            (int) $form->ID,
            esc_attr($form->post_title)
        );
    }

    protected static function heading_block(string $text, int $level = 2): string
    {
        $level = max(2, min(4, $level));
        $tag = 'h' . $level;

        return sprintf(
            "<!-- wp:heading {\"level\":%d} -->\n<%s class=\"wp-block-heading\">%s</%s>\n<!-- /wp:heading -->",
            $level,
            $tag,
            esc_html($text),
            $tag
        );
    }

    protected static function paragraph_block(string $html): string
    {
        return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
    }

    /**
     * @param list<string> $items
     */
    protected static function list_block(array $items, bool $ordered = false): string
    {
        $list_tag = $ordered ? 'ol' : 'ul';
        $block_open = $ordered ? '<!-- wp:list {"ordered":true} -->' : '<!-- wp:list -->';
        $inner = [];

        foreach ($items as $item) {
            $inner[] = '<!-- wp:list-item -->';
            $inner[] = '<li>' . $item . '</li>';
            $inner[] = '<!-- /wp:list-item -->';
            $inner[] = '';
        }

        if (!empty($inner)) {
            array_pop($inner);
        }

        return $block_open
            . "\n<" . $list_tag . ' class="wp-block-list">' . implode("\n", $inner) . '</' . $list_tag . ">\n<!-- /wp:list -->";
    }

    protected static function shortcode_block(string $shortcode): string
    {
        return "<!-- wp:shortcode -->\n{$shortcode}\n<!-- /wp:shortcode -->";
    }

    /**
     * @param array<int, array<string, mixed>> $questions
     */
    protected static function faq_block(array $questions): string
    {
        return Constructly_Rank_Math_Faq_Migration::section_heading_block() . "\n\n"
            . Constructly_Rank_Math_Faq_Migration::serialize_block([
                'titleWrapper' => 'h3',
                'questions' => $questions,
                'className' => 'bm-rank-math-faq',
            ]);
    }
}
