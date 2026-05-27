<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Homepage_Migration
{
    /**
     * @return array{post_id:int, content:string, migration:string}
     */
    public static function migrate_homepage(int $page_id): array
    {
        $page = get_post($page_id);

        if (!$page instanceof WP_Post || $page->post_type !== 'page') {
            throw new InvalidArgumentException('Homepage page not found.');
        }

        $content = self::build_homepage_content();

        wp_update_post([
            'ID' => $page_id,
            'post_content' => $content,
        ]);

        update_post_meta($page_id, 'rank_math_title', 'Строительные калькуляторы онлайн Brigmaster');
        update_post_meta(
            $page_id,
            'rank_math_description',
            'Онлайн-калькуляторы Brigmaster для фундамента, кирпича, стяжки, гипсокартона и плитки. Предварительная оценка материалов без обещания точной сметы.'
        );
        update_post_meta($page_id, '_constructly_content_migration', 'homepage-v5');

        return [
            'post_id' => $page_id,
            'content' => $content,
            'migration' => 'homepage-v5',
        ];
    }

    public static function build_homepage_content(): string
    {
        $links = Constructly_Migration_Helpers::resolve_links();
        $preview_basenames = Constructly_Migration_Helpers::homepage_calculator_preview_basenames();
        $preview_ids = [];
        foreach ($preview_basenames as $basename) {
            $preview_ids[] = Constructly_Migration_Helpers::resolve_homepage_calculator_preview_id($basename);
        }

        $blocks = [
            Constructly_Migration_Helpers::block('constructly/home-hero', [
                'title' => 'Строительные калькуляторы для быстрой и понятной оценки материалов',
                'lead' => 'Подберите объём и ориентировочный расход материалов по основным видам работ без перегруженных таблиц и ручных расчётов.',
                'primaryLabel' => 'Открыть калькуляторы',
                'primaryUrl' => '#calculators',
                'secondaryLabel' => 'Как это работает',
                'secondaryUrl' => '#how-it-works',
                'quickLinksLabel' => 'Часто открывают',
                'quickLinks' => [
                    ['label' => 'Фундамент', 'url' => $links['foundation']],
                    ['label' => 'Стяжка', 'url' => $links['screed']],
                    ['label' => 'Кирпич', 'url' => $links['brick']],
                ],
                'themeVariant' => 'dark',
            ]),
            Constructly_Migration_Helpers::block('constructly/how-it-works', [
                'anchor' => 'how-it-works',
                'title' => 'Как пользоваться сервисом',
                'subtitle' => 'Собрали расчёты так, чтобы ими можно было пользоваться для быстрой предварительной оценки и перед сверкой с фактической спецификацией.',
                'ctaLabel' => 'Перейти к калькуляторам',
                'ctaUrl' => '#calculators',
                'steps' => [
                    [
                        'number' => '01',
                        'title' => 'Выберите нужный калькулятор',
                        'text' => 'Откройте расчёт под конкретную задачу: фундамент, стяжка, кирпич, плитка, гипсокартон и другие типовые работы.',
                    ],
                    [
                        'number' => '02',
                        'title' => 'Введите исходные параметры',
                        'text' => 'Укажите площадь, толщину, размеры или другие базовые значения. Все поля подписаны простым и понятным языком.',
                    ],
                    [
                        'number' => '03',
                        'title' => 'Получите расчёт',
                        'text' => 'Сервис показывает объём, площадь или количество материала в зависимости от калькулятора и помогает быстро понять порядок расхода.',
                    ],
                    [
                        'number' => '04',
                        'title' => 'Сравните и уточните',
                        'text' => 'Используйте результат как рабочую отправную точку, а затем уточняйте спецификацию под свой объект, если нужен точный расчёт.',
                    ],
                ],
            ]),
            Constructly_Migration_Helpers::block('constructly/popular-calculators', [
                'anchor' => 'calculators',
                'title' => 'Популярные калькуляторы',
                'subtitle' => 'Быстрый доступ к основным расчётам для черновых и отделочных работ.',
                'cards' => [
                    [
                        'title' => 'Фундамент',
                        'description' => 'Хаб для выбора плитного, ленточного или свайного калькулятора с отдельными сценариями расчёта.',
                        'buttonLabel' => 'Рассчитать фундамент',
                        'buttonUrl' => $links['foundation'],
                        'icon' => 'Ф',
                        'meta' => 'Плита · Лента · Сваи',
                        'previewMediaId' => $preview_ids[0] ?? 0,
                    ],
                    [
                        'title' => 'Стяжка',
                        'description' => 'Расчёт раствора для стяжки пола с понятной подачей исходных параметров.',
                        'buttonLabel' => 'Рассчитать стяжку',
                        'buttonUrl' => $links['screed'],
                        'icon' => 'С',
                        'meta' => '',
                        'previewMediaId' => $preview_ids[1] ?? 0,
                    ],
                    [
                        'title' => 'Кирпич',
                        'description' => 'Оценка количества кирпича или ориентировочного объёма раствора по площади кладки.',
                        'buttonLabel' => 'Рассчитать кирпич',
                        'buttonUrl' => $links['brick'],
                        'icon' => 'К',
                        'meta' => '',
                        'previewMediaId' => $preview_ids[2] ?? 0,
                    ],
                    [
                        'title' => 'Плитка',
                        'description' => 'Помогает оценить площадь облицовки и количество плитки по размеру одной плитки.',
                        'buttonLabel' => 'Рассчитать плитку',
                        'buttonUrl' => $links['tile'],
                        'icon' => 'П',
                        'meta' => '',
                        'previewMediaId' => $preview_ids[3] ?? 0,
                    ],
                    [
                        'title' => 'Гипсокартон',
                        'description' => 'Подходит для быстрой прикидки листовой площади ГКЛ с запасом без расчёта каркаса и крепежа.',
                        'buttonLabel' => 'Рассчитать ГКЛ',
                        'buttonUrl' => $links['drywall'],
                        'icon' => 'Г',
                        'meta' => '',
                        'previewMediaId' => $preview_ids[4] ?? 0,
                    ],
                ],
            ]),
            Constructly_Migration_Helpers::block('constructly/why-brigmaster', [
                'title' => 'Зачем удобен Brigmaster',
                'items' => [
                    [
                        'title' => 'Собрано вокруг реальных задач',
                        'text' => 'Сервисы покрывают частые сценарии частного строительства и ремонта, где особенно важна быстрая предварительная оценка.',
                    ],
                    [
                        'title' => 'Понятная логика расчёта',
                        'text' => 'Формы не перегружены лишними полями, а результаты помогают быстро перейти к понятному ориентиру по объёму материалов.',
                    ],
                    [
                        'title' => 'Подходит для первого ориентирования',
                        'text' => 'Можно использовать на старте проекта, чтобы сравнить варианты, проверить гипотезу и понять масштаб будущей закупки.',
                    ],
                ],
            ]),
            Constructly_Migration_Helpers::block('constructly/trust', [
                'title' => 'Кто отвечает за проект, как считаем и где границы результата',
                'items' => [
                    [
                        'title' => 'Кто отвечает',
                        'text' => 'За страницы, тексты и расчётную логику отвечает команда Brigmaster. Мы поддерживаем сервис как инструмент предварительной оценки, а не как замену проектировщику или сметчику.',
                    ],
                    [
                        'title' => 'Методология',
                        'text' => 'Сначала проверяем реальные поля формы и фактический результат калькулятора, затем описываем их в контенте без обещаний лишней функциональности. Базовые расчёты опираются на геометрию и внутренние коэффициенты сервиса.',
                    ],
                    [
                        'title' => 'Редакционная проверка',
                        'text' => 'Ключевые страницы регулярно пересматриваются и сверяются с фактической логикой калькуляторов, чтобы описания не расходились с интерфейсом и результатом формы.',
                    ],
                    [
                        'title' => 'Ограничения',
                        'text' => 'Результаты подходят для предварительной оценки и не заменяют рабочую документацию, инженерный расчёт или коммерческое предложение подрядчика.',
                    ],
                ],
            ]),
            Constructly_Migration_Helpers::block('constructly/who-its-for', [
                'title' => 'Кому полезен сервис',
                'items' => [
                    'Тем, кто хочет быстро понять объём материалов до общения с подрядчиком или поставщиком.',
                    'Частным заказчикам, которые сравнивают несколько сценариев и хотят заранее понять порядок материалов.',
                    'Прорабам и мастерам для быстрой первичной оценки на созвоне или перед уточнением спецификации.',
                    'Всем, кто хочет сократить количество ручных промежуточных расчётов и быстрее перейти к принятию решения.',
                ],
                'aside' => 'Для предварительной оценки, сравнения сценариев и быстрого понимания объёма материалов.',
            ]),
            Constructly_Migration_Helpers::block('constructly/how-calculations-work', [
                'title' => 'Как устроены расчёты',
                'text' => 'Сервис использует только те входные параметры, которые реально есть в форме, и возвращает ровно тот результат, который можно увидеть в интерфейсе. Для финальных закупок и сложных объектов всегда нужна дополнительная проверка.',
                'linkLabel' => 'Открыть методологию расчётов',
                'linkUrl' => Constructly_Migration_Helpers::resolve_page_url(
                    ['metodologiya', 'metodologiya-raschetov'],
                    ['методология расчетов', 'methodology'],
                    home_url('/metodologiya/')
                ),
            ]),
            self::faq_block(),
            Constructly_Migration_Helpers::block('constructly/final-cta', [
                'title' => 'Начните с нужного калькулятора и получите ориентир уже сейчас',
                'text' => 'Откройте подходящий расчёт, введите параметры объекта и используйте результат как удобную отправную точку для следующего шага.',
                'buttonLabel' => 'Открыть калькуляторы',
                'buttonUrl' => '#calculators',
            ]),
        ];

        return implode("\n\n", $blocks);
    }

    private static function faq_block(): string
    {
        return Constructly_Rank_Math_Faq_Migration::section_heading_block() . "\n\n" . Constructly_Rank_Math_Faq_Migration::serialize_block([
            'titleWrapper' => 'h3',
            'questions' => [
                [
                    'id' => 'faq-estimates',
                    'title' => 'Для чего подходят эти калькуляторы?',
                    'content' => '<p>Они подходят для предварительной оценки материалов, быстрого сравнения сценариев и понимания порядка расхода до точной спецификации.</p>',
                    'visible' => true,
                ],
                [
                    'id' => 'faq-accuracy',
                    'title' => 'Можно ли использовать результат для закупки без уточнений?',
                    'content' => '<p>Лучше воспринимать результат как ориентир. Перед финальной закупкой стоит учесть особенности объекта, проект и требования подрядчика.</p>',
                    'visible' => true,
                ],
                [
                    'id' => 'faq-free',
                    'title' => 'Нужно ли регистрироваться для расчёта?',
                    'content' => '<p>Нет, калькуляторы открываются сразу. Достаточно перейти в нужный раздел и ввести исходные параметры.</p>',
                    'visible' => true,
                ],
                [
                    'id' => 'faq-which-calculator',
                    'title' => 'С какого калькулятора лучше начать?',
                    'content' => '<p>Начните с того этапа, который ближе всего к вашему объекту сейчас: фундамент, стяжка, кирпич, плитка или гипсокартон.</p>',
                    'visible' => true,
                ],
            ],
            'className' => 'bm-rank-math-faq',
        ]);
    }
}
