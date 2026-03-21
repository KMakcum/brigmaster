# Brigmaster Core

## Что это

Backend-плагин для расчета строительных материалов.

Архитектура:
- `Http -> Application -> Domain`
- Domain изолирован от WordPress API

## Требования

- PHP 8.2+
- WordPress
- Composer

## Установка

```bash
composer install
```

Далее активируйте плагин `brigmaster-core` в админке WordPress.

## Как быстро запустить страницы калькуляторов

1. Создайте отдельные страницы под калькуляторы.
2. Для каждой страницы поставьте свой shortcode:
   - Бетон: `[brigmaster_concrete_estimator]`
   - Ленточный фундамент: `[brigmaster_strip_foundation_estimator]`
   - Свайный фундамент: [brigmaster_pile_foundation_estimator]
   - Кирпич: `[brigmaster_brick_estimator]`
   - Стяжка: `[brigmaster_screed_estimator]`
   - Гипсокартон: `[brigmaster_drywall_estimator]`
   - Плитка: `[brigmaster_tile_estimator]`
3. Используйте готовые контентные шаблоны из `docs/page-templates.md`.
4. Добавьте внутренние ссылки между страницами калькуляторов.
5. Используйте только `brigmaster_*` shortcode для контента.

Обязательные страницы сайта (минимум):

- Главная
- Калькулятор бетона
- Калькулятор кирпича
- Калькулятор стяжки
- Калькулятор гипсокартона
- Калькулятор плитки
- О проекте
- Политика
- Соглашение
- Контакты

## Релизный минимум перед публикацией

- Контентные шаблоны страниц: `docs/page-templates.md`
- Чеклист публикации: `docs/publish-checklist.md`
- SEO-шаблоны мета-данных: `docs/seo-meta-templates.md`

## REST endpoint

- Method: `POST`
- URL: `/wp-json/brigmaster/v1/estimate`

## Clean контракт: slab_foundation

- `calculator`: только `slab_foundation`
- `mode`: `dimensions | area`
- `subType` для плитного фундамента не используется
- `height` обязателен всегда
- геометрия:
  - `mode=dimensions`: обязательны `length`, `width`, `height`
  - `mode=area`: обязательны `area`, `height`
- включаемые секции:
  - `includeReinforcement` (strict bool)
  - `includeFormwork` (strict bool)
- если `includeReinforcement=true`, доступны параметры (все `>0`):
  - `rebarDiameterMm` (default `12`)
  - `rebarStepMm` (default `200`)
  - `rebarLayers` (default `2`, допустимо `1|2`)
  - `rebarReservePercent` (default `10`)
- если `includeFormwork=true`, доступны параметры (все `>0`):
  - `formworkHeightM` (default `0.30`)
  - `formworkReservePercent` (default `10`)
- при `mode=area` и включенных `includeReinforcement/includeFormwork` обязательны `length` и `width`
- скрытые предположения по геометрии не делаются

## Пример запроса

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d '{
    "calculator":"slab_foundation",
    "mode":"dimensions",
    "length":10,
    "width":8,
    "height":0.25,
    "includeReinforcement":true,
    "includeFormwork":true
  }'
```

## Пример успешного ответа

```json
{
  "calculator": "slab_foundation",
  "mode": "dimensions",
  "concrete": {
    "areaM2": 80,
    "heightM": 0.25,
    "volumeM3": 20
  },
  "reinforcement": {
    "diameterMm": 12,
    "stepMm": 200,
    "layers": 2,
    "reservePercent": 10,
    "barsAlongLength": 41,
    "barsAlongWidth": 51,
    "totalLengthM": 1636,
    "totalLengthWithReserveM": 1799.6,
    "unitWeightKgPerM": 0.89,
    "massKg": 1599.64
  },
  "formwork": {
    "heightM": 0.3,
    "reservePercent": 10,
    "perimeterM": 36,
    "areaM2": 11.88,
    "linearMeters": 39.6
  }
}
```

## Пример ошибки валидации

```json
{
  "code": "validation_error",
  "message": "Validation failed.",
  "errors": {
    "length": [
      "The length field is required and must be greater than 0 when includeReinforcement/includeFormwork is true and mode is area."
    ],
    "width": [
      "The width field is required and must be greater than 0 when includeReinforcement/includeFormwork is true and mode is area."
    ]
  }
}
```

## Strip Foundation контракт

- `calculator`: `strip_foundation`
- `mode`: `perimeter | house | segments`

### Режимы геометрии

- `mode=perimeter`: `totalLengthM`, `widthM`, `heightM`
- `mode=house`: `houseLengthM`, `houseWidthM`, `widthM`, `heightM`
- `mode=segments`: `segments[]` (минимум 1), в каждом:
  - `segmentLengthM`, `segmentWidthM`, `segmentHeightM`

### Арматура

- `includeReinforcement` (strict bool)
- глобальные поля:
  - `longitudinalBarsCount`
  - `longitudinalDiameterMm`
  - `longitudinalReservePercent`
  - `transverseDiameterMm`
  - `transverseStepMm`
  - `transverseReservePercent`
- для `mode=segments` в каждом сегменте:
  - `segmentIncludeReinforcement` (bool)
  - `segmentUseGlobalRebarParams` (bool, default `true`)
  - если `segmentUseGlobalRebarParams=false`:
    - `segmentLongitudinalBarsCount`
    - `segmentLongitudinalDiameterMm`
    - `segmentTransverseDiameterMm`
    - `segmentTransverseStepMm`
- проценты запаса всегда глобальные

### Опалубка

- `includeFormwork` (strict bool)
- глобальные поля:
  - `formworkHeightM`
  - `formworkReservePercent`
- для `mode=segments` в каждом сегменте:
  - `segmentIncludeFormwork` (bool)
  - `segmentUseGlobalFormworkParams` (bool, default `true`)
  - если `segmentUseGlobalFormworkParams=false`:
    - `segmentFormworkHeightM`

### Ответ strip_foundation

- `calculator`, `mode`
- `concrete`:
  - `totalLengthM`
  - `volumeM3`
- `reinforcement` (только если `includeReinforcement=true`)
- `formwork` (только если `includeFormwork=true`)

## CLI примеры

```bash
php examples/estimate_cli.php
php examples/smoke_check.php
```
