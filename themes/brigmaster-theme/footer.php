<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$footer_columns = [
    [
        'location' => 'footer-column-1',
        'title' => __('Калькуляторы', 'brigmaster-theme'),
    ],
    [
        'location' => 'footer-column-2',
        'title' => __('Информация', 'brigmaster-theme'),
    ],
    [
        'location' => 'footer-column-3',
        'title' => __('О проекте', 'brigmaster-theme'),
    ],
];

$footer_description = trim((string) get_bloginfo('description'));
if ($footer_description === '') {
    $footer_description = __('Онлайн-калькуляторы для строительства и ремонта. Понятные расчеты и полезный контент для любых задач.', 'brigmaster-theme');
}

?>
</div>
<footer class="bm-footer" role="contentinfo">
    <div class="bm-container">
        <div class="bm-footer__grid">
            <div class="bm-footer__brand">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img
                        class="bm-footer__logo"
                        src="<?php echo esc_url(get_theme_file_uri('assets/src/images/logo_footer.png')); ?>"
                        width="152"
                        height="36"
                        alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                    >
                </a>
                <p class="bm-footer__desc"><?php echo esc_html($footer_description); ?></p>
            </div>

            <?php foreach ($footer_columns as $index => $column) : ?>
                <nav class="bm-footer__column" aria-labelledby="bm-footer-col-<?php echo esc_attr((string) ($index + 1)); ?>-title">
                    <div class="bm-footer__title" id="bm-footer-col-<?php echo esc_attr((string) ($index + 1)); ?>-title">
                        <?php echo esc_html($column['title']); ?>
                    </div>
                    <?php if (has_nav_menu($column['location'])) : ?>
                        <?php
                        wp_nav_menu(
                            [
                                'theme_location' => $column['location'],
                                'menu_class' => 'bm-footer__links',
                                'container' => false,
                                'fallback_cb' => false,
                                'depth' => 1,
                            ]
                        );
                        ?>
                    <?php endif; ?>
                </nav>
            <?php endforeach; ?>
        </div>
        <p class="bm-footer__bottom">
            <?php
            printf(
                /* translators: %s: current year */
                esc_html__('© %s Brigmaster.ru - все права защищены.', 'brigmaster-theme'),
                esc_html((string) wp_date('Y'))
            );
            ?>
        </p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
