function decodeFaqMarkup(value) {
  if (!value) {
    return value;
  }

  const normalized = String(value)
    .replace(/\\?u003c/gi, '<')
    .replace(/\\?u003e/gi, '>')
    .replace(/\\?u0026/gi, '&')
    .replace(/\\?u0022/gi, '"')
    .replace(/\\?u0027/gi, "'");

  const textarea = document.createElement('textarea');
  textarea.innerHTML = normalized;

  return textarea.value;
}

export function initRankMathFaq() {
  const faqRoots = document.querySelectorAll(
    '.bm-rank-math-faq, .rank-math-block, .wp-block-rank-math-faq-block',
  );

  faqRoots.forEach((root, rootIndex) => {
    if (!(root instanceof HTMLElement) || root.dataset.bmRankMathFaq === '1') {
      return;
    }

    if (!root.querySelector('.rank-math-question')) {
      return;
    }

    root.dataset.bmRankMathFaq = '1';
    root.classList.add('bm-rank-math-faq');

    const items = root.querySelectorAll('.rank-math-list-item, .rank-math-faq-item');

    items.forEach((item, index) => {
      const question = item.querySelector('.rank-math-question');
      const answer = item.querySelector('.rank-math-answer');

      if (!(item instanceof HTMLElement) || !(question instanceof HTMLElement) || !(answer instanceof HTMLElement)) {
        return;
      }

      question.innerHTML = decodeFaqMarkup(question.innerHTML);
      answer.innerHTML = decodeFaqMarkup(answer.innerHTML);

      const answerId = answer.id || `bm-faq-answer-${rootIndex}-${index}`;

      answer.id = answerId;
      question.setAttribute('tabindex', '0');
      question.setAttribute('role', 'button');
      question.setAttribute('aria-controls', answerId);
      question.setAttribute('aria-expanded', index === 0 ? 'true' : 'false');

      if (index === 0) {
        item.classList.add('is-open');
        answer.hidden = false;
      } else {
        answer.hidden = true;
      }

      const toggle = () => {
        const isOpen = item.classList.contains('is-open');

        item.classList.toggle('is-open', !isOpen);
        question.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        answer.hidden = isOpen;
      };

      question.addEventListener('click', toggle);
      question.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          toggle();
        }
      });
    });
  });
}
