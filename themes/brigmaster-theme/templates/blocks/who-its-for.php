<?php
declare(strict_types=1);

$title = (string) ($attributes['title'] ?? '');
$items = is_array($attributes['items'] ?? null) ? $attributes['items'] : [];
$aside = (string) ($attributes['aside'] ?? '');
?>
<section class="bm-home-section bm-home-section--who-its-for">
    <div class="bm-shell">
        <div class="bm-section-heading">
            <?php if ($title !== '') : ?>
                <h2><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
        </div>

        <div class="bm-audience-layout">
            <?php if ($items !== []) : ?>
                <ul class="bm-audience-list">
                    <?php foreach ($items as $item) : ?>
                        <?php if (is_string($item) && $item !== '') : ?>
                            <li><?php echo esc_html($item); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($aside !== '') : ?>
                <aside class="bm-audience-note">
                    <p><?php echo esc_html($aside); ?></p>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</section>
