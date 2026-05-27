<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

?>
<section class="brigmaster-foundation-hub" aria-labelledby="brigmaster-foundation-hub-title">
    <div class="brigmaster-content-block brigmaster-foundation-hub__hero">
        <h1 id="brigmaster-foundation-hub-title" class="brigmaster-foundation-hub__title">Калькулятор фундамента</h1>
        <p class="brigmaster-foundation-hub__lead">
            Три типа фундамента — три отдельных калькулятора. Эта страница помогает выбрать нужный сценарий и перейти к соответствующей форме,
            но не определяет тип основания автоматически. Если вы не уверены в конструкции, сначала уточните тип фундамента по геологии,
            нагрузкам и проекту, а уже потом используйте нужный калькулятор для предварительной оценки материалов.
        </p>
        <a class="bm-btn bm-btn--primary" href="#foundation-types">К типам фундамента</a>
    </div>

    <div id="foundation-types" class="brigmaster-content-block brigmaster-content-block--muted">
        <h2>Выберите тип фундамента</h2>
        <div class="brigmaster-foundation-hub__cards">
            <article class="brigmaster-foundation-hub__card">
                <?php
                $icon_key = 'slab';
                require CONSTRUCTLY_THEME_PATH . '/templates/blocks/partials/foundation-hub-icon.php';
                ?>
                <h3>Плитный фундамент</h3>
                <p class="brigmaster-foundation-hub__card-thesis">
                    Монолитная плита на весь контур. Калькулятор считает бетон, а при нужных режимах показывает арматуру и опалубку.
                </p>
                <a href="<?php echo constructly_esc_block_href(home_url('/kalkulyatory/fundament/plitnyj/')); ?>" class="bm-btn bm-btn--primary bm-btn--wide brigmaster-foundation-hub__cta--card">Открыть калькулятор плиты</a>
            </article>

            <article class="brigmaster-foundation-hub__card">
                <?php
                $icon_key = 'strip';
                require CONSTRUCTLY_THEME_PATH . '/templates/blocks/partials/foundation-hub-icon.php';
                ?>
                <h3>Ленточный фундамент</h3>
                <p class="brigmaster-foundation-hub__card-thesis">
                    Лента под несущими стенами. Калькулятор работает по длине и сечению, а также поддерживает режим по участкам.
                </p>
                <a href="<?php echo constructly_esc_block_href(home_url('/kalkulyatory/fundament/lentochnyj/')); ?>" class="bm-btn bm-btn--primary bm-btn--wide brigmaster-foundation-hub__cta--card">Открыть калькулятор ленты</a>
            </article>

            <article class="brigmaster-foundation-hub__card">
                <?php
                $icon_key = 'pile';
                require CONSTRUCTLY_THEME_PATH . '/templates/blocks/partials/foundation-hub-icon.php';
                ?>
                <h3>Свайный фундамент</h3>
                <p class="brigmaster-foundation-hub__card-thesis">
                    Сваи и ростверк. Калькулятор помогает оценить материалы, но несущая способность и выбор схемы всё равно подтверждаются проектом.
                </p>
                <a href="<?php echo constructly_esc_block_href(home_url('/kalkulyatory/fundament/svajnyj/')); ?>" class="bm-btn bm-btn--primary bm-btn--wide brigmaster-foundation-hub__cta--card">Открыть калькулятор свай</a>
            </article>
        </div>
    </div>

    <div class="brigmaster-content-block">
        <h2>Как выбрать тип фундамента</h2>
        <ol class="brigmaster-foundation-hub__criteria">
            <li>Проверьте тип грунта и уровень грунтовых вод на участке.</li>
            <li>Оцените вес дома, этажность и тип стеновых материалов.</li>
            <li>Сравните бюджет и сроки реализации разных решений.</li>
            <li>Учитывайте рельеф, перепады высот и особенности участка.</li>
            <li>Не используйте этот хаб как инструмент автоматического выбора фундамента: он только направляет к нужному калькулятору.</li>
            <li>Согласуйте итоговый вариант с проектировщиком.</li>
        </ol>
    </div>

    <div class="brigmaster-content-block">
        <h2>Часто задаваемые вопросы</h2>
        <div id="foundation-hub-faq-1" class="brigmaster-faq-item">
            <h3 class="brigmaster-faq-item__question">Что делает этот раздел, а что не делает?</h3>
            <p class="brigmaster-faq-item__answer">Хаб помогает перейти к нужному фундаментному калькулятору, но не выбирает тип основания автоматически и не заменяет решение проектировщика.</p>
        </div>
        <div id="foundation-hub-faq-2" class="brigmaster-faq-item">
            <h3 class="brigmaster-faq-item__question">Можно ли ориентироваться только на онлайн-калькулятор?</h3>
            <p class="brigmaster-faq-item__answer">Нет. Каждый фундаментный калькулятор дает предварительную оценку материалов, но не заменяет проект, геологию и проверку несущей способности.</p>
        </div>
        <div id="foundation-hub-faq-3" class="brigmaster-faq-item">
            <h3 class="brigmaster-faq-item__question">Как выбрать между плитой, лентой и сваями?</h3>
            <p class="brigmaster-faq-item__answer">Сначала определите конструктивную схему по грунту, нагрузкам и условиям участка. После этого используйте соответствующий калькулятор, чтобы оценить материалы внутри выбранного варианта.</p>
        </div>
        <div id="foundation-hub-faq-4" class="brigmaster-faq-item">
            <h3 class="brigmaster-faq-item__question">Почему результаты на разных страницах отличаются?</h3>
            <p class="brigmaster-faq-item__answer">Плитный, ленточный и свайный фундамент рассчитываются по разным моделям и с разным набором полей, поэтому итоговые показатели не совпадают между собой.</p>
        </div>
    </div>

    <div class="brigmaster-content-block brigmaster-content-block--muted">
        <h2>Полезные ссылки</h2>
        <ul class="brigmaster-foundation-hub__links">
            <li><a href="<?php echo constructly_esc_block_href(home_url('/kalkulyatory/fundament/plitnyj/')); ?>">Калькулятор плитного фундамента</a></li>
            <li><a href="<?php echo constructly_esc_block_href(home_url('/kalkulyatory/fundament/lentochnyj/')); ?>">Калькулятор ленточного фундамента</a></li>
            <li><a href="<?php echo constructly_esc_block_href(home_url('/kalkulyatory/fundament/svajnyj/')); ?>">Калькулятор свайного фундамента</a></li>
        </ul>
        <ul class="brigmaster-foundation-hub__links brigmaster-foundation-hub__links--compact">
            <li><a href="<?php echo constructly_esc_block_href(home_url('/kalkulyatory/kirpich/')); ?>">Калькулятор кирпича</a></li>
            <li><a href="<?php echo constructly_esc_block_href(home_url('/kalkulyatory/styazhka/')); ?>">Калькулятор стяжки</a></li>
            <li><a href="<?php echo constructly_esc_block_href(home_url('/kalkulyatory/plitka/')); ?>">Калькулятор плитки</a></li>
        </ul>
    </div>
</section>
