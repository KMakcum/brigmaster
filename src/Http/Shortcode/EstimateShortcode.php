<?php

declare(strict_types=1);

namespace Brigmaster\Http\Shortcode;

final class EstimateShortcode
{
    public function __construct(
        private readonly string $pluginFilePath
    ) {
    }

    public function registerShortcodes(): void
    {
        add_filter('rank_math/json_ld', [$this, 'addFoundationHubFaqSchema'], 20, 2);

        add_shortcode('brigmaster_concrete_estimator', [$this, 'renderConcreteShortcode']);
        add_shortcode('brigmaster_strip_foundation_estimator', [$this, 'renderStripFoundationShortcode']);
        add_shortcode('brigmaster_pile_foundation_estimator', [$this, 'renderPileFoundationShortcode']);
        add_shortcode('brigmaster_brick_estimator', [$this, 'renderBrickShortcode']);
        add_shortcode('brigmaster_screed_estimator', [$this, 'renderScreedShortcode']);
        add_shortcode('brigmaster_drywall_estimator', [$this, 'renderDrywallShortcode']);
        add_shortcode('brigmaster_tile_estimator', [$this, 'renderTileShortcode']);
        add_shortcode('brigmaster_foundation_hub', [$this, 'renderFoundationHubShortcode']);
    }

    public function renderConcreteShortcode(array $attributes = [], ?string $content = null, string $shortcodeTag = ''): string
    {
        return $this->renderEstimator(
            calculator: 'slab_foundation',
            title: 'Калькулятор плитного фундамента'
        );
    }

    public function renderStripFoundationShortcode(array $attributes = [], ?string $content = null, string $shortcodeTag = ''): string
    {
        return $this->renderEstimator(
            calculator: 'strip_foundation',
            title: 'Калькулятор ленточного фундамента'
        );
    }

    public function renderPileFoundationShortcode(array $attributes = [], ?string $content = null, string $shortcodeTag = ''): string
    {
        return $this->renderEstimator(
            calculator: 'pile_foundation',
            title: 'Калькулятор свайного фундамента'
        );
    }

    public function renderScreedShortcode(array $attributes = [], ?string $content = null, string $shortcodeTag = ''): string
    {
        return $this->renderEstimator(
            calculator: 'screed',
            title: 'Калькулятор стяжки'
        );
    }

    public function renderBrickShortcode(array $attributes = [], ?string $content = null, string $shortcodeTag = ''): string
    {
        return $this->renderEstimator(
            calculator: 'brick',
            title: 'Калькулятор кирпича'
        );
    }

    public function renderDrywallShortcode(array $attributes = [], ?string $content = null, string $shortcodeTag = ''): string
    {
        return $this->renderEstimator(
            calculator: 'drywall',
            title: 'Калькулятор гипсокартона'
        );
    }

    public function renderTileShortcode(array $attributes = [], ?string $content = null, string $shortcodeTag = ''): string
    {
        return $this->renderEstimator(
            calculator: 'tile',
            title: 'Калькулятор плитки'
        );
    }

    public function renderFoundationHubShortcode(array $attributes = [], ?string $content = null, string $shortcodeTag = ''): string
    {
        $this->enqueueAssets();

        ob_start();
        ?>
        <section class="brigmaster-foundation-hub" aria-labelledby="brigmaster-foundation-hub-title">
            <div class="brigmaster-content-block brigmaster-foundation-hub__hero">
                <h1 id="brigmaster-foundation-hub-title" class="brigmaster-foundation-hub__title">Калькулятор фундамента</h1>
                <p class="brigmaster-foundation-hub__lead">
                    Три типа фундамента — три отдельных калькулятора. Здесь можно выбрать тип расчёта фундамента и перейти к нужному калькулятору по карточке.
                    Откройте подходящий вариант и на открывшейся странице следуйте короткой инструкции над формой. Если не уверены в типе основания,
                    лучше обсудить выбор с проектировщиком или инженером по основаниям — калькуляторы помогают с оценкой материалов, а не подбирают конструкцию под ваш участок.
                </p>
                <a class="brigmaster-foundation-hub__cta" href="#foundation-types">К типам фундамента</a>
            </div>

            <div id="foundation-types" class="brigmaster-content-block brigmaster-content-block--muted">
                <h2>Выберите тип фундамента</h2>
                <div class="brigmaster-foundation-hub__cards">
                    <article class="brigmaster-foundation-hub__card">
                        <div class="brigmaster-foundation-hub__icon" aria-hidden="true"><svg class="brigmaster-foundation-hub__icon-svg" focusable="false" aria-hidden="true" version="1.0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 415 415" preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,415.000000) scale(0.100000,-0.100000)" fill="currentColor" stroke="none"><path d="M1865 3364 c-99 -58 -205 -121 -235 -139 -426 -252 -709 -420 -727 -431 -46 -29 -37 -104 12 -104 14 0 54 18 90 40 l65 40 0 -283 0 -283 -27 -11 c-69 -27 -518 -198 -603 -229 -52 -19 -103 -44 -112 -55 -16 -19 -18 -46 -18 -233 0 -160 3 -217 13 -230 7 -10 90 -47 183 -81 274 -102 551 -206 839 -315 149 -56 358 -135 466 -175 108 -40 206 -78 217 -84 36 -19 89 -12 174 23 74 30 559 213 1108 416 467 173 534 200 541 212 5 7 9 110 9 229 0 192 -2 219 -17 238 -12 13 -152 71 -383 159 l-365 139 -3 276 c-1 173 1 277 7 277 6 0 37 -16 71 -36 64 -37 105 -42 123 -13 27 43 7 61 -270 226 -277 166 -497 296 -618 366 -38 22 -97 57 -130 78 -102 63 -158 89 -195 89 -26 0 -79 -26 -215 -106z m450 -132 c121 -71 283 -166 360 -212 77 -46 179 -106 227 -134 l88 -51 0 -442 0 -443 -905 0 -905 0 0 443 0 442 58 34 c31 19 118 70 192 114 444 263 642 377 655 377 6 0 110 -58 230 -128z m-1243 -1230 c-3 -96 -1 -111 17 -133 l19 -24 964 -3 c995 -3 998 -2 1013 37 4 9 6 64 5 124 0 59 2 107 5 107 4 0 126 -45 273 -101 147 -55 287 -108 311 -116 31 -11 39 -18 30 -24 -14 -7 -227 -86 -804 -295 -159 -58 -349 -127 -420 -154 -262 -97 -384 -140 -398 -140 -12 0 -349 119 -482 170 -27 11 -232 85 -455 166 -664 241 -690 250 -694 255 -2 2 116 49 262 103 147 55 283 108 302 118 19 9 40 17 45 18 6 0 9 -40 7 -108z m-495 -268 c98 -36 309 -113 468 -171 160 -57 331 -120 380 -138 726 -269 637 -253 885 -161 96 35 294 108 440 161 146 54 418 153 605 222 187 69 357 131 378 139 l38 13 -3 -70 -3 -70 -65 -23 c-201 -72 -517 -187 -745 -271 -442 -163 -635 -235 -754 -277 l-114 -41 -116 40 c-64 23 -240 87 -391 143 -151 56 -354 130 -450 165 -96 35 -278 101 -405 148 -126 46 -254 92 -282 101 l-53 18 0 69 c0 38 2 69 4 69 2 0 85 -30 183 -66z"/></g></svg></div>
                        <h3>Плитный фундамент</h3>
                        <p class="brigmaster-foundation-hub__card-thesis">
                            Монолитная плита на весь контур — удобно оценить бетон, арматуру и опалубку в одном расчёте.
                        </p>
                        <a href="/kalkulyator-plitnogo-fundamenta/" class="brigmaster-foundation-hub__cta brigmaster-foundation-hub__cta--card">Открыть калькулятор плиты</a>
                    </article>

