# Сборка фронтенда темы Brigmaster

Каталог `assets/` — это **Vite-проект**: исходники в `src/`, готовые файлы попадают в `dist/`. Тема WordPress подключает бандлы по манифесту `dist/.vite/manifest.json` (см. `inc/class-constructly-assets.php`: хэндлы стилей/скриптов с префиксом `bm-`, например `bm-theme`).

## Требования

- Node.js **18+** (рекомендуется LTS)
- npm

## Установка зависимостей

```bash
cd wp-content/themes/brigmaster-theme/assets
npm install
```

## Скрипты

| Команда        | Назначение |
|----------------|------------|
| `npm run build` | Однократная production-сборка в `dist/` |
| `npm run dev`   | Сборка в watch-режиме (`vite build --watch`) — удобно при правках CSS/JS |

После изменений в `src/` на проде или перед коммитом обычно выполняют **`npm run build`**, чтобы обновились хешированные файлы в `dist/` и манифест.

## Точки входа (Vite)

Задаются в `vite.config.mjs`:

- `src/main.js` — основной фронт темы (стили страницы, общий JS)
- `src/editor.js` — стили/скрипты для редактора блоков
- `src/js/bm-custom-select.js` — виджет кастомного селекта
- `src/js/rank-math-faq.js` — оформление FAQ Rank Math

## Структура `src/` (ориентир)

- `src/css/` — модульные стили (импортируются из `main.js` / `editor.js`)
- `src/js/` — отдельные скрипты, подключаемые как отдельные entry

## Результат сборки

- **`dist/assets/`** — JS/CSS с хешами в именах
- **`dist/.vite/manifest.json`** — карта входов → файлов; без актуального манифеста PHP не сможет корректно подключить стили и скрипты

При первом развёртывании темы без готового `dist/` выполните `npm install` и `npm run build` в этом каталоге.
