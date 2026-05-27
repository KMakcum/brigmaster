<?php
declare(strict_types=1);

/**
 * Template Name: About (О проекте)
 * Template Post Type: page
 */

get_header();

?>
<main id="primary" class="site-main site-main--about" role="main">
    <?php bm_breadcrumbs(); ?>
    <?php
    while (have_posts()) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('bm-page bm-page--about'); ?>>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</main>
<?php
get_footer();
