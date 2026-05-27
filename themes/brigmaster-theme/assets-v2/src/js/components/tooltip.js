const VIEWPORT_GAP = 16;

const bubble = document.createElement('div');
bubble.className = 'bm-tooltip-bubble';
bubble.hidden = true;
document.body.appendChild(bubble);

let activeTrigger = null;

function clamp(value, min, max) {
  return Math.min(Math.max(value, min), max);
}

function positionBubble(trigger) {
  const rect = trigger.getBoundingClientRect();
  const bubbleRect = bubble.getBoundingClientRect();
  const maxLeft = window.innerWidth - bubbleRect.width - VIEWPORT_GAP;
  const centeredLeft = rect.left + rect.width / 2 - bubbleRect.width / 2;
  const left = clamp(centeredLeft, VIEWPORT_GAP, Math.max(VIEWPORT_GAP, maxLeft));
  const topCandidate = rect.top - bubbleRect.height - 8;
  const top = topCandidate >= VIEWPORT_GAP ? topCandidate : rect.bottom + 8;

  bubble.style.left = `${left}px`;
  bubble.style.top = `${top}px`;
}

function showTooltip(trigger) {
  const text = trigger.getAttribute('data-tooltip');

  if (!text) {
    return;
  }

  activeTrigger = trigger;
  bubble.textContent = text;
  bubble.hidden = false;
  positionBubble(trigger);
}

function hideTooltip(trigger) {
  if (activeTrigger !== trigger) {
    return;
  }

  activeTrigger = null;
  bubble.hidden = true;
}

document.addEventListener('pointerover', (event) => {
  const trigger = event.target instanceof Element ? event.target.closest('.bm-tooltip') : null;

  if (trigger instanceof HTMLElement) {
    showTooltip(trigger);
  }
});

document.addEventListener('pointerout', (event) => {
  const trigger = event.target instanceof Element ? event.target.closest('.bm-tooltip') : null;

  if (trigger instanceof HTMLElement) {
    hideTooltip(trigger);
  }
});

document.addEventListener('focusin', (event) => {
  if (event.target instanceof HTMLElement && event.target.classList.contains('bm-tooltip')) {
    showTooltip(event.target);
  }
});

document.addEventListener('focusout', (event) => {
  if (event.target instanceof HTMLElement && event.target.classList.contains('bm-tooltip')) {
    hideTooltip(event.target);
  }
});

window.addEventListener('resize', () => {
  if (activeTrigger) {
    positionBubble(activeTrigger);
  }
});

window.addEventListener('scroll', () => {
  if (activeTrigger) {
    positionBubble(activeTrigger);
  }
}, true);
