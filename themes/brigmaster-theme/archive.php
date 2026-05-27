<?php
declare(strict_types=1);

get_header();

global $wp_query;

?>
<main id="primary" class="site-main site-main--archive" role="main">
    <?php bm_breadcrumbs(); ?>
    <div class="bm-shell">
        <header class="bm-archive-header">
            <h1><?php echo esc_html(wp_strip_all_tags(get_the_archive_title())); ?></h1>
        </header>

        <?php if (have_posts()) : ?>
            <p class="bm-post-archive__found">
                <?php
                $found = isset($wp_query->found_posts) ? (int) $wp_query->found_posts : 0;
                echo esc_html(bm_archive_found_posts_label($found));
                ?>
            </p>
            <div class="bm-post-archive__grid">
                <?php
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/content', 'excerpt');
                endwhile;
                ?>
            </div>
            <div class="bm-pagination">
                <?php
                the_posts_pagination(
                    [
                        'mid_size' => 2,
                        'prev_text' => __('Назад', 'brigmaster-theme'),
                        'next_text' => __('Вперёд', 'brigmaster-theme'),
                    ]
                );
                ?>
            </div>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
