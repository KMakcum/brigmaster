<?php
declare(strict_types=1);

get_header();

?>
<main id="primary" class="site-main bm-404 bm-shell" role="main">
    <header>
        <h1><?php esc_html_e('Page not found', 'brigmaster-theme'); ?></h1>
    </header>
    <p><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to home', 'brigmaster-theme'); ?></a></p>
</main>
<?php
get_footer();
