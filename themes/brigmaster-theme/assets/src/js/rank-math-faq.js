document.addEventListener('DOMContentLoaded', function () {
  var decodeFaqMarkup = function (value) {
    if (!value) {
      return value;
    }

    var normalized = String(value)
      .replace(/\\?u003c/gi, '<')
      .replace(/\\?u003e/gi, '>')
      .replace(/\\?u0026/gi, '&')
      .replace(/\\?u0022/gi, '"')
      .replace(/\\?u0027/gi, "'");

    var textarea = document.createElement('textarea');
    textarea.innerHTML = normalized;

    return textarea.value;
  };

  var faqRoots = document.querySelectorAll('.bm-rank-math-faq, .rank-math-block, .wp-block-rank-math-faq-block');

  faqRoots.forEach(function (root, rootIndex) {
    if (!root.querySelector('.rank-math-question')) {
      return;
    }

    root.classList.add('bm-rank-math-faq');

    var items = root.querySelectorAll('.rank-math-list-item, .rank-math-faq-item');

    items.forEach(function (item, index) {
      var question = item.querySelector('.rank-math-question');
      var answer = item.querySelector('.rank-math-answer');

      if (!question || !answer) {
        return;
      }

      question.innerHTML = decodeFaqMarkup(question.innerHTML);
      answer.innerHTML = decodeFaqMarkup(answer.innerHTML);

      var answerId = answer.id || 'bm-faq-answer-' + rootIndex + '-' + index;

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

      var toggle = function () {
        var isOpen = item.classList.contains('is-open');

        item.classList.toggle('is-open', !isOpen);
        question.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        answer.hidden = isOpen;
      };

      question.addEventListener('click', toggle);
      question.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          toggle();
        }
      });
    });
  });
});
