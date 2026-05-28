/**
 * Custom single-select for: .brigmaster-estimate-form and form.wpcf7-form.
 * Native <select> stays in DOM (FormData, checkValidity, plugins); tabindex=-1 + aria-hidden;
 * label[for] id moves to role="combobox" trigger. Sync: change + MutationObserver on select.
 */
(() => {
  const FORM_ROOT = ".brigmaster-estimate-form, form.wpcf7-form";
  const SELECTS_IN_FORMS = ".brigmaster-estimate-form select, form.wpcf7-form select";
  let generatedId = 0;

  /** Same path as accordionChevronMarkup() in bm-core (SVG in DOM). */
  const CHEVRON_SVG_HTML =
    '<svg class="bm-custom-select__chevron-svg" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" focusable="false">' +
    '<path d="M2.25 4.25L6 7.75L9.75 4.25" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>' +
    "</svg>";

  const getVisibleOptions = (el) => Array.from(el.options).filter((o) => !o.hidden);

  function nextEnabledIndex(visible, from, dir) {
    let i = from + dir;
    while (i >= 0 && i < visible.length && visible[i].disabled) {
      i += dir;
    }
    return i < 0 || i >= visible.length ? from : i;
  }

  function defaultVisibleIndex(el) {
    const visible = getVisibleOptions(el);
    if (!visible.length) {
      return 0;
    }
    let i = Math.max(0, visible.indexOf(el.selectedOptions[0]));
    if (visible[i]?.disabled) {
      const j = visible.findIndex((o) => !o.disabled);
      return j >= 0 ? j : 0;
    }
    return i;
  }

  function syncAriaFromNative(select, trigger) {
    trigger.disabled = select.disabled;
    const db = select.getAttribute("aria-describedby");
    if (db) {
      trigger.setAttribute("aria-describedby", db);
    } else {
      trigger.removeAttribute("aria-describedby");
    }
    const inv = select.getAttribute("aria-invalid");
    if (inv === "true" || inv === "false") {
      trigger.setAttribute("aria-invalid", inv);
    } else {
      trigger.removeAttribute("aria-invalid");
    }
    if (select.required) {
      trigger.setAttribute("aria-required", "true");
    } else {
      trigger.removeAttribute("aria-required");
    }
  }

  function syncValueLabel(select, valueEl) {
    const opt = select.selectedOptions[0];
    valueEl.textContent = opt ? opt.text.trim() : "";
  }

  function rebuildOptions(select, listbox, trigger) {
    listbox.replaceChildren();
    getVisibleOptions(select).forEach((opt, idx) => {
      const row = document.createElement("div");
      row.className = "bm-custom-select__option";
      row.setAttribute("role", "option");
      row.id = `${listbox.id}-opt-${idx}`;
      row.dataset.value = opt.value;
      row.textContent = opt.text.trim() || opt.label || opt.value;
      if (opt.disabled) {
        row.classList.add("bm-custom-select__option--disabled");
        row.setAttribute("aria-disabled", "true");
      }
      row.setAttribute("aria-selected", opt.selected ? "true" : "false");
      if (opt.selected) {
        row.classList.add("bm-custom-select__option--selected");
      }
      listbox.appendChild(row);
    });
    const ad = trigger.getAttribute("aria-activedescendant");
    if (ad && !listbox.querySelector(`#${CSS.escape(ad)}`)) {
      trigger.removeAttribute("aria-activedescendant");
    }
  }

  function highlightActive(listbox, trigger, activeIndex) {
    const rows = listbox.querySelectorAll(".bm-custom-select__option");
    if (activeIndex < 0 || activeIndex >= rows.length) {
      trigger.removeAttribute("aria-activedescendant");
      return;
    }
    rows.forEach((row, i) => {
      row.classList.toggle("bm-custom-select__option--active", i === activeIndex);
    });
    const activeRow = rows[activeIndex];
    if (activeRow instanceof HTMLElement && activeRow.id) {
      trigger.setAttribute("aria-activedescendant", activeRow.id);
    }
  }

  function scrollOptionIntoView(listbox, activeIndex) {
    const row = listbox.querySelectorAll(".bm-custom-select__option")[activeIndex];
    if (row instanceof HTMLElement) {
      row.scrollIntoView({ block: "nearest" });
    }
  }

  /**
   * @param {HTMLSelectElement} select
   */
  function enhanceSelect(select) {
    if (!(select instanceof HTMLSelectElement) || select.multiple || select.dataset.bmCustomSelect === "1") {
      return;
    }
    if (!select.closest(FORM_ROOT)) {
      return;
    }
    select.dataset.bmCustomSelect = "1";

    const parent = select.parentNode;
    if (!parent) {
      return;
    }

    const wrapper = document.createElement("div");
    wrapper.className = "bm-custom-select";

    const trigger = document.createElement("button");
    trigger.type = "button";
    trigger.className = "bm-custom-select__trigger";
    trigger.setAttribute("role", "combobox");
    trigger.setAttribute("aria-haspopup", "listbox");
    trigger.setAttribute("aria-expanded", "false");

    const valueEl = document.createElement("span");
    valueEl.className = "bm-custom-select__value-text";
    valueEl.setAttribute("aria-hidden", "true");

    const chevron = document.createElement("span");
    chevron.className = "bm-custom-select__chevron";
    chevron.setAttribute("aria-hidden", "true");
    chevron.innerHTML = CHEVRON_SVG_HTML;

    trigger.append(valueEl, chevron);

    const listbox = document.createElement("div");
    listbox.className = "bm-custom-select__dropdown";
    listbox.setAttribute("role", "listbox");
    listbox.hidden = true;

    const originalId = select.id.trim();
    if (originalId) {
      trigger.id = originalId;
      select.removeAttribute("id");
      select.id = `${originalId}-native`;
    } else {
      generatedId += 1;
      const gen = `bm-cs-${generatedId}`;
      trigger.id = `${gen}-trigger`;
      select.id = `${gen}-native`;
    }

    listbox.id = `${trigger.id}-listbox`;
    trigger.setAttribute("aria-controls", listbox.id);

    select.classList.add("bm-custom-select__native");
    select.tabIndex = -1;
    select.setAttribute("aria-hidden", "true");

    parent.insertBefore(wrapper, select);
    wrapper.append(trigger, listbox, select);

    syncAriaFromNative(select, trigger);
    rebuildOptions(select, listbox, trigger);
    syncValueLabel(select, valueEl);

    let activeIndex = defaultVisibleIndex(select);

    const isOpen = () => !listbox.hidden;

    function setOpen(open) {
      listbox.hidden = !open;
      trigger.setAttribute("aria-expanded", open ? "true" : "false");
      wrapper.classList.toggle("bm-custom-select--open", open);
      if (!open) {
        trigger.removeAttribute("aria-activedescendant");
        document.removeEventListener("pointerdown", onDocPointer, true);
      } else {
        document.addEventListener("pointerdown", onDocPointer, true);
      }
    }

    /** @param {PointerEvent} e */
    function onDocPointer(e) {
      const t = e.target;
      if (t instanceof Node && wrapper.contains(t)) {
        return;
      }
      setOpen(false);
    }

    function applySelection(opt, visualIndex) {
      if (opt.disabled) {
        return;
      }
      if (select.value !== opt.value) {
        select.value = opt.value;
        select.dispatchEvent(new Event("input", { bubbles: true }));
        select.dispatchEvent(new Event("change", { bubbles: true }));
      }
      activeIndex = visualIndex;
      syncValueLabel(select, valueEl);
      rebuildOptions(select, listbox, trigger);
      setOpen(false);
      trigger.focus();
    }

    /** @param {KeyboardEvent} e */
    function onTriggerKeydown(e) {
      const visible = getVisibleOptions(select);
      if (!visible.length || select.disabled) {
        return;
      }

      if (e.key === "Tab" && isOpen()) {
        setOpen(false);
        return;
      }
      if (e.key === "Escape" && isOpen()) {
        e.preventDefault();
        setOpen(false);
        return;
      }

      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        if (isOpen()) {
          const cur = visible[activeIndex];
          if (cur && !cur.disabled) {
            applySelection(cur, activeIndex);
          }
        } else {
          activeIndex = defaultVisibleIndex(select);
          setOpen(true);
          highlightActive(listbox, trigger, activeIndex);
        }
        return;
      }

      if (e.key === "ArrowDown" || e.key === "ArrowUp") {
        e.preventDefault();
        if (!isOpen()) {
          activeIndex = defaultVisibleIndex(select);
          setOpen(true);
        } else {
          activeIndex = nextEnabledIndex(visible, activeIndex, e.key === "ArrowDown" ? 1 : -1);
        }
        highlightActive(listbox, trigger, activeIndex);
        scrollOptionIntoView(listbox, activeIndex);
        return;
      }

      if (!isOpen()) {
        return;
      }
      if (e.key === "Home") {
        e.preventDefault();
        const first = visible.findIndex((o) => !o.disabled);
        activeIndex = first >= 0 ? first : 0;
      } else if (e.key === "End") {
        e.preventDefault();
        let last = visible.length - 1;
        while (last > 0 && visible[last].disabled) {
          last -= 1;
        }
        activeIndex = last;
      } else {
        return;
      }
      highlightActive(listbox, trigger, activeIndex);
      scrollOptionIntoView(listbox, activeIndex);
    }

    trigger.addEventListener("keydown", onTriggerKeydown);
    trigger.addEventListener("click", () => {
      if (select.disabled) {
        return;
      }
      if (isOpen()) {
        setOpen(false);
        return;
      }
      activeIndex = defaultVisibleIndex(select);
      setOpen(true);
      highlightActive(listbox, trigger, activeIndex);
    });

    listbox.addEventListener("pointerdown", (e) => {
      const row = e.target instanceof Element ? e.target.closest("[role='option']") : null;
      if (!(row instanceof HTMLElement) || row.dataset.value == null || row.getAttribute("aria-disabled") === "true") {
        return;
      }
      e.preventDefault();
      const vis = getVisibleOptions(select);
      const idx = vis.findIndex((o) => o.value === row.dataset.value);
      if (idx >= 0 && !vis[idx].disabled) {
        applySelection(vis[idx], idx);
      }
    });

    select.addEventListener("change", () => {
      activeIndex = defaultVisibleIndex(select);
      syncValueLabel(select, valueEl);
      rebuildOptions(select, listbox, trigger);
      syncAriaFromNative(select, trigger);
    });

    const mo = new MutationObserver(() => {
      syncAriaFromNative(select, trigger);
      rebuildOptions(select, listbox, trigger);
      syncValueLabel(select, valueEl);
      const len = getVisibleOptions(select).length;
      if (len > 0 && activeIndex >= len) {
        activeIndex = len - 1;
      }
      if (isOpen()) {
        highlightActive(listbox, trigger, activeIndex);
      }
    });
    mo.observe(select, {
      attributes: true,
      attributeFilter: ["disabled", "required", "aria-invalid", "aria-describedby", "class"],
      childList: true,
      subtree: true,
    });
  }

  function initAll() {
    document.querySelectorAll(SELECTS_IN_FORMS).forEach((el) => {
      if (el instanceof HTMLSelectElement) {
        enhanceSelect(el);
      }
    });
  }

  function scheduleInit() {
    window.setTimeout(initAll, 0);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", scheduleInit);
  } else {
    scheduleInit();
  }

  document.addEventListener("wpcf7submit", scheduleInit, false);
})();
