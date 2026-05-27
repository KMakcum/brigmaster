<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Foundation_Hub_Migration
{
    /**
     * @return array{post_id:int, content:string, migration:string}
     */
    public static function migrate_foundation_hub_page(int $page_id): array
    {
        $page = get_post($page_id);

        if (!$page instanceof WP_Post || $page->post_type !== 'page') {
            throw new InvalidArgumentException('Foundation hub page not found.');
        }

        $original = (string) $page->post_content;
        $working = Constructly_Legacy_Content_Migration::transform_legacy_content($original);
        $next = self::replace_foundation_hub_mono_block_with_sections($working);

        if ($next === $working) {
            if (str_contains($working, 'constructly/foundation-hub-hero')) {
                self::apply_foundation_hub_page_meta($page_id);

                return [
                    'post_id' => $page_id,
                    'content' => $working,
                    'migration' => 'foundation-hub-v3',
                ];
            }

            if (self::should_rebuild_foundation_hub_from_scratch($page, $working)) {
                $next = self::build_foundation_hub_page_content();
            } else {
                throw new InvalidArgumentException('No constructly/foundation-hub block found to replace.');
            }
        }

        wp_update_post([
            'ID' => $page_id,
            'post_content' => $next,
        ]);

        self::apply_foundation_hub_page_meta($page_id);

        return [
            'post_id' => $page_id,
            'content' => $next,
            'migration' => 'foundation-hub-v3',
        ];
    }

    private static function apply_foundation_hub_page_meta(int $page_id): void
    {
        update_post_meta($page_id, 'rank_math_title', 'Калькулятор фундамента Brigmaster: выбрать тип расчета');
        update_post_meta(
            $page_id,
            'rank_math_description',
            'Хаб Brigmaster для перехода к калькулятору плиты, ленты или свай. Помогает выбрать нужный сценарий расчета, но не подбирает тип фундамента автоматически.'
        );
        update_post_meta($page_id, '_constructly_content_migration', 'foundation-hub-v3');
    }

    private static function should_rebuild_foundation_hub_from_scratch(WP_Post $page, string $content): bool
    {
        if (in_array($page->post_name, ['kalkulyator-fundamenta', 'fundament'], true)) {
            return true;
        }

        if (str_contains($content, 'brigmaster-foundation-hub')) {
            return true;
        }

        if (str_contains($content, 'wp:constructly/foundation-hub') && !str_contains($content, 'constructly/foundation-hub-hero')) {
            return true;
        }

        return false;
    }

    public static function build_foundation_hub_page_content(): string
    {
        $links = Constructly_Migration_Helpers::default_foundation_hub_links();

        $blocks = [
            Constructly_Migration_Helpers::block('constructly/foundation-hub-hero', [
                'title' => 'Калькулятор фундамента',
                'lead' => 'Три типа фундамента — три отдельных калькулятора. Эта страница помогает выбрать нужный сценарий и перейти к соответствующей форме, '
                    . 'но не определяет тип основания автоматически. Если вы не уверены в конструкции, сначала уточните тип фундамента по геологии, нагрузкам и проекту, '
                    . 'а уже потом используйте нужный калькулятор для предварительной оценки материалов.',
                'ctaLabel' => 'К типам фундамента',
                'ctaUrl' => '#foundation-types',
            ]),
            Constructly_Migration_Helpers::block('constructly/foundation-hub-type-cards', [
                'sectionTitle' => 'Выберите тип фундамента',
                'anchorId' => 'foundation-types',
                'cards' => [
                    [
                        'title' => 'Плитный фундамент',
                        'thesis' => 'Монолитная плита на весь контур. Калькулятор считает бетон, а при нужных режимах показывает арматуру и опалубку.',
                        'buttonLabel' => 'Открыть калькулятор плиты',
                        'buttonUrl' => $links['slab'],
                        'icon' => 'slab',
                    ],
                    [
                        'title' => 'Ленточный фундамент',
                        'thesis' => 'Лента под несущими стенами. Калькулятор работает по длине и сечению, а также поддерживает режим по участкам.',
                        'buttonLabel' => 'Открыть калькулятор ленты',
                        'buttonUrl' => $links['strip'],
                        'icon' => 'strip',
                    ],
                    [
                        'title' => 'Свайный фундамент',
                        'thesis' => 'Сваи и ростверк. Калькулятор помогает оценить материалы, но несущая способность и выбор схемы всё равно подтверждаются проектом.',
                        'buttonLabel' => 'Открыть калькулятор свай',
                        'buttonUrl' => $links['pile'],
                        'icon' => 'pile',
                    ],
                ],
            ]),
            Constructly_Migration_Helpers::block('constructly/foundation-hub-criteria', [
                'sectionTitle' => 'Как выбрать тип фундамента',
                'items' => [
                    'Проверьте тип грунта и уровень грунтовых вод на участке.',
                    'Оцените вес дома, этажность и тип стеновых материалов.',
                    'Сравните бюджет и сроки реализации разных решений.',
                    'Учитывайте рельеф, перепады высот и особенности участка.',
                    'Не используйте этот хаб как инструмент автоматического выбора фундамента: он только направляет к нужному калькулятору.',
                    'Согласуйте итоговый вариант с проектировщиком.',
                ],
            ]),
            self::faq_block(),
            Constructly_Migration_Helpers::block('constructly/foundation-hub-links', [
                'sectionTitle' => 'Полезные ссылки',
                'primaryLinks' => [
                    ['label' => 'Калькулятор плитного фундамента', 'url' => $links['slab']],
                    ['label' => 'Калькулятор ленточного фундамента', 'url' => $links['strip']],
                    ['label' => 'Калькулятор свайного фундамента', 'url' => $links['pile']],
                ],
                'secondaryLinks' => [
                    ['label' => 'Калькулятор кирпича', 'url' => $links['brick']],
                    ['label' => 'Калькулятор стяжки', 'url' => $links['screed']],
                    ['label' => 'Калькулятор плитки', 'url' => $links['tile']],
                ],
            ]),
        ];

        return implode("\n\n", $blocks);
    }

    public static function replace_foundation_hub_mono_block_with_sections(string $content): string
    {
        $replacement = self::build_foundation_hub_page_content();

        $wrapped_shortcodes = [
            "<!-- wp:shortcode -->\n[brigmaster_foundation_hub]\n<!-- /wp:shortcode -->",
            "<!-- wp:shortcode -->\r\n[brigmaster_foundation_hub]\r\n<!-- /wp:shortcode -->",
        ];

        foreach ($wrapped_shortcodes as $needle) {
            if (str_contains($content, $needle)) {
                $next = str_replace($needle, $replacement, $content);
                if ($next !== $content) {
                    return $next;
                }
            }
        }

        if (str_contains($content, '[brigmaster_foundation_hub]')) {
            $next = str_replace('[brigmaster_foundation_hub]', $replacement, $content);
            if ($next !== $content) {
                return $next;
            }
        }

        $regex_replaced = preg_replace_callback(
            '/<!--\s*wp:constructly\/foundation-hub(?:\s[^\/]*?)?\s*\/-->/s',
            static function () use ($replacement): string {
                return $replacement;
            },
            $content,
            1
        );

        if (is_string($regex_replaced) && $regex_replaced !== $content) {
            return $regex_replaced;
        }

        $blocks = parse_blocks($content);
        if ($blocks !== []) {
            $out = [];
            $found = false;

            foreach ($blocks as $block) {
                if (!is_array($block)) {
                    continue;
                }

                $name = $block['blockName'] ?? null;
                if ($name === null || $name === '') {
                    $out[] = $block;
                    continue;
                }

                if ($name === 'constructly/foundation-hub') {
                    $found = true;
                    foreach (parse_blocks($replacement) as $piece) {
                        if (is_array($piece)) {
                            $out[] = $piece;
                        }
                    }

                    continue;
                }

                $out[] = $block;
            }

            if ($found) {
                return serialize_blocks($out);
            }
        }

        $pattern = '/<!--\s*wp:constructly\/foundation-hub(?:\s[^\/]*?)?\s*\/-->/s';

        $next = preg_replace_callback(
            $pattern,
            static function () use ($replacement): string {
                return $replacement;
            },
            $content,
            1
        );

        return is_string($next) ? $next : $content;
    }

    private static function faq_block(): string
    {
        return Constructly_Rank_Math_Faq_Migration::section_heading_block() . "\n\n" . Constructly_Rank_Math_Faq_Migration::serialize_block([
            'titleWrapper' => 'h3',
            'questions' => [
                [
                    'id' => 'foundation-hub-faq-1',
                    'title' => 'Что делает этот раздел, а что не делает?',
                    'content' => '<p>Хаб помогает перейти к нужному фундаментному калькулятору, но не выбирает тип основания автоматически и не заменяет решение проектировщика.</p>',
                    'visible' => true,
                ],
                [
                    'id' => 'foundation-hub-faq-2',
                    'title' => 'Можно ли ориентироваться только на онлайн-калькулятор?',
                    'content' => '<p>Нет. Каждый фундаментный калькулятор дает предварительную оценку материалов, но не заменяет проект, геологию и проверку несущей способности.</p>',
                    'visible' => true,
                ],
                [
                    'id' => 'foundation-hub-faq-3',
                    'title' => 'Как выбрать между плитой, лентой и сваями?',
                    'content' => '<p>Сначала определите конструктивную схему по грунту, нагрузкам и условиям участка. После этого используйте соответствующий калькулятор, чтобы оценить материалы внутри выбранного варианта.</p>',
                    'visible' => true,
                ],
                [
                    'id' => 'foundation-hub-faq-4',
                    'title' => 'Почему результаты на разных страницах отличаются?',
                    'content' => '<p>Плитный, ленточный и свайный фундамент рассчитываются по разным моделям и с разным набором полей, поэтому итоговые показатели не совпадают между собой.</p>',
                    'visible' => true,
                ],
            ],
            'className' => 'bm-rank-math-faq',
        ]);
    }
}
