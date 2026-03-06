<?php

declare(strict_types=1);

namespace Constructly\Http\Shortcode;

final class EstimateShortcode
{
    public function __construct(
        private readonly string $pluginFilePath
    ) {
    }

    public function registerShortcodes(): void
    {
        add_shortcode('constructly_concrete_estimator', [$this, 'renderConcreteShortcode']);
        add_shortcode('constructly_brick_estimator', [$this, 'renderBrickShortcode']);
        add_shortcode('constructly_screed_estimator', [$this, 'renderScreedShortcode']);
        add_shortcode('constructly_drywall_estimator', [$this, 'renderDrywallShortcode']);
        add_shortcode('constructly_tile_estimator', [$this, 'renderTileShortcode']);
    }

    public function renderConcreteShortcode(array $attributes = [], ?string $content = null, string $shortcodeTag = ''): string
    {
        return $this->renderEstimator(
            calculator: 'concrete',
            title: 'Калькулятор бетона'
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

    private function renderEstimator(string $calculator, string $title): string
    {
        if (!in_array($calculator, ['concrete', 'brick', 'screed', 'drywall', 'tile'], true)) {
            return '';
        }

        $this->enqueueAssets();

        $instanceId = wp_unique_id('constructly-' . $calculator . '-');
        $modeFieldId = $instanceId . 'mode';
        $areaFieldId = $instanceId . 'area';
        $thicknessFieldId = $instanceId . 'thickness';
        $subTypeFieldId = $instanceId . 'sub-type';
        $lengthFieldId = $instanceId . 'length';
        $widthFieldId = $instanceId . 'width';
        $heightFieldId = $instanceId . 'height';
        $tileLengthFieldId = $instanceId . 'tile-length-cm';
        $tileWidthFieldId = $instanceId . 'tile-width-cm';
        $areaHintId = $instanceId . 'area-hint';
        $thicknessHintId = $instanceId . 'thickness-hint';
        $lengthHintId = $instanceId . 'length-hint';
        $widthHintId = $instanceId . 'width-hint';
        $heightHintId = $instanceId . 'height-hint';
        $tileLengthHintId = $instanceId . 'tile-length-hint';
        $tileWidthHintId = $instanceId . 'tile-width-hint';
        ob_start();
        ?>
        <div class="constructly-estimator" data-calculator="<?php echo esc_attr($calculator); ?>">
            <h3 class="constructly-estimator__title"><?php echo esc_html($title); ?></h3>
            <form class="constructly-estimate-form" novalidate>
                <input type="hidden" name="calculator" value="<?php echo esc_attr($calculator); ?>">

                <div class="constructly-estimator__field">
                    <label for="<?php echo esc_attr($modeFieldId); ?>">Режим расчета</label>
                    <select id="<?php echo esc_attr($modeFieldId); ?>" name="mode" required>
                        <option value="normative">Норматив</option>
                        <option value="reserve">С запасом</option>
                        <option value="beginner">Для новичка</option>
                    </select>
                    <div class="constructly-estimator__error" data-field-error="mode" aria-live="polite"></div>
                </div>

                <?php if ($calculator === 'concrete') : ?>
                    <div class="constructly-estimator__field-group" data-field-group="concrete-subtype">
                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($subTypeFieldId); ?>">Тип бетона</label>
                            <select id="<?php echo esc_attr($subTypeFieldId); ?>" name="subType">
                                <option value="slab">Плита (slab)</option>
                                <option value="strip">Лента (strip)</option>
                            </select>
                            <div class="constructly-estimator__error" data-field-error="subType" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="constructly-estimator__field-group" data-field-group="concrete-slab">
                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($areaFieldId); ?>">Площадь (м²)</label>
                            <input id="<?php echo esc_attr($areaFieldId); ?>" type="number" name="area" min="0.01" step="0.01" aria-describedby="<?php echo esc_attr($areaHintId); ?>">
                            <p id="<?php echo esc_attr($areaHintId); ?>" class="constructly-estimator__hint">Введите площадь в м² (например, 25.5).</p>
                            <div class="constructly-estimator__error" data-field-error="area" aria-live="polite"></div>
                        </div>

                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($thicknessFieldId); ?>">Толщина (м)</label>
                            <input id="<?php echo esc_attr($thicknessFieldId); ?>" type="number" name="thickness" min="0.001" step="0.001" aria-describedby="<?php echo esc_attr($thicknessHintId); ?>">
                            <p id="<?php echo esc_attr($thicknessHintId); ?>" class="constructly-estimator__hint">Вводите в метрах: 0.1 = 10 см.</p>
                            <div class="constructly-estimator__error" data-field-error="thickness" aria-live="polite"></div>
                        </div>
                    </div>

                    <div class="constructly-estimator__field-group constructly-estimator__field-group--hidden" data-field-group="concrete-strip">
                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($lengthFieldId); ?>">Длина (м)</label>
                            <input id="<?php echo esc_attr($lengthFieldId); ?>" type="number" name="length" min="0.01" step="0.01" aria-describedby="<?php echo esc_attr($lengthHintId); ?>">
                            <p id="<?php echo esc_attr($lengthHintId); ?>" class="constructly-estimator__hint">Введите длину в метрах.</p>
                            <div class="constructly-estimator__error" data-field-error="length" aria-live="polite"></div>
                        </div>

                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($widthFieldId); ?>">Ширина (м)</label>
                            <input id="<?php echo esc_attr($widthFieldId); ?>" type="number" name="width" min="0.01" step="0.01" aria-describedby="<?php echo esc_attr($widthHintId); ?>">
                            <p id="<?php echo esc_attr($widthHintId); ?>" class="constructly-estimator__hint">Введите ширину в метрах.</p>
                            <div class="constructly-estimator__error" data-field-error="width" aria-live="polite"></div>
                        </div>

                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($heightFieldId); ?>">Высота (м)</label>
                            <input id="<?php echo esc_attr($heightFieldId); ?>" type="number" name="height" min="0.01" step="0.01" aria-describedby="<?php echo esc_attr($heightHintId); ?>">
                            <p id="<?php echo esc_attr($heightHintId); ?>" class="constructly-estimator__hint">Введите высоту в метрах.</p>
                            <div class="constructly-estimator__error" data-field-error="height" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($calculator === 'brick') : ?>
                    <div class="constructly-estimator__field-group" data-field-group="brick-subtype">
                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($subTypeFieldId); ?>">Тип расчета кирпича</label>
                            <select id="<?php echo esc_attr($subTypeFieldId); ?>" name="subType">
                                <option value="bricks">Кирпичи (bricks)</option>
                                <option value="mortar">Раствор (mortar)</option>
                            </select>
                            <div class="constructly-estimator__error" data-field-error="subType" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (in_array($calculator, ['brick', 'screed', 'drywall', 'tile'], true)) : ?>
                    <div class="constructly-estimator__field-group" data-field-group="area">
                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($areaFieldId); ?>">Площадь (м²)</label>
                            <input id="<?php echo esc_attr($areaFieldId); ?>" type="number" name="area" min="0.01" step="0.01" aria-describedby="<?php echo esc_attr($areaHintId); ?>">
                            <p id="<?php echo esc_attr($areaHintId); ?>" class="constructly-estimator__hint">Введите площадь в м² (например, 25.5).</p>
                            <div class="constructly-estimator__error" data-field-error="area" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($calculator === 'screed') : ?>
                    <div class="constructly-estimator__field-group" data-field-group="thickness">
                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($thicknessFieldId); ?>">Толщина (м)</label>
                            <input id="<?php echo esc_attr($thicknessFieldId); ?>" type="number" name="thickness" min="0.001" step="0.001" aria-describedby="<?php echo esc_attr($thicknessHintId); ?>">
                            <p id="<?php echo esc_attr($thicknessHintId); ?>" class="constructly-estimator__hint">Вводите в метрах: 0.1 = 10 см.</p>
                            <div class="constructly-estimator__error" data-field-error="thickness" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($calculator === 'tile') : ?>
                    <div class="constructly-estimator__field-group" data-field-group="tile-size">
                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($tileLengthFieldId); ?>">Длина плитки (см)</label>
                            <input id="<?php echo esc_attr($tileLengthFieldId); ?>" type="number" name="tileLengthCm" min="0.1" step="0.1" aria-describedby="<?php echo esc_attr($tileLengthHintId); ?>">
                            <p id="<?php echo esc_attr($tileLengthHintId); ?>" class="constructly-estimator__hint">Введите длину одной плитки в сантиметрах.</p>
                            <div class="constructly-estimator__error" data-field-error="tileLengthCm" aria-live="polite"></div>
                        </div>

                        <div class="constructly-estimator__field">
                            <label for="<?php echo esc_attr($tileWidthFieldId); ?>">Ширина плитки (см)</label>
                            <input id="<?php echo esc_attr($tileWidthFieldId); ?>" type="number" name="tileWidthCm" min="0.1" step="0.1" aria-describedby="<?php echo esc_attr($tileWidthHintId); ?>">
                            <p id="<?php echo esc_attr($tileWidthHintId); ?>" class="constructly-estimator__hint">Введите ширину одной плитки в сантиметрах.</p>
                            <div class="constructly-estimator__error" data-field-error="tileWidthCm" aria-live="polite"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <button type="submit">Рассчитать</button>
                <div class="constructly-estimator__error" data-field-error="general" aria-live="assertive"></div>
            </form>

            <div class="constructly-estimator__result" data-result hidden aria-live="polite" aria-atomic="true">
                <p><strong>Объем:</strong> <span data-result-volume>-</span> м3</p>
                <p><strong>Количество материала:</strong> <span data-result-material>-</span></p>
            </div>
        </div>
        <?php

        return (string) ob_get_clean();
    }

    private function enqueueAssets(): void
    {
        $styleHandle = 'constructly-estimate-form';
        $scriptHandle = 'constructly-estimate-form';
        $assetVersion = '0.2.0';
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
            'constructlyEstimateFormData',
            [
                'endpoint' => esc_url_raw(rest_url('constructly/v1/estimate')),
                'networkErrorMessage' => 'Не удалось выполнить запрос. Проверьте подключение и попробуйте снова.',
            ]
        );

        wp_enqueue_style($styleHandle);
        wp_enqueue_script($scriptHandle);
    }
}
