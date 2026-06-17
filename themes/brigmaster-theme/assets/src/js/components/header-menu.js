import { registerComponent } from '../core/bootstrap.js';

const DESKTOP_MQ = '(min-width: 1024px)';

function closeSubmenus(navRoot) {
  navRoot
    ?.querySelectorAll('.menu-item.is-open')
    .forEach((item) => item.classList.remove('is-open'));
}

function initHeaderMenu(header) {
  const toggle = header.querySelector('.bm-header__toggle');
  const panel = header.querySelector('.bm-header__panel');
  const overlay = header.querySelector('.bm-header__overlay');
  const navRoot = header.querySelector('.bm-nav-primary');

  if (!toggle || !panel) return;

  const setOpen = (open) => {
    header.classList.toggle('is-menu-open', open);
    if (open) {
      header.classList.remove('is-scroll-hidden');
    }
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.setAttribute('aria-label', open ? 'Закрыть меню' : 'Открыть меню');
    document.body.classList.toggle('bm-scroll-lock', open);

    if (overlay) {
      overlay.classList.toggle('is-visible', open);
      overlay.setAttribute('aria-hidden', open ? 'false' : 'true');
    }

    if (!open) {
      closeSubmenus(navRoot);
    }
  };

  const close = () => setOpen(false);

  toggle.addEventListener('click', () => {
    setOpen(!header.classList.contains('is-menu-open'));
  });

  overlay?.addEventListener('click', close);

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && header.classList.contains('is-menu-open')) {
      close();
    }
  });

  window.matchMedia(DESKTOP_MQ).addEventListener('change', (event) => {
    if (event.matches) close();
  });

  panel.querySelectorAll('a[href]').forEach((link) => {
    link.addEventListener('click', () => {
      if (!window.matchMedia(DESKTOP_MQ).matches) close();
    });
  });

  let lastScrollY = window.scrollY;
  let scrollTicking = false;

  const halfViewport = () => window.innerHeight * 0.5;

  const updateHeaderOnScroll = () => {
    const scrollY = window.scrollY;

    if (header.classList.contains('is-menu-open')) {
      header.classList.remove('is-scroll-hidden');
    } else if (scrollY < halfViewport()) {
      header.classList.remove('is-scroll-hidden');
    } else if (scrollY < lastScrollY) {
      header.classList.remove('is-scroll-hidden');
    } else if (scrollY > lastScrollY) {
      header.classList.add('is-scroll-hidden');
    }

    lastScrollY = scrollY;
    scrollTicking = false;
  };

  const onScroll = () => {
    if (scrollTicking) return;
    scrollTicking = true;
    requestAnimationFrame(updateHeaderOnScroll);
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });
  updateHeaderOnScroll();
}

registerComponent('header-menu', initHeaderMenu);
