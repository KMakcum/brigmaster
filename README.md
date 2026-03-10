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

## Как быстро запустить 5 страниц

1. Создайте 5 отдельных страниц под калькуляторы.
2. Для каждой страницы поставьте свой shortcode:
   - Бетон: `[brigmaster_concrete_estimator]`
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
- Общие поля для всех: `calculator`, `mode`
- `mode`: `normative | reserve | beginner`

## Контракт полей по калькуляторам

### concrete

- `subType`:
  - необязателен, по умолчанию `slab`
  - если передан, допустим только `slab | strip`
- `subType=slab`: нужны `area`, `thickness`
- `subType=strip`: нужны `length`, `width`, `height`

### brick

- нужны `area`, `subType`
- `subType`: `bricks | mortar`
- `thickness` не нужен

### screed

- нужны `area`, `thickness`

### drywall

- нужен `area`
- `thickness` не нужен

### tile

- нужны `area`, `tileLengthCm`, `tileWidthCm`
- `thickness` не нужен

## Примеры success/error

### 1) concrete slab (success)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"concrete\",\"mode\":\"normative\",\"area\":10,\"thickness\":0.2}"
```

```json
{
  "calculator": "concrete",
  "mode": "normative",
  "calculatedVolume": 2.00,
  "calculatedMaterialAmount": 2.00
}
```

### 1) concrete (error)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"concrete\",\"mode\":\"normative\",\"subType\":\"strip\",\"length\":10,\"width\":0.5}"
```

```json
{
  "code": "validation_error",
  "message": "Validation failed.",
  "errors": {
    "height": [
      "The height field is required and must be numeric for concrete strip."
    ]
  }
}
```

### 2) brick (success)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"brick\",\"mode\":\"normative\",\"area\":10,\"subType\":\"bricks\"}"
```

```json
{
  "calculator": "brick",
  "mode": "normative",
  "calculatedVolume": 10.00,
  "calculatedMaterialAmount": 500.00
}
```

### 2) brick (error)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"brick\",\"mode\":\"normative\",\"area\":10}"
```

```json
{
  "code": "validation_error",
  "message": "Validation failed.",
  "errors": {
    "subType": [
      "The subType field is required for brick."
    ]
  }
}
```

### 3) screed (success)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"screed\",\"mode\":\"normative\",\"area\":12,\"thickness\":0.05}"
```

```json
{
  "calculator": "screed",
  "mode": "normative",
  "calculatedVolume": 0.60,
  "calculatedMaterialAmount": 0.57
}
```

### 3) screed (error)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"screed\",\"mode\":\"normative\",\"area\":12}"
```

```json
{
  "code": "validation_error",
  "message": "Validation failed.",
  "errors": {
    "thickness": [
      "The thickness field is required and must be numeric for screed."
    ]
  }
}
```

### 4) drywall (success)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"drywall\",\"mode\":\"normative\",\"area\":10}"
```

```json
{
  "calculator": "drywall",
  "mode": "normative",
  "calculatedVolume": 10.00,
  "calculatedMaterialAmount": 10.50
}
```

### 4) drywall (error)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"drywall\",\"mode\":\"normative\"}"
```

```json
{
  "code": "validation_error",
  "message": "Validation failed.",
  "errors": {
    "area": [
      "The area field is required and must be numeric for drywall."
    ]
  }
}
```

### 5) tile (success)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"tile\",\"mode\":\"normative\",\"area\":10,\"tileLengthCm\":30,\"tileWidthCm\":30}"
```

```json
{
  "calculator": "tile",
  "mode": "normative",
  "calculatedVolume": 10.00,
  "calculatedMaterialAmount": 111.11
}
```

### 5) tile (error)

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d "{\"calculator\":\"tile\",\"mode\":\"normative\",\"area\":10,\"tileLengthCm\":30}"
```

```json
{
  "code": "validation_error",
  "message": "Validation failed.",
  "errors": {
    "tileWidthCm": [
      "The tileWidthCm field is required and must be numeric for tile."
    ]
  }
}
```

## CLI примеры

```bash
php examples/estimate_cli.php
php examples/smoke_check.php
```
