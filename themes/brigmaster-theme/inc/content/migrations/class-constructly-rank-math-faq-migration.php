<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Rank_Math_Faq_Migration
{
    public const FAQ_SECTION_TITLE = 'Часто задаваемые вопросы';

    /**
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    public static function merge_default_attributes(array $attributes): array
    {
        $defaults = [
            'listStyle' => '',
            'titleWrapper' => 'h3',
            'sizeSlug' => 'thumbnail',
            'questions' => [],
            'listCssClasses' => '',
            'titleCssClasses' => '',
            'contentCssClasses' => '',
            'textAlign' => 'left',
        ];

        $attributes = array_merge($defaults, $attributes);
        $attributes['questions'] = self::normalize_questions($attributes['questions'] ?? []);

        return $attributes;
    }

    /**
     * Saved markup for Rank Math FAQ (must match block save.js) so the editor validates and RichText fields stay clean.
     *
     * @param array<string, mixed> $attributes
     */
    public static function saved_inner_html(array $attributes): string
    {
        $attributes = self::merge_default_attributes($attributes);

        $classes = ['wp-block-rank-math-faq-block'];
        if (!empty($attributes['className'])) {
            $classes[] = trim((string) $attributes['className']);
        }

        $wrapper_class = esc_attr(implode(' ', array_filter(array_unique($classes))));

        $title_tag = isset($attributes['titleWrapper']) ? strtolower((string) $attributes['titleWrapper']) : 'h3';
        if (!preg_match('/^h[1-6]$/', $title_tag)) {
            $title_tag = 'h3';
        }

        $out = '<div class="' . $wrapper_class . '">';
        $questions = isset($attributes['questions']) && is_array($attributes['questions']) ? $attributes['questions'] : [];

        foreach ($questions as $question) {
            if (!is_array($question)) {
                continue;
            }
            if (empty($question['visible']) || empty($question['title']) || empty($question['content'])) {
                continue;
            }

            $out .= '<div class="rank-math-faq-item">';
            $out .= '<' . $title_tag . ' class="rank-math-question">' . wp_kses_post((string) $question['title']) . '</' . $title_tag . '>';
            $out .= '<div class="rank-math-answer">' . wp_kses_post((string) $question['content']) . '</div>';
            $out .= '</div>';
        }

        $out .= '</div>';

        return $out;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public static function serialize_block(array $attributes): string
    {
        $attrs = self::merge_default_attributes($attributes);
        $inner = self::saved_inner_html($attrs);

        return serialize_block([
            'blockName' => 'rank-math/faq-block',
            'attrs' => $attrs,
            'innerBlocks' => [],
            'innerHTML' => $inner,
            'innerContent' => [$inner],
        ]);
    }

    public static function section_heading_block(): string
    {
        return serialize_block(self::heading_block_structure());
    }

    public static function normalize_rank_math_faq_in_content(string $content): string
    {
        $blocks = parse_blocks($content);
        if ($blocks === []) {
            return $content;
        }

        $blocks = self::normalize_blocks_tree($blocks);

        return serialize_blocks($blocks);
    }

    public static function ensure_faq_section_heading_in_content(string $content): string
    {
        $blocks = parse_blocks($content);
        if ($blocks === []) {
            return $content;
        }

        $blocks = self::insert_headings_before_rank_math_blocks($blocks);

        return serialize_blocks($blocks);
    }

    public static function normalize_content_for_storage(string $content): string
    {
        $content = self::normalize_rank_math_faq_in_content($content);

        return self::ensure_faq_section_heading_in_content($content);
    }

    /**
     * @return array<string, mixed>
     */
    private static function heading_block_structure(): array
    {
        $title = self::FAQ_SECTION_TITLE;
        $inner = '<h2 class="wp-block-heading bm-faq-section__title">' . esc_html($title) . '</h2>';

        return [
            'blockName' => 'core/heading',
            'attrs' => [
                'level' => 2,
                'className' => 'bm-faq-section__title',
                'content' => $title,
            ],
            'innerBlocks' => [],
            'innerHTML' => $inner,
            'innerContent' => [$inner],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $blocks
     * @return array<int, array<string, mixed>>
     */
    private static function normalize_blocks_tree(array $blocks): array
    {
        $out = [];

        foreach ($blocks as $block) {
            if (!is_array($block)) {
                continue;
            }

            $name = $block['blockName'] ?? null;
            if ($name === null || $name === '') {
                $out[] = $block;
                continue;
            }

            if (!empty($block['innerBlocks'])) {
                $block['innerBlocks'] = self::normalize_blocks_tree($block['innerBlocks']);
            }

            if ($name === 'rank-math/faq-block' && !empty($block['attrs']) && is_array($block['attrs'])) {
                $merged = self::merge_default_attributes($block['attrs']);
                $inner = self::saved_inner_html($merged);
                $block['attrs'] = $merged;
                $block['innerHTML'] = $inner;
                $block['innerContent'] = [$inner];
                $block['innerBlocks'] = [];
            }

            $out[] = $block;
        }

        return $out;
    }

    /**
     * Inserts the standard FAQ heading only at the top level.
     *
     * @param array<int, array<string, mixed>> $blocks
     * @return array<int, array<string, mixed>>
     */
    private static function insert_headings_before_rank_math_blocks(array $blocks): array
    {
        $out = [];

        foreach ($blocks as $block) {
            if (!is_array($block)) {
                continue;
            }

            $name = $block['blockName'] ?? null;
            if ($name === null || $name === '') {
                $out[] = $block;
                continue;
            }

            if ($name === 'rank-math/faq-block') {
                $prev = end($out);
                if ($prev === false || !self::is_faq_section_heading_block($prev)) {
                    $out[] = self::heading_block_structure();
                }
            }

            $out[] = $block;
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $block
     */
    private static function is_faq_section_heading_block(array $block): bool
    {
        if (($block['blockName'] ?? '') !== 'core/heading') {
            return false;
        }

        $inner = (string) ($block['innerHTML'] ?? '');
        if (str_contains($inner, self::FAQ_SECTION_TITLE)) {
            return true;
        }

        $content = $block['attrs']['content'] ?? '';

        return is_string($content) && str_contains($content, self::FAQ_SECTION_TITLE);
    }

    /**
     * @param mixed $questions
     * @return array<int, array<string, mixed>>
     */
    private static function normalize_questions(mixed $questions): array
    {
        if (!is_array($questions)) {
            return [];
        }

        $normalized = [];

        foreach ($questions as $question) {
            if (!is_array($question)) {
                continue;
            }

            $question['title'] = self::normalize_rich_text_value($question['title'] ?? '');
            $question['content'] = self::normalize_rich_text_value($question['content'] ?? '');
            $normalized[] = $question;
        }

        return $normalized;
    }

    private static function normalize_rich_text_value(mixed $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $decoded = preg_replace_callback(
            '/(?:\\\\u|u)([0-9a-fA-F]{4})/',
            static function (array $matches): string {
                return mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UCS-2BE');
            },
            $value
        );

        $decoded = html_entity_decode((string) $decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return wp_kses_post($decoded);
    }
}
