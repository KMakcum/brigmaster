<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$section_title = (string) ($attributes['sectionTitle'] ?? '');
$primary_links = is_array($attributes['primaryLinks'] ?? null) ? $attributes['primaryLinks'] : [];
$secondary_links = is_array($attributes['secondaryLinks'] ?? null) ? $attributes['secondaryLinks'] : [];
?>
<div class="brigmaster-content-block brigmaster-content-block--muted">
    <?php if ($section_title !== '') : ?>
        <h2><?php echo esc_html($section_title); ?></h2>
    <?php endif; ?>
    <?php if ($primary_links !== []) : ?>
        <ul class="brigmaster-foundation-hub__links">
            <?php foreach ($primary_links as $row) : ?>
                <?php
                $label = isset($row['label']) ? (string) $row['label'] : '';
                $url = isset($row['url']) ? (string) $row['url'] : '#';
                if ($label === '') {
                    continue;
                }
                ?>
                <li><a href="<?php echo constructly_esc_block_href($url); ?>"><?php echo esc_html($label); ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if ($secondary_links !== []) : ?>
        <ul class="brigmaster-foundation-hub__links brigmaster-foundation-hub__links--compact">
            <?php foreach ($secondary_links as $row) : ?>
                <?php
                $label = isset($row['label']) ? (string) $row['label'] : '';
                $url = isset($row['url']) ? (string) $row['url'] : '#';
                if ($label === '') {
                    continue;
                }
                ?>
                <li><a href="<?php echo constructly_esc_block_href($url); ?>"><?php echo esc_html($label); ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