                    <article class="brigmaster-foundation-hub__card">
                        <div class="brigmaster-foundation-hub__icon" aria-hidden="true"><svg class="brigmaster-foundation-hub__icon-svg" focusable="false" aria-hidden="true" version="1.0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 420 420" preserveAspectRatio="xMidYMid meet"><g transform="translate(0,420) scale(0.100000,-0.100000)" fill="currentColor" stroke="none"><path d="M1925 3388 c-324 -190 -668 -394 -920 -544 -132 -79 -155 -106 -120 -144 26 -29 52 -25 123 20 l62 39 0 -277 0 -278 -27 -10 c-16 -6 -136 -53 -268 -104 -132 -50 -286 -110 -342 -132 -61 -23 -109 -48 -118 -61 -12 -17 -15 -60 -15 -224 0 -111 4 -213 9 -226 6 -16 23 -29 48 -37 21 -7 126 -45 233 -86 284 -106 926 -345 1140 -424 102 -38 219 -82 260 -99 41 -17 84 -31 95 -31 11 0 56 14 100 31 129 49 308 116 485 180 91 33 296 109 455 169 160 59 393 145 519 191 191 70 230 87 237 107 5 13 9 114 9 225 0 164 -3 207 -15 224 -9 13 -62 40 -138 69 -67 26 -212 82 -322 124 -110 43 -225 87 -255 98 -30 11 -55 25 -54 31 1 6 2 130 3 276 l1 265 64 -40 c71 -45 110 -51 126 -19 19 34 4 65 -47 97 -46 29 -371 222 -483 287 -30 17 -176 103 -325 190 -148 87 -284 164 -302 172 -59 24 -91 15 -218 -59z m254 -81 c72 -41 481 -280 714 -418 l107 -63 0 -443 0 -443 -910 0 -910 0 0 443 0 442 203 119 c262 154 467 274 592 347 55 32 106 59 114 59 7 0 48 -19 90 -43z m-1111 -1254 l-3 -57 -130 -47 c-143 -52 -174 -76 -139 -108 10 -10 138 -61 284 -114 146 -53 371 -135 500 -182 454 -168 482 -177 525 -172 22 2 166 50 320 107 308 112 689 250 860 311 60 22 113 43 118 48 5 5 7 19 5 32 -2 18 -18 28 -83 51 -44 16 -110 40 -147 54 l-68 26 0 54 c0 30 2 54 5 54 2 0 33 -11 67 -25 87 -34 395 -150 500 -188 49 -18 88 -34 88 -37 0 -3 -53 -24 -117 -47 -65 -23 -228 -83 -363 -133 -135 -50 -274 -102 -310 -115 -36 -13 -121 -44 -190 -70 -547 -203 -676 -247 -715 -242 -44 5 -465 158 -1312 476 -178 66 -327 121 -333 121 -5 0 -10 5 -10 10 0 6 5 10 11 10 11 0 433 160 554 209 39 16 74 30 78 30 4 1 7 -25 5 -56z m12 -162 c0 -11 12 -29 26 -40 27 -21 31 -21 988 -21 1046 0 996 -3 1010 55 8 31 9 31 80 2 l58 -24 -74 -25 c-190 -67 -634 -227 -823 -298 -115 -43 -220 -81 -232 -84 -15 -5 -93 19 -245 74 -406 149 -662 241 -778 282 -154 53 -151 51 -88 75 68 27 78 27 78 4z m-280 -268 c387 -145 717 -268 1060 -393 l185 -68 3 -141 c1 -78 0 -141 -3 -141 -2 0 -69 25 -147 56 -79 30 -204 78 -278 105 -74 27 -169 63 -210 79 -41 16 -185 70 -320 120 -135 50 -350 130 -477 177 l-233 87 0 139 c0 129 1 139 18 132 9 -4 190 -72 402 -152z m2998 11 l-3 -136 -350 -128 c-285 -104 -1229 -457 -1298 -485 -16 -7 -17 2 -15 135 l3 143 190 69 c105 38 219 80 255 93 222 80 1090 402 1160 430 62 25 61 28 58 -121z"/></g></svg></div>
                        <h3>Ленточный фундамент</h3>
                        <p class="brigmaster-foundation-hub__card-thesis">
                            Лента под несущими стенами — считаем объём по длине и сечению, можно задать участки.
                        </p>
                        <a href="/kalkulyator-lentochnogo-fundamenta/" class="brigmaster-foundation-hub__cta brigmaster-foundation-hub__cta--card">Открыть калькулятор ленты</a>
                    </article>

                    <article class="brigmaster-foundation-hub__card">
                        <div class="brigmaster-foundation-hub__icon" aria-hidden="true"><svg class="brigmaster-foundation-hub__icon-svg" focusable="false" aria-hidden="true" version="1.0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 420 420" preserveAspectRatio="xMidYMid meet"><g transform="translate(0,420) scale(0.100000,-0.100000)" fill="currentColor" stroke="none"><path d="M1870 3441 c-102 -59 -279 -163 -395 -231 -455 -266 -594 -352 -600 -371 -10 -30 4 -58 30 -65 17 -4 43 5 89 30 35 20 67 36 70 36 4 0 5 -113 4 -252 l-3 -252 -308 -105 c-169 -58 -312 -113 -317 -121 -5 -8 -10 -74 -10 -146 0 -113 2 -133 18 -146 9 -8 70 -32 135 -53 l117 -39 0 -377 0 -377 24 -29 c32 -37 84 -53 178 -53 85 0 161 22 183 53 13 18 15 73 15 329 0 237 3 308 12 308 7 0 88 -26 180 -57 l168 -58 0 -342 c0 -193 4 -353 10 -367 16 -42 63 -60 178 -64 81 -3 113 -1 148 13 75 28 74 25 74 331 0 149 2 273 5 276 3 3 53 -11 111 -30 l105 -34 97 31 c53 18 103 35 110 37 9 5 12 -54 12 -273 0 -230 3 -282 15 -300 22 -32 98 -53 189 -53 91 0 144 12 178 39 l23 19 5 357 5 357 169 59 c93 33 173 59 178 59 4 0 8 -140 8 -310 l0 -311 25 -23 c39 -36 103 -50 203 -43 95 6 155 26 172 56 6 13 10 156 10 398 l0 379 129 42 c77 25 132 49 135 58 3 9 6 76 6 150 0 128 -1 134 -22 148 -13 8 -157 58 -320 112 l-298 99 0 253 c0 138 2 252 4 252 2 0 30 -16 62 -35 61 -37 83 -42 115 -25 22 12 25 49 7 72 -7 8 -56 40 -108 70 -52 30 -203 120 -335 198 -132 79 -312 184 -400 235 -88 51 -198 115 -245 144 -124 74 -125 74 -350 -58z m263 -17 c18 -9 205 -118 417 -242 212 -124 402 -235 422 -246 l38 -21 0 -438 0 -437 -917 2 -918 3 -3 431 -2 430 162 96 c569 335 748 438 761 438 4 0 22 -7 40 -16z m-1063 -1313 c0 -133 1 -141 22 -155 20 -14 131 -16 1001 -16 l978 0 24 25 c24 23 25 29 25 155 0 71 2 130 4 130 7 0 490 -161 494 -164 2 -2 -41 -19 -95 -36 -54 -18 -377 -129 -718 -248 -341 -118 -642 -221 -670 -229 l-50 -14 -435 151 c-512 178 -1025 355 -1060 365 -19 6 25 25 215 91 132 45 246 83 253 83 9 1 12 -33 12 -138z m-305 -176 c945 -329 1311 -455 1325 -455 18 0 181 54 620 207 369 128 964 333 968 333 1 0 2 -32 0 -71 l-3 -72 -40 -12 c-22 -7 -296 -101 -610 -210 -313 -108 -653 -225 -753 -259 l-184 -61 -296 102 c-164 56 -490 169 -727 251 -236 82 -456 157 -487 167 l-58 18 0 74 c0 68 2 74 18 68 9 -4 111 -40 227 -80z m255 -620 c0 -263 -2 -306 -16 -319 -18 -19 -155 -23 -198 -6 l-26 10 0 258 c0 143 3 299 6 348 l7 90 113 -39 114 -39 0 -303z m2380 30 l0 -344 -22 -13 c-32 -17 -170 -17 -191 1 -15 12 -17 47 -17 320 l0 307 108 36 c59 21 110 37 115 38 4 0 7 -155 7 -345z m-1710 39 l95 -32 0 -277 c0 -249 -2 -278 -17 -286 -23 -12 -175 -11 -199 1 -18 10 -19 25 -19 326 l0 315 23 -7 c12 -4 65 -22 117 -40z m940 -273 l0 -319 -22 -6 c-42 -11 -174 -6 -191 7 -16 11 -17 37 -15 286 l3 274 105 38 c58 21 108 38 113 38 4 1 7 -142 7 -318z"/></g></svg></div>
                        <h3>Свайный фундамент</h3>
                        <p class="brigmaster-foundation-hub__card-thesis">
                            Сваи и ростверк — оценка по вашим вводным; итог по несущей способности и геологии всё равно в проекте.
                        </p>
                        <a href="/kalkulyator-svajnogo-fundamenta/" class="brigmaster-foundation-hub__cta brigmaster-foundation-hub__cta--card">Открыть калькулятор свай</a>
                    </article>
                </div>
            </div>

            <div class="brigmaster-content-block">
                <h2>Как выбрать тип фундамента</h2>
                <ol class="brigmaster-foundation-hub__criteria">
                    <li>Проверьте тип грунта и уровень грунтовых вод на участке.</li>
                    <li>Оцените вес дома, этажность и тип стеновых материалов.</li>
                    <li>Сравните бюджет и сроки реализации разных решений.</li>
                    <li>Учитывайте рельеф, перепады высот и особенности участка.</li>
                    <li>Согласуйте итоговый вариант с проектировщиком.</li>
                </ol>
            </div>

            <div class="brigmaster-content-block">
                <h2>Частые вопросы по фундаментам</h2>
                <div id="foundation-hub-faq-1" class="brigmaster-faq-item">
                    <h3 class="brigmaster-faq-item__question">Какой фундамент выбрать для одноэтажного дома?</h3>
                    <p class="brigmaster-faq-item__answer">Выбор зависит от грунта, конструкции дома и бюджета.</p>
                </div>
                <div id="foundation-hub-faq-2" class="brigmaster-faq-item">
                    <h3 class="brigmaster-faq-item__question">Можно ли ориентироваться только на онлайн-калькулятор?</h3>
                    <p class="brigmaster-faq-item__answer">Калькулятор дает предварительную оценку, но не заменяет проект.</p>
                </div>
                <div id="foundation-hub-faq-3" class="brigmaster-faq-item">
                    <h3 class="brigmaster-faq-item__question">Нужен ли запас по материалам?</h3>
                    <p class="brigmaster-faq-item__answer">Обычно закладывают запас на технологические потери.</p>
                </div>
                <div id="foundation-hub-faq-4" class="brigmaster-faq-item">
                    <h3 class="brigmaster-faq-item__question">Почему результаты на разных страницах отличаются?</h3>
                    <p class="brigmaster-faq-item__answer">Каждый тип фундамента рассчитывается по своей модели.</p>
                </div>
            </div>

