<?php
declare(strict_types=1);

$title = (string) ($attributes['title'] ?? '');
$lead = (string) ($attributes['lead'] ?? '');
$primary_label = (string) ($attributes['primaryLabel'] ?? '');
$primary_url = (string) ($attributes['primaryUrl'] ?? '#calculators');
$secondary_label = (string) ($attributes['secondaryLabel'] ?? '');
$secondary_url = (string) ($attributes['secondaryUrl'] ?? '#how-it-works');
$quick_links_label = (string) ($attributes['quickLinksLabel'] ?? '');
$quick_links = is_array($attributes['quickLinks'] ?? null) ? $attributes['quickLinks'] : [];
$theme_variant = (string) ($attributes['themeVariant'] ?? 'dark');
$section_classes = 'bm-home-section bm-home-section--hero bm-home-section--hero-' . sanitize_html_class($theme_variant);
?>
<section class="<?php echo esc_attr($section_classes); ?>">
    <div class="bm-shell">
        <div class="bm-hero">
            <div class="bm-hero__content">
                <?php if ($title !== '') : ?>
                    <h1 class="bm-hero__title"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>

                <?php if ($lead !== '') : ?>
                    <p class="bm-hero__lead"><?php echo esc_html($lead); ?></p>
                <?php endif; ?>

                <div class="bm-hero__actions">
                    <?php if ($primary_label !== '') : ?>
                        <a class="bm-btn bm-btn--primary" href="<?php echo constructly_esc_block_href($primary_url); ?>">
                            <?php echo esc_html($primary_label); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($secondary_label !== '') : ?>
                        <a class="bm-btn bm-btn--secondary" href="<?php echo constructly_esc_block_href($secondary_url); ?>">
                            <?php echo esc_html($secondary_label); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($quick_links_label !== '' && $quick_links !== []) : ?>
                    <div class="bm-hero__quick-links">
                        <span class="bm-hero__quick-links-label"><?php echo esc_html($quick_links_label); ?></span>
                        <div class="bm-hero__quick-links-list">
                            <?php foreach ($quick_links as $link) : ?>
                                <?php
                                $label = isset($link['label']) ? (string) $link['label'] : '';
                                $url = isset($link['url']) ? (string) $link['url'] : '#';
                                ?>
                                <?php if ($label !== '') : ?>
                                    <a href="<?php echo constructly_esc_block_href($url); ?>"><?php echo esc_html($label); ?></a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bm-hero__visual" aria-hidden="true">
                <div class="bm-hero__diagram">
                    <span class="bm-hero__diagram-line"></span>
                    <span class="bm-hero__diagram-grid"></span>
                    <span class="bm-hero__diagram-card">Площадь</span>
                    <span class="bm-hero__diagram-card">Объём</span>
                    <span class="bm-hero__diagram-card">Масса</span>
                </div>
            </div>
        </div>
    </div>
</section>
