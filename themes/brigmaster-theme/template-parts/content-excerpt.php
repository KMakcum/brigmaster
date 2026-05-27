<?php
declare(strict_types=1);

$reading_mins = bm_reading_time_minutes(get_the_ID());
$cats = get_the_category();
$primary_cat = !empty($cats) && $cats[0] instanceof WP_Term ? $cats[0] : null;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('bm-post-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a class="bm-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
            <?php
            the_post_thumbnail('medium_large', [
                'class' => 'bm-post-card__thumb',
                'loading' => 'lazy',
                'decoding' => 'async',
            ]);
            ?>
        </a>
    <?php else : ?>
        <div class="bm-post-card__media bm-post-card__media--placeholder" aria-hidden="true"></div>
    <?php endif; ?>

    <div class="bm-post-card__body">
        <?php if ($primary_cat !== null) : ?>
            <p class="bm-post-card__category"><?php echo esc_html(function_exists('mb_strtoupper') ? mb_strtoupper($primary_cat->name, 'UTF-8') : strtoupper($primary_cat->name)); ?></p>
        <?php endif; ?>

        <h3 class="bm-post-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <div class="bm-post-card__excerpt">
            <?php the_excerpt(); ?>
        </div>

        <div class="bm-post-card__meta">
            <span class="bm-post-card__reading">
                <?php
                echo esc_html(
                    sprintf(
                        /* translators: %d: minutes */
                        _n('%d мин', '%d мин', $reading_mins, 'brigmaster-theme'),
                        $reading_mins
                    )
                );
                ?>
            </span>
            <span class="bm-post-card__meta-sep" aria-hidden="true">·</span>
            <time class="bm-post-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                <?php echo esc_html(get_the_date('d.m.Y')); ?>
            </time>
        </div>
    </div>
</article>
