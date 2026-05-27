<?php
declare(strict_types=1);

get_header();

?>
<main id="primary" class="site-main" role="main">
    <?php bm_breadcrumbs(); ?>
    <?php if (have_posts()) : ?>
        <?php
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/content');
        endwhile;
        the_posts_navigation();
        ?>
    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>
</main>
<?php
get_footer();
