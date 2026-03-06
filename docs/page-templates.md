# Page Templates For Editors

Готовые copy-paste шаблоны для WordPress-редактора.
Стиль блоков опирается на UI Kit и классы из `assets/css/estimate-form.css`.

Как использовать:
1. Создайте страницу.
2. Вставьте H1.
3. Сразу после H1 вставьте shortcode калькулятора.
4. Ниже вставьте HTML-блоки из шаблона.

---

## 1) Калькулятор бетона

### H1
`Калькулятор бетона онлайн (плита и лента)`

### Shortcode (сразу после H1)
```text
[constructly_concrete_estimator]
```

### Блоки страницы
```html
<section class="constructly-content-block">
  <h2>Как производится расчет</h2>
  <ol>
    <li>Выберите режим расчета: норматив / с запасом / для новичка.</li>
    <li>Для типа slab введите площадь и толщину.</li>
    <li>Для типа strip введите длину, ширину и высоту.</li>
    <li>Нажмите «Рассчитать» и получите объем и количество материала.</li>
  </ol>
</section>

<section class="constructly-content-block">
  <h2>FAQ</h2>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">В каких единицах вводить размеры?</p>
    <p class="constructly-faq-item__answer">Все линейные размеры вводятся в метрах.</p>
  </div>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">Что выбрать: slab или strip?</p>
    <p class="constructly-faq-item__answer">slab — для плиты, strip — для ленточной конструкции.</p>
  </div>
</section>

<section class="constructly-content-block">
  <h2>Таблица норм (СП/ГОСТ)</h2>
  <table class="constructly-norms-table">
    <thead>
      <tr><th>Документ</th><th>Что регулирует</th></tr>
    </thead>
    <tbody>
      <tr><td>СП 63.13330</td><td>Бетонные конструкции</td></tr>
      <tr><td>СП 70.13330</td><td>Несущие и ограждающие конструкции</td></tr>
      <tr><td>ГОСТ 7473</td><td>Смеси бетонные</td></tr>
    </tbody>
  </table>
</section>

<section class="constructly-content-block constructly-content-block--muted">
  <h2>Другие калькуляторы</h2>
  <ul>
    <li><a href="/kalkulyator-kirpicha">Калькулятор кирпича</a></li>
    <li><a href="/kalkulyator-styazhki">Калькулятор стяжки</a></li>
    <li><a href="/kalkulyator-gipsokartona">Калькулятор гипсокартона</a></li>
    <li><a href="/kalkulyator-plitki">Калькулятор плитки</a></li>
  </ul>
</section>

<section class="constructly-disclaimer">
  Расчет ориентировочный. Выполнен на основании действующих СП и ГОСТ.
</section>
```

---

## 2) Калькулятор кирпича

### H1
`Калькулятор кирпича онлайн (кирпичи и раствор)`

### Shortcode (сразу после H1)
```text
[constructly_brick_estimator]
```

### Блоки страницы
```html
<section class="constructly-content-block">
  <h2>Как производится расчет</h2>
  <ol>
    <li>Выберите режим расчета.</li>
    <li>Выберите тип расчета: bricks или mortar.</li>
    <li>Введите площадь кладки в м².</li>
    <li>Получите ориентировочный расчет материалов.</li>
  </ol>
</section>

<section class="constructly-content-block">
  <h2>FAQ</h2>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">Что считает режим bricks?</p>
    <p class="constructly-faq-item__answer">Ориентировочное количество кирпичей по площади.</p>
  </div>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">Что считает режим mortar?</p>
    <p class="constructly-faq-item__answer">Ориентировочный объем раствора.</p>
  </div>
</section>

<section class="constructly-content-block">
  <h2>Таблица норм (СП/ГОСТ)</h2>
  <table class="constructly-norms-table">
    <thead>
      <tr><th>Документ</th><th>Что регулирует</th></tr>
    </thead>
    <tbody>
      <tr><td>СП 15.13330</td><td>Каменные и армокаменные конструкции</td></tr>
      <tr><td>СП 70.13330</td><td>Несущие и ограждающие конструкции</td></tr>
      <tr><td>ГОСТ 530</td><td>Кирпич и камень керамические</td></tr>
    </tbody>
  </table>
</section>

<section class="constructly-content-block constructly-content-block--muted">
  <h2>Другие калькуляторы</h2>
  <ul>
    <li><a href="/kalkulyator-betona">Калькулятор бетона</a></li>
    <li><a href="/kalkulyator-styazhki">Калькулятор стяжки</a></li>
    <li><a href="/kalkulyator-gipsokartona">Калькулятор гипсокартона</a></li>
    <li><a href="/kalkulyator-plitki">Калькулятор плитки</a></li>
  </ul>
</section>

<section class="constructly-disclaimer">
  Расчет ориентировочный. Выполнен на основании действующих СП и ГОСТ.
</section>
```

---

## 3) Калькулятор стяжки

### H1
`Калькулятор стяжки пола онлайн`

### Shortcode (сразу после H1)
```text
[constructly_screed_estimator]
```

