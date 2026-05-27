# brigmaster-theme — assets-v2

Новая дизайн-система и фронтенд темы Brigmaster. Параллельная папка к старой `assets/`, после полной миграции старая удаляется, эта переименовывается в `assets/`. См. `docs/design/AGENT_HANDOFF.md` и `docs/design/NEW_DESIGN_SYSTEM.md`.

## Стек

- Vite 6 (multi-page build)
- SCSS (Dart Sass через sass-embedded)
- vanilla ES modules
- Stylelint + ESLint + Prettier

## Команды

```bash
npm install        # один раз
npm run dev        # dev-сервер с HMR (http://localhost:5173)
npm run build      # production build в ./dist
npm run preview    # отдать ./dist локально
npm run sprite     # пересобрать SVG-спрайт из src/icons/sprite/*
npm run lint       # stylelint + eslint
npm run format     # prettier --write
```

`npm run build` автоматически вызывает `npm run sprite` перед сборкой.

## Как правильно открывать собранный HTML

Не открывай `dist/.../index.html` двойным кликом (протокол `file://`), иначе модульные `js/css` ассеты блокируются браузером по CORS/MIME, и стили «пропадают».

Используй один из двух вариантов:

1. Через Vite preview:
   - `npm run preview`
   - открыть URL из терминала (обычно `http://localhost:4173/pages/ui-kit/`)
2. Через Local WP (HTTP, не file://):
   - `http://constructly.local/wp-content/themes/brigmaster-theme/assets-v2/dist/pages/ui-kit/index.html`

## Структура

```
src/
  main.scss              общие токены + base + utilities (импортится из common)
  common.scss            точка сборки общих стилей (импортит main.scss + global components)
  common.js              bootstrap инициализации компонентов на каждой странице
  styles/
    tokens/              дизайн-токены (colors, spacing, typography, radii, ...)
    abstracts/           SCSS-only утилиты (mixins, functions, media)
    base/                reset, root, fonts, typography, utilities
    layout/              header, footer, container, section, grid
    components/          UI-компоненты (button, input, card, ...)
    pages/               специфичные стили страниц
  pages/
    <name>/              одна папка = одна точка входа Vite
      index.html
      <name>.js          импортит common + page-specific
      <name>.scss
  js/
    core/                базовая инфраструктура (bootstrap, dom helpers)
    components/          поведенческие компоненты (dropdown, accordion, ...)
  icons/
    sprite/              отдельные SVG-иконки, собираются в _sprite.svg
    _sprite.svg          собранный спрайт (генерируется, в git не коммитим)
  images/
    illustrations/       SVG/PNG иллюстрации
  fonts/
    inter/               Inter Regular/Medium/SemiBold/Bold WOFF2
scripts/
  build-sprite.mjs       сборка SVG-спрайта
```

## Multi-page build

Каждая папка в `src/pages/` с `index.html` автоматически становится точкой входа. Vite кладёт результат в `dist/<name>.html` + `dist/assets/<chunk>-[hash].{js,css}` + `dist/.vite/manifest.json` для интеграции в WordPress.

Общий код (`src/common.*`, `src/styles/*`, `src/js/core/*`, `src/js/components/*`) выделяется в отдельный `common` chunk и подключается ко всем страницам.

## Шрифты

Положить файлы Inter в `src/fonts/inter/`:

- `Inter-Regular.woff2`
- `Inter-Medium.woff2`
- `Inter-SemiBold.woff2`
- `Inter-Bold.woff2`

Источник: https://rsms.me/inter/ → Download → внутри архива папка `Inter Web/` содержит нужные файлы.

`.woff` (legacy fallback) не нужен — все целевые браузеры поддерживают WOFF2.

## Подключение к теме

Пока эта папка не подключена в `functions.php`. Это произойдёт на финальном этапе миграции (см. `docs/design/AGENT_HANDOFF.md` этап 9).
