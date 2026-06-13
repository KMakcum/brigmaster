<?php
declare(strict_types=1);

$anchor = trim((string) ($attributes['anchor'] ?? 'how-it-works'));
$title = (string) ($attributes['title'] ?? '');
$subtitle = (string) ($attributes['subtitle'] ?? '');
$steps = is_array($attributes['steps'] ?? null) ? $attributes['steps'] : [];
?>
<section id="<?php echo esc_attr(sanitize_title($anchor) ?: 'how-it-works'); ?>" class="bm-section bm-how-it-works" aria-labelledby="how-title">
    <div class="bm-container">
        <header class="bm-section-toolbar">
            <div class="bm-section-toolbar__main">
                <?php if ($title !== '') : ?>
                    <h2 id="how-title" class="bm-section-toolbar__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <?php if ($subtitle !== '') : ?>
                    <p class="bm-section-toolbar__lead"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>
        </header>

        <?php if ($steps !== []) : ?>
            <ol class="bm-how-it-works__steps">
                <?php foreach ($steps as $index => $step) : ?>
                    <?php
                    if (!is_array($step)) {
                        continue;
                    }

                    $step_title = (string) ($step['title'] ?? '');
                    $step_text = (string) ($step['text'] ?? '');
                    $step_icon = (string) ($step['icon'] ?? 'check-circle');
                    ?>
                    <li class="bm-how-it-works__step">
                        <span class="bm-how-it-works__icon" aria-hidden="true">
                            <svg class="bm-icon bm-how-it-works__icon-svg">
                                <use href="#bm-icon-<?php echo esc_attr($step_icon); ?>"></use>
                            </svg>
                        </span>
                        <?php if ($step_title !== '') : ?>
                            <h3 class="bm-how-it-works__step-title"><?php echo esc_html(($index + 1) . '. ' . $step_title); ?></h3>
                        <?php endif; ?>
                        <?php if ($step_text !== '') : ?>
                            <p class="bm-how-it-works__step-text"><?php echo esc_html($step_text); ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>
</section>
