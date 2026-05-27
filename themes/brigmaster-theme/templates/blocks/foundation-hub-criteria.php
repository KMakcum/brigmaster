<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$section_title = (string) ($attributes['sectionTitle'] ?? '');
$items = is_array($attributes['items'] ?? null) ? $attributes['items'] : [];
?>
<div class="brigmaster-content-block">
    <?php if ($section_title !== '') : ?>
        <h2><?php echo esc_html($section_title); ?></h2>
    <?php endif; ?>
    <?php if ($items !== []) : ?>
        <ol class="brigmaster-foundation-hub__criteria">
            <?php foreach ($items as $item) : ?>
                <?php
                $line = is_string($item) ? $item : (string) ($item['text'] ?? '');
                if ($line === '') {
                    continue;
                }
                ?>
                <li><?php echo esc_html($line); ?></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>
