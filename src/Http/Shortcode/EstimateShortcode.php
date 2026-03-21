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
                <h1 id="brigmaster-foundation-hub-title">Калькулятор фундамента</h1>
                <p>
                    Это общий раздел для расчета разных типов фундамента. Ниже выберите подходящий вариант конструкции
                    и перейдите на отдельную страницу калькулятора. Каждый тип имеет собственные параметры и логику расчета.
                </p>
                <a class="brigmaster-foundation-hub__cta" href="#foundation-types">Выбрать тип фундамента</a>
            </div>

            <div id="foundation-types" class="brigmaster-content-block brigmaster-content-block--muted">
                <h2>Выберите тип фундамента</h2>
                <div class="brigmaster-foundation-hub__cards">
                    <article class="brigmaster-foundation-hub__card">
                        <div class="brigmaster-foundation-hub__icon" aria-hidden="true">PL</div>
                        <h3>Калькулятор плитного фундамента</h3>
                        <ul>
                            <li>Подходит, если нужна монолитная плита под всем домом.</li>
                            <li>Подходит, если важна устойчивость на сложных грунтах.</li>
                            <li>Подходит, если нужен базовый расчет бетона, арматуры и опалубки.</li>
                        </ul>
                        <a href="/kalkulyator-plitnogo-fundamenta/" class="brigmaster-foundation-hub__link">Перейти к расчету</a>
                    </article>

                    <article class="brigmaster-foundation-hub__card">
                        <div class="brigmaster-foundation-hub__icon" aria-hidden="true">LF</div>
                        <h3>Калькулятор ленточного фундамента</h3>
                        <ul>
                            <li>Подходит, если планируется лента под несущими стенами.</li>
                            <li>Подходит, если нужен расчет по длине и сечению ленты.</li>
                            <li>Подходит, если хотите сравнить расход материалов по вариантам.</li>
                        </ul>
                        <a href="/kalkulyator-lentochnogo-fundamenta/" class="brigmaster-foundation-hub__link">Перейти к расчету</a>
                    </article>

                    <article class="brigmaster-foundation-hub__card">
                        <div class="brigmaster-foundation-hub__icon" aria-hidden="true">SF</div>
                        <h3>Калькулятор свайного фундамента</h3>
                        <ul>
                            <li>Подходит, если рассматриваете фундамент на сваях.</li>
                            <li>Подходит, если нужно оценить количество и шаг свай.</li>
                            <li>Подходит, если хотите быстро проверить базовый сценарий.</li>
                        </ul>
                        <a href="/kalkulyator-svainogo-fundamenta/" class="brigmaster-foundation-hub__link">Перейти к расчету</a>
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
                <div class="brigmaster-faq-item">
                    <p class="brigmaster-faq-item__question">Какой фундамент выбрать для одноэтажного дома?</p>
                    <p class="brigmaster-faq-item__answer">Placeholder: выбор зависит от грунта, конструкции дома и бюджета.</p>
                </div>
                <div class="brigmaster-faq-item">
                    <p class="brigmaster-faq-item__question">Можно ли ориентироваться только на онлайн-калькулятор?</p>
                    <p class="brigmaster-faq-item__answer">Placeholder: калькулятор дает предварительную оценку, но не заменяет проект.</p>
                </div>
                <div class="brigmaster-faq-item">
                    <p class="brigmaster-faq-item__question">Нужен ли запас по материалам?</p>
                    <p class="brigmaster-faq-item__answer">Placeholder: обычно закладывают запас на технологические потери.</p>
                </div>
                <div class="brigmaster-faq-item">
                    <p class="brigmaster-faq-item__question">Почему результаты на разных страницах отличаются?</p>
                    <p class="brigmaster-faq-item__answer">Placeholder: каждый тип фундамента рассчитывается по своей модели.</p>
                </div>
            </div>

            <div class="brigmaster-content-block brigmaster-content-block--muted">
                <h2>Полезные ссылки</h2>
                <ul class="brigmaster-foundation-hub__links">
                    <li><a href="/kalkulyator-plitnogo-fundamenta/">Калькулятор плитного фундамента</a></li>
                    <li><a href="/kalkulyator-lentochnogo-fundamenta/">Калькулятор ленточного фундамента</a></li>
                    <li><a href="/kalkulyator-svainogo-fundamenta/">Калькулятор свайного фундамента</a></li>
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
        ob_start();
        ?>
        <div class="brigmaster-estimator brigmaster-estimator--<?php echo esc_attr(str_replace('_', '-', $calculator)); ?>" data-calculator="<?php echo esc_attr($calculator); ?>">
            <h3 class="brigmaster-estimator__title"><?php echo esc_html($title); ?></h3>
            <form class="brigmaster-estimate-form" novalidate>
                <input type="hidden" name="calculator" value="<?php echo esc_attr($calculator); ?>">

                <?php if ($calculator !== 'pile_foundation') : ?>
                    <div class="brigmaster-estimator__field" data-field-group="estimator-mode">
                        <label for="<?php echo esc_attr($modeFieldId); ?>">Режим расчета</label>
                        <select id="<?php echo esc_attr($modeFieldId); ?>" name="mode" required>
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

                <?php if (in_array($calculator, ['strip_foundation', 'pile_foundation'], true)) : ?>
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

                        <div class="brigmaster-estimator__field" data-field-group="estimator-mode">
                            <label for="<?php echo esc_attr($modeFieldId); ?>">Режим расчета ростверка</label>
                            <select id="<?php echo esc_attr($modeFieldId); ?>" name="mode" required>
                                <option value="perimeter">По общей длине ростверка</option>
                                <option value="house">По параметрам дома</option>
                                <option value="segments">По участкам ростверка</option>
                            </select>
                            <div class="brigmaster-estimator__error" data-field-error="mode" aria-live="polite"></div>
                        </div>
                    <?php endif; ?>
                    <?php $stripLabel = $calculator === 'pile_foundation' ? 'ростверка' : 'ленты'; ?>
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
                            <p class="brigmaster-estimator__hint">Например: периметр 10x8 без внутренних участков = 36 м.</p>
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
                            <p class="brigmaster-estimator__hint">Часто в частном строительстве используют 0.3-0.5 м.</p>
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
                            <p class="brigmaster-estimator__hint">Часто берут 0.8-1.2 м в зависимости от проекта.</p>
                            <div class="brigmaster-estimator__error" data-field-error="heightM" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four brigmaster-estimator__field-group--hidden" data-field-group="strip-house">
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($houseLengthFieldId); ?>">Длина дома (м)</label>
                            <input id="<?php echo esc_attr($houseLengthFieldId); ?>" type="number" name="houseLengthM" min="0.01" step="0.01" value="10">
                            <p class="brigmaster-estimator__hint">Используется для расчета периметра: 2 * (длина + ширина).</p>
                            <div class="brigmaster-estimator__error" data-field-error="houseLengthM" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($houseWidthFieldId); ?>">Ширина дома (м)</label>
                            <input id="<?php echo esc_attr($houseWidthFieldId); ?>" type="number" name="houseWidthM" min="0.01" step="0.01" value="8">
                            <p class="brigmaster-estimator__hint">Вместе с длиной дает общий периметр наружного контура.</p>
                            <div class="brigmaster-estimator__error" data-field-error="houseWidthM" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($instanceId . 'house-width-m'); ?>">Ширина <?php echo esc_html($stripLabel); ?> (м)</label>
                            <input id="<?php echo esc_attr($instanceId . 'house-width-m'); ?>" type="number" name="houseModeWidthM" min="0.01" step="0.01" value="0.4" data-strip-house-width-input>
                            <p class="brigmaster-estimator__hint">Ширина поперечного сечения для режима дома.</p>
                            <div class="brigmaster-estimator__error" data-field-error="widthM" aria-live="polite"></div>
                        </div>
                        <div class="brigmaster-estimator__field">
                            <label for="<?php echo esc_attr($instanceId . 'house-height-m'); ?>">Высота <?php echo esc_html($stripLabel); ?> (м)</label>
                            <input id="<?php echo esc_attr($instanceId . 'house-height-m'); ?>" type="number" name="houseModeHeightM" min="0.01" step="0.01" value="1" data-strip-house-height-input>
                            <p class="brigmaster-estimator__hint">Высота поперечного сечения для режима дома.</p>
                            <div class="brigmaster-estimator__error" data-field-error="heightM" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="brigmaster-estimator__field-group brigmaster-estimator__field-group--hidden" data-field-group="strip-segments">
                        <div class="brigmaster-estimator__segment-list" data-strip-segments-list>
                            <article class="brigmaster-estimator__segment-card" data-strip-segment-item data-segment-index="0">
                                <div class="brigmaster-estimator__segment-head">
                                    <h4 class="brigmaster-estimator__segment-title">Участок 1</h4>
                                    <button type="button" class="brigmaster-estimator__segment-remove" data-strip-remove-segment disabled>Удалить</button>
                                </div>
                                <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--three">
                                    <div class="brigmaster-estimator__field">
                                        <label>Длина участка <?php echo esc_html($stripLabel); ?> (м)</label>
                                        <input type="number" min="0.01" step="0.01" value="10" data-segment-input="segmentLengthM">
                                        <p class="brigmaster-estimator__hint">Длина конкретного участка.</p>
                                        <div class="brigmaster-estimator__error" data-segment-error-field="segmentLengthM" data-field-error="segments.0.segmentLengthM" aria-live="polite"></div>
                                    </div>
                                    <div class="brigmaster-estimator__field">
                                        <label>Ширина участка <?php echo esc_html($stripLabel); ?> (м)</label>
                                        <input type="number" min="0.01" step="0.01" value="0.4" data-segment-input="segmentWidthM">
                                        <p class="brigmaster-estimator__hint">Ширина сечения этого участка.</p>
                                        <div class="brigmaster-estimator__error" data-segment-error-field="segmentWidthM" data-field-error="segments.0.segmentWidthM" aria-live="polite"></div>
                                    </div>
                                    <div class="brigmaster-estimator__field">
                                        <label>Высота участка <?php echo esc_html($stripLabel); ?> (м)</label>
                                        <input type="number" min="0.01" step="0.01" value="1" data-segment-input="segmentHeightM">
                                        <p class="brigmaster-estimator__hint">Высота сечения этого участка.</p>
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

                <button type="submit">Рассчитать</button>
                <div class="brigmaster-estimator__error" data-field-error="general" aria-live="assertive"></div>
            </form>

            <div class="brigmaster-estimator__result" data-result hidden aria-live="polite" aria-atomic="true">
                <?php if ($calculator === 'slab_foundation') : ?>
                    <div class="brigmaster-estimator__result-grid">
                        <section class="brigmaster-estimator__result-card" data-result-card="concrete">
                            <h4>Бетон</h4>
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
                            <h4>Бетон</h4>
                            <p><strong>Общая длина ленты:</strong> <span data-result-strip-concrete-length>-</span> м</p>
                            <p><strong>Объем бетона:</strong> <span data-result-strip-concrete-volume>-</span> м3</p>
                        </section>
                        <section class="brigmaster-estimator__result-card" data-result-card="strip-reinforcement" hidden>
                            <h4>Арматура</h4>
                            <p><strong>Продольная:</strong> <span data-result-strip-rebar-longitudinal-mass>-</span> кг</p>
                            <p><strong>Поперечная:</strong> <span data-result-strip-rebar-transverse-mass>-</span> кг</p>
                            <p><strong>Итого:</strong> <span data-result-strip-rebar-total-mass>-</span> кг</p>
                        </section>
                        <section class="brigmaster-estimator__result-card" data-result-card="strip-formwork" hidden>
                            <h4>Опалубка</h4>
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
                <?php else : ?>
                    <p><strong>Объем:</strong> <span data-result-volume>-</span> м3</p>
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
        $assetVersion = '0.6.2';
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

        wp_localize_script(
            $scriptHandle,
            'brigmasterEstimateFormData',
            [
                'endpoint' => esc_url_raw(rest_url('brigmaster/v1/estimate')),
                'networkErrorMessage' => 'Не удалось выполнить запрос. Проверьте подключение и попробуйте снова.',
            ]
        );

        wp_enqueue_style($styleHandle);
        wp_enqueue_script($scriptHandle);
    }
}
