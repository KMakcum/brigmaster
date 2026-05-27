<?php
declare(strict_types=1);

$title = (string) ($attributes['title'] ?? '');
$text = (string) ($attributes['text'] ?? '');
$link_label = (string) ($attributes['linkLabel'] ?? '');
$link_url = (string) ($attributes['linkUrl'] ?? '#calculators');
?>
<section class="bm-home-section bm-home-section--how-calculations-work">
    <div class="bm-shell">
        <div class="bm-process-strip">
            <?php if ($title !== '') : ?>
                <h2><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($text !== '') : ?>
                <p><?php echo esc_html($text); ?></p>
            <?php endif; ?>
            <?php if ($link_label !== '') : ?>
                <a href="<?php echo constructly_esc_block_href($link_url); ?>"><?php echo esc_html($link_label); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>
