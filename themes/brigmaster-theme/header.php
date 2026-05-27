<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'brigmaster-theme'); ?></a>
<header class="bm-site-header" role="banner">
    <div class="bm-site-header__inner">
        <a class="bm-site-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <span class="bm-logo-placeholder" aria-hidden="true"></span>
            <span><?php bloginfo('name'); ?></span>
        </a>

        <?php if (has_nav_menu('primary')) : ?>
            <?php
            wp_nav_menu(
                [
                    'theme_location' => 'primary',
                    'menu_class' => 'menu',
                    'menu_id' => 'primary-navigation',
                    'container' => 'nav',
                    'container_class' => 'bm-nav-primary',
                    'container_aria_label' => __('Primary', 'brigmaster-theme'),
                    'depth' => 3,
                    'fallback_cb' => false,
                ]
            );
            ?>
        <?php endif; ?>

        <div class="bm-site-header__cta">
            <a class="bm-btn bm-btn--primary" href="<?php echo esc_url(home_url('/kalkulyatory/')); ?>">
                <?php esc_html_e('Открыть калькуляторы', 'brigmaster-theme'); ?>
            </a>
        </div>
    </div>
</header>