### Блоки страницы
```html
<section class="constructly-content-block">
  <h2>Как производится расчет</h2>
  <ol>
    <li>Выберите режим расчета.</li>
    <li>Введите площадь в м².</li>
    <li>Введите толщину в метрах (например, 0.05 = 5 см).</li>
    <li>Нажмите «Рассчитать» и получите результат.</li>
  </ol>
</section>

<section class="constructly-content-block">
  <h2>FAQ</h2>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">В каких единицах вводится толщина?</p>
    <p class="constructly-faq-item__answer">Толщина вводится в метрах.</p>
  </div>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">Можно ли вводить сантиметры?</p>
    <p class="constructly-faq-item__answer">Сначала переведите в метры: 10 см = 0.1 м.</p>
  </div>
</section>

<section class="constructly-content-block">
  <h2>Таблица норм (СП/ГОСТ)</h2>
  <table class="constructly-norms-table">
    <thead>
      <tr><th>Документ</th><th>Что регулирует</th></tr>
    </thead>
    <tbody>
      <tr><td>СП 29.13330</td><td>Полы</td></tr>
      <tr><td>СП 71.13330</td><td>Изоляционные и отделочные покрытия</td></tr>
      <tr><td>ГОСТ 31358</td><td>Сухие строительные смеси</td></tr>
    </tbody>
  </table>
</section>

<section class="constructly-content-block constructly-content-block--muted">
  <h2>Другие калькуляторы</h2>
  <ul>
    <li><a href="/kalkulyator-betona">Калькулятор бетона</a></li>
    <li><a href="/kalkulyator-kirpicha">Калькулятор кирпича</a></li>
    <li><a href="/kalkulyator-gipsokartona">Калькулятор гипсокартона</a></li>
    <li><a href="/kalkulyator-plitki">Калькулятор плитки</a></li>
  </ul>
</section>

<section class="constructly-disclaimer">
  Расчет ориентировочный. Выполнен на основании действующих СП и ГОСТ.
</section>
```

---

## 4) Калькулятор гипсокартона

### H1
`Калькулятор гипсокартона онлайн`

### Shortcode (сразу после H1)
```text
[constructly_drywall_estimator]
```

### Блоки страницы
```html
<section class="constructly-content-block">
  <h2>Как производится расчет</h2>
  <ol>
    <li>Выберите режим расчета.</li>
    <li>Введите площадь обшивки в м².</li>
    <li>Нажмите «Рассчитать» для получения ориентировочного результата.</li>
  </ol>
</section>

<section class="constructly-content-block">
  <h2>FAQ</h2>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">Что включать в площадь?</p>
    <p class="constructly-faq-item__answer">Суммарную площадь поверхностей под обшивку.</p>
  </div>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">Нужно ли вычитать окна и двери?</p>
    <p class="constructly-faq-item__answer">Для грубой оценки можно не вычитать, для точности лучше вычесть.</p>
  </div>
</section>

<section class="constructly-content-block">
  <h2>Таблица норм (СП/ГОСТ)</h2>
  <table class="constructly-norms-table">
    <thead>
      <tr><th>Документ</th><th>Что регулирует</th></tr>
    </thead>
    <tbody>
      <tr><td>СП 163.1325800</td><td>Конструкции с ГКЛ</td></tr>
      <tr><td>СП 71.13330</td><td>Отделочные покрытия</td></tr>
      <tr><td>ГОСТ 32614</td><td>Листы гипсокартонные</td></tr>
    </tbody>
  </table>
</section>

<section class="constructly-content-block constructly-content-block--muted">
  <h2>Другие калькуляторы</h2>
  <ul>
    <li><a href="/kalkulyator-betona">Калькулятор бетона</a></li>
    <li><a href="/kalkulyator-kirpicha">Калькулятор кирпича</a></li>
    <li><a href="/kalkulyator-styazhki">Калькулятор стяжки</a></li>
    <li><a href="/kalkulyator-plitki">Калькулятор плитки</a></li>
  </ul>
</section>

<section class="constructly-disclaimer">
  Расчет ориентировочный. Выполнен на основании действующих СП и ГОСТ.
</section>
```

---

## 5) Калькулятор плитки

### H1
`Калькулятор плитки онлайн`

### Shortcode (сразу после H1)
```text
[constructly_tile_estimator]
```

### Блоки страницы
```html
<section class="constructly-content-block">
  <h2>Как производится расчет</h2>
  <ol>
    <li>Выберите режим расчета.</li>
    <li>Введите площадь укладки в м².</li>
    <li>Укажите длину и ширину плитки в сантиметрах.</li>
    <li>Нажмите «Рассчитать» и получите ориентировочный результат.</li>
  </ol>
</section>

<section class="constructly-content-block">
  <h2>FAQ</h2>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">В каких единицах вводить размер плитки?</p>
    <p class="constructly-faq-item__answer">Размер плитки вводится в сантиметрах.</p>
  </div>
  <div class="constructly-faq-item">
    <p class="constructly-faq-item__question">Нужно ли учитывать запас на подрезку?</p>
    <p class="constructly-faq-item__answer">Да, для этого удобнее использовать режим «с запасом».</p>
  </div>
</section>

<section class="constructly-content-block">
  <h2>Таблица норм (СП/ГОСТ)</h2>
  <table class="constructly-norms-table">
    <thead>
      <tr><th>Документ</th><th>Что регулирует</th></tr>
    </thead>
    <tbody>
      <tr><td>СП 29.13330</td><td>Полы</td></tr>
      <tr><td>СП 71.13330</td><td>Отделочные покрытия</td></tr>
      <tr><td>ГОСТ 13996</td><td>Плитки керамические</td></tr>
    </tbody>
  </table>
</section>

<section class="constructly-content-block constructly-content-block--muted">
  <h2>Другие калькуляторы</h2>
  <ul>
    <li><a href="/kalkulyator-betona">Калькулятор бетона</a></li>
    <li><a href="/kalkulyator-kirpicha">Калькулятор кирпича</a></li>
    <li><a href="/kalkulyator-styazhki">Калькулятор стяжки</a></li>
    <li><a href="/kalkulyator-gipsokartona">Калькулятор гипсокартона</a></li>
  </ul>
</section>

<section class="constructly-disclaimer">
  Расчет ориентировочный. Выполнен на основании действующих СП и ГОСТ.
</section>
```
