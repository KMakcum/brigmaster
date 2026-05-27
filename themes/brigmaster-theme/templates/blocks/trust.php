<?php
declare(strict_types=1);

$title = (string) ($attributes['title'] ?? '');
$items = is_array($attributes['items'] ?? null) ? $attributes['items'] : [];
?>
<section class="bm-home-section bm-home-section--trust">
    <div class="bm-shell">
        <div class="bm-section-heading">
            <?php if ($title !== '') : ?>
                <h2><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
        </div>

        <?php if ($items !== []) : ?>
            <div class="bm-trust-grid">
                <?php foreach ($items as $item) : ?>
                    <?php
                    $item_title = isset($item['title']) ? (string) $item['title'] : '';
                    $item_text = isset($item['text']) ? (string) $item['text'] : '';
                    ?>
                    <article class="bm-trust-card">
                        <?php if ($item_title !== '') : ?>
                            <h3><?php echo esc_html($item_title); ?></h3>
                        <?php endif; ?>
                        <?php if ($item_text !== '') : ?>
                            <p><?php echo esc_html($item_text); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
