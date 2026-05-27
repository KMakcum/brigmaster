import { registerComponent } from '../core/bootstrap.js';

function slugify(text) {
  return text
    .trim()
    .toLowerCase()
    .replace(/[^\p{L}\p{N}\s-]/gu, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
}

function ensureHeadingIds(prose) {
  const used = new Set();
  prose.querySelectorAll('h2, h3').forEach((heading) => {
    if (heading.id) {
      used.add(heading.id);
      return;
    }
    let base = slugify(heading.textContent || 'section');
    if (!base) base = 'section';
    let id = base;
    let n = 2;
    while (used.has(id)) {
      id = `${base}-${n}`;
      n += 1;
    }
    heading.id = id;
    used.add(id);
  });
}

function getProseRoot() {
  return document.querySelector('.bm-content-layout__main.bm-prose');
}

function getAnchorScrollOffset() {
  const header = document.querySelector('.bm-header');
  const headerHeight = header?.getBoundingClientRect().height ?? 0;
  const rootStyles = getComputedStyle(document.documentElement);
  const gap = parseFloat(rootStyles.getPropertyValue('--bm-space-4')) || 16;
  return headerHeight + gap;
}

function scrollToAnchor(target) {
  const offset = getAnchorScrollOffset();
  const top = Math.max(0, window.scrollY + target.getBoundingClientRect().top - offset);
  window.scrollTo({ top, behavior: 'smooth' });
}

function bindTocLink(link) {
  if (link.dataset.bmTocBound === '1') return;
  link.dataset.bmTocBound = '1';

  link.addEventListener('click', (e) => {
    const href = link.getAttribute('href');
    if (!href?.startsWith('#')) return;
    const target = document.getElementById(href.slice(1));
    if (!target) return;
    e.preventDefault();
    scrollToAnchor(target);
    history.replaceState(null, '', href);
  });
}

function initTocRoot(root) {
  const prose = getProseRoot();
  if (prose) ensureHeadingIds(prose);

  if (root.classList.contains('bm-toc--collapsible')) {
    const toggle = root.querySelector('.bm-toc__toggle');
    if (toggle && !toggle.dataset.bmTocBound) {
      toggle.dataset.bmTocBound = '1';
      toggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const open = root.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }
  }

  root.querySelectorAll('.bm-toc__link').forEach((link) => {
    bindTocLink(link, root);
  });
}

registerComponent('toc', initTocRoot);
