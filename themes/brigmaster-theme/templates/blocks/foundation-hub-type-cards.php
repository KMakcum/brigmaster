<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$section_title = (string) ($attributes['sectionTitle'] ?? '');
$anchor_id = trim((string) ($attributes['anchorId'] ?? 'foundation-types'));
if ($anchor_id === '') {
    $anchor_id = 'foundation-types';
}
$cards = is_array($attributes['cards'] ?? null) ? $attributes['cards'] : [];
?>
<div id="<?php echo esc_attr($anchor_id); ?>" class="brigmaster-content-block brigmaster-content-block--muted">
    <?php if ($section_title !== '') : ?>
        <h2><?php echo esc_html($section_title); ?></h2>
    <?php endif; ?>
    <?php if ($cards !== []) : ?>
        <div class="brigmaster-foundation-hub__cards">
            <?php foreach ($cards as $card) : ?>
                <?php
                $card_title = isset($card['title']) ? (string) $card['title'] : '';
                $thesis = isset($card['thesis']) ? (string) $card['thesis'] : '';
                $button_label = isset($card['buttonLabel']) ? (string) $card['buttonLabel'] : '';
                $button_url = isset($card['buttonUrl']) ? (string) $card['buttonUrl'] : '#';
                ?>
                <article class="brigmaster-foundation-hub__card">
                    <?php
                    $icon_key = isset($card['icon']) ? (string) $card['icon'] : 'slab';
                    require CONSTRUCTLY_THEME_PATH . '/templates/blocks/partials/foundation-hub-icon.php';
                    ?>
                    <?php if ($card_title !== '') : ?>
                        <h3><?php echo esc_html($card_title); ?></h3>
                    <?php endif; ?>
                    <?php if ($thesis !== '') : ?>
                        <p class="brigmaster-foundation-hub__card-thesis"><?php echo esc_html($thesis); ?></p>
                    <?php endif; ?>
                    <?php if ($button_label !== '') : ?>
                        <a class="bm-btn bm-btn--primary bm-btn--wide brigmaster-foundation-hub__cta--card" href="<?php echo constructly_esc_block_href($button_url); ?>"><?php echo esc_html($button_label); ?></a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
