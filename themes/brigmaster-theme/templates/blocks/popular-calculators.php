<?php
declare(strict_types=1);

$anchor = trim((string) ($attributes['anchor'] ?? ''));
if ($anchor === '') {
    $anchor = 'calculators';
}
$title = (string) ($attributes['title'] ?? '');
$subtitle = (string) ($attributes['subtitle'] ?? '');
$cards = is_array($attributes['cards'] ?? null) ? $attributes['cards'] : [];
$section_attributes = ' id="' . esc_attr(sanitize_title($anchor) ?: 'calculators') . '"';
?>
<section<?php echo $section_attributes; ?> class="bm-home-section bm-home-section--popular-calculators">
    <div class="bm-shell">
        <div class="bm-section-heading">
            <?php if ($title !== '') : ?>
                <h2><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($subtitle !== '') : ?>
                <p class="bm-section-heading__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($cards !== []) : ?>
            <div class="bm-calculators-grid">
                <?php foreach ($cards as $card) : ?>
                    <?php
                    $card_title = isset($card['title']) ? (string) $card['title'] : '';
                    $description = isset($card['description']) ? (string) $card['description'] : '';
                    $button_label = isset($card['buttonLabel']) ? (string) $card['buttonLabel'] : '';
                    $button_url = isset($card['buttonUrl']) ? (string) $card['buttonUrl'] : '#calculators';
                    $icon = isset($card['icon']) ? (string) $card['icon'] : '';
                    $preview_id = isset($card['previewMediaId']) ? absint($card['previewMediaId']) : 0;
                    $meta = isset($card['meta']) ? (string) $card['meta'] : '';

                    if ($card_title === 'Гипсокартон' && str_contains($button_url, '/o-proekte')) {
                        $drywall_page = get_page_by_path('kalkulyatory/gipsokarton');
                        $button_url = $drywall_page instanceof WP_Post ? (string) get_permalink($drywall_page) : home_url('/kalkulyatory/gipsokarton/');
                    }
                    ?>
                    <article class="bm-calculator-card">
                        <div class="bm-calculator-card__preview" aria-hidden="true">
                            <?php if ($preview_id > 0 && wp_attachment_is_image($preview_id)) : ?>
                                <?php
                                echo wp_get_attachment_image(
                                    $preview_id,
                                    'medium',
                                    false,
                                    [
                                        'class' => 'bm-calculator-card__preview-img',
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]
                                );
                                ?>
                            <?php else : ?>
                                <span><?php echo esc_html($icon); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($card_title !== '') : ?>
                            <h3><?php echo esc_html($card_title); ?></h3>
                        <?php endif; ?>
                        <?php if ($description !== '') : ?>
                            <p><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                        <?php if ($meta !== '') : ?>
                            <p class="bm-calculator-card__meta"><?php echo esc_html($meta); ?></p>
                        <?php endif; ?>
                        <?php if ($button_label !== '') : ?>
                            <a class="bm-btn bm-btn--primary bm-btn--wide" href="<?php echo constructly_esc_block_href($button_url); ?>">
                                <?php echo esc_html($button_label); ?>
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
