# HTML partials

## `card-article.html`

Canonical markup for `bm-card-article` (Figma Article Card). Placeholders: `{{href}}`, `{{image}}`, `{{imageAlt}}`, `{{tag}}`, `{{title}}`, `{{excerpt}}`, `{{readTime}}`, `{{date}}`.

## Data + include

Page HTML uses `<!-- @cards <dataset> -->` (see `vite.config.mjs`). Dataset JSON lives in `partials/data/<dataset>.json`.

Example (home): `<!-- @cards home-articles -->` in `pages/home/index.html`.

WP migration: copy structure into `template-parts/card-article.php` and pass the same fields from the loop.

## FAQ section

- `faq-section.html` — section shell (toolbar + grid wrapper).
- `faq-item.html` — one accordion row (`{{id}}`, `{{question}}`, `{{answer}}`).
- `partials/data/<name>.json` — section meta + `items[]`.
- Page marker: `<!-- @faq-section home-faq -->`.

WP migration: `template-parts/faq-section.php` + `template-parts/faq-item.php`.

## CTA block

- `cta-block.html` — `bm-cta-block` + optional `<img>` (absolute, full area). Placeholders: `{{modifierClass}}`, `{{imageTag}}` (full `<img …>` or empty), `{{title}}`, `{{lead}}`, `{{buttonHref}}`, `{{buttonClass}}`, `{{buttonText}}`.
