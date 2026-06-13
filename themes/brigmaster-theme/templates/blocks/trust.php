<?php
declare(strict_types=1);

$title = (string) ($attributes['title'] ?? '');
$subtitle = (string) ($attributes['subtitle'] ?? '');
$anchor = trim((string) ($attributes['anchor'] ?? ''));
$title_id = trim((string) ($attributes['titleId'] ?? 'trust-title'));
$theme_variant = (string) ($attributes['themeVariant'] ?? 'bg');
$link_label = (string) ($attributes['linkLabel'] ?? '');
$link_url = (string) ($attributes['linkUrl'] ?? '');
$items = is_array($attributes['items'] ?? null) ? $attributes['items'] : [];

if ($title_id === '') {
    $title_id = 'trust-title';
}

$section_classes = 'bm-section';
if ($theme_variant === 'bg') {
    $section_classes .= ' bm-section--bg';
}
?>
<section<?php echo $anchor !== '' ? ' id="' . esc_attr($anchor) . '"' : ''; ?> class="<?php echo esc_attr($section_classes); ?>" aria-labelledby="<?php echo esc_attr($title_id); ?>">
    <div class="bm-container">
        <header class="bm-section-toolbar">
            <div class="bm-section-toolbar__main">
                <?php if ($title !== '') : ?>
                    <h2 id="<?php echo esc_attr($title_id); ?>" class="bm-section-toolbar__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <?php if ($subtitle !== '') : ?>
                    <p class="bm-section-toolbar__lead"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>
        </header>

        <?php if ($items !== []) : ?>
            <div class="bm-card-grid bm-card-grid--cols-4">
                <?php foreach ($items as $item) : ?>
                    <?php
                    if (!is_array($item)) {
                        continue;
                    }

                    $item_title = (string) ($item['title'] ?? '');
                    $item_text = (string) ($item['text'] ?? '');
                    $item_icon = (string) ($item['icon'] ?? 'check-circle');
                    ?>
                    <article class="bm-card bm-card-feature">
                        <span class="bm-card-feature__icon" aria-hidden="true">
                            <svg class="bm-icon bm-card-feature__icon-svg">
                                <use href="#bm-icon-<?php echo esc_attr($item_icon); ?>"></use>
                            </svg>
                        </span>
                        <div class="bm-card-feature__content">
                            <?php if ($item_title !== '') : ?>
                                <h3 class="bm-card-feature__title"><?php echo esc_html($item_title); ?></h3>
                            <?php endif; ?>
                            <?php if ($item_text !== '') : ?>
                                <p class="bm-card-feature__text"><?php echo esc_html($item_text); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($link_label !== '' && $link_url !== '') : ?>
            <a href="<?php echo constructly_esc_block_href($link_url); ?>" class="bm-section-toolbar__link bm-section__footer-link">
                <?php echo esc_html($link_label); ?>
                <svg class="bm-icon bm-icon--sm" aria-hidden="true">
                    <use href="#bm-icon-arrow-right"></use>
                </svg>
            </a>
        <?php endif; ?>
    </div>
</section>
