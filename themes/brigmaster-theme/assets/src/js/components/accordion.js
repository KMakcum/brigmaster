import { registerComponent } from '../core/bootstrap.js';

function initAccordion(root) {
  root.addEventListener('click', (event) => {
    const trigger = event.target.closest('.bm-accordion__trigger');
    if (!trigger) return;

    const item = trigger.closest('.bm-accordion__item');
    if (!item) return;

    const willOpen = !item.classList.contains('is-open');

    root.querySelectorAll('.bm-accordion__item.is-open').forEach((openItem) => {
      if (openItem !== item) {
        openItem.classList.remove('is-open');
        openItem.querySelector('.bm-accordion__trigger')?.setAttribute('aria-expanded', 'false');
      }
    });

    item.classList.toggle('is-open', willOpen);
    trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
  });
}

registerComponent('accordion', initAccordion);
