<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$title = (string) ($attributes['title'] ?? '');
$lead = (string) ($attributes['lead'] ?? '');
$title_id = trim((string) ($attributes['titleId'] ?? 'calculator-title'));
$features = is_array($attributes['features'] ?? null) ? $attributes['features'] : [];
$breadcrumbs = is_array($attributes['breadcrumbs'] ?? null) ? $attributes['breadcrumbs'] : [];

if ($title_id === '') {
    $title_id = 'calculator-title';
}
?>
<section class="bm-section bm-calculator-hero" aria-labelledby="<?php echo esc_attr($title_id); ?>">
    <div class="bm-container">
        <?php if ($breadcrumbs !== []) : ?>
            <nav class="bm-breadcrumbs bm-breadcrumbs--chevron bm-breadcrumbs--link-brand" aria-label="Хлебные крошки">
                <ol class="bm-breadcrumbs__list">
                    <?php foreach ($breadcrumbs as $breadcrumb) : ?>
                        <?php
                        if (!is_array($breadcrumb)) {
                            continue;
                        }

                        $label = (string) ($breadcrumb['label'] ?? '');
                        $url = (string) ($breadcrumb['url'] ?? '');
                        if ($label === '') {
                            continue;
                        }
                        ?>
                        <li class="bm-breadcrumbs__item"<?php echo $url === '' ? ' aria-current="page"' : ''; ?>>
                            <?php if ($url !== '') : ?>
                                <a href="<?php echo constructly_esc_block_href($url); ?>"><?php echo esc_html($label); ?></a>
                            <?php else : ?>
                                <?php echo esc_html($label); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>

        <div class="bm-calculator-hero__content">
            <?php if ($title !== '') : ?>
                <h1 id="<?php echo esc_attr($title_id); ?>" class="bm-calculator-hero__title"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>
            <?php if ($lead !== '') : ?>
                <p class="bm-calculator-hero__lead"><?php echo esc_html($lead); ?></p>
            <?php endif; ?>
            <?php if ($features !== []) : ?>
                <ul class="bm-calculator-hero__meta" aria-label="Особенности калькулятора">
                    <?php foreach ($features as $feature) : ?>
                        <?php
                        if (!is_array($feature)) {
                            continue;
                        }

                        $icon = (string) ($feature['icon'] ?? 'check-circle');
                        $text = (string) ($feature['text'] ?? '');
                        if ($text === '') {
                            continue;
                        }
                        ?>
                        <li class="bm-calculator-hero__meta-item">
                            <svg class="bm-icon" aria-hidden="true" focusable="false">
                                <use href="#bm-icon-<?php echo esc_attr($icon); ?>"></use>
                            </svg>
                            <span><?php echo esc_html($text); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>
