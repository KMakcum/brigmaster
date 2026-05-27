<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$title = (string) ($attributes['title'] ?? '');
$lead = (string) ($attributes['lead'] ?? '');
$cta_label = (string) ($attributes['ctaLabel'] ?? '');
$cta_url = (string) ($attributes['ctaUrl'] ?? '#foundation-types');
$title_id = 'brigmaster-foundation-hub-title';
?>
<div class="brigmaster-content-block brigmaster-foundation-hub__hero">
    <?php if ($title !== '') : ?>
        <h1 id="<?php echo esc_attr($title_id); ?>" class="brigmaster-foundation-hub__title"><?php echo esc_html($title); ?></h1>
    <?php endif; ?>
    <?php if ($lead !== '') : ?>
        <p class="brigmaster-foundation-hub__lead"><?php echo esc_html($lead); ?></p>
    <?php endif; ?>
    <?php if ($cta_label !== '') : ?>
        <a class="bm-btn bm-btn--primary" href="<?php echo constructly_esc_block_href($cta_url); ?>"><?php echo esc_html($cta_label); ?></a>
    <?php endif; ?>
</div>
