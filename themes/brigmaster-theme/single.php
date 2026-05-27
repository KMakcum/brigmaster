<?php
declare(strict_types=1);

get_header();

?>
<main id="primary" class="site-main site-main--single-post" role="main">
    <?php bm_breadcrumbs(); ?>
    <?php
    while (have_posts()) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('bm-post-single'); ?>>
            <header class="bm-post-single__header bm-shell">
                <h1 class="bm-post-single__title"><?php the_title(); ?></h1>
                <p class="bm-post-single__meta">
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                </p>
            </header>
            <div class="bm-post-single__content entry-content bm-shell">
                <?php the_content(); ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</main>
<?php
get_footer();
