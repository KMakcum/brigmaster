<?php
declare(strict_types=1);

$title = (string) ($attributes['title'] ?? '');
$text = (string) ($attributes['text'] ?? '');
$button_label = (string) ($attributes['buttonLabel'] ?? '');
$button_url = (string) ($attributes['buttonUrl'] ?? '#calculators');
?>
<section class="bm-home-section bm-home-section--final-cta">
    <div class="bm-shell">
        <div class="bm-final-cta">
            <?php if ($title !== '') : ?>
                <h2><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($text !== '') : ?>
                <p><?php echo esc_html($text); ?></p>
            <?php endif; ?>
            <?php if ($button_label !== '') : ?>
                <a class="bm-btn bm-btn--light" href="<?php echo constructly_esc_block_href($button_url); ?>">
                    <?php echo esc_html($button_label); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
