# Page Templates For Editors

Готовые шаблоны для редактора WordPress.

## Shortcodes

Калькуляторы: задайте непустой `title`, иначе заголовок над формой не выводится.

- `[brigmaster_concrete_estimator title="Калькулятор плитного фундамента"]`
- `[brigmaster_strip_foundation_estimator title="Калькулятор ленточного фундамента"]`
- `[brigmaster_pile_foundation_estimator title="Калькулятор свайного фундамента"]`
- `[brigmaster_brick_estimator title="Калькулятор кирпича"]`
- `[brigmaster_screed_estimator title="Калькулятор стяжки"]`
- `[brigmaster_drywall_estimator title="Калькулятор гипсокартона"]`
- `[brigmaster_tile_estimator title="Калькулятор плитки"]`

Хаб «Калькулятор фундамента»: в редакторе темы Constructly используйте блок **Constructly Foundation Hub** (`constructly/foundation-hub`). Шорткод `[brigmaster_foundation_hub]` при активной дочерней теме отдаёт ту же разметку через тему.

## Example Layout Blocks

Визуальные стили для этих классов подключает дочерняя тема Constructly: `assets/frontend/css/integrations/constructly-core-hub.css` (контентные блоки, FAQ, таблицы) и `constructly-core-estimator.css` (форма калькулятора).

- `brigmaster-content-block`
- `brigmaster-content-block--muted`
- `brigmaster-faq-item`
- `brigmaster-faq-item__question`
- `brigmaster-faq-item__answer`
- `brigmaster-norms-table`
- `brigmaster-disclaimer`

## Minimal Page Structure

1. H1 of calculator page.
2. Calculator shortcode directly after H1.
3. Block "How calculation works".
4. FAQ block.
5. Normative references table.
6. Links to other calculator pages.
7. Disclaimer block.
