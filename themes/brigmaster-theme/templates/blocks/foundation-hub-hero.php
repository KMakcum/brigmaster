<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$title = (string) ($attributes['title'] ?? '');
$lead = (string) ($attributes['lead'] ?? '');
$image = (string) ($attributes['image'] ?? '');
$features = is_array($attributes['features'] ?? null) ? $attributes['features'] : [];
$breadcrumbs = is_array($attributes['breadcrumbs'] ?? null) ? $attributes['breadcrumbs'] : [];
$title_id = (string) ($attributes['titleId'] ?? 'hub-hero-title');
?>
<section class="bm-page-hero" aria-labelledby="<?php echo esc_attr($title_id); ?>">
    <?php if ($image !== '') : ?>
        <img class="bm-page-hero__image" src="<?php echo constructly_esc_block_image_src($image); ?>" alt="" loading="lazy" decoding="async">
    <?php endif; ?>
    <div class="bm-container bm-page-hero__container">
        <?php if ($breadcrumbs !== []) : ?>
            <nav class="bm-breadcrumbs bm-breadcrumbs--chevron bm-breadcrumbs--link-brand bm-page-hero__breadcrumbs" aria-label="Breadcrumb">
                <ol>
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
                        <li>
                            <?php if ($url !== '') : ?>
                                <a href="<?php echo constructly_esc_block_href($url); ?>"><?php echo esc_html($label); ?></a>
                            <?php else : ?>
                                <span aria-current="page"><?php echo esc_html($label); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>

        <div class="bm-hero">
            <?php if ($title !== '') : ?>
                <h1 id="<?php echo esc_attr($title_id); ?>" class="bm-hero__title"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>
            <?php if ($lead !== '') : ?>
                <p class="bm-hero__lead"><?php echo esc_html($lead); ?></p>
            <?php endif; ?>
            <?php if ($features !== []) : ?>
                <ul class="bm-hero__features">
                    <?php foreach ($features as $feature) : ?>
                        <?php
                        if (!is_array($feature)) {
                            continue;
                        }

                        $feature_icon = (string) ($feature['icon'] ?? 'check-circle');
                        $feature_title = (string) ($feature['title'] ?? '');
                        $feature_text = (string) ($feature['text'] ?? '');
                        ?>
                        <li class="bm-hero__feature">
                            <svg class="bm-icon bm-hero__feature-icon" aria-hidden="true">
                                <use href="#bm-icon-<?php echo esc_attr($feature_icon); ?>"></use>
                            </svg>
                            <span class="bm-hero__feature-copy">
                                <?php if ($feature_title !== '') : ?>
                                    <span class="bm-hero__feature-title"><?php echo esc_html($feature_title); ?></span>
                                <?php endif; ?>
                                <?php if ($feature_text !== '') : ?>
                                    <span class="bm-hero__feature-text"><?php echo esc_html($feature_text); ?></span>
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>
