import iconSprite from '../../icons/_sprite.svg?raw';

const SPRITE_ID = 'bm-icon-sprite';

export function injectIconSprite() {
  if (document.getElementById(SPRITE_ID)) {
    return;
  }

  const tpl = document.createElement('template');
  tpl.innerHTML = iconSprite.trim();
  const svg = tpl.content.querySelector('svg');

  if (!svg) {
    return;
  }

  svg.id = SPRITE_ID;
  svg.setAttribute('aria-hidden', 'true');
  svg.style.display = 'none';
  document.body.insertBefore(svg, document.body.firstChild);
}
