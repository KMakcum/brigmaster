<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

?>
<footer class="site-footer bm-site-footer" role="contentinfo">
    <div class="bm-site-footer__inner bm-footer-wrap">
        <div class="bm-footer-grid">
            <div class="bm-footer-about">
                <span class="bm-logo-placeholder bm-logo-placeholder--footer" aria-hidden="true"></span>
                <strong><?php bloginfo('name'); ?></strong>
                <p><?php bloginfo('description'); ?></p>
                <div class="bm-social" aria-label="<?php esc_attr_e('Social', 'brigmaster-theme'); ?>">
                    <?php esc_html_e('Telegram · VK · YouTube (placeholders - replace with assets).', 'brigmaster-theme'); ?>
                </div>
            </div>

            <?php if (has_nav_menu('footer-column-1')) : ?>
                <nav class="bm-footer-column" aria-labelledby="bm-footer-col-1-title">
                    <p class="bm-footer-heading" id="bm-footer-col-1-title"><?php esc_html_e('Калькуляторы', 'brigmaster-theme'); ?></p>
                    <?php
                    wp_nav_menu(
                        [
                            'theme_location' => 'footer-column-1',
                            'menu_class' => 'menu footer-column footer-column-1',
                            'fallback_cb' => false,
                            'depth' => 1,
                            'container' => false,
                        ]
                    );
                    ?>
                </nav>
            <?php endif; ?>

            <?php if (has_nav_menu('footer-column-2')) : ?>
                <nav class="bm-footer-column" aria-labelledby="bm-footer-col-2-title">
                    <p class="bm-footer-heading" id="bm-footer-col-2-title"><?php esc_html_e('Информация', 'brigmaster-theme'); ?></p>
                    <?php
                    wp_nav_menu(
                        [
                            'theme_location' => 'footer-column-2',
                            'menu_class' => 'menu footer-column footer-column-2',
                            'fallback_cb' => false,
                            'depth' => 1,
                            'container' => false,
                        ]
                    );
                    ?>
                </nav>
            <?php endif; ?>

            <?php if (has_nav_menu('footer-column-3')) : ?>
                <nav class="bm-footer-column" aria-labelledby="bm-footer-col-3-title">
                    <p class="bm-footer-heading" id="bm-footer-col-3-title"><?php esc_html_e('О проекте', 'brigmaster-theme'); ?></p>
                    <?php
                    wp_nav_menu(
                        [
                            'theme_location' => 'footer-column-3',
                            'menu_class' => 'menu footer-column footer-column-3',
                            'fallback_cb' => false,
                            'depth' => 1,
                            'container' => false,
                        ]
                    );
                    ?>
                </nav>
            <?php endif; ?>
        </div>
        <div class="bm-footer-bottom">
            <?php esc_html_e('© Brigmaster.ru - все права защищены.', 'brigmaster-theme'); ?>
        </div>
    </div>
</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
