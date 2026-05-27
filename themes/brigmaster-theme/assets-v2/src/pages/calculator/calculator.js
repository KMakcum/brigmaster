import '../../common.js';
import './calculator.scss';

const resultPanel = document.querySelector('[data-result-panel]');
const resultOpenButton = document.querySelector('[data-result-open]');
const stickyResult = document.querySelector('.bm-calculator-sticky-result');
const estimatorForm = document.querySelector('.brigmaster-estimate-form');
const schemeCanvas = document.querySelector('.bm-calculator-scheme__canvas');

const closeResultPanel = () => {
  if (!resultPanel || !resultOpenButton) {
    return;
  }

  resultPanel.classList.remove('is-open');
  document.body.classList.remove('bm-scroll-lock');
  resultOpenButton.setAttribute('aria-expanded', 'false');
};

if (resultPanel && resultOpenButton) {
  resultOpenButton.addEventListener('click', () => {
    const isOpen = resultPanel.classList.toggle('is-open');

    document.body.classList.toggle('bm-scroll-lock', isOpen);
    resultOpenButton.setAttribute('aria-expanded', String(isOpen));
  });

  resultPanel.addEventListener('click', (event) => {
    if (event.target === resultPanel) {
      closeResultPanel();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeResultPanel();
    }
  });
}

if (stickyResult && estimatorForm) {
  estimatorForm.addEventListener('submit', (event) => {
    event.preventDefault();
    stickyResult.classList.add('is-visible');
  });
}

if (schemeCanvas instanceof HTMLCanvasElement) {
  const ctx = schemeCanvas.getContext('2d');

  if (ctx) {
    ctx.clearRect(0, 0, schemeCanvas.width, schemeCanvas.height);
    ctx.strokeStyle = '#3e5f7b';
    ctx.lineWidth = 10;
    ctx.strokeRect(90, 72, 436, 267);
    ctx.lineWidth = 6;
    ctx.strokeRect(150, 122, 316, 167);
    ctx.fillStyle = '#3e5f7b';
    ctx.beginPath();
    ctx.arc(90, 72, 7, 0, Math.PI * 2);
    ctx.arc(526, 72, 7, 0, Math.PI * 2);
    ctx.arc(526, 339, 7, 0, Math.PI * 2);
    ctx.arc(90, 339, 7, 0, Math.PI * 2);
    ctx.fill();
  }
}
