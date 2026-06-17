import { registerComponent } from '../core/bootstrap.js';

function closeAll(root) {
  root.querySelectorAll('.menu-item.is-open').forEach((item) => {
    item.classList.remove('is-open');
    const toggle = item.querySelector(
      ':scope > .bm-nav-primary__item-row .bm-nav-primary__submenu-toggle',
    );
    toggle?.setAttribute('aria-expanded', 'false');
  });
}

function wrapSubmenuPanels(navRoot) {
  navRoot.querySelectorAll('.sub-menu').forEach((subMenu) => {
    if (subMenu.querySelector(':scope > .bm-nav-primary__submenu-inner')) return;

    const inner = document.createElement('div');
    inner.className = 'bm-nav-primary__submenu-inner';

    while (subMenu.firstChild) {
      inner.appendChild(subMenu.firstChild);
    }

    subMenu.appendChild(inner);
  });
}

function enhanceSubmenuToggles(navRoot) {
  navRoot.querySelectorAll('.menu-item-has-children').forEach((item) => {
    if (item.querySelector(':scope > .bm-nav-primary__item-row')) return;

    const link = item.querySelector(':scope > a');
    if (!link) return;

    const row = document.createElement('div');
    row.className = 'bm-nav-primary__item-row';
    item.insertBefore(row, link);
    row.appendChild(link);

    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'bm-nav-primary__submenu-toggle';
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Раскрыть подменю');

    const chevron = document.createElement('span');
    chevron.className = 'bm-nav-primary__chevron';
    chevron.setAttribute('aria-hidden', 'true');
    toggle.appendChild(chevron);
    row.appendChild(toggle);
  });
}

function initPrimaryNav(navRoot) {
  wrapSubmenuPanels(navRoot);
  enhanceSubmenuToggles(navRoot);

  navRoot.addEventListener('click', (event) => {
    const toggle = event.target.closest('.bm-nav-primary__submenu-toggle');
    if (!toggle) return;

    event.preventDefault();
    event.stopPropagation();

    const item = toggle.closest('.menu-item-has-children');
    if (!item) return;

    const parentList = item.parentElement;
    if (parentList) {
      parentList.querySelectorAll(':scope > .menu-item.is-open').forEach((sibling) => {
        if (sibling !== item) {
          sibling.classList.remove('is-open');
          sibling
            .querySelector(':scope > .bm-nav-primary__item-row .bm-nav-primary__submenu-toggle')
            ?.setAttribute('aria-expanded', 'false');
        }
      });
    }

    const nextOpen = !item.classList.contains('is-open');
    item.classList.toggle('is-open', nextOpen);
    toggle.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
  });

  document.addEventListener('click', (event) => {
    if (!navRoot.contains(event.target)) closeAll(navRoot);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeAll(navRoot);
  });
}

registerComponent('nav-dropdown', initPrimaryNav);
