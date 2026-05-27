<?php
declare(strict_types=1);

$anchor = trim((string) ($attributes['anchor'] ?? ''));
if ($anchor === '') {
    $anchor = 'how-it-works';
}
$title = (string) ($attributes['title'] ?? '');
$subtitle = (string) ($attributes['subtitle'] ?? '');
$cta_label = (string) ($attributes['ctaLabel'] ?? '');
$cta_url = (string) ($attributes['ctaUrl'] ?? '#calculators');
$steps = is_array($attributes['steps'] ?? null) ? $attributes['steps'] : [];
$section_attributes = ' id="' . esc_attr(sanitize_title($anchor) ?: 'how-it-works') . '"';
?>
<section<?php echo $section_attributes; ?> class="bm-home-section bm-home-section--how-it-works">
    <div class="bm-shell">
        <div class="bm-section-heading">
            <?php if ($title !== '') : ?>
                <h2><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($subtitle !== '') : ?>
                <p class="bm-section-heading__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($steps !== []) : ?>
            <div class="bm-steps-grid">
                <?php foreach ($steps as $step) : ?>
                    <?php
                    $number = isset($step['number']) ? (string) $step['number'] : '';
                    $step_title = isset($step['title']) ? (string) $step['title'] : '';
                    $step_text = isset($step['text']) ? (string) $step['text'] : '';
                    ?>
                    <article class="bm-step-card">
                        <?php if ($number !== '') : ?>
                            <span class="bm-step-card__number"><?php echo esc_html($number); ?></span>
                        <?php endif; ?>
                        <?php if ($step_title !== '') : ?>
                            <h3><?php echo esc_html($step_title); ?></h3>
                        <?php endif; ?>
                        <?php if ($step_text !== '') : ?>
                            <p><?php echo esc_html($step_text); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($cta_label !== '') : ?>
            <div class="bm-section-footer">
                <a class="bm-btn bm-btn--secondary" href="<?php echo constructly_esc_block_href($cta_url); ?>">
                    <?php echo esc_html($cta_label); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