            <div class="brigmaster-content-block brigmaster-content-block--muted">
                <h2>Полезные ссылки</h2>
                <ul class="brigmaster-foundation-hub__links">
                    <li><a href="/kalkulyator-plitnogo-fundamenta/">Калькулятор плитного фундамента</a></li>
                    <li><a href="/kalkulyator-lentochnogo-fundamenta/">Калькулятор ленточного фундамента</a></li>
                    <li><a href="/kalkulyator-svajnogo-fundamenta/">Калькулятор свайного фундамента</a></li>
                </ul>
                <ul class="brigmaster-foundation-hub__links brigmaster-foundation-hub__links--compact">
                    <li><a href="/kalkulyator-kirpicha/">Калькулятор кирпича</a></li>
                    <li><a href="/kalkulyator-styazhki/">Калькулятор стяжки</a></li>
                    <li><a href="/kalkulyator-plitki/">Калькулятор плитки</a></li>
                </ul>
            </div>
        </section>
        <?php

        return (string) ob_get_clean();
    }

    /**
     * Injects FAQPage for pages that only render [brigmaster_foundation_hub] (Rank Math merges into WebPage).
     *
     * @param array<string, mixed> $data
     * @param mixed                $jsonLd Unused; signature matches rank_math/json_ld.
     * @return array<string, mixed>
     */
    public function addFoundationHubFaqSchema(array $data, mixed $jsonLd): array
    {
        if (!is_singular()) {
            return $data;
        }

        global $post;
        if (!$post instanceof \WP_Post) {
            return $data;
        }

        if (!has_shortcode((string) $post->post_content, 'brigmaster_foundation_hub')) {
            return $data;
        }

        $pairs = [
            [
                'id' => 'foundation-hub-faq-1',
                'name' => 'Какой фундамент выбрать для одноэтажного дома?',
                'text' => 'Выбор зависит от грунта, конструкции дома и бюджета.',
            ],
            [
                'id' => 'foundation-hub-faq-2',
                'name' => 'Можно ли ориентироваться только на онлайн-калькулятор?',
                'text' => 'Калькулятор дает предварительную оценку, но не заменяет проект.',
            ],
            [
                'id' => 'foundation-hub-faq-3',
                'name' => 'Нужен ли запас по материалам?',
                'text' => 'Обычно закладывают запас на технологические потери.',
            ],
            [
                'id' => 'foundation-hub-faq-4',
                'name' => 'Почему результаты на разных страницах отличаются?',
                'text' => 'Каждый тип фундамента рассчитывается по своей модели.',
            ],
        ];

        if (!isset($data['faqs'])) {
            $data['faqs'] = [
                '@type' => 'FAQPage',
                'mainEntity' => [],
            ];
        }

        $permalinkBase = get_permalink($post) . '#';
        foreach ($pairs as $row) {
            $data['faqs']['mainEntity'][] = [
                '@type' => 'Question',
                'url' => esc_url($permalinkBase . $row['id']),
                'name' => $row['name'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $row['text'],
                ],
            ];
        }

        return $data;
    }

    private function renderEstimator(string $calculator, string $title): string
    {
        if (!in_array($calculator, ['slab_foundation', 'strip_foundation', 'pile_foundation', 'brick', 'screed', 'drywall', 'tile'], true)) {
            return '';
        }

        $this->enqueueAssets();

        $instanceId = wp_unique_id('brigmaster-' . $calculator . '-');
        $modeFieldId = $instanceId . 'mode';
        $areaFieldId = $instanceId . 'area';
        $thicknessFieldId = $instanceId . 'thickness';
        $subTypeFieldId = $instanceId . 'sub-type';
        $lengthFieldId = $instanceId . 'length';
        $widthFieldId = $instanceId . 'width';
        $heightFieldId = $instanceId . 'height';
        $includeReinforcementFieldId = $instanceId . 'include-reinforcement';
        $includeFormworkFieldId = $instanceId . 'include-formwork';
        $totalLengthFieldId = $instanceId . 'total-length-m';
        $widthMFieldId = $instanceId . 'width-m';
        $heightMFieldId = $instanceId . 'height-m';
        $houseLengthFieldId = $instanceId . 'house-length-m';
        $houseWidthFieldId = $instanceId . 'house-width-m';
        $houseStripWidthFieldId = $instanceId . 'strip-section-width-m';
        $longitudinalBarsCountFieldId = $instanceId . 'longitudinal-bars-count';
        $longitudinalDiameterFieldId = $instanceId . 'longitudinal-diameter-mm';
        $longitudinalReserveFieldId = $instanceId . 'longitudinal-reserve-percent';
        $transverseDiameterFieldId = $instanceId . 'transverse-diameter-mm';
        $transverseStepFieldId = $instanceId . 'transverse-step-mm';
        $transverseReserveFieldId = $instanceId . 'transverse-reserve-percent';
        $includePilesFieldId = $instanceId . 'include-piles';
        $includeGrillageFieldId = $instanceId . 'include-grillage';
        $pileTypeFieldId = $instanceId . 'pile-type';
        $pilesCountFieldId = $instanceId . 'piles-count';
        $pileShaftDiameterFieldId = $instanceId . 'pile-shaft-diameter-m';
        $pileShaftHeightFieldId = $instanceId . 'pile-shaft-height-m';
        $includePileBaseFieldId = $instanceId . 'include-pile-base';
        $pileBaseDiameterFieldId = $instanceId . 'pile-base-diameter-m';
        $pileBaseHeightFieldId = $instanceId . 'pile-base-height-m';
        $includePileReinforcementFieldId = $instanceId . 'include-pile-reinforcement';
        $pileReinforcementBarsCountFieldId = $instanceId . 'pile-reinforcement-bars-count';
        $pileReinforcementDiameterFieldId = $instanceId . 'pile-reinforcement-diameter-mm';
        $pileReinforcementReserveFieldId = $instanceId . 'pile-reinforcement-reserve-percent';
        $rebarDiameterFieldId = $instanceId . 'rebar-diameter-mm';
        $rebarStepFieldId = $instanceId . 'rebar-step-mm';
        $rebarLayersFieldId = $instanceId . 'rebar-layers';
        $rebarReserveFieldId = $instanceId . 'rebar-reserve-percent';
        $formworkHeightFieldId = $instanceId . 'formwork-height-m';
        $formworkReserveFieldId = $instanceId . 'formwork-reserve-percent';
        $tileLengthFieldId = $instanceId . 'tile-length-cm';
        $tileWidthFieldId = $instanceId . 'tile-width-cm';
        $areaHintId = $instanceId . 'area-hint';
        $thicknessHintId = $instanceId . 'thickness-hint';
        $lengthHintId = $instanceId . 'length-hint';
        $widthHintId = $instanceId . 'width-hint';
        $heightHintId = $instanceId . 'height-hint';
        $areaModeNoticeId = $instanceId . 'area-mode-notice';
        $rebarDiameterHintId = $instanceId . 'rebar-diameter-hint';
        $rebarStepHintId = $instanceId . 'rebar-step-hint';
        $rebarLayersHintId = $instanceId . 'rebar-layers-hint';
        $rebarReserveHintId = $instanceId . 'rebar-reserve-hint';
        $formworkHeightHintId = $instanceId . 'formwork-height-hint';
        $formworkReserveHintId = $instanceId . 'formwork-reserve-hint';
        $tileLengthHintId = $instanceId . 'tile-length-hint';
        $tileWidthHintId = $instanceId . 'tile-width-hint';
        $rebarDiameterTooltipId = $instanceId . 'rebar-diameter-tooltip';
        $rebarStepTooltipId = $instanceId . 'rebar-step-tooltip';
        $rebarLayersTooltipId = $instanceId . 'rebar-layers-tooltip';
        $rebarReserveTooltipId = $instanceId . 'rebar-reserve-tooltip';
        $formworkHeightTooltipId = $instanceId . 'formwork-height-tooltip';
        $formworkReserveTooltipId = $instanceId . 'formwork-reserve-tooltip';
        $reinforcementModeLockTooltipId = $instanceId . 'reinforcement-mode-lock-tooltip';
        $formworkModeLockTooltipId = $instanceId . 'formwork-mode-lock-tooltip';
        $modeHintId = $instanceId . 'mode-hint';
        $grillageModeHintId = $instanceId . 'grillage-mode-hint';
        $estimatorModifierClass = in_array($calculator, ['strip_foundation', 'pile_foundation'], true)
            ? ' brigmaster-estimator--with-accordions'
            : '';
        ob_start();
        ?>
        <div class="brigmaster-estimator brigmaster-estimator--<?php echo esc_attr(str_replace('_', '-', $calculator)); ?><?php echo esc_attr($estimatorModifierClass); ?>" data-calculator="<?php echo esc_attr($calculator); ?>">
            <h2 class="brigmaster-estimator__title"><?php echo esc_html($title); ?></h2>
            <form class="brigmaster-estimate-form" novalidate>
                <input type="hidden" name="calculator" value="<?php echo esc_attr($calculator); ?>">

                <?php if ($calculator !== 'pile_foundation') : ?>
                    <div class="brigmaster-estimator__field" data-field-group="estimator-mode">
                        <label for="<?php echo esc_attr($modeFieldId); ?>">Режим расчета</label>
                        <select id="<?php echo esc_attr($modeFieldId); ?>" name="mode" required aria-describedby="<?php echo esc_attr($modeHintId); ?>">
                            <?php if ($calculator === 'slab_foundation') : ?>
                                <option value="dimensions">По длине и ширине</option>
                                <option value="area">По площади</option>
                            <?php elseif ($calculator === 'strip_foundation') : ?>
                                <option value="perimeter">По общей длине ленты</option>
                                <option value="house">По параметрам дома</option>
                                <option value="segments">По участкам ленты</option>
                            <?php else : ?>
                                <option value="normative">Норматив</option>
                                <option value="reserve">С запасом</option>
                                <option value="beginner">Для новичка</option>
                            <?php endif; ?>
                        </select>
                        <p id="<?php echo esc_attr($modeHintId); ?>" class="brigmaster-estimator__mode-hint" data-mode-hint aria-live="polite"></p>
                        <div class="brigmaster-estimator__error" data-field-error="mode" aria-live="polite"></div>
                    </div>
                <?php endif; ?>

                <?php if ($calculator === 'slab_foundation') : ?>
                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--two" data-field-group="slab-dimensions">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($lengthFieldId); ?>">Длина (м)</label>
                            <input id="<?php echo esc_attr($lengthFieldId); ?>" type="number" name="length" min="0.01" step="0.01" value="8" aria-describedby="<?php echo esc_attr($lengthHintId); ?>">
                            <p id="<?php echo esc_attr($lengthHintId); ?>" class="brigmaster-estimator__hint">Введите длину в метрах.</p>
                            <div class="brigmaster-estimator__error" data-field-error="length" aria-live="polite"></div>
                        </div>

                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($widthFieldId); ?>">Ширина (м)</label>
                            <input id="<?php echo esc_attr($widthFieldId); ?>" type="number" name="width" min="0.01" step="0.01" value="6" aria-describedby="<?php echo esc_attr($widthHintId); ?>">
                            <p id="<?php echo esc_attr($widthHintId); ?>" class="brigmaster-estimator__hint">Введите ширину в метрах.</p>
                            <div class="brigmaster-estimator__error" data-field-error="width" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-group--hidden brigmaster-estimator__field-grid" data-field-group="slab-area">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($areaFieldId); ?>">Площадь (м²)</label>
                            <input id="<?php echo esc_attr($areaFieldId); ?>" type="number" name="area" min="0.01" step="0.01" value="48" aria-describedby="<?php echo esc_attr($areaHintId); ?>">
                            <p id="<?php echo esc_attr($areaHintId); ?>" class="brigmaster-estimator__hint">Введите площадь в м² (например, 48).</p>
                            <div class="brigmaster-estimator__error" data-field-error="area" aria-live="polite"></div>
                        </div>

                        <div id="<?php echo esc_attr($areaModeNoticeId); ?>" class="brigmaster-estimator__notice brigmaster-estimator__field-group--hidden" data-area-mode-notice>
                            Для расчета арматуры и опалубки дополнительно нужны длина и ширина.
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid" data-field-group="slab-height">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($heightFieldId); ?>">Высота (м)</label>
                            <input id="<?php echo esc_attr($heightFieldId); ?>" type="number" name="height" min="0.001" step="0.001" value="0.25" aria-describedby="<?php echo esc_attr($heightHintId); ?>">
                            <p id="<?php echo esc_attr($heightHintId); ?>" class="brigmaster-estimator__hint">Введите высоту в метрах.</p>
                            <div class="brigmaster-estimator__error" data-field-error="height" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group">
                        <div class="brigmaster-estimator__field brigmaster-estimator__toggle" data-toggle-field="reinforcement">
                            <input id="<?php echo esc_attr($includeReinforcementFieldId); ?>" type="checkbox" name="includeReinforcement" value="1" checked>
                            <label for="<?php echo esc_attr($includeReinforcementFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Учитывать арматуру</span>
                                <span class="brigmaster-estimator__tooltip-anchor brigmaster-estimator__tooltip-anchor--hidden">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger data-mode-lock-trigger aria-label="Подсказка: арматура недоступна в режиме по площади" aria-expanded="false" aria-controls="<?php echo esc_attr($reinforcementModeLockTooltipId); ?>">i</button>
                                    <div id="<?php echo esc_attr($reinforcementModeLockTooltipId); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Для расчета арматуры и опалубки нужны длина и ширина. Переключитесь в режим расчета по длине и ширине.
                                    </div>
                                </span>
                            </label>
                            <div class="brigmaster-estimator__error" data-field-error="includeReinforcement" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four" data-field-group="slab-reinforcement">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($rebarDiameterFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Диаметр арматуры (мм)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($rebarDiameterTooltipId); ?>">i</button>
                                    <div id="<?php echo esc_attr($rebarDiameterTooltipId); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Диаметр влияет на массу арматуры. Чем больше диаметр, тем больше итоговый вес.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($rebarDiameterFieldId); ?>" type="number" name="rebarDiameterMm" min="1" step="1" value="12" aria-describedby="<?php echo esc_attr($rebarDiameterHintId); ?>">
                            <p id="<?php echo esc_attr($rebarDiameterHintId); ?>" class="brigmaster-estimator__hint">Стандартно для частного дома обычно 10-14 мм.</p>
                            <div class="brigmaster-estimator__error" data-field-error="rebarDiameterMm" aria-live="polite"></div>
                        </div>

                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($rebarStepFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Шаг арматуры (мм)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: шаг арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($rebarStepTooltipId); ?>">i</button>
                                    <div id="<?php echo esc_attr($rebarStepTooltipId); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Шаг сетки между стержнями, обычно 150-250 мм.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($rebarStepFieldId); ?>" type="number" name="rebarStepMm" min="50" step="10" value="200" aria-describedby="<?php echo esc_attr($rebarStepHintId); ?>">
                            <p id="<?php echo esc_attr($rebarStepHintId); ?>" class="brigmaster-estimator__hint">Чем меньше шаг, тем плотнее сетка и выше расход.</p>
                            <div class="brigmaster-estimator__error" data-field-error="rebarStepMm" aria-live="polite"></div>
                        </div>

                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($rebarLayersFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Слои арматуры</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: слои арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($rebarLayersTooltipId); ?>">i</button>
                                    <div id="<?php echo esc_attr($rebarLayersTooltipId); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        1 слой для легких нагрузок, 2 слоя - стандарт для жилых домов.
                                    </div>
                                </span>
                            </label>
                            <select id="<?php echo esc_attr($rebarLayersFieldId); ?>" name="rebarLayers" aria-describedby="<?php echo esc_attr($rebarLayersHintId); ?>">
                                <option value="1">1 слой</option>
                                <option value="2" selected>2 слоя</option>
                            </select>
                            <p id="<?php echo esc_attr($rebarLayersHintId); ?>" class="brigmaster-estimator__hint">Для монолитной плиты чаще используют 2 слоя.</p>
                            <div class="brigmaster-estimator__error" data-field-error="rebarLayers" aria-live="polite"></div>
                        </div>

                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($rebarReserveFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Запас арматуры (%)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: запас арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($rebarReserveTooltipId); ?>">i</button>
                                    <div id="<?php echo esc_attr($rebarReserveTooltipId); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Рекомендуемый запас 5-15% в зависимости от сложности схемы.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($rebarReserveFieldId); ?>" type="number" name="rebarReservePercent" min="1" step="1" value="10" aria-describedby="<?php echo esc_attr($rebarReserveHintId); ?>">
                            <p id="<?php echo esc_attr($rebarReserveHintId); ?>" class="brigmaster-estimator__hint">Компенсирует подрезку и нахлесты.</p>
                            <div class="brigmaster-estimator__error" data-field-error="rebarReservePercent" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group">
                        <div class="brigmaster-estimator__field brigmaster-estimator__toggle" data-toggle-field="formwork">
                            <input id="<?php echo esc_attr($includeFormworkFieldId); ?>" type="checkbox" name="includeFormwork" value="1" checked>
                            <label for="<?php echo esc_attr($includeFormworkFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Учитывать опалубку</span>
                                <span class="brigmaster-estimator__tooltip-anchor brigmaster-estimator__tooltip-anchor--hidden">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger data-mode-lock-trigger aria-label="Подсказка: опалубка недоступна в режиме по площади" aria-expanded="false" aria-controls="<?php echo esc_attr($formworkModeLockTooltipId); ?>">i</button>
                                    <div id="<?php echo esc_attr($formworkModeLockTooltipId); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Для расчета арматуры и опалубки нужны длина и ширина. Переключитесь в режим расчета по длине и ширине.
                                    </div>
                                </span>
                            </label>
                            <div class="brigmaster-estimator__error" data-field-error="includeFormwork" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--two" data-field-group="slab-formwork">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($formworkHeightFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Высота опалубки (м)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: высота опалубки" aria-expanded="false" aria-controls="<?php echo esc_attr($formworkHeightTooltipId); ?>">i</button>
                                    <div id="<?php echo esc_attr($formworkHeightTooltipId); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Опалубка считается по периметру плиты и указанной высоте.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($formworkHeightFieldId); ?>" type="number" name="formworkHeightM" min="0.01" step="0.01" value="0.30" aria-describedby="<?php echo esc_attr($formworkHeightHintId); ?>">
                            <p id="<?php echo esc_attr($formworkHeightHintId); ?>" class="brigmaster-estimator__hint">Высота бокового щита, обычно равна высоте заливки.</p>
                            <div class="brigmaster-estimator__error" data-field-error="formworkHeightM" aria-live="polite"></div>
                        </div>

                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($formworkReserveFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Запас опалубки (%)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: запас опалубки" aria-expanded="false" aria-controls="<?php echo esc_attr($formworkReserveTooltipId); ?>">i</button>
                                    <div id="<?php echo esc_attr($formworkReserveTooltipId); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Рекомендуется 5-15% в зависимости от типа щитов и геометрии.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($formworkReserveFieldId); ?>" type="number" name="formworkReservePercent" min="1" step="1" value="10" aria-describedby="<?php echo esc_attr($formworkReserveHintId); ?>">
                            <p id="<?php echo esc_attr($formworkReserveHintId); ?>" class="brigmaster-estimator__hint">Запас на подрезку и стыковки.</p>
                            <div class="brigmaster-estimator__error" data-field-error="formworkReservePercent" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($calculator === 'pile_foundation') : ?>
                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--two" data-field-group="pile-toggles">
                        <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
                            <input id="<?php echo esc_attr($includePilesFieldId); ?>" type="checkbox" name="includePiles" value="1" checked>
                            <label for="<?php echo esc_attr($includePilesFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Рассчитать сваи</span>
                            </label>
                            <div class="brigmaster-estimator__error" data-field-error="includePiles" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
                            <input id="<?php echo esc_attr($includeGrillageFieldId); ?>" type="checkbox" name="includeGrillage" value="1" checked>
                            <label for="<?php echo esc_attr($includeGrillageFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Рассчитать ростверк</span>
                            </label>
                            <div class="brigmaster-estimator__error" data-field-error="includeGrillage" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (in_array($calculator, ['strip_foundation', 'pile_foundation'], true)) : ?>
                    <div class="brigmaster-estimator__accordions" data-estimator-accordions>
                    <?php if ($calculator === 'pile_foundation') : ?>
                        <details class="brigmaster-estimator__accordion" open data-pile-panel="piles">
                            <summary class="brigmaster-estimator__accordion-summary">Сваи</summary>
                            <div class="brigmaster-estimator__accordion-body">
                        <div class="brigmaster-estimator__field-group" data-field-group="pile-type">
                            <div class="brigmaster-estimator__field">
                                <label for="<?php echo esc_attr($pileTypeFieldId); ?>">Тип свай</label>
                                <select id="<?php echo esc_attr($pileTypeFieldId); ?>" name="pileType">
                                    <option value="bored">Буронабивные</option>
                                    <option value="screw">Винтовые</option>
                                    <option value="driven">Забивные</option>
                                </select>
                                <p class="brigmaster-estimator__hint" data-pile-type-note>
                                    Для винтовых и забивных свай бетон не требуется. Для буронабивных выполняется расчет бетона.
                                </p>
                                <div class="brigmaster-estimator__error" data-field-error="pileType" aria-live="polite"></div>
                            </div>
                        </div>

                        <div class="brigmaster-estimator__field-group" data-field-group="pile-base-toggle">
                            <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
                                <input id="<?php echo esc_attr($includePileBaseFieldId); ?>" type="checkbox" name="includePileBase" value="1" checked>
                                <label for="<?php echo esc_attr($includePileBaseFieldId); ?>" class="brigmaster-estimator__label-row">
                                    <span>Учитывать уширение сваи (пяту)</span>
                                </label>
                                <div class="brigmaster-estimator__error" data-field-error="includePileBase" aria-live="polite"></div>
                            </div>
                        </div>

                        <div class="brigmaster-estimator__field-group" data-field-group="pile-reinforcement-toggle">
                            <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
                                <input id="<?php echo esc_attr($includePileReinforcementFieldId); ?>" type="checkbox" name="includePileReinforcement" value="1">
                                <label for="<?php echo esc_attr($includePileReinforcementFieldId); ?>" class="brigmaster-estimator__label-row">
                                    <span>Учитывать арматуру свай</span>
                                </label>
                                <div class="brigmaster-estimator__error" data-field-error="includePileReinforcement" aria-live="polite"></div>
                            </div>
                        </div>

                        <div class="brigmaster-estimator__field-group" data-field-group="pile-primary-row">
                            <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--pile-primary" data-pile-primary-grid>
                                <div class="brigmaster-estimator__field brigmaster-estimator__field--pile-count">
                                    <label for="<?php echo esc_attr($pilesCountFieldId); ?>" class="brigmaster-estimator__label-row">
                                        <span>Количество свай</span>
                                        <span class="brigmaster-estimator__tooltip-anchor">
                                            <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: количество свай" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'pile-count-tooltip'); ?>">i</button>
                                            <div id="<?php echo esc_attr($instanceId . 'pile-count-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                                Количество свай определяют по нагрузке здания и несущей способности одной сваи по геологии участка. Для точного подбора используйте проект.
                                            </div>
                                        </span>
                                    </label>
                                    <input id="<?php echo esc_attr($pilesCountFieldId); ?>" type="number" name="pilesCount" min="1" step="1" value="20" inputmode="numeric">
                                    <div class="brigmaster-estimator__error" data-field-error="pilesCount" aria-live="polite"></div>
                                </div>
                                <div class="brigmaster-estimator__field" data-pile-primary-cell="shaft-diameter">
                                    <label for="<?php echo esc_attr($pileShaftDiameterFieldId); ?>">Диаметр ствола сваи (м)</label>
                                    <input id="<?php echo esc_attr($pileShaftDiameterFieldId); ?>" type="number" name="pileShaftDiameterM" min="0.01" step="0.01" value="0.3">
                                    <div class="brigmaster-estimator__error" data-field-error="pileShaftDiameterM" aria-live="polite"></div>
                                </div>
                                <div class="brigmaster-estimator__field" data-pile-primary-cell="shaft-height">
                                    <label for="<?php echo esc_attr($pileShaftHeightFieldId); ?>">Высота ствола сваи (м)</label>
                                    <input id="<?php echo esc_attr($pileShaftHeightFieldId); ?>" type="number" name="pileShaftHeightM" min="0.01" step="0.01" value="2">
                                    <div class="brigmaster-estimator__error" data-field-error="pileShaftHeightM" aria-live="polite"></div>
                                </div>
                            </div>
                        </div>

                        <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--two" data-field-group="pile-base-fields">
                            <div class="brigmaster-estimator__field">
                                <label for="<?php echo esc_attr($pileBaseDiameterFieldId); ?>">Диаметр уширения (м)</label>
                                <input id="<?php echo esc_attr($pileBaseDiameterFieldId); ?>" type="number" name="pileBaseDiameterM" min="0.01" step="0.01" value="0.5">
                                <div class="brigmaster-estimator__error" data-field-error="pileBaseDiameterM" aria-live="polite"></div>
                            </div>
                            <div class="brigmaster-estimator__field">
                                <label for="<?php echo esc_attr($pileBaseHeightFieldId); ?>">Высота уширения (м)</label>
                                <input id="<?php echo esc_attr($pileBaseHeightFieldId); ?>" type="number" name="pileBaseHeightM" min="0.01" step="0.01" value="0.3">
                                <div class="brigmaster-estimator__error" data-field-error="pileBaseHeightM" aria-live="polite"></div>
                            </div>
                        </div>

                        <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--three" data-field-group="pile-reinforcement-fields">
                            <div class="brigmaster-estimator__field">
                                <label for="<?php echo esc_attr($pileReinforcementBarsCountFieldId); ?>">Кол-во стержней в свае</label>
                                <input id="<?php echo esc_attr($pileReinforcementBarsCountFieldId); ?>" type="number" name="pileReinforcementBarsCount" min="1" step="1" value="4">
                                <div class="brigmaster-estimator__error" data-field-error="pileReinforcementBarsCount" aria-live="polite"></div>
                            </div>
                            <div class="brigmaster-estimator__field">
                                <label for="<?php echo esc_attr($pileReinforcementDiameterFieldId); ?>">Диаметр арматуры свай (мм)</label>
                                <input id="<?php echo esc_attr($pileReinforcementDiameterFieldId); ?>" type="number" name="pileReinforcementDiameterMm" min="1" step="1" value="12">
                                <div class="brigmaster-estimator__error" data-field-error="pileReinforcementDiameterMm" aria-live="polite"></div>
                            </div>
                            <div class="brigmaster-estimator__field">
                                <label for="<?php echo esc_attr($pileReinforcementReserveFieldId); ?>">Запас арматуры свай (%)</label>
                                <input id="<?php echo esc_attr($pileReinforcementReserveFieldId); ?>" type="number" name="pileReinforcementReservePercent" min="1" step="1" value="10">
                                <div class="brigmaster-estimator__error" data-field-error="pileReinforcementReservePercent" aria-live="polite"></div>
                            </div>
                        </div>

                            </div>
                        </details>
                    <?php endif; ?>
                    <?php $stripLabel = $calculator === 'pile_foundation' ? 'ростверка' : 'ленты'; ?>
                        <details class="brigmaster-estimator__accordion" open<?php echo $calculator === 'pile_foundation' ? ' data-pile-panel="grillage"' : ''; ?>>
                            <summary class="brigmaster-estimator__accordion-summary"><?php echo $calculator === 'pile_foundation' ? 'Геометрия ростверка' : 'Геометрия ленты'; ?></summary>
                            <div class="brigmaster-estimator__accordion-body">
                    <?php if ($calculator === 'pile_foundation') : ?>
                        <div class="brigmaster-estimator__field" data-field-group="estimator-mode">
                            <label for="<?php echo esc_attr($modeFieldId); ?>">Режим расчета ростверка</label>
                            <select id="<?php echo esc_attr($modeFieldId); ?>" name="mode" required aria-describedby="<?php echo esc_attr($grillageModeHintId); ?>">
                                <option value="perimeter">По общей длине ростверка</option>
                                <option value="house">По параметрам дома</option>
                                <option value="segments">По участкам ростверка</option>
                            </select>
                            <p id="<?php echo esc_attr($grillageModeHintId); ?>" class="brigmaster-estimator__mode-hint" data-mode-hint aria-live="polite"></p>
                            <div class="brigmaster-estimator__error" data-field-error="mode" aria-live="polite"></div>
                        </div>
                    <?php endif; ?>
                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--three" data-field-group="strip-perimeter">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($totalLengthFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Общая длина <?php echo esc_html($stripLabel); ?> (м)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: общая длина" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'total-length-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'total-length-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Суммарная длина всех участков. От нее напрямую зависит общий объем бетона и расход материалов.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($totalLengthFieldId); ?>" type="number" name="totalLengthM" min="0.01" step="0.01" value="10">
                            <div class="brigmaster-estimator__error" data-field-error="totalLengthM" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($widthMFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Ширина <?php echo esc_html($stripLabel); ?> (м)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: ширина" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'width-m-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'width-m-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Ширина поперечного сечения. При увеличении ширины объем бетона растет линейно.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($widthMFieldId); ?>" type="number" name="widthM" min="0.01" step="0.01" value="0.4">
                            <div class="brigmaster-estimator__error" data-field-error="widthM" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($heightMFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Высота <?php echo esc_html($stripLabel); ?> (м)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: высота" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'height-m-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'height-m-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Высота сечения. Вместе с шириной и длиной определяет объем бетона.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($heightMFieldId); ?>" type="number" name="heightM" min="0.01" step="0.01" value="1">
                            <div class="brigmaster-estimator__error" data-field-error="heightM" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four brigmaster-estimator__field-group--hidden" data-field-group="strip-house">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($houseLengthFieldId); ?>">Длина дома (м)</label>
                            <input id="<?php echo esc_attr($houseLengthFieldId); ?>" type="number" name="houseLengthM" min="0.01" step="0.01" value="10">
                            <div class="brigmaster-estimator__error" data-field-error="houseLengthM" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($houseWidthFieldId); ?>">Ширина дома (м)</label>
                            <input id="<?php echo esc_attr($houseWidthFieldId); ?>" type="number" name="houseWidthM" min="0.01" step="0.01" value="8">
                            <div class="brigmaster-estimator__error" data-field-error="houseWidthM" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($houseStripWidthFieldId); ?>">Ширина <?php echo esc_html($stripLabel); ?> (м)</label>
                            <input id="<?php echo esc_attr($houseStripWidthFieldId); ?>" type="number" name="houseModeWidthM" min="0.01" step="0.01" value="0.4" data-strip-house-width-input>
                            <div class="brigmaster-estimator__error" data-field-error="widthM" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($instanceId . 'house-height-m'); ?>">Высота <?php echo esc_html($stripLabel); ?> (м)</label>
                            <input id="<?php echo esc_attr($instanceId . 'house-height-m'); ?>" type="number" name="houseModeHeightM" min="0.01" step="0.01" value="1" data-strip-house-height-input>
                            <div class="brigmaster-estimator__error" data-field-error="heightM" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-group--hidden" data-field-group="strip-segments">
                        <div class="brigmaster-estimator__segment-list" data-strip-segments-list>
                            <article class="brigmaster-estimator__segment-card" data-strip-segment-item data-segment-index="0">
                                <div class="brigmaster-estimator__segment-head">
                                    <h3 class="brigmaster-estimator__segment-title">Участок 1</h3>
                                    <button type="button" class="brigmaster-estimator__segment-remove" data-strip-remove-segment disabled>Удалить</button>
                                </div>
                                <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--three">
                                    <div class="brigmaster-estimator__field">
                                        <label>Длина участка <?php echo esc_html($stripLabel); ?> (м)</label>
                                        <input type="number" min="0.01" step="0.01" value="10" data-segment-input="segmentLengthM">
                                        <div class="brigmaster-estimator__error" data-segment-error-field="segmentLengthM" data-field-error="segments.0.segmentLengthM" aria-live="polite"></div>
                                    </div>
                                    <div class="brigmaster-estimator__field">
                                        <label>Ширина участка <?php echo esc_html($stripLabel); ?> (м)</label>
                                        <input type="number" min="0.01" step="0.01" value="0.4" data-segment-input="segmentWidthM">
                                        <div class="brigmaster-estimator__error" data-segment-error-field="segmentWidthM" data-field-error="segments.0.segmentWidthM" aria-live="polite"></div>
                                    </div>
                                    <div class="brigmaster-estimator__field">
                                        <label>Высота участка <?php echo esc_html($stripLabel); ?> (м)</label>
                                        <input type="number" min="0.01" step="0.01" value="1" data-segment-input="segmentHeightM">
                                        <div class="brigmaster-estimator__error" data-segment-error-field="segmentHeightM" data-field-error="segments.0.segmentHeightM" aria-live="polite"></div>
                                    </div>
                                </div>
                                <div class="brigmaster-estimator__segment-section" data-segment-rebar-root>
                                    <div class="brigmaster-estimator__segment-toggles">
                                        <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
                                            <input id="<?php echo esc_attr($instanceId . 'segment-0-include-rebar'); ?>" type="checkbox" checked data-segment-include-reinforcement data-checkbox-key="segment-include-rebar">
                                            <label for="<?php echo esc_attr($instanceId . 'segment-0-include-rebar'); ?>" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-include-rebar">
                                                <span>Учитывать арматуру для этого участка</span>
                                            </label>
                                            <div class="brigmaster-estimator__error" data-segment-error-field="segmentIncludeReinforcement" data-field-error="segments.0.segmentIncludeReinforcement" aria-live="polite"></div>
                                        </div>
                                    </div>
                                    <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four brigmaster-estimator__field-group brigmaster-estimator__field-group--hidden" data-segment-rebar-local>
                                        <div class="brigmaster-estimator__field">
                                            <label class="brigmaster-estimator__label-row">
                                                <span>Кол-во продольных стержней</span>
                                                <span class="brigmaster-estimator__tooltip-anchor">
                                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: количество продольных стержней" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'segment-0-seg-long-bars-tooltip'); ?>">i</button>
                                                    <div id="<?php echo esc_attr($instanceId . 'segment-0-seg-long-bars-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                                        Число рабочих стержней в сечении этого участка. Обычно 4–6. Больше стержней — выше расход арматуры.
                                                    </div>
                                                </span>
                                            </label>
                                            <input type="number" min="1" step="1" value="4" data-segment-input="segmentLongitudinalBarsCount">
                                            <p class="brigmaster-estimator__hint">Обычно 4-6 стержней для частного дома.</p>
                                            <div class="brigmaster-estimator__error" data-segment-error-field="segmentLongitudinalBarsCount" data-field-error="segments.0.segmentLongitudinalBarsCount" aria-live="polite"></div>
                                        </div>
                                        <div class="brigmaster-estimator__field">
                                            <label class="brigmaster-estimator__label-row">
                                                <span>Диаметр продольной (мм)</span>
                                                <span class="brigmaster-estimator__tooltip-anchor">
                                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр продольной арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'segment-0-seg-long-diameter-tooltip'); ?>">i</button>
                                                    <div id="<?php echo esc_attr($instanceId . 'segment-0-seg-long-diameter-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                                        Диаметр рабочих стержней в мм. Типично 10–14 мм. Чем больше диаметр, тем выше масса и прочность.
                                                    </div>
                                                </span>
                                            </label>
                                            <input type="number" min="1" step="1" value="12" data-segment-input="segmentLongitudinalDiameterMm">
                                            <p class="brigmaster-estimator__hint">Чаще всего 10-14 мм.</p>
                                            <div class="brigmaster-estimator__error" data-segment-error-field="segmentLongitudinalDiameterMm" data-field-error="segments.0.segmentLongitudinalDiameterMm" aria-live="polite"></div>
                                        </div>
                                        <div class="brigmaster-estimator__field">
                                            <label class="brigmaster-estimator__label-row">
                                                <span>Диаметр поперечной (мм)</span>
                                                <span class="brigmaster-estimator__tooltip-anchor">
                                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр поперечной арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'segment-0-seg-transverse-diameter-tooltip'); ?>">i</button>
                                                    <div id="<?php echo esc_attr($instanceId . 'segment-0-seg-transverse-diameter-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                                        Диаметр хомутов в мм. Обычно 6–10 мм. Влияет на массу поперечной арматуры.
                                                    </div>
                                                </span>
                                            </label>
                                            <input type="number" min="1" step="1" value="8" data-segment-input="segmentTransverseDiameterMm">
                                            <p class="brigmaster-estimator__hint">Обычно 6-10 мм для хомутов.</p>
                                            <div class="brigmaster-estimator__error" data-segment-error-field="segmentTransverseDiameterMm" data-field-error="segments.0.segmentTransverseDiameterMm" aria-live="polite"></div>
                                        </div>
                                        <div class="brigmaster-estimator__field">
                                            <label class="brigmaster-estimator__label-row">
                                                <span>Шаг поперечной (мм)</span>
                                                <span class="brigmaster-estimator__tooltip-anchor">
                                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: шаг поперечной арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'segment-0-seg-transverse-step-tooltip'); ?>">i</button>
                                                    <div id="<?php echo esc_attr($instanceId . 'segment-0-seg-transverse-step-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                                        Расстояние между хомутами в мм. Типично 200–400 мм. Меньший шаг — больше хомутов и расход стали.
                                                    </div>
                                                </span>
                                            </label>
                                            <input type="number" min="1" step="10" value="300" data-segment-input="segmentTransverseStepMm">
                                            <p class="brigmaster-estimator__hint">Меньше шаг = больше хомутов и расход стали.</p>
                                            <div class="brigmaster-estimator__error" data-segment-error-field="segmentTransverseStepMm" data-field-error="segments.0.segmentTransverseStepMm" aria-live="polite"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="brigmaster-estimator__segment-section" data-segment-formwork-root>
                                    <div class="brigmaster-estimator__segment-toggles">
                                        <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
                                            <input id="<?php echo esc_attr($instanceId . 'segment-0-include-formwork'); ?>" type="checkbox" checked data-segment-include-formwork data-checkbox-key="segment-include-formwork">
                                            <label for="<?php echo esc_attr($instanceId . 'segment-0-include-formwork'); ?>" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-include-formwork">
                                                <span>Учитывать опалубку для этого участка</span>
                                            </label>
                                            <div class="brigmaster-estimator__error" data-segment-error-field="segmentIncludeFormwork" data-field-error="segments.0.segmentIncludeFormwork" aria-live="polite"></div>
                                        </div>
                                    </div>
                                    <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-group brigmaster-estimator__field-group--hidden" data-segment-formwork-local>
                                        <div class="brigmaster-estimator__field">
                                            <label>Высота опалубки участка (м)</label>
                                            <input type="number" min="0.01" step="0.01" value="0.8" data-segment-input="segmentFormworkHeightM">
                                            <p class="brigmaster-estimator__hint">Считаются только боковые щиты участка.</p>
                                            <div class="brigmaster-estimator__error" data-segment-error-field="segmentFormworkHeightM" data-field-error="segments.0.segmentFormworkHeightM" aria-live="polite"></div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <button type="button" class="brigmaster-estimator__segment-add" data-strip-add-segment>
                            <?php echo $calculator === 'pile_foundation' ? 'Добавить участок ростверка' : 'Добавить участок ленты'; ?>
                        </button>
                        <div class="brigmaster-estimator__error" data-field-error="segments" aria-live="polite"></div>
                    </div>
                            </div>
                        </details>
                        <details class="brigmaster-estimator__accordion" open<?php echo $calculator === 'pile_foundation' ? ' data-pile-panel="grillage"' : ''; ?>>
                            <summary class="brigmaster-estimator__accordion-summary"><?php echo $calculator === 'pile_foundation' ? 'Арматура ростверка' : 'Арматура'; ?></summary>
                            <div class="brigmaster-estimator__accordion-body">

                    <div class="brigmaster-estimator__field-group">
                        <div class="brigmaster-estimator__field brigmaster-estimator__toggle" data-toggle-field="strip-reinforcement">
                            <input id="<?php echo esc_attr($instanceId . 'strip-include-reinforcement'); ?>" type="checkbox" name="includeReinforcement" value="1" checked>
                            <label for="<?php echo esc_attr($instanceId . 'strip-include-reinforcement'); ?>" class="brigmaster-estimator__label-row">
                                <span><?php echo $calculator === 'pile_foundation' ? 'Учитывать арматуру ростверга' : 'Учитывать арматуру'; ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--three" data-field-group="strip-reinforcement-global">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($longitudinalBarsCountFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Кол-во продольных стержней</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: количество продольных стержней" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'strip-long-bars-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'strip-long-bars-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Число рабочих стержней в поперечном сечении ленты. Для частного дома обычно 4–6. Больше стержней — выше расход арматуры.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($longitudinalBarsCountFieldId); ?>" type="number" name="longitudinalBarsCount" min="1" step="1" value="4">
                            <div class="brigmaster-estimator__error" data-field-error="longitudinalBarsCount" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($longitudinalDiameterFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Диаметр продольной (мм)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр продольной арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'strip-long-diameter-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'strip-long-diameter-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Диаметр рабочих стержней в мм. Типично 10–14 мм. Чем больше диаметр, тем выше масса и прочность.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($longitudinalDiameterFieldId); ?>" type="number" name="longitudinalDiameterMm" min="1" step="1" value="12">
                            <div class="brigmaster-estimator__error" data-field-error="longitudinalDiameterMm" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($longitudinalReserveFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Запас продольной (%)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: запас продольной арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'strip-long-reserve-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'strip-long-reserve-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Процент запаса на нахлёсты и подрезку. Рекомендуется 5–15%.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($longitudinalReserveFieldId); ?>" type="number" name="longitudinalReservePercent" min="1" step="1" value="10">
                            <div class="brigmaster-estimator__error" data-field-error="longitudinalReservePercent" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($transverseDiameterFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Диаметр поперечной (мм)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр поперечной арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'strip-transverse-diameter-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'strip-transverse-diameter-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Диаметр хомутов в мм. Обычно 6–10 мм. Влияет на массу поперечной арматуры.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($transverseDiameterFieldId); ?>" type="number" name="transverseDiameterMm" min="1" step="1" value="8">
                            <div class="brigmaster-estimator__error" data-field-error="transverseDiameterMm" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($transverseStepFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Шаг поперечной (мм)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: шаг поперечной арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'strip-transverse-step-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'strip-transverse-step-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Расстояние между хомутами в мм. Типично 200–400 мм. Меньший шаг — больше хомутов и расход стали.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($transverseStepFieldId); ?>" type="number" name="transverseStepMm" min="1" step="10" value="300">
                            <div class="brigmaster-estimator__error" data-field-error="transverseStepMm" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($transverseReserveFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Запас поперечной (%)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: запас поперечной арматуры" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'strip-transverse-reserve-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'strip-transverse-reserve-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Процент запаса на монтаж и отходы. Рекомендуется 5–15%.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($transverseReserveFieldId); ?>" type="number" name="transverseReservePercent" min="1" step="1" value="10">
                            <div class="brigmaster-estimator__error" data-field-error="transverseReservePercent" aria-live="polite"></div>
                        </div>
                    </div>
                            </div>
                        </details>
                        <details class="brigmaster-estimator__accordion" open<?php echo $calculator === 'pile_foundation' ? ' data-pile-panel="grillage"' : ''; ?>>
                            <summary class="brigmaster-estimator__accordion-summary"><?php echo $calculator === 'pile_foundation' ? 'Опалубка ростверка' : 'Опалубка'; ?></summary>
                            <div class="brigmaster-estimator__accordion-body">

                    <div class="brigmaster-estimator__field-group">
                        <div class="brigmaster-estimator__field brigmaster-estimator__toggle" data-toggle-field="strip-formwork">
                            <input id="<?php echo esc_attr($instanceId . 'strip-include-formwork'); ?>" type="checkbox" name="includeFormwork" value="1" checked>
                            <label for="<?php echo esc_attr($instanceId . 'strip-include-formwork'); ?>" class="brigmaster-estimator__label-row">
                                <span><?php echo $calculator === 'pile_foundation' ? 'Учитывать опалубку ростверга' : 'Учитывать опалубку'; ?></span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: учитывать опалубку" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'strip-formwork-height-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'strip-formwork-height-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        В расчёт попадают только боковые щиты ленты (высота задаётся ниже). Распорки, подкосы и крепёж не учитываются.
                                    </div>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--two" data-field-group="strip-formwork-global">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($formworkHeightFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Высота опалубки (м)</span>
                            </label>
                            <input id="<?php echo esc_attr($formworkHeightFieldId); ?>" type="number" name="formworkHeightM" min="0.01" step="0.01" value="0.8">
                            <div class="brigmaster-estimator__error" data-field-error="formworkHeightM" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($formworkReserveFieldId); ?>" class="brigmaster-estimator__label-row">
                                <span>Запас опалубки (%)</span>
                                <span class="brigmaster-estimator__tooltip-anchor">
                                    <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: запас опалубки ленты" aria-expanded="false" aria-controls="<?php echo esc_attr($instanceId . 'strip-formwork-reserve-tooltip'); ?>">i</button>
                                    <div id="<?php echo esc_attr($instanceId . 'strip-formwork-reserve-tooltip'); ?>" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                                        Запас учитывает подрезку и стыки щитов. В расчет входят только боковые поверхности.
                                    </div>
                                </span>
                            </label>
                            <input id="<?php echo esc_attr($formworkReserveFieldId); ?>" type="number" name="formworkReservePercent" min="1" step="1" value="10">
                            <div class="brigmaster-estimator__error" data-field-error="formworkReservePercent" aria-live="polite"></div>
                        </div>
                    </div>
                            </div>
                        </details>
                    </div>
                <?php endif; ?>

                <?php if ($calculator === 'brick') : ?>
                    <div class="brigmaster-estimator__field-group" data-field-group="brick-subtype">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($subTypeFieldId); ?>">Тип расчета кирпича</label>
                            <select id="<?php echo esc_attr($subTypeFieldId); ?>" name="subType">
                                <option value="bricks">Кирпичи (bricks)</option>
                                <option value="mortar">Раствор (mortar)</option>
                            </select>
                            <div class="brigmaster-estimator__error" data-field-error="subType" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (in_array($calculator, ['brick', 'screed', 'drywall', 'tile'], true)) : ?>
                    <div class="brigmaster-estimator__field-group" data-field-group="area">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($areaFieldId); ?>">Площадь (м²)</label>
                            <input id="<?php echo esc_attr($areaFieldId); ?>" type="number" name="area" min="0.01" step="0.01" aria-describedby="<?php echo esc_attr($areaHintId); ?>">
                            <p id="<?php echo esc_attr($areaHintId); ?>" class="brigmaster-estimator__hint">Введите площадь в м² (например, 25.5).</p>
                            <div class="brigmaster-estimator__error" data-field-error="area" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($calculator === 'screed') : ?>
                    <div class="brigmaster-estimator__field-group" data-field-group="thickness">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($thicknessFieldId); ?>">Толщина (м)</label>
                            <input id="<?php echo esc_attr($thicknessFieldId); ?>" type="number" name="thickness" min="0.001" step="0.001" aria-describedby="<?php echo esc_attr($thicknessHintId); ?>">
                            <p id="<?php echo esc_attr($thicknessHintId); ?>" class="brigmaster-estimator__hint">Вводите в метрах: 0.1 = 10 см.</p>
                            <div class="brigmaster-estimator__error" data-field-error="thickness" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($calculator === 'tile') : ?>
                    <div class="brigmaster-estimator__field-group" data-field-group="tile-size">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($tileLengthFieldId); ?>">Длина плитки (см)</label>
                            <input id="<?php echo esc_attr($tileLengthFieldId); ?>" type="number" name="tileLengthCm" min="0.1" step="0.1" aria-describedby="<?php echo esc_attr($tileLengthHintId); ?>">
                            <p id="<?php echo esc_attr($tileLengthHintId); ?>" class="brigmaster-estimator__hint">Введите длину одной плитки в сантиметрах.</p>
                            <div class="brigmaster-estimator__error" data-field-error="tileLengthCm" aria-live="polite"></div>
                        </div>

                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($tileWidthFieldId); ?>">Ширина плитки (см)</label>
                            <input id="<?php echo esc_attr($tileWidthFieldId); ?>" type="number" name="tileWidthCm" min="0.1" step="0.1" aria-describedby="<?php echo esc_attr($tileWidthHintId); ?>">
                            <p id="<?php echo esc_attr($tileWidthHintId); ?>" class="brigmaster-estimator__hint">Введите ширину одной плитки в сантиметрах.</p>
                            <div class="brigmaster-estimator__error" data-field-error="tileWidthCm" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="brigmaster-estimator__validation-summary" data-validation-summary role="alert" hidden></div>
                <button type="submit">Рассчитать</button>
                <div class="brigmaster-estimator__error" data-field-error="general" aria-live="assertive"></div>
            </form>

            <div class="brigmaster-estimator__result" data-result hidden aria-live="polite" aria-atomic="true" tabindex="-1">
                <p class="brigmaster-estimator__result-stale-notice" data-result-stale-notice hidden>Результат устарел — нажмите «Рассчитать» снова.</p>
                <?php if ($calculator === 'slab_foundation') : ?>
                    <div class="brigmaster-estimator__result-grid">
                        <section class="brigmaster-estimator__result-card" data-result-card="concrete">
                            <h3>Бетон</h3>
                            <p><strong>Объем:</strong> <span data-result-concrete-volume>-</span> м3</p>
                            <p><strong>Площадь:</strong> <span data-result-concrete-area>-</span> м2</p>
                            <p><strong>Высота:</strong> <span data-result-concrete-height>-</span> м</p>
                        </section>
                        <section class="brigmaster-estimator__result-card" data-result-card="reinforcement" hidden></section>
                        <section class="brigmaster-estimator__result-card" data-result-card="formwork" hidden></section>
                    </div>
                    <div class="brigmaster-estimator__scheme" data-slab-scheme></div>
                <?php elseif ($calculator === 'strip_foundation') : ?>
                    <div class="brigmaster-estimator__result-grid">
                        <section class="brigmaster-estimator__result-card" data-result-card="strip-concrete">
                            <h3>Бетон</h3>
                            <p><strong>Общая длина ленты:</strong> <span data-result-strip-concrete-length>-</span> м</p>
                            <p><strong>Объем бетона:</strong> <span data-result-strip-concrete-volume>-</span> м3</p>
                        </section>
                        <section class="brigmaster-estimator__result-card" data-result-card="strip-reinforcement" hidden>
                            <h3>Арматура</h3>
                            <p><strong>Продольная:</strong> <span data-result-strip-rebar-longitudinal-mass>-</span> кг</p>
                            <p><strong>Поперечная:</strong> <span data-result-strip-rebar-transverse-mass>-</span> кг</p>
                            <p><strong>Итого:</strong> <span data-result-strip-rebar-total-mass>-</span> кг</p>
                        </section>
                        <section class="brigmaster-estimator__result-card" data-result-card="strip-formwork" hidden>
                            <h3>Опалубка</h3>
                            <p><strong>Суммарная площадь щитов:</strong> <span data-result-strip-formwork-area>-</span> м2</p>
                            <p><strong>Погонные метры:</strong> <span data-result-strip-formwork-linear>-</span> м</p>
                        </section>
                    </div>
                <?php elseif ($calculator === 'pile_foundation') : ?>
                    <div class="brigmaster-estimator__result-grid brigmaster-estimator__result-grid--pile-foundation">
                        <div class="brigmaster-estimator__result-section" data-result-section="pile-header" hidden>
                            <h3 class="brigmaster-estimator__result-section-title">Сваи</h3>
                            <p class="brigmaster-estimator__result-section-subtitle">Параметры свай</p>
                            <p><strong>Тип:</strong> <span data-result-pile-type>-</span></p>
                            <p><strong>Количество:</strong> <span data-result-pile-count>-</span> шт</p>
                            <p data-result-pile-note-row hidden><strong>Примечание:</strong> <span data-result-pile-note>-</span></p>
                        </div>
                        <section class="brigmaster-estimator__result-card" data-result-card="pile-concrete" hidden>
                            <h4>Бетон свай</h4>
                            <p><strong>Объем бетона:</strong> <span data-result-pile-concrete-volume>-</span> м3</p>
                            <p data-result-pile-per-pile-row hidden><strong>На 1 сваю:</strong> <span data-result-pile-concrete-per-pile>-</span> м3</p>
                        </section>
                        <section class="brigmaster-estimator__result-card" data-result-card="pile-reinforcement" hidden></section>
                        <div class="brigmaster-estimator__result-section" data-result-section="grillage-header" hidden>
                            <h3 class="brigmaster-estimator__result-section-title">Ростверк</h3>
                        </div>
                        <section class="brigmaster-estimator__result-card" data-result-card="strip-concrete" hidden>
                            <h4>Бетон ростверка</h4>
                            <p><strong>Общая длина:</strong> <span data-result-strip-concrete-length>-</span> м</p>
                            <p><strong>Объем бетона:</strong> <span data-result-strip-concrete-volume>-</span> м3</p>
                        </section>
                        <section class="brigmaster-estimator__result-card" data-result-card="strip-reinforcement" hidden></section>
                        <section class="brigmaster-estimator__result-card" data-result-card="strip-formwork" hidden>
                            <h4>Опалубка ростверка</h4>
                            <p><strong>Суммарная площадь щитов:</strong> <span data-result-strip-formwork-area>-</span> м2</p>
                            <p><strong>Погонные метры:</strong> <span data-result-strip-formwork-linear>-</span> м</p>
                        </section>
                    </div>
                <?php elseif ($calculator === 'screed') : ?>
                    <p><strong>Объём смеси:</strong> <span data-result-volume>-</span> м³</p>
                    <p><strong>Расход материала (ориентир):</strong> <span data-result-material>-</span></p>
                <?php elseif ($calculator === 'tile') : ?>
                    <p><strong>Площадь покрытия:</strong> <span data-result-volume>-</span> м²</p>
                    <p><strong>Количество плиток (с запасом):</strong> <span data-result-material>-</span> шт</p>
                <?php elseif ($calculator === 'drywall') : ?>
                    <p><strong>Площадь по вводу:</strong> <span data-result-volume>-</span> м²</p>
                    <p><strong>Площадь листов с запасом:</strong> <span data-result-material>-</span> м²</p>
                    <p class="brigmaster-estimator__result-note">Каркас и шаг стоек в расчёт не входят. При двойной обшивке удвойте площадь или сделайте два расчёта.</p>
                <?php else : ?>
                    <p><strong>Площадь кладки:</strong> <span data-result-volume>-</span> м²</p>
                    <p><strong>Количество материала:</strong> <span data-result-material>-</span></p>
                <?php endif; ?>
            </div>
            <div class="brigmaster-estimator__tooltip-backdrop" data-tooltip-backdrop hidden></div>
        </div>
        <?php

        return (string) ob_get_clean();
    }

    private function enqueueAssets(): void
    {
        $styleHandle = 'brigmaster-estimate-form';
        $scriptHandle = 'brigmaster-estimate-form';
        $assetVersion = '0.8.6';
        $baseUrl = plugin_dir_url($this->pluginFilePath);

        wp_register_style(
            $styleHandle,
            $baseUrl . 'assets/css/estimate-form.css',
            [],
            $assetVersion
        );

        wp_register_script(
            $scriptHandle,
            $baseUrl . 'assets/js/estimate-form.js',
            [],
            $assetVersion,
            true
        );

        $metrika = $this->getYandexMetrikaFrontendConfig();

        wp_localize_script(
            $scriptHandle,
            'brigmasterEstimateFormData',
            [
                'endpoint' => esc_url_raw(rest_url('brigmaster/v1/estimate')),
                'networkErrorMessage' => 'Не удалось выполнить запрос. Проверьте подключение и попробуйте снова.',
                'metrikaCounterId' => $metrika['counterId'],
                'metrikaEnabled' => $metrika['enabled'],
            ]
        );

        wp_enqueue_style($styleHandle);
        wp_enqueue_script($scriptHandle);
    }

    /**
     * Yandex Metrika reachGoal is enabled only on production host and when counter ID is set.
     * Counter ID resolution: wp-config constant → official plugin option yam_options → filter.
     *
     * @return array{counterId: int, enabled: bool}
     */
    private function getYandexMetrikaFrontendConfig(): array
    {
        $host = wp_parse_url(home_url(), PHP_URL_HOST);
        $host = is_string($host) ? strtolower($host) : '';
        $productionHosts = ['brigmaster.ru', 'www.brigmaster.ru'];
        $isProductionHost = in_array($host, $productionHosts, true);

        $counterId = 0;
        if (defined('BRIGMASTER_YANDEX_METRIKA_COUNTER_ID')) {
            $counterId = (int) BRIGMASTER_YANDEX_METRIKA_COUNTER_ID;
        }

        if ($counterId <= 0) {
            $counterId = $this->resolveYandexMetrikaCounterIdFromYamPluginOptions();
        }

        $counterId = (int) apply_filters('brigmaster_yandex_metrika_counter_id', $counterId);

        $enabled = $isProductionHost && $counterId > 0;

        return [
            'counterId' => $enabled ? $counterId : 0,
            'enabled' => $enabled,
        ];
    }

    /**
     * Reads first valid tag number from Yandex.Metrica WordPress plugin (wp-yandex-metrika) option yam_options.
     */
    private function resolveYandexMetrikaCounterIdFromYamPluginOptions(): int
    {
        $options = get_option('yam_options', null);
        if (!is_array($options) || empty($options['counters']) || !is_array($options['counters'])) {
            return 0;
        }

        foreach ($options['counters'] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $number = isset($row['number']) ? trim((string) $row['number']) : '';
            if ($number !== '' && preg_match('/^\d+$/', $number) === 1) {
                return (int) $number;
            }
        }

        return 0;
    }
}
