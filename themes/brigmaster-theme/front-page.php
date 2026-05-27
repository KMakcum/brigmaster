<?php
declare(strict_types=1);

get_header();

?>
<main id="primary" class="site-main site-main--front" role="main">
    <?php
    while (have_posts()) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('bm-page bm-page--front'); ?>>
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
