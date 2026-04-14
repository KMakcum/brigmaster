(() => {
  function formatNumber(value) {
    const numericValue = Number(value);
    if (!Number.isFinite(numericValue)) {
      return "-";
    }

    const normalized = numericValue.toFixed(3).replace(/\.?0+$/, "");
    const parts = normalized.split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    return parts.join(".");
  }

  const MODE_HINTS_SLAB = {
    dimensions:
      "Ввод по длине и ширине — нужен для опций арматуры и опалубки.",
    area:
      "Ввод по площади; арматура и опалубка — только если дополнительно указать габариты (как в форме).",
  };

  const MODE_HINTS_STRIP = {
    perimeter:
      "Считаем объём по одной общей длине и размерам сечения.",
    house:
      "Сначала получаем длину по габаритам дома, затем считаем объём по сечению.",
    segments:
      "Длина складывается из участков, объём — по суммарной длине и сечению.",
  };

  const MODE_HINTS_SIMPLE = {
    normative:
      "Базовый вариант без дополнительного запаса по сравнению с другими режимами калькулятора.",
    reserve:
      "Добавляет запас на подрезку, потери и разброс при выполнении работ.",
    beginner:
      "Более «щадящий» вариант с запасом — удобно при первой самостоятельной закупке.",
  };

  function buildPrefixedFieldName(prefix, suffix) {
    if (!prefix) {
      return suffix;
    }

    return `${prefix}${suffix.charAt(0).toUpperCase()}${suffix.slice(1)}`;
  }

  function getMixtureErrorPrefix(prefix) {
    if (prefix === "pile") {
      return "pileMixture";
    }
    if (prefix === "grillage") {
      return "grillageMixture";
    }

    return "mixture";
  }

  function getEstimatorShell(form) {
    return form.closest(".brigmaster-estimator") ?? form.parentElement;
  }

  function updateValidationSummary(form) {
    const box = form.querySelector("[data-validation-summary]");
    if (!box) {
      return;
    }
    const errors = [...form.querySelectorAll("[data-field-error]")].filter(
      (n) => n.textContent && String(n.textContent).trim()
    );
    if (errors.length >= 1) {
      box.textContent = "Исправьте отмеченные поля.";
      box.hidden = false;
    } else {
      box.textContent = "";
      box.hidden = true;
    }
  }

  function markResultStale(form) {
    if (form.dataset.suspendStaleTracking === "1") {
      return;
    }

    const resultNode = getEstimatorShell(form)?.querySelector("[data-result]");
    if (
      !resultNode ||
      resultNode.hidden ||
      !resultNode.classList.contains("is-success")
    ) {
      return;
    }
    resultNode.classList.add("is-stale");
    const notice = resultNode.querySelector("[data-result-stale-notice]");
    if (notice) {
      notice.hidden = false;
    }
  }

  function finalizeSuccessfulResult(form) {
    const shell = getEstimatorShell(form);
    const resultNode = shell?.querySelector("[data-result]");
    if (resultNode) {
      resultNode.classList.remove("is-stale");
      const notice = resultNode.querySelector("[data-result-stale-notice]");
      if (notice) {
        notice.hidden = true;
      }
    }
    const el = shell?.querySelector("[data-result]");
    if (el && !el.hidden) {
      el.scrollIntoView({ behavior: "smooth", block: "start" });
      try {
        el.focus({ preventScroll: true });
      } catch (_e) {
        /* ignore */
      }
    }

    window.setTimeout(() => {
      delete form.dataset.suspendStaleTracking;
    }, 0);
  }

  function syncResultGridLayout(resultNode) {
    if (!(resultNode instanceof Element)) {
      return;
    }

    const grids = resultNode.querySelectorAll(".brigmaster-estimator__result-grid");
    grids.forEach((grid) => {
      if (!(grid instanceof HTMLElement)) {
        return;
      }

      const visibleCards = Array.from(
        grid.querySelectorAll(
          '.brigmaster-estimator__result-card:not([hidden]):not(.brigmaster-estimator__result-card--mixture)'
        )
      );

      const columns = Math.max(1, visibleCards.length);
      grid.style.setProperty("--brigmaster-result-columns", String(columns));
    });
  }

  function updateModeHintForForm(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    const mode = form.querySelector('[name="mode"]')?.value || "";
    const hintNodes = form.querySelectorAll("[data-mode-hint]");
    if (!hintNodes.length) {
      return;
    }
    let text = "";
    if (
      (calculator === "slab_foundation" || calculator === "screed") &&
      MODE_HINTS_SLAB[mode]
    ) {
      text = MODE_HINTS_SLAB[mode];
    } else if (
      (calculator === "strip_foundation" || calculator === "pile_foundation") &&
      MODE_HINTS_STRIP[mode]
    ) {
      text = MODE_HINTS_STRIP[mode];
    } else if (MODE_HINTS_SIMPLE[mode]) {
      text = MODE_HINTS_SIMPLE[mode];
    }
    hintNodes.forEach((node) => {
      node.textContent = text;
    });
  }

  function initModeScenarioUi(form) {
    const modeSelect = form.querySelector('[name="mode"]');
    if (modeSelect) {
      modeSelect.addEventListener("change", () => {
        updateModeHintForForm(form);
      });
    }
    updateModeHintForForm(form);
  }

  function initStaleOnFormChange(form) {
    if (form.dataset.staleBound === "1") {
      return;
    }
    form.dataset.staleBound = "1";
    form.addEventListener("change", () => {
      markResultStale(form);
    });
    form.addEventListener("input", (event) => {
      const target = event.target;
      if (
        target instanceof HTMLInputElement ||
        target instanceof HTMLTextAreaElement
      ) {
        markResultStale(form);
      }
    });
  }

  function clearErrors(form) {
    const errorNodes = form.querySelectorAll("[data-field-error]");
    errorNodes.forEach((node) => {
      node.textContent = "";
    });
    form.classList.remove("has-errors");
    const summary = form.querySelector("[data-validation-summary]");
    if (summary) {
      summary.textContent = "";
      summary.hidden = true;
    }
  }

  function clearResult(form) {
    const resultNode = getEstimatorShell(form)?.querySelector("[data-result]");
    if (!resultNode) {
      return;
    }

    const concreteVolumeNode = resultNode.querySelector("[data-result-concrete-volume]");
    const concreteAreaNode = resultNode.querySelector("[data-result-concrete-area]");
    const concreteHeightNode = resultNode.querySelector("[data-result-concrete-height]");
    const reinforcementCard = resultNode.querySelector('[data-result-card="reinforcement"]');
    const formworkCard = resultNode.querySelector('[data-result-card="formwork"]');
    const schemeNode = resultNode.querySelector("[data-slab-scheme]");
    const volumeNode = resultNode.querySelector("[data-result-volume]");
    const materialNode = resultNode.querySelector("[data-result-material]");
    const stripConcreteLengthNode = resultNode.querySelector("[data-result-strip-concrete-length]");
    const stripConcreteVolumeNode = resultNode.querySelector("[data-result-strip-concrete-volume]");
    const stripRebarLongitudinalNode = resultNode.querySelector("[data-result-strip-rebar-longitudinal-mass]");
    const stripRebarTransverseNode = resultNode.querySelector("[data-result-strip-rebar-transverse-mass]");
    const stripRebarTotalNode = resultNode.querySelector("[data-result-strip-rebar-total-mass]");
    const stripFormworkAreaNode = resultNode.querySelector("[data-result-strip-formwork-area]");
    const stripFormworkLinearNode = resultNode.querySelector("[data-result-strip-formwork-linear]");
    const stripConcreteCard = resultNode.querySelector('[data-result-card="strip-concrete"]');
    const stripReinforcementCard = resultNode.querySelector('[data-result-card="strip-reinforcement"]');
    const stripFormworkCard = resultNode.querySelector('[data-result-card="strip-formwork"]');
    const calculatorValue = form.querySelector('[name="calculator"]')?.value;
    const pileReinforcementCard = resultNode.querySelector('[data-result-card="pile-reinforcement"]');
    const pileConcreteCard = resultNode.querySelector('[data-result-card="pile-concrete"]');
    const pileHeaderSection = resultNode.querySelector('[data-result-section="pile-header"]');
    const grillageHeaderSection = resultNode.querySelector('[data-result-section="grillage-header"]');
    const pileTypeNode = resultNode.querySelector("[data-result-pile-type]");
    const pileCountNode = resultNode.querySelector("[data-result-pile-count]");
    const pileConcreteVolumeNode = resultNode.querySelector("[data-result-pile-concrete-volume]");
    const pileConcretePerPileNode = resultNode.querySelector("[data-result-pile-concrete-per-pile]");
    const pilePerPileRow = resultNode.querySelector("[data-result-pile-per-pile-row]");
    const pileNoteNode = resultNode.querySelector("[data-result-pile-note]");
    const pileNoteRow = resultNode.querySelector("[data-result-pile-note-row]");
    const screedAreaNode = resultNode.querySelector("[data-result-screed-area]");
    const screedHeightNode = resultNode.querySelector("[data-result-screed-height]");
    const screedRebarCard = resultNode.querySelector('[data-result-card="screed-reinforcement"]');
    const screedRebarMass = resultNode.querySelector("[data-result-screed-rebar-mass]");
    const screedRebarLen = resultNode.querySelector("[data-result-screed-rebar-length]");
    const mixtureCard = resultNode.querySelector('[data-result-card="mixture"]');
    const pileFoundationMixtureCard = resultNode.querySelector('[data-result-card="pile-foundation-mixture"]');
    const grillageMixtureCard = resultNode.querySelector('[data-result-card="grillage-mixture"]');

    if (concreteVolumeNode) {
      concreteVolumeNode.textContent = "-";
    }
    if (concreteAreaNode) {
      concreteAreaNode.textContent = "-";
    }
    if (concreteHeightNode) {
      concreteHeightNode.textContent = "-";
    }
    if (reinforcementCard) {
      reinforcementCard.hidden = true;
      reinforcementCard.innerHTML = "";
    }
    if (formworkCard) {
      formworkCard.hidden = true;
      formworkCard.innerHTML = "";
    }
    if (schemeNode) {
      schemeNode.innerHTML = "";
    }

    if (volumeNode) {
      volumeNode.textContent = "-";
    }

    if (materialNode) {
      materialNode.textContent = "-";
    }
    if (screedAreaNode) {
      screedAreaNode.textContent = "-";
    }
    if (screedHeightNode) {
      screedHeightNode.textContent = "-";
    }
    if (screedRebarCard && screedRebarMass && screedRebarLen) {
      screedRebarCard.hidden = true;
      screedRebarMass.textContent = "-";
      screedRebarLen.textContent = "-";
    }
    if (mixtureCard) {
      mixtureCard.hidden = true;
      mixtureCard.innerHTML = "";
    }
    if (pileFoundationMixtureCard) {
      pileFoundationMixtureCard.hidden = true;
      pileFoundationMixtureCard.innerHTML = "";
    }
    if (grillageMixtureCard) {
      grillageMixtureCard.hidden = true;
      grillageMixtureCard.innerHTML = "";
    }
    if (stripConcreteLengthNode) {
      stripConcreteLengthNode.textContent = "-";
    }
    if (stripConcreteVolumeNode) {
      stripConcreteVolumeNode.textContent = "-";
    }
    if (stripRebarLongitudinalNode) {
      stripRebarLongitudinalNode.textContent = "-";
    }
    if (stripRebarTransverseNode) {
      stripRebarTransverseNode.textContent = "-";
    }
    if (stripRebarTotalNode) {
      stripRebarTotalNode.textContent = "-";
    }
    if (stripFormworkAreaNode) {
      stripFormworkAreaNode.textContent = "-";
    }
    if (stripFormworkLinearNode) {
      stripFormworkLinearNode.textContent = "-";
    }
    if (stripConcreteCard && calculatorValue === "pile_foundation") {
      stripConcreteCard.hidden = true;
    }
    if (stripReinforcementCard) {
      stripReinforcementCard.hidden = true;
      if (calculatorValue === "pile_foundation") {
        stripReinforcementCard.innerHTML = "";
      }
    }
    if (stripFormworkCard) {
      stripFormworkCard.hidden = true;
    }
    if (pileHeaderSection) {
      pileHeaderSection.hidden = true;
    }
    if (grillageHeaderSection) {
      grillageHeaderSection.hidden = true;
    }
    if (pileConcreteCard) {
      pileConcreteCard.hidden = true;
    }
    if (pileTypeNode) {
      pileTypeNode.textContent = "-";
    }
    if (pileCountNode) {
      pileCountNode.textContent = "-";
    }
    if (pileConcreteVolumeNode) {
      pileConcreteVolumeNode.textContent = "-";
    }
    if (pileConcretePerPileNode) {
      pileConcretePerPileNode.textContent = "-";
    }
    if (pilePerPileRow) {
      pilePerPileRow.hidden = true;
    }
    if (pileNoteNode) {
      pileNoteNode.textContent = "-";
    }
    if (pileNoteRow) {
      pileNoteRow.hidden = true;
    }
    if (pileReinforcementCard) {
      pileReinforcementCard.hidden = true;
      pileReinforcementCard.innerHTML = "";
    }

    resultNode.classList.remove("is-stale");
    const staleNotice = resultNode.querySelector("[data-result-stale-notice]");
    if (staleNotice) {
      staleNotice.hidden = true;
    }
    resultNode.hidden = true;
    resultNode.classList.remove("is-success");
  }

  function setFieldError(form, field, message) {
    const target = form.querySelector(`[data-field-error="${field}"]`);
    if (!target) {
      return;
    }

    target.textContent = message;
    if (message) {
      form.classList.add("has-errors");
    }
  }

  function readTrimmed(formData, field) {
    return String(formData.get(field) || "").trim();
  }

  function isPositiveNumber(value) {
    const numericValue = Number(value);
    return Number.isFinite(numericValue) && numericValue > 0;
  }

  function isPositiveInteger(value) {
    const numericValue = Number(value);
    return Number.isInteger(numericValue) && numericValue > 0;
  }

  function escapeHtml(text) {
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function renderSlabScheme(form, responsePayload, requestPayload) {
    const schemeNode = getEstimatorShell(form)?.querySelector("[data-slab-scheme]");
    if (!schemeNode) {
      return;
    }

    const concrete = responsePayload?.concrete || {};
    const reinforcement = responsePayload?.reinforcement || null;
    const formwork = responsePayload?.formwork || null;
    const lengthRaw = Number(requestPayload.length);
    const widthRaw = Number(requestPayload.width);
    const areaRaw = Number(concrete.areaM2);
    const resolvedLength = Number.isFinite(lengthRaw) && lengthRaw > 0
      ? lengthRaw
      : Number.isFinite(areaRaw) && areaRaw > 0
      ? Math.sqrt(areaRaw)
      : 1;
    const resolvedWidth = Number.isFinite(widthRaw) && widthRaw > 0
      ? widthRaw
      : Number.isFinite(areaRaw) && areaRaw > 0
      ? Math.sqrt(areaRaw)
      : 1;
    const horizontalIsLength = resolvedLength >= resolvedWidth;
    const horizontalValue = horizontalIsLength ? resolvedLength : resolvedWidth;
    const verticalValue = horizontalIsLength ? resolvedWidth : resolvedLength;
    const maxSide = Math.max(horizontalValue, verticalValue);
    const horizontalScale = maxSide > 0 ? horizontalValue / maxSide : 1;
    const verticalScale = maxSide > 0 ? verticalValue / maxSide : 1;
    const isNearSquare = Math.abs(resolvedLength - resolvedWidth) < 0.001;
    const drawingLeft = 64;
    const drawingWidth = 404;
    let slabWidth = 0;
    let slabHeight = 0;

    if (isNearSquare) {
      slabWidth = 200;
      slabHeight = 200;
    } else {
      slabWidth = Math.max(250, Math.min(400, Math.round(drawingWidth * horizontalScale)));
      slabHeight = Math.max(110, Math.min(180, Math.round(180 * verticalScale)));
    }

    const drawingCenterY = isNearSquare ? 152 : 120;
    const slabX = drawingLeft + Math.round((drawingWidth - slabWidth) / 2);
    const slabY = drawingCenterY - Math.round(slabHeight / 2);
    const slabRight = slabX + slabWidth;
    const slabBottom = slabY + slabHeight;
    const horizontalLengthLineY = Math.max(16, slabY - 24);
    const horizontalLengthTickTopY = Math.max(12, horizontalLengthLineY - 4);
    const horizontalLengthTickBottomY = horizontalLengthLineY + 4;
    const horizontalLengthLabelY = Math.max(8, horizontalLengthLineY - 10);
    const verticalNearLineX = Math.min(516, slabRight + 22);
    const verticalFarLineX = Math.min(538, slabRight + 44);
    const verticalLabelNearX = Math.min(548, slabRight + 30);
    const verticalLabelFarX = Math.min(550, slabRight + 52);
    const widthPosition = horizontalIsLength ? "vertical" : "horizontal";
    const lengthPosition = horizontalIsLength ? "horizontal" : "vertical";
    const lengthLabelText = `L: ${formatNumber(resolvedLength)} м`;
    const widthLabelText = `W: ${formatNumber(resolvedWidth)} м`;
    const infoItems = [
      `<li><strong>Площадь плиты:</strong> ${formatNumber(concrete.areaM2)} м2</li>`,
      `<li><strong>Длина плиты:</strong> ${formatNumber(resolvedLength)} м</li>`,
      `<li><strong>Ширина плиты:</strong> ${formatNumber(resolvedWidth)} м</li>`,
      `<li><strong>Высота плиты:</strong> ${formatNumber(requestPayload.height)} м</li>`,
    ];
    if (formwork) {
      infoItems.push(
        `<li><strong>Высота опалубки:</strong> ${formatNumber(formwork.heightM)} м</li>`
      );
    }
    if (reinforcement) {
      infoItems.push(
        `<li><strong>Арматура:</strong><br> Ø${formatNumber(
          reinforcement.diameterMm
        )}, шаг ${formatNumber(reinforcement.stepMm)} мм, ${formatNumber(
          reinforcement.layers
        )} слоя</li>`
      );
    }

    const minSlabDim = Math.min(slabWidth, slabHeight);
    let rebarInset = Math.max(6, Math.min(14, Math.round(minSlabDim * 0.035)));
    rebarInset = Math.min(rebarInset, Math.max(0, Math.floor(minSlabDim / 2) - 2));
    const rLeft = slabX + rebarInset;
    const rRight = slabRight - rebarInset;
    const rTop = slabY + rebarInset;
    const rBottom = slabBottom - rebarInset;
    const rSpanX = rRight - rLeft;
    const rSpanY = rBottom - rTop;

    const reinforcementLayer = reinforcement
      ? `
      <g class="brigmaster-slab-scheme__rebar">
        <line x1="${rLeft}" y1="${rTop}" x2="${rRight}" y2="${rTop}" />
        <line x1="${rLeft}" y1="${rTop + rSpanY * 0.33}" x2="${rRight}" y2="${rTop + rSpanY * 0.33}" />
        <line x1="${rLeft}" y1="${rTop + rSpanY * 0.66}" x2="${rRight}" y2="${rTop + rSpanY * 0.66}" />
        <line x1="${rLeft}" y1="${rBottom}" x2="${rRight}" y2="${rBottom}" />
        <line x1="${rLeft}" y1="${rTop}" x2="${rLeft}" y2="${rBottom}" />
        <line x1="${rLeft + rSpanX * 0.2}" y1="${rTop}" x2="${rLeft + rSpanX * 0.2}" y2="${rBottom}" />
        <line x1="${rLeft + rSpanX * 0.4}" y1="${rTop}" x2="${rLeft + rSpanX * 0.4}" y2="${rBottom}" />
        <line x1="${rLeft + rSpanX * 0.6}" y1="${rTop}" x2="${rLeft + rSpanX * 0.6}" y2="${rBottom}" />
        <line x1="${rLeft + rSpanX * 0.8}" y1="${rTop}" x2="${rLeft + rSpanX * 0.8}" y2="${rBottom}" />
        <line x1="${rRight}" y1="${rTop}" x2="${rRight}" y2="${rBottom}" />
      </g>`
      : "";

    const formworkLayer = formwork
      ? `
      <rect x="${slabX - 12}" y="${slabY - 12}" width="${slabWidth + 24}" height="${slabHeight + 24}" class="brigmaster-slab-scheme__formwork" />`
      : "";

    const legendItems = [
      `<span class="brigmaster-slab-scheme__legend-item"><span class="brigmaster-slab-scheme__legend-mark brigmaster-slab-scheme__legend-mark--slab" aria-hidden="true"></span><span class="brigmaster-slab-scheme__legend-text">— плита</span></span>`,
    ];
    if (reinforcement) {
      legendItems.push(
        `<span class="brigmaster-slab-scheme__legend-item"><span class="brigmaster-slab-scheme__legend-mark brigmaster-slab-scheme__legend-mark--rebar" aria-hidden="true"></span><span class="brigmaster-slab-scheme__legend-text">— арматура</span></span>`
      );
    }
    if (formwork) {
      legendItems.push(
        `<span class="brigmaster-slab-scheme__legend-item"><span class="brigmaster-slab-scheme__legend-mark brigmaster-slab-scheme__legend-mark--formwork" aria-hidden="true"></span><span class="brigmaster-slab-scheme__legend-text">— опалубка</span></span>`
      );
    }
    const legendHtml = `<div class="brigmaster-slab-scheme__legend" aria-label="Легенда схемы">${legendItems.join(
      ""
    )}</div>`;

    schemeNode.innerHTML = `
      <h3 class="brigmaster-estimator__scheme-title">Схема плиты</h3>
      <div class="brigmaster-slab-scheme-layout">
        <ul class="brigmaster-slab-scheme__facts" data-scheme-facts>
          ${infoItems.join("")}
        </ul>
        <svg viewBox="0 0 560 260" class="brigmaster-slab-scheme" aria-label="Схема плитного фундамента">
          <rect x="${slabX}" y="${slabY}" width="${slabWidth}" height="${slabHeight}" class="brigmaster-slab-scheme__slab" />
          ${formworkLayer}
          ${reinforcementLayer}
          ${
            lengthPosition === "horizontal"
              ? `<line x1="${slabX}" y1="${horizontalLengthLineY}" x2="${slabRight}" y2="${horizontalLengthLineY}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${slabX}" y1="${horizontalLengthTickTopY}" x2="${slabX}" y2="${horizontalLengthTickBottomY}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${slabRight}" y1="${horizontalLengthTickTopY}" x2="${slabRight}" y2="${horizontalLengthTickBottomY}" class="brigmaster-slab-scheme__dimension" />
                 <text x="${slabX + slabWidth / 2}" y="${horizontalLengthLabelY}" text-anchor="middle" class="brigmaster-slab-scheme__label">${escapeHtml(
                   lengthLabelText
                 )}</text>`
              : `<line x1="${verticalFarLineX}" y1="${slabY}" x2="${verticalFarLineX}" y2="${slabBottom}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${verticalFarLineX - 4}" y1="${slabY}" x2="${verticalFarLineX + 4}" y2="${slabY}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${verticalFarLineX - 4}" y1="${slabBottom}" x2="${verticalFarLineX + 4}" y2="${slabBottom}" class="brigmaster-slab-scheme__dimension" />
                 <text x="${verticalLabelFarX}" y="${(slabY + slabBottom) / 2}" class="brigmaster-slab-scheme__label">${escapeHtml(
                   lengthLabelText
                 )}</text>`
          }
          ${
            widthPosition === "horizontal"
              ? `<line x1="${slabX}" y1="${slabY - 24}" x2="${slabRight}" y2="${slabY - 24}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${slabX}" y1="${slabY - 28}" x2="${slabX}" y2="${slabY - 20}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${slabRight}" y1="${slabY - 28}" x2="${slabRight}" y2="${slabY - 20}" class="brigmaster-slab-scheme__dimension" />
                 <text x="${slabX + slabWidth / 2}" y="${slabY - 34}" text-anchor="middle" class="brigmaster-slab-scheme__label">${escapeHtml(
                   widthLabelText
                 )}</text>`
              : `<line x1="${verticalNearLineX}" y1="${slabY}" x2="${verticalNearLineX}" y2="${slabBottom}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${verticalNearLineX - 4}" y1="${slabY}" x2="${verticalNearLineX + 4}" y2="${slabY}" class="brigmaster-slab-scheme__dimension" />
                 <line x1="${verticalNearLineX - 4}" y1="${slabBottom}" x2="${verticalNearLineX + 4}" y2="${slabBottom}" class="brigmaster-slab-scheme__dimension" />
                 <text x="${verticalLabelNearX}" y="${(slabY + slabBottom) / 2}" class="brigmaster-slab-scheme__label">${escapeHtml(
                   widthLabelText
                 )}</text>`
          }
        </svg>
      </div>
      ${legendHtml}
    `;
  }

  function showSlabResult(form, payload, requestPayload) {
    const resultNode = getEstimatorShell(form)?.querySelector("[data-result]");
    if (!resultNode) {
      return;
    }

    const concreteVolumeNode = resultNode.querySelector("[data-result-concrete-volume]");
    const concreteAreaNode = resultNode.querySelector("[data-result-concrete-area]");
    const concreteHeightNode = resultNode.querySelector("[data-result-concrete-height]");
    const reinforcementCard = resultNode.querySelector('[data-result-card="reinforcement"]');
    const formworkCard = resultNode.querySelector('[data-result-card="formwork"]');
    const concrete = payload?.concrete || {};
    const reinforcement = payload?.reinforcement || null;
    const formwork = payload?.formwork || null;

    if (concreteVolumeNode) {
      concreteVolumeNode.textContent = formatNumber(concrete.volumeM3);
    }
    if (concreteAreaNode) {
      concreteAreaNode.textContent = formatNumber(concrete.areaM2);
    }
    if (concreteHeightNode) {
      concreteHeightNode.textContent = formatNumber(concrete.heightM);
    }

    if (reinforcementCard) {
      if (reinforcement) {
        reinforcementCard.hidden = false;
        reinforcementCard.innerHTML = `
          <h3>Арматура</h3>
          <p><strong>Масса:</strong> ${formatNumber(reinforcement.massKg)} кг</p>
          <p><strong>Общая длина (с запасом):</strong> ${formatNumber(
            reinforcement.totalLengthWithReserveM
          )} м</p>
          <p><strong>Сетка:</strong> Ø${formatNumber(
            reinforcement.diameterMm
          )}, шаг ${formatNumber(reinforcement.stepMm)} мм, ${formatNumber(
          reinforcement.layers
        )} слоя</p>
        `;
      } else {
        reinforcementCard.hidden = true;
        reinforcementCard.innerHTML = "";
      }
    }

    if (formworkCard) {
      if (formwork) {
        formworkCard.hidden = false;
        formworkCard.innerHTML = `
          <h3>Опалубка</h3>
          <p><strong>Площадь щитов:</strong> ${formatNumber(formwork.areaM2)} м2</p>
          <p><strong>Погонные метры:</strong> ${formatNumber(formwork.linearMeters)} м</p>
          <p><strong>Высота:</strong> ${formatNumber(formwork.heightM)} м</p>
        `;
      } else {
        formworkCard.hidden = true;
        formworkCard.innerHTML = "";
      }
    }

    renderSlabScheme(form, payload, requestPayload);
    renderMixtureCard(
      resultNode.querySelector('[data-result-card="mixture"]'),
      payload?.mixture,
      "Смесь и материалы"
    );
    syncResultGridLayout(resultNode);
    resultNode.hidden = false;
    resultNode.classList.add("is-success");
    finalizeSuccessfulResult(form);
  }

  function buildMixtureResultHtml(mixture, title) {
    if (!mixture || typeof mixture !== "object") {
      return "";
    }

    const type = String(mixture.type || "").trim();
    const summaryItems = [];
    const componentItems = [];
    const noteLines = [];

    const buildPurchaseText = (component) => {
      const unit = String(component.purchaseUnit || "");
      const displayUnitWeight = formatNumber(component.displayUnitWeight);
      if (unit === "bag") {
        return `${formatNumber(component.requiredUnits)} шт мешков, к покупке рекомендовано ${formatNumber(
          component.roundedUnits
        )} шт мешков по ${displayUnitWeight} кг`;
      }

      return `${formatNumber(component.requiredUnits)} т, к покупке рекомендовано ${formatNumber(
        component.roundedUnits
      )} т`;
    };

    if (type === "ready") {
      summaryItems.push(
        `<div class="brigmaster-estimator__mixture-item"><span>Тип</span><strong>${escapeHtml(
          mixture.displayType || "Готовая"
        )}</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Объём</span><strong>${formatNumber(
          mixture.volumeM3
        )} м³</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Цена за м³</span><strong>${formatNumber(
          mixture.pricePerM3
        )}</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Итоговая стоимость</span><strong>${formatNumber(
          mixture.totalCost
        )}</strong></div>`
      );
    } else if (type === "dry_ready") {
      summaryItems.push(
        `<div class="brigmaster-estimator__mixture-item"><span>Тип</span><strong>${escapeHtml(
          mixture.displayType || "Готовая, сухая"
        )}</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Ориентир расхода</span><strong>${formatNumber(
          mixture.consumptionKgPerM2Per10mm
        )} кг на 1 м² при 10 мм</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Общий вес смеси</span><strong>${formatNumber(
          mixture.totalWeightKg
        )} кг</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Мешки</span><strong>${formatNumber(
          mixture.requiredBags
        )} шт мешков, к покупке рекомендовано ${formatNumber(
          mixture.roundedBags
        )} шт мешков по ${formatNumber(mixture.bagWeightKg)} кг</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Стоимость</span><strong>${formatNumber(
          mixture.totalCostExact
        )} / ${formatNumber(mixture.totalCostRounded)}</strong></div>`
      );
    } else if (type === "self_mix") {
      summaryItems.push(
        `<div class="brigmaster-estimator__mixture-item"><span>Тип</span><strong>${escapeHtml(
          mixture.displayType || "Самомесная"
        )}</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Объём готовой смеси</span><strong>${formatNumber(
          mixture.volumeM3
        )} м³</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Вода</span><strong>${formatNumber(
          mixture.waterLiters
        )} л</strong></div>`,
        `<div class="brigmaster-estimator__mixture-item"><span>Итоговая стоимость</span><strong>${formatNumber(
          mixture.totalCostExact
        )} / ${formatNumber(mixture.totalCostRounded)}</strong></div>`
      );

      const components = mixture.components || {};
      Object.values(components).forEach((component) => {
        if (!component || typeof component !== "object") {
          return;
        }
        componentItems.push(
          `<div class="brigmaster-estimator__mixture-component">
            <h4>${escapeHtml(component.label || "Материал")}</h4>
            <p><strong>${formatNumber(component.weightKg)}</strong> кг</p>
            <p>${buildPurchaseText(component)}</p>
            <p>Стоимость: ${formatNumber(component.totalCostExact)} / ${formatNumber(
            component.totalCostRounded
          )}</p>
          </div>`
        );
      });
    }

    if (mixture.note) {
      noteLines.push(
        `<p class="brigmaster-estimator__result-note">${escapeHtml(
          String(mixture.note)
        )}</p>`
      );
    }

    return `
      <h3>${escapeHtml(title)}</h3>
      <div class="brigmaster-estimator__mixture-summary">
        ${summaryItems.join("")}
      </div>
      ${
        componentItems.length
          ? `<div class="brigmaster-estimator__mixture-components">${componentItems.join(
              ""
            )}</div>`
          : ""
      }
      ${noteLines.join("")}
    `;
  }

  function renderMixtureCard(cardNode, mixture, title) {
    if (!cardNode) {
      return;
    }

    const html = buildMixtureResultHtml(mixture, title);
    if (!html) {
      cardNode.hidden = true;
      cardNode.innerHTML = "";
      cardNode.classList.remove("brigmaster-estimator__result-card--mixture");
      return;
    }

    cardNode.hidden = false;
    cardNode.classList.add("brigmaster-estimator__result-card--mixture");
    cardNode.innerHTML = html;
  }

  function buildStripReinforcementHtml(reinforcement, title = "Арматура") {
    if (!reinforcement) {
      return "";
    }
    const byDiameter = Array.isArray(reinforcement?.byDiameter)
      ? reinforcement.byDiameter
      : null;
    const totalMassKg = reinforcement?.totalMassKg;

    const buildDiameterLabel = (diameter) =>
      Number.isFinite(Number(diameter)) ? `Ø${formatNumber(diameter)} мм` : "Ø-";
    const lengthLines = byDiameter && byDiameter.length > 0
      ? byDiameter
        .map((item) => {
          const d = item?.diameterMm;
          const lenWithReserve = item?.totalLengthWithReserveM;
          return `<li>${buildDiameterLabel(d)} - ${formatNumber(lenWithReserve)} м</li>`;
        })
      : [];
    const massLines = byDiameter && byDiameter.length > 0
      ? byDiameter
        .map((item) => {
          const d = item?.diameterMm;
          const mass = item?.massKg;
          return `<li>${buildDiameterLabel(d)} - ${formatNumber(mass)} кг</li>`;
        })
      : [];

    // Fallback for legacy payloads without byDiameter
    if (lengthLines.length === 0) {
      const longitudinalDiameter = reinforcement?.longitudinal?.diameterMm;
      const longitudinalLength = reinforcement?.longitudinal?.totalLengthWithReserveM;
      const transverseDiameter = reinforcement?.transverse?.globalDiameterMm;
      const transverseLength = reinforcement?.transverse?.totalLengthWithReserveM;
      if (Number.isFinite(Number(longitudinalLength))) {
        lengthLines.push(
          `<li>${buildDiameterLabel(longitudinalDiameter)} - ${formatNumber(longitudinalLength)} м</li>`
        );
      }
      if (
        Number.isFinite(Number(transverseLength)) &&
        String(transverseDiameter) !== String(longitudinalDiameter)
      ) {
        lengthLines.push(
          `<li>${buildDiameterLabel(transverseDiameter)} - ${formatNumber(transverseLength)} м</li>`
        );
      }
    }
    if (massLines.length === 0) {
      const longitudinalDiameter = reinforcement?.longitudinal?.diameterMm;
      const longitudinalMass = reinforcement?.longitudinal?.massKg;
      const transverseDiameter = reinforcement?.transverse?.globalDiameterMm;
      const transverseMass = reinforcement?.transverse?.massKg;
      if (Number.isFinite(Number(longitudinalMass))) {
        massLines.push(
          `<li>${buildDiameterLabel(longitudinalDiameter)} - ${formatNumber(longitudinalMass)} кг</li>`
        );
      }
      if (
        Number.isFinite(Number(transverseMass)) &&
        String(transverseDiameter) !== String(longitudinalDiameter)
      ) {
        massLines.push(
          `<li>${buildDiameterLabel(transverseDiameter)} - ${formatNumber(transverseMass)} кг</li>`
        );
      }
    }

    const totalLengthWithReserveM = byDiameter && byDiameter.length > 0
      ? byDiameter.reduce(
          (sum, item) => sum + (Number.isFinite(Number(item?.totalLengthWithReserveM)) ? Number(item.totalLengthWithReserveM) : 0),
          0
        )
      : (Number(reinforcement?.longitudinal?.totalLengthWithReserveM) || 0) +
        (Number(reinforcement?.transverse?.totalLengthWithReserveM) || 0);

    return `
      <h3>${escapeHtml(title)}</h3>
      <div class="brigmaster-estimator__rebar-columns">
        <div class="brigmaster-estimator__rebar-column">
          <p class="brigmaster-estimator__rebar-column-title"><strong>Длина (с запасом):</strong></p>
          <ul class="brigmaster-estimator__result-list">
            ${lengthLines.join("")}
            <li><strong>Всего:</strong> ${formatNumber(totalLengthWithReserveM)} м</li>
          </ul>
        </div>
        <div class="brigmaster-estimator__rebar-column">
          <p class="brigmaster-estimator__rebar-column-title"><strong>Масса:</strong></p>
          <ul class="brigmaster-estimator__result-list">
            ${massLines.join("")}
            <li><strong>Всего:</strong> ${formatNumber(totalMassKg)} кг</li>
          </ul>
        </div>
      </div>
    `;
  }

  function renderStripReinforcementCard(reinforcementCard, reinforcement, title = "Арматура") {
    if (!reinforcementCard) {
      return;
    }
    const html = buildStripReinforcementHtml(reinforcement, title);
    if (!html) {
      reinforcementCard.hidden = true;
      reinforcementCard.innerHTML = "";
      return;
    }
    reinforcementCard.hidden = false;
    reinforcementCard.innerHTML = html;
  }

  /**
   * Pile reinforcement block (same column layout as grillage strip reinforcement).
   * @param {object} pileReinforcement - API piles.reinforcement
   */
  function buildPileReinforcementColumnsHtml(pileReinforcement) {
    if (!pileReinforcement) {
      return "";
    }
    const byDiameter = Array.isArray(pileReinforcement?.byDiameter)
      ? pileReinforcement.byDiameter
      : [];
    const buildDiameterLabel = (diameter) =>
      Number.isFinite(Number(diameter)) ? `Ø${formatNumber(diameter)} мм` : "Ø-";
    const lengthLines = byDiameter.map((item) => {
      const d = item?.diameterMm;
      const lenWithReserve = item?.totalLengthWithReserveM;
      return `<li>${buildDiameterLabel(d)} - ${formatNumber(lenWithReserve)} м</li>`;
    });
    const massLines = byDiameter.map((item) => {
      const d = item?.diameterMm;
      const mass = item?.massKg;
      return `<li>${buildDiameterLabel(d)} - ${formatNumber(mass)} кг</li>`;
    });
    const fallbackDiameter = Number(pileReinforcement?.diameterMm);
    if (lengthLines.length === 0 && Number.isFinite(fallbackDiameter)) {
      lengthLines.push(
        `<li>${buildDiameterLabel(fallbackDiameter)} - ${formatNumber(pileReinforcement?.totalLengthWithReserveM)} м</li>`
      );
      massLines.push(
        `<li>${buildDiameterLabel(fallbackDiameter)} - ${formatNumber(pileReinforcement?.massKg)} кг</li>`
      );
    }
    const totalLengthWithReserveM = byDiameter.length > 0
      ? byDiameter.reduce(
          (sum, item) => sum + (Number.isFinite(Number(item?.totalLengthWithReserveM)) ? Number(item.totalLengthWithReserveM) : 0),
          0
        )
      : Number(pileReinforcement?.totalLengthWithReserveM) || 0;
    const totalMassKg = byDiameter.length > 0
      ? byDiameter.reduce(
          (sum, item) => sum + (Number.isFinite(Number(item?.massKg)) ? Number(item.massKg) : 0),
          0
        )
      : Number(pileReinforcement?.massKg) || 0;

    if (lengthLines.length === 0 && massLines.length === 0) {
      return "";
    }

    return `
      <div class="brigmaster-estimator__rebar-columns">
        <div class="brigmaster-estimator__rebar-column">
          <p class="brigmaster-estimator__rebar-column-title"><strong>Длина (с запасом):</strong></p>
          <ul class="brigmaster-estimator__result-list">
            ${lengthLines.join("")}
            <li><strong>Всего:</strong> ${formatNumber(totalLengthWithReserveM)} м</li>
          </ul>
        </div>
        <div class="brigmaster-estimator__rebar-column">
          <p class="brigmaster-estimator__rebar-column-title"><strong>Масса:</strong></p>
          <ul class="brigmaster-estimator__result-list">
            ${massLines.join("")}
            <li><strong>Всего:</strong> ${formatNumber(totalMassKg)} кг</li>
          </ul>
        </div>
      </div>
    `;
  }

  function showStripResult(form, payload) {
    const resultNode = getEstimatorShell(form)?.querySelector("[data-result]");
    if (!resultNode) {
      return;
    }

    const concrete = payload?.concrete || {};
    const reinforcement = payload?.reinforcement || null;
    const formwork = payload?.formwork || null;
    const concreteLengthNode = resultNode.querySelector("[data-result-strip-concrete-length]");
    const concreteVolumeNode = resultNode.querySelector("[data-result-strip-concrete-volume]");
    const reinforcementCard = resultNode.querySelector('[data-result-card="strip-reinforcement"]');
    const formworkCard = resultNode.querySelector('[data-result-card="strip-formwork"]');

    if (concreteLengthNode) {
      concreteLengthNode.textContent = formatNumber(concrete.totalLengthM);
    }
    if (concreteVolumeNode) {
      concreteVolumeNode.textContent = formatNumber(concrete.volumeM3);
    }

    renderStripReinforcementCard(reinforcementCard, reinforcement);

    if (formworkCard) {
      if (formwork) {
        formworkCard.hidden = false;
        const areaNode = formworkCard.querySelector("[data-result-strip-formwork-area]");
        const linearNode = formworkCard.querySelector("[data-result-strip-formwork-linear]");
        if (areaNode) {
          areaNode.textContent = formatNumber(formwork.totalFormworkAreaWithReserveM2);
        }
        if (linearNode) {
          linearNode.textContent = formatNumber(formwork.totalFormworkLinearM);
        }
      } else {
        formworkCard.hidden = true;
      }
    }

    renderMixtureCard(
      resultNode.querySelector('[data-result-card="mixture"]'),
      payload?.mixture,
      "Смесь и материалы"
    );

    syncResultGridLayout(resultNode);
    resultNode.hidden = false;
    resultNode.classList.add("is-success");
    finalizeSuccessfulResult(form);
  }

  function showPileFoundationResult(form, payload) {
    const resultNode = getEstimatorShell(form)?.querySelector("[data-result]");
    if (!resultNode) {
      return;
    }

    const piles = payload?.piles || null;
    const concrete = payload?.concrete || null;
    const reinforcement = payload?.reinforcement || null;
    const formwork = payload?.formwork || null;
    const pileHeaderSection = resultNode.querySelector('[data-result-section="pile-header"]');
    const pileConcreteCard = resultNode.querySelector('[data-result-card="pile-concrete"]');
    const pileTypeNode = resultNode.querySelector("[data-result-pile-type]");
    const pileCountNode = resultNode.querySelector("[data-result-pile-count]");
    const pileConcreteVolumeNode = resultNode.querySelector("[data-result-pile-concrete-volume]");
    const pileConcretePerPileNode = resultNode.querySelector("[data-result-pile-concrete-per-pile]");
    const pilePerPileRow = resultNode.querySelector("[data-result-pile-per-pile-row]");
    const pileNoteNode = resultNode.querySelector("[data-result-pile-note]");
    const pileNoteRow = resultNode.querySelector("[data-result-pile-note-row]");
    const pileReinforcementCard = resultNode.querySelector('[data-result-card="pile-reinforcement"]');
    const pileFoundationMixtureCard = resultNode.querySelector('[data-result-card="pile-foundation-mixture"]');
    const grillageHeaderSection = resultNode.querySelector('[data-result-section="grillage-header"]');
    const concreteCard = resultNode.querySelector('[data-result-card="strip-concrete"]');
    const concreteLengthNode = resultNode.querySelector("[data-result-strip-concrete-length]");
    const concreteVolumeNode = resultNode.querySelector("[data-result-strip-concrete-volume]");
    const reinforcementCard = resultNode.querySelector('[data-result-card="strip-reinforcement"]');
    const formworkCard = resultNode.querySelector('[data-result-card="strip-formwork"]');
    const grillageMixtureCard = resultNode.querySelector('[data-result-card="grillage-mixture"]');
    const formworkAreaNode = resultNode.querySelector("[data-result-strip-formwork-area]");
    const formworkLinearNode = resultNode.querySelector("[data-result-strip-formwork-linear]");
    const requestPayload = form._lastRequestPayload || {};
    const includePiles = requestPayload.includePiles === true;
    const includeGrillage = requestPayload.includeGrillage === true;

    if (pileHeaderSection) {
      if (includePiles && piles) {
        pileHeaderSection.hidden = false;
        const pileTypeRaw = String(piles.pileType || "").trim();
        if (pileTypeNode) {
          const pileTypeMap = {
            bored: "Буронабивные",
            screw: "Винтовые",
            driven: "Забивные",
          };
          pileTypeNode.textContent = pileTypeMap[pileTypeRaw] || pileTypeRaw || "-";
        }
        if (pileCountNode) {
          pileCountNode.textContent = formatNumber(piles.count);
        }
        if (pileNoteNode) {
          const noteRaw = String(piles.note || "").trim();
          const noteRu =
            noteRaw === "Concrete for piles is not required for screw/driven pile types."
              ? "Для винтовых и забивных свай бетон не требуется."
              : noteRaw;
          pileNoteNode.textContent = noteRu || "-";
        }
        if (pileNoteRow) {
          const noteRaw = String(piles.note || "").trim();
          const showNote = (pileTypeRaw === "screw" || pileTypeRaw === "driven") && !!noteRaw;
          pileNoteRow.hidden = !showNote;
        }
      } else {
        pileHeaderSection.hidden = true;
      }
    }

    if (pileConcreteCard) {
      if (includePiles && piles) {
        const concreteVolume = Number(piles.concreteVolumeM3);
        if (Number.isFinite(concreteVolume) && concreteVolume > 0) {
          pileConcreteCard.hidden = false;
          if (pileConcreteVolumeNode) {
            pileConcreteVolumeNode.textContent = formatNumber(piles.concreteVolumeM3);
          }
          const perPile = piles.concreteVolumePerPileM3;
          const perPileNum = Number(perPile);
          if (pilePerPileRow && pileConcretePerPileNode) {
            if (perPile != null && Number.isFinite(perPileNum)) {
              pilePerPileRow.hidden = false;
              pileConcretePerPileNode.textContent = formatNumber(perPile);
            } else {
              pilePerPileRow.hidden = true;
            }
          }
        } else {
          pileConcreteCard.hidden = true;
        }
      } else {
        pileConcreteCard.hidden = true;
      }
    }

    if (pileReinforcementCard) {
      const pileReinforcement = includePiles && piles?.reinforcement ? piles.reinforcement : null;
      const columnsHtml = buildPileReinforcementColumnsHtml(pileReinforcement);
      if (columnsHtml) {
        pileReinforcementCard.hidden = false;
        pileReinforcementCard.innerHTML = `<h4>Арматура свай</h4>${columnsHtml}`;
      } else {
        pileReinforcementCard.hidden = true;
        pileReinforcementCard.innerHTML = "";
      }
    }

    const hasGrillageBlock = !!(concrete || reinforcement || formwork);
    if (grillageHeaderSection) {
      grillageHeaderSection.hidden = !(includeGrillage && hasGrillageBlock);
    }

    if (concreteCard) {
      if (includeGrillage && concrete) {
        concreteCard.hidden = false;
        if (concreteLengthNode) {
          concreteLengthNode.textContent = formatNumber(concrete.totalLengthM);
        }
        if (concreteVolumeNode) {
          concreteVolumeNode.textContent = formatNumber(concrete.volumeM3);
        }
      } else {
        concreteCard.hidden = true;
      }
    }

    if (includeGrillage) {
      renderStripReinforcementCard(reinforcementCard, reinforcement, "Арматура ростверка");
    } else if (reinforcementCard) {
      reinforcementCard.hidden = true;
      reinforcementCard.innerHTML = "";
    }

    if (formworkCard) {
      if (includeGrillage && formwork) {
        formworkCard.hidden = false;
        if (formworkAreaNode) {
          formworkAreaNode.textContent = formatNumber(formwork.totalFormworkAreaWithReserveM2);
        }
        if (formworkLinearNode) {
          formworkLinearNode.textContent = formatNumber(formwork.totalFormworkLinearM);
        }
      } else {
        formworkCard.hidden = true;
      }
    }

    renderMixtureCard(
      pileFoundationMixtureCard,
      payload?.mixture || payload?.piles?.mixture,
      payload?.mixture ? "Смесь и материалы" : "Смесь для свай"
    );
    renderMixtureCard(
      grillageMixtureCard,
      payload?.grillageMixture,
      "Смесь для ростверка"
    );

    syncResultGridLayout(resultNode);
    resultNode.hidden = false;
    resultNode.classList.add("is-success");
    finalizeSuccessfulResult(form);
  }

  function showScreedResult(form, payload) {
    const resultNode = getEstimatorShell(form)?.querySelector("[data-result]");
    if (!resultNode) {
      return;
    }

    const volumeNode = resultNode.querySelector("[data-result-volume]");
    const areaNode = resultNode.querySelector("[data-result-screed-area]");
    const heightNode = resultNode.querySelector("[data-result-screed-height]");
    const rebarCard = resultNode.querySelector('[data-result-card="screed-reinforcement"]');
    const rebarMass = resultNode.querySelector("[data-result-screed-rebar-mass]");
    const rebarLen = resultNode.querySelector("[data-result-screed-rebar-length]");
    const reinforcement = payload?.reinforcement;

    if (volumeNode) {
      volumeNode.textContent = formatNumber(payload?.concrete?.volumeM3);
    }
    if (areaNode) {
      const areaVal = payload?.concrete?.areaM2;
      areaNode.textContent = Number.isFinite(Number(areaVal))
        ? formatNumber(areaVal)
        : "-";
    }
    if (heightNode) {
      heightNode.textContent = formatNumber(payload?.concrete?.heightM);
    }

    if (rebarCard && rebarMass && rebarLen) {
      if (reinforcement && Number.isFinite(Number(reinforcement.massKg))) {
        rebarCard.hidden = false;
        rebarMass.textContent = formatNumber(reinforcement.massKg);
        rebarLen.textContent = formatNumber(reinforcement.totalLengthWithReserveM);
      } else {
        rebarCard.hidden = true;
        rebarMass.textContent = "-";
        rebarLen.textContent = "-";
      }
    }

    renderMixtureCard(
      resultNode.querySelector('[data-result-card="mixture"]'),
      payload?.mixture,
      "Смесь и материалы"
    );

    syncResultGridLayout(resultNode);
    resultNode.hidden = false;
    resultNode.classList.add("is-success");
    finalizeSuccessfulResult(form);
  }

  function showResult(form, payload) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator === "slab_foundation") {
      const requestPayload = form._lastRequestPayload || {};
      showSlabResult(form, payload, requestPayload);
      return;
    }
    if (calculator === "strip_foundation") {
      showStripResult(form, payload);
      return;
    }
    if (calculator === "pile_foundation") {
      showPileFoundationResult(form, payload);
      return;
    }
    if (calculator === "screed") {
      showScreedResult(form, payload);
      return;
    }

    const resultNode = getEstimatorShell(form)?.querySelector("[data-result]");
    if (!resultNode) {
      return;
    }

    const volumeNode = resultNode.querySelector("[data-result-volume]");
    const materialNode = resultNode.querySelector("[data-result-material]");

    if (volumeNode) {
      volumeNode.textContent = formatNumber(payload.calculatedVolume);
    }

    if (materialNode) {
      materialNode.textContent = formatNumber(payload.calculatedMaterialAmount);
    }

    syncResultGridLayout(resultNode);
    resultNode.hidden = false;
    resultNode.classList.add("is-success");
    finalizeSuccessfulResult(form);
  }

  function buildStripSegmentPayload(segmentNode, includeReinforcement, includeFormwork) {
    const getSegmentValue = (field) =>
      String(segmentNode.querySelector(`[data-segment-input="${field}"]`)?.value || "").trim();

    const segmentPayload = {
      segmentLengthM: getSegmentValue("segmentLengthM"),
      segmentWidthM: getSegmentValue("segmentWidthM"),
      segmentHeightM: getSegmentValue("segmentHeightM"),
    };

    if (includeReinforcement) {
      const segmentIncludeReinforcement =
        segmentNode.querySelector("[data-segment-include-reinforcement]")?.checked !== false;
      segmentPayload.segmentIncludeReinforcement = segmentIncludeReinforcement;
      if (segmentIncludeReinforcement) {
        const segmentUseGlobalRebarParams =
          segmentNode.querySelector("[data-segment-use-global-rebar]")?.checked !== false;
        segmentPayload.segmentUseGlobalRebarParams = segmentUseGlobalRebarParams;
        if (!segmentUseGlobalRebarParams) {
          segmentPayload.segmentLongitudinalBarsCount = getSegmentValue("segmentLongitudinalBarsCount");
          segmentPayload.segmentLongitudinalDiameterMm = getSegmentValue("segmentLongitudinalDiameterMm");
          segmentPayload.segmentTransverseDiameterMm = getSegmentValue("segmentTransverseDiameterMm");
          segmentPayload.segmentTransverseStepMm = getSegmentValue("segmentTransverseStepMm");
        }
      }
    }

    if (includeFormwork) {
      const segmentIncludeFormwork =
        segmentNode.querySelector("[data-segment-include-formwork]")?.checked !== false;
      segmentPayload.segmentIncludeFormwork = segmentIncludeFormwork;
      if (segmentIncludeFormwork) {
        const segmentUseGlobalFormworkParams =
          segmentNode.querySelector("[data-segment-use-global-formwork]")?.checked !== false;
        segmentPayload.segmentUseGlobalFormworkParams = segmentUseGlobalFormworkParams;
        if (!segmentUseGlobalFormworkParams) {
          segmentPayload.segmentFormworkHeightM = getSegmentValue("segmentFormworkHeightM");
        }
      }
    }

    return segmentPayload;
  }

  function validateBaseFields(form, payload) {
    if (!payload.mode) {
      setFieldError(form, "mode", "Выберите режим расчета.");
      return false;
    }

    return true;
  }

  function validatePositiveField(form, payload, fieldName, message) {
    if (!isPositiveNumber(payload[fieldName])) {
      setFieldError(form, fieldName, message);
      return false;
    }

    return true;
  }

  function validatePositiveValue(form, errorKey, value, message) {
    if (!isPositiveNumber(value)) {
      setFieldError(form, errorKey, message);
      return false;
    }

    return true;
  }

  function validateSelectedValue(form, errorKey, value, allowedValues, message) {
    if (!allowedValues.includes(value)) {
      setFieldError(form, errorKey, message);
      return false;
    }

    return true;
  }

  function normalizePurchaseWeight(rawValue, purchaseUnit) {
    const numericValue = Number(rawValue);
    if (!Number.isFinite(numericValue) || numericValue <= 0) {
      return rawValue;
    }

    return purchaseUnit === "tonne"
      ? String(numericValue * 1000)
      : String(numericValue);
  }

  function buildMixturePayload(form, formData, prefix = "", options = {}) {
    const allowDryReady = options.allowDryReady === true;
    const includeGravel = options.includeGravel !== false;
    const errorPrefix = getMixtureErrorPrefix(prefix);
    const nameOf = (suffix) => buildPrefixedFieldName(prefix, suffix);
    const mixtureType = readTrimmed(formData, nameOf("mixtureType"));
    const allowedTypes = allowDryReady
      ? ["ready", "dry_ready", "self_mix"]
      : ["ready", "self_mix"];

    let isValid = validateSelectedValue(
      form,
      `${errorPrefix}.type`,
      mixtureType,
      allowedTypes,
      "Выберите корректный тип смеси."
    );

    const mixture = {
      type: mixtureType,
    };

    if (mixtureType === "ready") {
      mixture.readyConcretePricePerM3 = readTrimmed(
        formData,
        nameOf("readyConcretePricePerM3")
      );
      isValid =
        validatePositiveValue(
          form,
          `${errorPrefix}.readyConcretePricePerM3`,
          mixture.readyConcretePricePerM3,
          "Цена раствора за м³ должна быть больше 0."
        ) && isValid;
      return { mixture, isValid };
    }

    if (mixtureType === "dry_ready") {
      mixture.dryMixBagWeightKg = readTrimmed(formData, nameOf("dryMixBagWeightKg"));
      mixture.dryMixBagPrice = readTrimmed(formData, nameOf("dryMixBagPrice"));
      isValid =
        validatePositiveValue(
          form,
          `${errorPrefix}.dryMixBagWeightKg`,
          mixture.dryMixBagWeightKg,
          "Вес мешка должен быть больше 0."
        ) && isValid;
      isValid =
        validatePositiveValue(
          form,
          `${errorPrefix}.dryMixBagPrice`,
          mixture.dryMixBagPrice,
          "Цена мешка должна быть больше 0."
        ) && isValid;
      return { mixture, isValid };
    }

    mixture.cementShare = readTrimmed(formData, nameOf("cementShare"));
    mixture.cementPurchaseUnit = readTrimmed(formData, nameOf("cementPurchaseUnit"));
    mixture.cementUnitWeightKg = normalizePurchaseWeight(
      readTrimmed(formData, nameOf("cementUnitWeightKg")),
      mixture.cementPurchaseUnit
    );
    mixture.cementUnitPrice = readTrimmed(formData, nameOf("cementUnitPrice"));
    mixture.sandShare = readTrimmed(formData, nameOf("sandShare"));
    mixture.sandPurchaseUnit = readTrimmed(formData, nameOf("sandPurchaseUnit"));
    mixture.sandUnitWeightKg = normalizePurchaseWeight(
      readTrimmed(formData, nameOf("sandUnitWeightKg")),
      mixture.sandPurchaseUnit
    );
    mixture.sandUnitPrice = readTrimmed(formData, nameOf("sandUnitPrice"));

    isValid =
      validatePositiveValue(
        form,
        `${errorPrefix}.cementShare`,
        mixture.cementShare,
        "Доля цемента должна быть больше 0."
      ) && isValid;
    isValid =
      validateSelectedValue(
        form,
        `${errorPrefix}.cementPurchaseUnit`,
        mixture.cementPurchaseUnit,
        ["bag", "tonne"],
        "Выберите единицу покупки цемента."
      ) && isValid;
    isValid =
      validatePositiveValue(
        form,
        `${errorPrefix}.cementUnitWeightKg`,
        mixture.cementUnitWeightKg,
          "Вес единицы цемента должен быть больше 0."
      ) && isValid;
    isValid =
      validatePositiveValue(
        form,
        `${errorPrefix}.cementUnitPrice`,
        mixture.cementUnitPrice,
        "Цена цемента должна быть больше 0."
      ) && isValid;
    isValid =
      validatePositiveValue(
        form,
        `${errorPrefix}.sandShare`,
        mixture.sandShare,
        "Доля песка должна быть больше 0."
      ) && isValid;
    isValid =
      validateSelectedValue(
        form,
        `${errorPrefix}.sandPurchaseUnit`,
        mixture.sandPurchaseUnit,
        ["bag", "tonne"],
        "Выберите единицу покупки песка."
      ) && isValid;
    isValid =
      validatePositiveValue(
        form,
        `${errorPrefix}.sandUnitWeightKg`,
        mixture.sandUnitWeightKg,
          "Вес единицы песка должен быть больше 0."
      ) && isValid;
    isValid =
      validatePositiveValue(
        form,
        `${errorPrefix}.sandUnitPrice`,
        mixture.sandUnitPrice,
        "Цена песка должна быть больше 0."
      ) && isValid;

    if (includeGravel) {
      mixture.gravelShare = readTrimmed(formData, nameOf("gravelShare"));
      mixture.gravelPurchaseUnit = readTrimmed(
        formData,
        nameOf("gravelPurchaseUnit")
      );
      mixture.gravelUnitWeightKg = normalizePurchaseWeight(
        readTrimmed(formData, nameOf("gravelUnitWeightKg")),
        mixture.gravelPurchaseUnit
      );
      mixture.gravelUnitPrice = readTrimmed(formData, nameOf("gravelUnitPrice"));

      isValid =
        validatePositiveValue(
          form,
          `${errorPrefix}.gravelShare`,
          mixture.gravelShare,
          "Доля щебня должна быть больше 0."
        ) && isValid;
      isValid =
        validateSelectedValue(
          form,
          `${errorPrefix}.gravelPurchaseUnit`,
          mixture.gravelPurchaseUnit,
          ["bag", "tonne"],
          "Выберите единицу покупки щебня."
        ) && isValid;
      isValid =
        validatePositiveValue(
          form,
          `${errorPrefix}.gravelUnitWeightKg`,
          mixture.gravelUnitWeightKg,
          "Вес единицы щебня должен быть больше 0."
        ) && isValid;
      isValid =
        validatePositiveValue(
          form,
          `${errorPrefix}.gravelUnitPrice`,
          mixture.gravelUnitPrice,
          "Цена щебня должна быть больше 0."
        ) && isValid;
    }

    return { mixture, isValid };
  }

  function buildPayload(form, formData) {
    const calculator = readTrimmed(formData, "calculator");
    const mode = readTrimmed(formData, "mode");
    const payload = {
      calculator,
      mode,
    };

    let isValid = validateBaseFields(form, payload);

    if (calculator === "slab_foundation") {
      const isAreaMode = mode === "area";
      const includeReinforcement = isAreaMode
        ? false
        : formData.get("includeReinforcement") !== null;
      const includeFormwork = isAreaMode
        ? false
        : formData.get("includeFormwork") !== null;
      payload.includeReinforcement = includeReinforcement;
      payload.includeFormwork = includeFormwork;
      payload.height = readTrimmed(formData, "height");

      isValid =
        validatePositiveField(
          form,
          payload,
          "height",
          "Высота должна быть больше 0."
        ) && isValid;

      if (mode === "dimensions") {
        payload.length = readTrimmed(formData, "length");
        payload.width = readTrimmed(formData, "width");

        isValid =
          validatePositiveField(
            form,
            payload,
            "length",
            "Длина должна быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "width",
            "Ширина должна быть больше 0."
          ) && isValid;
      } else if (mode === "area") {
        payload.area = readTrimmed(formData, "area");
        isValid =
          validatePositiveField(
            form,
            payload,
            "area",
            "Площадь должна быть больше 0."
          ) && isValid;

      } else {
        setFieldError(form, "mode", "Выберите режим dimensions или area.");
        isValid = false;
      }

      if (includeReinforcement) {
        payload.rebarDiameterMm = readTrimmed(formData, "rebarDiameterMm");
        payload.rebarStepMm = readTrimmed(formData, "rebarStepMm");
        payload.rebarLayers = readTrimmed(formData, "rebarLayers");
        payload.rebarReservePercent = readTrimmed(formData, "rebarReservePercent");

        isValid =
          validatePositiveField(
            form,
            payload,
            "rebarDiameterMm",
            "Диаметр арматуры должен быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "rebarStepMm",
            "Шаг арматуры должен быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "rebarLayers",
            "Количество слоев должно быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "rebarReservePercent",
            "Запас арматуры должен быть больше 0."
          ) && isValid;
      }

      if (includeFormwork) {
        payload.formworkHeightM = readTrimmed(formData, "formworkHeightM");
        payload.formworkReservePercent = readTrimmed(
          formData,
          "formworkReservePercent"
        );

        isValid =
          validatePositiveField(
            form,
            payload,
            "formworkHeightM",
            "Высота опалубки должна быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "formworkReservePercent",
            "Запас опалубки должен быть больше 0."
          ) && isValid;
      }

      const mixtureResult = buildMixturePayload(form, formData, "", {
        allowDryReady: false,
        includeGravel: true,
      });
      payload.mixture = mixtureResult.mixture;
      isValid = mixtureResult.isValid && isValid;
    } else if (calculator === "strip_foundation") {
      const includeReinforcement = formData.get("includeReinforcement") !== null;
      const includeFormwork = formData.get("includeFormwork") !== null;
      payload.includeReinforcement = includeReinforcement;
      payload.includeFormwork = includeFormwork;

      if (mode === "perimeter") {
        payload.totalLengthM = readTrimmed(formData, "totalLengthM");
        payload.widthM = readTrimmed(formData, "widthM");
        payload.heightM = readTrimmed(formData, "heightM");
        isValid =
          validatePositiveField(
            form,
            payload,
            "totalLengthM",
            "Общая длина ленты должна быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "widthM",
            "Ширина ленты должна быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "heightM",
            "Высота ленты должна быть больше 0."
          ) && isValid;
      } else if (mode === "house") {
        payload.houseLengthM = readTrimmed(formData, "houseLengthM");
        payload.houseWidthM = readTrimmed(formData, "houseWidthM");
        payload.widthM = String(form.querySelector("[data-strip-house-width-input]")?.value || "").trim();
        payload.heightM = String(form.querySelector("[data-strip-house-height-input]")?.value || "").trim();
        isValid =
          validatePositiveField(
            form,
            payload,
            "houseLengthM",
            "Длина дома должна быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "houseWidthM",
            "Ширина дома должна быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "widthM",
            "Ширина ленты должна быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "heightM",
            "Высота ленты должна быть больше 0."
          ) && isValid;
      } else if (mode === "segments") {
        const segmentNodes = form.querySelectorAll("[data-strip-segment-item]");
        payload.segments = Array.from(segmentNodes).map((segmentNode) =>
          buildStripSegmentPayload(segmentNode, includeReinforcement, includeFormwork)
        );
        if (!payload.segments.length) {
          setFieldError(form, "segments", "Добавьте хотя бы один участок.");
          isValid = false;
        }
        payload.segments.forEach((segment, index) => {
          const lengthField = `segments.${index}.segmentLengthM`;
          const widthField = `segments.${index}.segmentWidthM`;
          const heightField = `segments.${index}.segmentHeightM`;
          if (!isPositiveNumber(segment.segmentLengthM)) {
            setFieldError(form, lengthField, "Длина участка должна быть больше 0.");
            isValid = false;
          }
          if (!isPositiveNumber(segment.segmentWidthM)) {
            setFieldError(form, widthField, "Ширина участка должна быть больше 0.");
            isValid = false;
          }
          if (!isPositiveNumber(segment.segmentHeightM)) {
            setFieldError(form, heightField, "Высота участка должна быть больше 0.");
            isValid = false;
          }

          if (includeReinforcement && segment.segmentIncludeReinforcement && segment.segmentUseGlobalRebarParams === false) {
            if (!isPositiveNumber(segment.segmentLongitudinalBarsCount)) {
              setFieldError(
                form,
                `segments.${index}.segmentLongitudinalBarsCount`,
                "Количество продольных стержней должно быть больше 0."
              );
              isValid = false;
            }
            if (!isPositiveNumber(segment.segmentLongitudinalDiameterMm)) {
              setFieldError(
                form,
                `segments.${index}.segmentLongitudinalDiameterMm`,
                "Диаметр продольной арматуры должен быть больше 0."
              );
              isValid = false;
            }
            if (!isPositiveNumber(segment.segmentTransverseDiameterMm)) {
              setFieldError(
                form,
                `segments.${index}.segmentTransverseDiameterMm`,
                "Диаметр поперечной арматуры должен быть больше 0."
              );
              isValid = false;
            }
            if (!isPositiveNumber(segment.segmentTransverseStepMm)) {
              setFieldError(
                form,
                `segments.${index}.segmentTransverseStepMm`,
                "Шаг поперечной арматуры должен быть больше 0."
              );
              isValid = false;
            }
          }

          if (includeFormwork && segment.segmentIncludeFormwork && segment.segmentUseGlobalFormworkParams === false) {
            if (!isPositiveNumber(segment.segmentFormworkHeightM)) {
              setFieldError(
                form,
                `segments.${index}.segmentFormworkHeightM`,
                "Высота опалубки участка должна быть больше 0."
              );
              isValid = false;
            }
          }
        });
      } else {
        setFieldError(form, "mode", "Выберите режим perimeter, house или segments.");
        isValid = false;
      }

      if (includeReinforcement) {
        payload.longitudinalBarsCount = readTrimmed(formData, "longitudinalBarsCount");
        payload.longitudinalDiameterMm = readTrimmed(formData, "longitudinalDiameterMm");
        payload.longitudinalReservePercent = readTrimmed(formData, "longitudinalReservePercent");
        payload.transverseDiameterMm = readTrimmed(formData, "transverseDiameterMm");
        payload.transverseStepMm = readTrimmed(formData, "transverseStepMm");
        payload.transverseReservePercent = readTrimmed(formData, "transverseReservePercent");

        isValid =
          validatePositiveField(
            form,
            payload,
            "longitudinalBarsCount",
            "Количество продольных стержней должно быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "longitudinalDiameterMm",
            "Диаметр продольной арматуры должен быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "longitudinalReservePercent",
            "Запас продольной арматуры должен быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "transverseDiameterMm",
            "Диаметр поперечной арматуры должен быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "transverseStepMm",
            "Шаг поперечной арматуры должен быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "transverseReservePercent",
            "Запас поперечной арматуры должен быть больше 0."
          ) && isValid;
      }

      if (includeFormwork) {
        payload.formworkHeightM = readTrimmed(formData, "formworkHeightM");
        payload.formworkReservePercent = readTrimmed(formData, "formworkReservePercent");

        isValid =
          validatePositiveField(
            form,
            payload,
            "formworkHeightM",
            "Высота опалубки должна быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "formworkReservePercent",
            "Запас опалубки должен быть больше 0."
          ) && isValid;
      }

      const mixtureResult = buildMixturePayload(form, formData, "", {
        allowDryReady: false,
        includeGravel: true,
      });
      payload.mixture = mixtureResult.mixture;
      isValid = mixtureResult.isValid && isValid;
    } else if (calculator === "pile_foundation") {
      const includePiles = formData.get("includePiles") !== null;
      const includeGrillage = formData.get("includeGrillage") !== null;
      payload.includePiles = includePiles;
      payload.includeGrillage = includeGrillage;
      payload.pileType = readTrimmed(formData, "pileType") || "bored";

      if (!includePiles && !includeGrillage) {
        setFieldError(form, "general", "Включите расчёт свай или ростверка.");
        isValid = false;
      }

      if (includePiles) {
        payload.pilesCount = readTrimmed(formData, "pilesCount");
        if (!isPositiveInteger(payload.pilesCount)) {
          setFieldError(
            form,
            "pilesCount",
            "Укажите количество свай: целое число больше 0."
          );
          isValid = false;
        }

        if (payload.pileType === "bored") {
          payload.pileShaftDiameterM = readTrimmed(formData, "pileShaftDiameterM");
          payload.pileShaftHeightM = readTrimmed(formData, "pileShaftHeightM");
          payload.includePileBase = formData.get("includePileBase") !== null;
          payload.includePileReinforcement = formData.get("includePileReinforcement") !== null;
          isValid =
            validatePositiveField(
              form,
              payload,
              "pileShaftDiameterM",
              "Диаметр ствола сваи должен быть больше 0."
            ) && isValid;
          isValid =
            validatePositiveField(
              form,
              payload,
              "pileShaftHeightM",
              "Высота ствола сваи должна быть больше 0."
            ) && isValid;
          if (payload.includePileBase) {
            payload.pileBaseDiameterM = readTrimmed(formData, "pileBaseDiameterM");
            payload.pileBaseHeightM = readTrimmed(formData, "pileBaseHeightM");
            isValid =
              validatePositiveField(
                form,
                payload,
                "pileBaseDiameterM",
                "Диаметр уширения сваи должен быть больше 0."
              ) && isValid;
            isValid =
              validatePositiveField(
                form,
                payload,
                "pileBaseHeightM",
                "Высота уширения сваи должна быть больше 0."
              ) && isValid;
          }
          if (payload.includePileReinforcement) {
            payload.pileReinforcementBarsCount = readTrimmed(formData, "pileReinforcementBarsCount");
            payload.pileReinforcementDiameterMm = readTrimmed(formData, "pileReinforcementDiameterMm");
            payload.pileReinforcementReservePercent = readTrimmed(formData, "pileReinforcementReservePercent");
            if (!isPositiveInteger(payload.pileReinforcementBarsCount)) {
              setFieldError(form, "pileReinforcementBarsCount", "Количество стержней должно быть целым числом больше 0.");
              isValid = false;
            }
            isValid =
              validatePositiveField(
                form,
                payload,
                "pileReinforcementDiameterMm",
                "Диаметр арматуры свай должен быть больше 0."
              ) && isValid;
            isValid =
              validatePositiveField(
                form,
                payload,
                "pileReinforcementReservePercent",
                "Запас арматуры свай должен быть больше 0."
              ) && isValid;
          }
        } else {
          payload.includePileBase = false;
          payload.includePileReinforcement = false;
        }
      }

      if (includeGrillage) {
        const includeReinforcement = formData.get("includeReinforcement") !== null;
        const includeFormwork = formData.get("includeFormwork") !== null;
        payload.includeReinforcement = includeReinforcement;
        payload.includeFormwork = includeFormwork;

        if (mode === "perimeter") {
          payload.totalLengthM = readTrimmed(formData, "totalLengthM");
          payload.widthM = readTrimmed(formData, "widthM");
          payload.heightM = readTrimmed(formData, "heightM");
          isValid =
            validatePositiveField(
              form,
              payload,
              "totalLengthM",
              "Общая длина ростверка должна быть больше 0."
            ) && isValid;
          isValid =
            validatePositiveField(
              form,
              payload,
              "widthM",
              "Ширина ростверка должна быть больше 0."
            ) && isValid;
          isValid =
            validatePositiveField(
              form,
              payload,
              "heightM",
              "Высота ростверка должна быть больше 0."
            ) && isValid;
        } else if (mode === "house") {
          payload.houseLengthM = readTrimmed(formData, "houseLengthM");
          payload.houseWidthM = readTrimmed(formData, "houseWidthM");
          payload.widthM = String(form.querySelector("[data-strip-house-width-input]")?.value || "").trim();
          payload.heightM = String(form.querySelector("[data-strip-house-height-input]")?.value || "").trim();
          isValid =
            validatePositiveField(
              form,
              payload,
              "houseLengthM",
              "Длина дома должна быть больше 0."
            ) && isValid;
          isValid =
            validatePositiveField(
              form,
              payload,
              "houseWidthM",
              "Ширина дома должна быть больше 0."
            ) && isValid;
          isValid =
            validatePositiveField(
              form,
              payload,
              "widthM",
              "Ширина ростверка должна быть больше 0."
            ) && isValid;
          isValid =
            validatePositiveField(
              form,
              payload,
              "heightM",
              "Высота ростверка должна быть больше 0."
            ) && isValid;
        } else if (mode === "segments") {
          const segmentNodes = form.querySelectorAll("[data-strip-segment-item]");
          payload.segments = Array.from(segmentNodes).map((segmentNode) =>
            buildStripSegmentPayload(segmentNode, includeReinforcement, includeFormwork)
          );
          if (!payload.segments.length) {
            setFieldError(form, "segments", "Добавьте хотя бы один участок.");
            isValid = false;
          }
          payload.segments.forEach((segment, index) => {
            const lengthField = `segments.${index}.segmentLengthM`;
            const widthField = `segments.${index}.segmentWidthM`;
            const heightField = `segments.${index}.segmentHeightM`;
            if (!isPositiveNumber(segment.segmentLengthM)) {
              setFieldError(form, lengthField, "Длина участка должна быть больше 0.");
              isValid = false;
            }
            if (!isPositiveNumber(segment.segmentWidthM)) {
              setFieldError(form, widthField, "Ширина участка должна быть больше 0.");
              isValid = false;
            }
            if (!isPositiveNumber(segment.segmentHeightM)) {
              setFieldError(form, heightField, "Высота участка должна быть больше 0.");
              isValid = false;
            }
            if (includeReinforcement && segment.segmentIncludeReinforcement && segment.segmentUseGlobalRebarParams === false) {
              if (!isPositiveNumber(segment.segmentLongitudinalBarsCount)) {
                setFieldError(
                  form,
                  `segments.${index}.segmentLongitudinalBarsCount`,
                  "Количество продольных стержней должно быть больше 0."
                );
                isValid = false;
              }
              if (!isPositiveNumber(segment.segmentLongitudinalDiameterMm)) {
                setFieldError(
                  form,
                  `segments.${index}.segmentLongitudinalDiameterMm`,
                  "Диаметр продольной арматуры должен быть больше 0."
                );
                isValid = false;
              }
              if (!isPositiveNumber(segment.segmentTransverseDiameterMm)) {
                setFieldError(
                  form,
                  `segments.${index}.segmentTransverseDiameterMm`,
                  "Диаметр поперечной арматуры должен быть больше 0."
                );
                isValid = false;
              }
              if (!isPositiveNumber(segment.segmentTransverseStepMm)) {
                setFieldError(
                  form,
                  `segments.${index}.segmentTransverseStepMm`,
                  "Шаг поперечной арматуры должен быть больше 0."
                );
                isValid = false;
              }
            }
            if (includeFormwork && segment.segmentIncludeFormwork && segment.segmentUseGlobalFormworkParams === false) {
              if (!isPositiveNumber(segment.segmentFormworkHeightM)) {
                setFieldError(
                  form,
                  `segments.${index}.segmentFormworkHeightM`,
                  "Высота опалубки участка должна быть больше 0."
                );
                isValid = false;
              }
            }
          });
        } else {
          setFieldError(form, "mode", "Выберите режим perimeter, house или segments.");
          isValid = false;
        }

        if (includeReinforcement) {
          payload.longitudinalBarsCount = readTrimmed(formData, "longitudinalBarsCount");
          payload.longitudinalDiameterMm = readTrimmed(formData, "longitudinalDiameterMm");
          payload.longitudinalReservePercent = readTrimmed(formData, "longitudinalReservePercent");
          payload.transverseDiameterMm = readTrimmed(formData, "transverseDiameterMm");
          payload.transverseStepMm = readTrimmed(formData, "transverseStepMm");
          payload.transverseReservePercent = readTrimmed(formData, "transverseReservePercent");

          isValid =
            validatePositiveField(form, payload, "longitudinalBarsCount", "Количество продольных стержней должно быть больше 0.") && isValid;
          isValid =
            validatePositiveField(form, payload, "longitudinalDiameterMm", "Диаметр продольной арматуры должен быть больше 0.") && isValid;
          isValid =
            validatePositiveField(form, payload, "longitudinalReservePercent", "Запас продольной арматуры должен быть больше 0.") && isValid;
          isValid =
            validatePositiveField(form, payload, "transverseDiameterMm", "Диаметр поперечной арматуры должен быть больше 0.") && isValid;
          isValid =
            validatePositiveField(form, payload, "transverseStepMm", "Шаг поперечной арматуры должен быть больше 0.") && isValid;
          isValid =
            validatePositiveField(form, payload, "transverseReservePercent", "Запас поперечной арматуры должен быть больше 0.") && isValid;
        }

        if (includeFormwork) {
          payload.formworkHeightM = readTrimmed(formData, "formworkHeightM");
          payload.formworkReservePercent = readTrimmed(formData, "formworkReservePercent");
          isValid =
            validatePositiveField(form, payload, "formworkHeightM", "Высота опалубки должна быть больше 0.") && isValid;
          isValid =
            validatePositiveField(form, payload, "formworkReservePercent", "Запас опалубки должен быть больше 0.") && isValid;
        }
      } else {
        payload.includeReinforcement = false;
        payload.includeFormwork = false;
      }

      const useUnifiedConcreteMixtureSettings =
        formData.get("useUnifiedConcreteMixtureSettings") !== null;
      payload.useUnifiedConcreteMixtureSettings = useUnifiedConcreteMixtureSettings;

      if (useUnifiedConcreteMixtureSettings) {
        const mixtureResult = buildMixturePayload(form, formData, "", {
          allowDryReady: false,
          includeGravel: true,
        });
        payload.mixture = mixtureResult.mixture;
        isValid = mixtureResult.isValid && isValid;
      } else {
        const pileNeedsConcrete = includePiles && payload.pileType === "bored";
        if (pileNeedsConcrete) {
          const pileMixtureResult = buildMixturePayload(form, formData, "pile", {
            allowDryReady: false,
            includeGravel: true,
          });
          payload.pileMixture = pileMixtureResult.mixture;
          isValid = pileMixtureResult.isValid && isValid;
        }

        if (includeGrillage) {
          const grillageMixtureResult = buildMixturePayload(
            form,
            formData,
            "grillage",
            {
              allowDryReady: false,
              includeGravel: true,
            }
          );
          payload.grillageMixture = grillageMixtureResult.mixture;
          isValid = grillageMixtureResult.isValid && isValid;
        }
      }
    } else if (calculator === "brick") {
      const subType = readTrimmed(formData, "subType");
      payload.subType = subType || "bricks";
      payload.area = readTrimmed(formData, "area");

      isValid =
        validatePositiveField(
          form,
          payload,
          "area",
          "Площадь должна быть больше 0."
        ) && isValid;
    } else if (calculator === "screed") {
      const isAreaMode = mode === "area";
      const includeReinforcement = isAreaMode
        ? false
        : formData.get("includeReinforcement") !== null;
      payload.includeReinforcement = includeReinforcement;
      payload.height = readTrimmed(formData, "height");

      isValid =
        validatePositiveField(
          form,
          payload,
          "height",
          "Высота должна быть больше 0."
        ) && isValid;

      if (mode === "dimensions") {
        payload.length = readTrimmed(formData, "length");
        payload.width = readTrimmed(formData, "width");
        isValid =
          validatePositiveField(
            form,
            payload,
            "length",
            "Длина должна быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "width",
            "Ширина должна быть больше 0."
          ) && isValid;
      } else if (mode === "area") {
        payload.area = readTrimmed(formData, "area");
        isValid =
          validatePositiveField(
            form,
            payload,
            "area",
            "Площадь должна быть больше 0."
          ) && isValid;
      } else {
        setFieldError(form, "mode", "Выберите режим dimensions или area.");
        isValid = false;
      }

      if (includeReinforcement) {
        payload.rebarDiameterMm = readTrimmed(formData, "rebarDiameterMm");
        payload.rebarStepMm = readTrimmed(formData, "rebarStepMm");
        payload.rebarLayers = readTrimmed(formData, "rebarLayers");
        payload.rebarReservePercent = readTrimmed(formData, "rebarReservePercent");

        isValid =
          validatePositiveField(
            form,
            payload,
            "rebarDiameterMm",
            "Диаметр арматуры должен быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "rebarStepMm",
            "Шаг арматуры должен быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "rebarLayers",
            "Количество слоёв должно быть больше 0."
          ) && isValid;
        isValid =
          validatePositiveField(
            form,
            payload,
            "rebarReservePercent",
            "Запас арматуры должен быть больше 0."
          ) && isValid;
      }

      const mixtureResult = buildMixturePayload(form, formData, "", {
        allowDryReady: true,
        includeGravel: false,
      });
      payload.mixture = mixtureResult.mixture;
      isValid = mixtureResult.isValid && isValid;
    } else if (calculator === "drywall") {
      payload.area = readTrimmed(formData, "area");

      isValid =
        validatePositiveField(
          form,
          payload,
          "area",
          "Площадь должна быть больше 0."
        ) && isValid;
    } else if (calculator === "tile") {
      payload.area = readTrimmed(formData, "area");
      payload.tileLengthCm = readTrimmed(formData, "tileLengthCm");
      payload.tileWidthCm = readTrimmed(formData, "tileWidthCm");

      isValid =
        validatePositiveField(
          form,
          payload,
          "area",
          "Площадь должна быть больше 0."
        ) && isValid;
      isValid =
        validatePositiveField(
          form,
          payload,
          "tileLengthCm",
          "Длина плитки должна быть больше 0."
        ) && isValid;
      isValid =
        validatePositiveField(
          form,
          payload,
          "tileWidthCm",
          "Ширина плитки должна быть больше 0."
        ) && isValid;
    } else {
      setFieldError(form, "general", "Неизвестный тип калькулятора.");
      isValid = false;
    }

    return {
      isValid,
      payload,
    };
  }

  function setLoadingState(form, submitButton, isLoading) {
    if (!submitButton) {
      return;
    }

    if (!submitButton.dataset.defaultText) {
      submitButton.dataset.defaultText = submitButton.textContent || "Рассчитать";
    }

    submitButton.disabled = isLoading;
    submitButton.textContent = isLoading
      ? "Расчет..."
      : submitButton.dataset.defaultText;
    form.classList.toggle("is-loading", isLoading);
  }

  function handleValidationErrors(form, errors) {
    if (!errors || typeof errors !== "object") {
      setFieldError(form, "general", "Ошибка валидации. Проверьте данные формы.");
      return;
    }

    let hasMappedErrors = false;
    Object.entries(errors).forEach(([field, messages]) => {
      if (!Array.isArray(messages) || messages.length === 0) {
        return;
      }
      const hasField = form.querySelector(`[data-field-error="${field}"]`);
      if (hasField) {
        setFieldError(form, field, String(messages[0]));
        hasMappedErrors = true;
      } else {
        setFieldError(form, "general", String(messages[0]));
      }
    });

    if (!hasMappedErrors && !form.querySelector('[data-field-error="general"]')?.textContent) {
      setFieldError(form, "general", "Ошибка валидации. Проверьте данные формы.");
    }
    updateValidationSummary(form);
  }

  function toggleVisibility(node, isVisible) {
    if (!node) {
      return;
    }

    node.classList.toggle("brigmaster-estimator__field-group--hidden", !isVisible);
  }

  function setModeLockState(form, shouldLock) {
    const toggleNames = ["includeReinforcement", "includeFormwork"];
    toggleNames.forEach((name) => {
      const checkbox = form.querySelector(`[name="${name}"]`);
      if (!checkbox) {
        return;
      }
      const row = checkbox.closest("[data-toggle-field]");
      const lockTrigger = row?.querySelector("[data-mode-lock-trigger]");
      const lockAnchor = lockTrigger?.closest(".brigmaster-estimator__tooltip-anchor");
      if (shouldLock) {
        if (!Object.prototype.hasOwnProperty.call(checkbox.dataset, "previousChecked")) {
          checkbox.dataset.previousChecked = checkbox.checked ? "1" : "0";
        }
        checkbox.checked = false;
        checkbox.disabled = true;
        checkbox.setAttribute("aria-disabled", "true");
        row?.classList.add("is-disabled");
        lockAnchor?.classList.remove("brigmaster-estimator__tooltip-anchor--hidden");
        return;
      }

      checkbox.disabled = false;
      checkbox.removeAttribute("aria-disabled");
      if (Object.prototype.hasOwnProperty.call(checkbox.dataset, "previousChecked")) {
        checkbox.checked = checkbox.dataset.previousChecked === "1";
        delete checkbox.dataset.previousChecked;
      }
      row?.classList.remove("is-disabled");
      lockAnchor?.classList.add("brigmaster-estimator__tooltip-anchor--hidden");
    });
  }

  function syncSlabFoundationGroups(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator !== "slab_foundation") {
      return;
    }

    closeAllTooltips(form);
    const mode = form.querySelector('[name="mode"]')?.value || "dimensions";
    const includeReinforcementToggle = form.querySelector('[name="includeReinforcement"]');
    const includeFormworkToggle = form.querySelector('[name="includeFormwork"]');
    const isAreaMode = mode === "area";
    setModeLockState(form, isAreaMode);

    const includeReinforcement = !!includeReinforcementToggle?.checked;
    const includeFormwork = !!includeFormworkToggle?.checked;
    const dimensionsGroup = form.querySelector('[data-field-group="slab-dimensions"]');
    const areaGroup = form.querySelector('[data-field-group="slab-area"]');
    const heightGroup = form.querySelector('[data-field-group="slab-height"]');
    const noticeNode = form.querySelector("[data-area-mode-notice]");
    const reinforcementGroup = form.querySelector(
      '[data-field-group="slab-reinforcement"]'
    );
    const formworkGroup = form.querySelector('[data-field-group="slab-formwork"]');

    toggleVisibility(dimensionsGroup, mode === "dimensions");
    toggleVisibility(areaGroup, mode === "area");
    toggleVisibility(heightGroup, true);
    toggleVisibility(noticeNode, false);
    toggleVisibility(reinforcementGroup, mode === "dimensions" && includeReinforcement);
    toggleVisibility(formworkGroup, mode === "dimensions" && includeFormwork);
  }

  function createStripSegmentMarkup(index) {
    return `
      <article class="brigmaster-estimator__segment-card" data-strip-segment-item data-segment-index="${index}">
        <div class="brigmaster-estimator__segment-head">
          <h3 class="brigmaster-estimator__segment-title">Участок ${index + 1}</h3>
          <button type="button" class="brigmaster-estimator__segment-remove" data-strip-remove-segment>Удалить</button>
        </div>
        <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--three">
          <div class="brigmaster-estimator__field">
            <label for="segment-${index}-length">Длина участка (м)</label>
            <input id="segment-${index}-length" type="number" min="0.01" step="0.01" value="10" data-segment-input="segmentLengthM">
            <div class="brigmaster-estimator__error" data-segment-error-field="segmentLengthM" data-field-error="segments.${index}.segmentLengthM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label for="segment-${index}-width">Ширина участка (м)</label>
            <input id="segment-${index}-width" type="number" min="0.01" step="0.01" value="0.4" data-segment-input="segmentWidthM">
            <div class="brigmaster-estimator__error" data-segment-error-field="segmentWidthM" data-field-error="segments.${index}.segmentWidthM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label for="segment-${index}-height">Высота участка (м)</label>
            <input id="segment-${index}-height" type="number" min="0.01" step="0.01" value="1" data-segment-input="segmentHeightM">
            <div class="brigmaster-estimator__error" data-segment-error-field="segmentHeightM" data-field-error="segments.${index}.segmentHeightM" aria-live="polite"></div>
          </div>
        </div>
        <div class="brigmaster-estimator__segment-section" data-segment-rebar-root>
          <div class="brigmaster-estimator__segment-toggles">
            <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
              <input id="segment-${index}-include-rebar" type="checkbox" checked data-segment-include-reinforcement data-checkbox-key="segment-include-rebar">
              <label for="segment-${index}-include-rebar" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-include-rebar"><span>Учитывать арматуру для этого участка</span></label>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentIncludeReinforcement" data-field-error="segments.${index}.segmentIncludeReinforcement" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field brigmaster-estimator__toggle brigmaster-estimator__field-group" data-segment-rebar-settings>
              <input id="segment-${index}-use-global-rebar" type="checkbox" checked data-segment-use-global-rebar data-checkbox-key="segment-use-global-rebar">
              <label for="segment-${index}-use-global-rebar" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-use-global-rebar">
                <span>Использовать общие параметры</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: использовать общие параметры арматуры" aria-expanded="false" aria-controls="segment-${index}-use-global-rebar-tooltip">i</button>
                  <div id="segment-${index}-use-global-rebar-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    При включении для этого участка применяются общие настройки арматуры из глобального блока ниже.
                  </div>
                </span>
              </label>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentUseGlobalRebarParams" data-field-error="segments.${index}.segmentUseGlobalRebarParams" aria-live="polite"></div>
            </div>
          </div>
          <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four brigmaster-estimator__field-group brigmaster-estimator__field-group--hidden" data-segment-rebar-local>
            <div class="brigmaster-estimator__field">
              <label for="segment-${index}-longitudinal-bars-count" class="brigmaster-estimator__label-row">
                <span>Кол-во продольных стержней</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: количество продольных стержней" aria-expanded="false" aria-controls="segment-${index}-seg-long-bars-tooltip">i</button>
                  <div id="segment-${index}-seg-long-bars-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Число рабочих стержней в сечении этого участка. Обычно 4–6. Больше стержней — выше расход арматуры.
                  </div>
                </span>
              </label>
              <input id="segment-${index}-longitudinal-bars-count" type="number" min="1" step="1" value="4" data-segment-input="segmentLongitudinalBarsCount">
              <p class="brigmaster-estimator__hint">Обычно 4-6 стержней для частного дома.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentLongitudinalBarsCount" data-field-error="segments.${index}.segmentLongitudinalBarsCount" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label for="segment-${index}-longitudinal-diameter" class="brigmaster-estimator__label-row">
                <span>Диаметр продольной (мм)</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр продольной арматуры" aria-expanded="false" aria-controls="segment-${index}-seg-long-diameter-tooltip">i</button>
                  <div id="segment-${index}-seg-long-diameter-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Диаметр рабочих стержней в мм. Типично 10–14 мм. Чем больше диаметр, тем выше масса и прочность.
                  </div>
                </span>
              </label>
              <input id="segment-${index}-longitudinal-diameter" type="number" min="1" step="1" value="12" data-segment-input="segmentLongitudinalDiameterMm">
              <p class="brigmaster-estimator__hint">Чаще всего 10-14 мм.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentLongitudinalDiameterMm" data-field-error="segments.${index}.segmentLongitudinalDiameterMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label for="segment-${index}-transverse-diameter" class="brigmaster-estimator__label-row">
                <span>Диаметр поперечной (мм)</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр поперечной арматуры" aria-expanded="false" aria-controls="segment-${index}-seg-transverse-diameter-tooltip">i</button>
                  <div id="segment-${index}-seg-transverse-diameter-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Диаметр хомутов в мм. Обычно 6–10 мм. Влияет на массу поперечной арматуры.
                  </div>
                </span>
              </label>
              <input id="segment-${index}-transverse-diameter" type="number" min="1" step="1" value="8" data-segment-input="segmentTransverseDiameterMm">
              <p class="brigmaster-estimator__hint">Обычно 6-10 мм для хомутов.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentTransverseDiameterMm" data-field-error="segments.${index}.segmentTransverseDiameterMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label for="segment-${index}-transverse-step" class="brigmaster-estimator__label-row">
                <span>Шаг поперечной (мм)</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: шаг поперечной арматуры" aria-expanded="false" aria-controls="segment-${index}-seg-transverse-step-tooltip">i</button>
                  <div id="segment-${index}-seg-transverse-step-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Расстояние между хомутами в мм. Типично 200–400 мм. Меньший шаг — больше хомутов и расход стали.
                  </div>
                </span>
              </label>
              <input id="segment-${index}-transverse-step" type="number" min="10" step="10" value="300" data-segment-input="segmentTransverseStepMm">
              <p class="brigmaster-estimator__hint">Меньше шаг = больше хомутов и расход стали.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentTransverseStepMm" data-field-error="segments.${index}.segmentTransverseStepMm" aria-live="polite"></div>
            </div>
          </div>
        </div>
        <div class="brigmaster-estimator__segment-section" data-segment-formwork-root>
          <div class="brigmaster-estimator__segment-toggles">
            <div class="brigmaster-estimator__field brigmaster-estimator__toggle">
              <input id="segment-${index}-include-formwork" type="checkbox" checked data-segment-include-formwork data-checkbox-key="segment-include-formwork">
              <label for="segment-${index}-include-formwork" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-include-formwork"><span>Учитывать опалубку для этого участка</span></label>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentIncludeFormwork" data-field-error="segments.${index}.segmentIncludeFormwork" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field brigmaster-estimator__toggle brigmaster-estimator__field-group" data-segment-formwork-settings>
              <input id="segment-${index}-use-global-formwork" type="checkbox" checked data-segment-use-global-formwork data-checkbox-key="segment-use-global-formwork">
              <label for="segment-${index}-use-global-formwork" class="brigmaster-estimator__label-row" data-label-for-checkbox="segment-use-global-formwork">
                <span>Использовать общие параметры</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: использовать общие параметры опалубки" aria-expanded="false" aria-controls="segment-${index}-use-global-formwork-tooltip">i</button>
                  <div id="segment-${index}-use-global-formwork-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    При включении для этого участка применяются общие настройки опалубки из глобального блока ниже.
                  </div>
                </span>
              </label>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentUseGlobalFormworkParams" data-field-error="segments.${index}.segmentUseGlobalFormworkParams" aria-live="polite"></div>
            </div>
          </div>
          <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-group brigmaster-estimator__field-group--hidden" data-segment-formwork-local>
            <div class="brigmaster-estimator__field">
              <label for="segment-${index}-formwork-height">Высота опалубки участка (м)</label>
              <input id="segment-${index}-formwork-height" type="number" min="0.01" step="0.01" value="0.8" data-segment-input="segmentFormworkHeightM">
              <p class="brigmaster-estimator__hint">Считаются только боковые щиты участка.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentFormworkHeightM" data-field-error="segments.${index}.segmentFormworkHeightM" aria-live="polite"></div>
            </div>
          </div>
        </div>
      </article>
    `;
  }

  function reindexStripSegments(form) {
    const listNode = form.querySelector("[data-strip-segments-list]");
    if (!listNode) {
      return;
    }
    const segmentNodes = listNode.querySelectorAll("[data-strip-segment-item]");
    segmentNodes.forEach((segmentNode, index) => {
      segmentNode.dataset.segmentIndex = String(index);
      const titleNode = segmentNode.querySelector(".brigmaster-estimator__segment-title");
      if (titleNode) {
        titleNode.textContent = `Участок ${index + 1}`;
      }
      const errorNodes = segmentNode.querySelectorAll("[data-segment-error-field]");
      errorNodes.forEach((errorNode) => {
        const field = errorNode.getAttribute("data-segment-error-field");
        if (field) {
          errorNode.setAttribute("data-field-error", `segments.${index}.${field}`);
        }
      });
      const removeButton = segmentNode.querySelector("[data-strip-remove-segment]");
      if (removeButton) {
        removeButton.disabled = segmentNodes.length === 1;
      }

      const checkboxMappings = [
        { key: "segment-include-rebar", id: `segment-${index}-include-rebar` },
        { key: "segment-use-global-rebar", id: `segment-${index}-use-global-rebar` },
        { key: "segment-include-formwork", id: `segment-${index}-include-formwork` },
        { key: "segment-use-global-formwork", id: `segment-${index}-use-global-formwork` },
      ];
      checkboxMappings.forEach(({ key, id }) => {
        const checkbox = segmentNode.querySelector(`[data-checkbox-key="${key}"]`);
        const label = segmentNode.querySelector(`[data-label-for-checkbox="${key}"]`);
        if (checkbox) {
          checkbox.id = id;
        }
        if (label) {
          label.setAttribute("for", id);
        }
      });

      const inputMappings = [
        { field: "segmentLengthM", id: `segment-${index}-length` },
        { field: "segmentWidthM", id: `segment-${index}-width` },
        { field: "segmentHeightM", id: `segment-${index}-height` },
        { field: "segmentLongitudinalBarsCount", id: `segment-${index}-longitudinal-bars-count` },
        { field: "segmentLongitudinalDiameterMm", id: `segment-${index}-longitudinal-diameter` },
        { field: "segmentTransverseDiameterMm", id: `segment-${index}-transverse-diameter` },
        { field: "segmentTransverseStepMm", id: `segment-${index}-transverse-step` },
        { field: "segmentFormworkHeightM", id: `segment-${index}-formwork-height` },
      ];
      inputMappings.forEach(({ field, id }) => {
        const input = segmentNode.querySelector(`[data-segment-input="${field}"]`);
        if (input) {
          input.id = id;
          const fieldWrap = input.closest(".brigmaster-estimator__field");
          const label = fieldWrap?.querySelector("label[for]");
          if (label) {
            label.setAttribute("for", id);
          }
        }
      });

      const rebarSettings = segmentNode.querySelector("[data-segment-rebar-settings]");
      const formworkSettings = segmentNode.querySelector("[data-segment-formwork-settings]");
      const rebarLocal = segmentNode.querySelector("[data-segment-rebar-local]");
      const formworkLocal = segmentNode.querySelector("[data-segment-formwork-local]");
      const useGlobalRebarNode = segmentNode.querySelector("[data-segment-use-global-rebar]");
      const useGlobalFormworkNode = segmentNode.querySelector("[data-segment-use-global-formwork]");
      if (index === 0) {
        if (useGlobalRebarNode) {
          useGlobalRebarNode.checked = true;
          useGlobalRebarNode.disabled = true;
        }
        if (useGlobalFormworkNode) {
          useGlobalFormworkNode.checked = true;
          useGlobalFormworkNode.disabled = true;
        }
        toggleVisibility(rebarSettings, false);
        toggleVisibility(formworkSettings, false);
        toggleVisibility(rebarLocal, false);
        toggleVisibility(formworkLocal, false);
      } else {
        if (useGlobalRebarNode) {
          useGlobalRebarNode.disabled = false;
        }
        if (useGlobalFormworkNode) {
          useGlobalFormworkNode.disabled = false;
        }
      }
    });
  }

  function syncStripSegmentVisibility(segmentNode, includeReinforcementGlobal, includeFormworkGlobal) {
    const includeRebarNode = segmentNode.querySelector("[data-segment-include-reinforcement]");
    const useGlobalRebarNode = segmentNode.querySelector("[data-segment-use-global-rebar]");
    const rebarSettings = segmentNode.querySelector("[data-segment-rebar-settings]");
    const rebarLocal = segmentNode.querySelector("[data-segment-rebar-local]");
    const includeFormworkNode = segmentNode.querySelector("[data-segment-include-formwork]");
    const useGlobalFormworkNode = segmentNode.querySelector("[data-segment-use-global-formwork]");
    const formworkSettings = segmentNode.querySelector("[data-segment-formwork-settings]");
    const formworkLocal = segmentNode.querySelector("[data-segment-formwork-local]");

    const includeRebarSegment = includeReinforcementGlobal && !!includeRebarNode?.checked;
    const useGlobalRebar = !!useGlobalRebarNode?.checked;
    const includeFormworkSegment = includeFormworkGlobal && !!includeFormworkNode?.checked;
    const useGlobalFormwork = !!useGlobalFormworkNode?.checked;
    const isFirstSegment = segmentNode.dataset.segmentIndex === "0";

    if (useGlobalRebarNode) {
      const shouldDisableUseGlobalRebar =
        isFirstSegment || !includeReinforcementGlobal || !includeRebarSegment;
      useGlobalRebarNode.disabled = shouldDisableUseGlobalRebar;
      if (shouldDisableUseGlobalRebar) {
        useGlobalRebarNode.checked = true;
      }
    }
    if (useGlobalFormworkNode) {
      const shouldDisableUseGlobalFormwork =
        isFirstSegment || !includeFormworkGlobal || !includeFormworkSegment;
      useGlobalFormworkNode.disabled = shouldDisableUseGlobalFormwork;
      if (shouldDisableUseGlobalFormwork) {
        useGlobalFormworkNode.checked = true;
      }
    }

    toggleVisibility(rebarSettings, !isFirstSegment && includeReinforcementGlobal);
    toggleVisibility(
      rebarLocal,
      includeReinforcementGlobal &&
        includeRebarSegment &&
        !isFirstSegment &&
        !useGlobalRebarNode?.checked
    );
    toggleVisibility(formworkSettings, !isFirstSegment && includeFormworkGlobal);
    toggleVisibility(
      formworkLocal,
      includeFormworkGlobal &&
        includeFormworkSegment &&
        !isFirstSegment &&
        !useGlobalFormworkNode?.checked
    );
  }

  function syncStripFoundationGroups(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator !== "strip_foundation" && calculator !== "pile_foundation") {
      return;
    }

    const mode = form.querySelector('[name="mode"]')?.value || "perimeter";
    const includeGrillage = calculator === "pile_foundation"
      ? !!form.querySelector('[name="includeGrillage"]')?.checked
      : true;
    const includeReinforcement = includeGrillage && !!form.querySelector('[name="includeReinforcement"]')?.checked;
    const includeFormwork = includeGrillage && !!form.querySelector('[name="includeFormwork"]')?.checked;
    const perimeterGroup = form.querySelector('[data-field-group="strip-perimeter"]');
    const houseGroup = form.querySelector('[data-field-group="strip-house"]');
    const segmentsGroup = form.querySelector('[data-field-group="strip-segments"]');
    const globalRebarGroup = form.querySelector('[data-field-group="strip-reinforcement-global"]');
    const globalFormworkGroup = form.querySelector('[data-field-group="strip-formwork-global"]');
    const pileTypeGroup = form.querySelector('[data-field-group="pile-type"]');
    const pilePrimaryRowGroup = form.querySelector('[data-field-group="pile-primary-row"]');
    const pilePrimaryGrid = form.querySelector("[data-pile-primary-grid]");
    const pileShaftDiameterCell = form.querySelector('[data-pile-primary-cell="shaft-diameter"]');
    const pileShaftHeightCell = form.querySelector('[data-pile-primary-cell="shaft-height"]');
    const pileBaseToggleGroup = form.querySelector('[data-field-group="pile-base-toggle"]');
    const pileBaseFieldsGroup = form.querySelector('[data-field-group="pile-base-fields"]');
    const pileReinforcementToggleGroup = form.querySelector('[data-field-group="pile-reinforcement-toggle"]');
    const pileReinforcementFieldsGroup = form.querySelector('[data-field-group="pile-reinforcement-fields"]');
    const reinforcementToggleRow = form.querySelector('[data-toggle-field="strip-reinforcement"]');
    const formworkToggleRow = form.querySelector('[data-toggle-field="strip-formwork"]');
    const modeField = form.querySelector('[data-field-group="estimator-mode"]');

    if (calculator === "pile_foundation") {
      const includePiles = !!form.querySelector('[name="includePiles"]')?.checked;
      const pileType = form.querySelector('[name="pileType"]')?.value || "bored";
      const includePileBase = !!form.querySelector('[name="includePileBase"]')?.checked;
      const includePileReinforcement = !!form.querySelector('[name="includePileReinforcement"]')?.checked;
      const isBored = pileType === "bored";

      toggleVisibility(pileTypeGroup, includePiles);
      toggleVisibility(pilePrimaryRowGroup, includePiles);
      toggleVisibility(pileShaftDiameterCell, includePiles && isBored);
      toggleVisibility(pileShaftHeightCell, includePiles && isBored);
      if (pilePrimaryGrid) {
        pilePrimaryGrid.classList.toggle("is-bored-layout", includePiles && isBored);
      }
      toggleVisibility(modeField, includeGrillage);
      toggleVisibility(pileBaseToggleGroup, includePiles && isBored);
      toggleVisibility(pileBaseFieldsGroup, includePiles && isBored && includePileBase);
      toggleVisibility(pileReinforcementToggleGroup, includePiles && isBored);
      toggleVisibility(
        pileReinforcementFieldsGroup,
        includePiles && isBored && includePileReinforcement
      );
      toggleVisibility(reinforcementToggleRow, includeGrillage);
      toggleVisibility(formworkToggleRow, includeGrillage);

      const pilesPanel = form.querySelector('[data-pile-panel="piles"]');
      const grillagePanels = form.querySelectorAll('[data-pile-panel="grillage"]');
      if (pilesPanel instanceof HTMLDetailsElement) {
        pilesPanel.hidden = !includePiles;
      }
      grillagePanels.forEach((panel) => {
        if (panel instanceof HTMLDetailsElement) {
          panel.hidden = !includeGrillage;
        }
      });
    }

    toggleVisibility(perimeterGroup, includeGrillage && mode === "perimeter");
    toggleVisibility(houseGroup, includeGrillage && mode === "house");
    toggleVisibility(segmentsGroup, includeGrillage && mode === "segments");
    toggleVisibility(globalRebarGroup, includeReinforcement);
    toggleVisibility(globalFormworkGroup, includeFormwork);

    const segmentNodes = form.querySelectorAll("[data-strip-segment-item]");
    segmentNodes.forEach((segmentNode) => {
      syncStripSegmentVisibility(segmentNode, includeReinforcement, includeFormwork);
    });
  }

  function isMobileTooltipViewport() {
    return window.matchMedia("(max-width: 767px)").matches;
  }

  function setTooltipBackdropVisible(form, isVisible) {
    const backdrop = getEstimatorShell(form)?.querySelector("[data-tooltip-backdrop]");
    if (!backdrop) {
      return;
    }
    backdrop.hidden = !isVisible;
    backdrop.classList.toggle("is-visible", isVisible);
  }

  function closeAllTooltips(form) {
    const triggers = form.querySelectorAll("[data-tooltip-trigger]");
    triggers.forEach((trigger) => {
      const tooltipId = trigger.getAttribute("aria-controls");
      if (!tooltipId) {
        return;
      }
      const tooltip = form.querySelector(`#${tooltipId}`);
      if (!tooltip) {
        return;
      }
      trigger.setAttribute("aria-expanded", "false");
      tooltip.hidden = true;
      tooltip.classList.remove("is-open");
      tooltip.style.position = "";
      tooltip.style.left = "";
      tooltip.style.right = "";
      tooltip.style.top = "";
      tooltip.style.bottom = "";
      tooltip.style.maxWidth = "";
    });
    setTooltipBackdropVisible(form, false);
  }

  function positionTooltipWithinViewport(trigger, tooltip) {
    if (isMobileTooltipViewport()) {
      tooltip.style.position = "";
      tooltip.style.left = "";
      tooltip.style.right = "";
      tooltip.style.top = "";
      tooltip.style.bottom = "";
      tooltip.style.maxWidth = "";
      return;
    }

    const margin = 12;
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    const triggerRect = trigger.getBoundingClientRect();
    tooltip.style.position = "fixed";
    tooltip.style.right = "";
    tooltip.style.bottom = "";
    tooltip.style.maxWidth = `${Math.max(220, viewportWidth - margin * 2)}px`;
    tooltip.style.top = `${Math.round(triggerRect.bottom + 8)}px`;
    tooltip.style.left = `${Math.round(triggerRect.right - tooltip.offsetWidth)}px`;

    const rect = tooltip.getBoundingClientRect();
    let left = rect.left;
    let top = rect.top;

    if (rect.left < margin) {
      left = margin;
    }
    if (rect.right > viewportWidth - margin) {
      left = Math.max(margin, viewportWidth - margin - rect.width);
    }
    if (rect.bottom > viewportHeight - margin) {
      const aboveTop = triggerRect.top - rect.height - 8;
      top = aboveTop >= margin ? aboveTop : Math.max(margin, viewportHeight - margin - rect.height);
    }
    if (top < margin) {
      top = margin;
    }

    tooltip.style.left = `${Math.round(left)}px`;
    tooltip.style.top = `${Math.round(top)}px`;
  }

  function openTooltip(form, trigger, tooltip) {
    closeAllTooltips(form);
    trigger.setAttribute("aria-expanded", "true");
    tooltip.hidden = false;
    tooltip.classList.add("is-open");
    positionTooltipWithinViewport(trigger, tooltip);
    setTooltipBackdropVisible(form, isMobileTooltipViewport());
  }

  function toggleTooltip(form, trigger, shouldOpen) {
    const tooltipId = trigger.getAttribute("aria-controls");
    if (!tooltipId) {
      return;
    }
    const tooltip = form.querySelector(`#${tooltipId}`);
    if (!tooltip) {
      return;
    }

    if (shouldOpen) {
      openTooltip(form, trigger, tooltip);
      return;
    }

    trigger.setAttribute("aria-expanded", "false");
    tooltip.hidden = true;
    tooltip.classList.remove("is-open");
    setTooltipBackdropVisible(form, false);
  }

  function initTooltips(form) {
    const triggers = form.querySelectorAll("[data-tooltip-trigger]");
    if (!triggers.length) {
      return;
    }

    triggers.forEach((trigger) => {
      if (trigger.dataset.tooltipBound === "1") {
        return;
      }
      trigger.addEventListener("mouseenter", () => {
        if (!isMobileTooltipViewport()) {
          toggleTooltip(form, trigger, true);
        }
      });
      trigger.addEventListener("mouseleave", () => {
        if (!isMobileTooltipViewport()) {
          toggleTooltip(form, trigger, false);
        }
      });
      trigger.addEventListener("focus", () => toggleTooltip(form, trigger, true));
      trigger.addEventListener("blur", () => {
        if (!isMobileTooltipViewport()) {
          toggleTooltip(form, trigger, false);
        }
      });
      trigger.addEventListener("click", (event) => {
        event.preventDefault();
        if (isMobileTooltipViewport()) {
          const expanded = trigger.getAttribute("aria-expanded") === "true";
          toggleTooltip(form, trigger, !expanded);
        } else {
          toggleTooltip(form, trigger, true);
        }
      });
      trigger.dataset.tooltipBound = "1";
    });

    if (form.dataset.tooltipGlobalBound !== "1") {
      const backdrop = getEstimatorShell(form)?.querySelector("[data-tooltip-backdrop]");
      if (backdrop) {
        backdrop.addEventListener("click", () => {
          closeAllTooltips(form);
        });
      }

      document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
          closeAllTooltips(form);
        }
      });

      window.addEventListener("resize", () => {
        const openTrigger = form.querySelector('[data-tooltip-trigger][aria-expanded="true"]');
        if (!openTrigger) {
          return;
        }
        const tooltipId = openTrigger.getAttribute("aria-controls");
        if (!tooltipId) {
          return;
        }
        const tooltip = form.querySelector(`#${tooltipId}`);
        if (!tooltip || tooltip.hidden) {
          return;
        }
        positionTooltipWithinViewport(openTrigger, tooltip);
        setTooltipBackdropVisible(form, isMobileTooltipViewport());
      });

      form.dataset.tooltipGlobalBound = "1";
    }

  }

  function syncScreedFormGroups(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator !== "screed") {
      return;
    }
    closeAllTooltips(form);
    const mode = form.querySelector('[name="mode"]')?.value || "dimensions";
    const isAreaMode = mode === "area";
    setModeLockState(form, isAreaMode);

    const includeRebar = form.querySelector('[name="includeReinforcement"]')?.checked === true;
    const dimensionsGroup = form.querySelector('[data-field-group="screed-dimensions"]');
    const areaGroup = form.querySelector('[data-field-group="screed-area"]');
    const heightGroup = form.querySelector('[data-field-group="screed-height"]');
    const rebarGroup = form.querySelector('[data-field-group="screed-reinforcement"]');

    toggleVisibility(dimensionsGroup, mode === "dimensions");
    toggleVisibility(areaGroup, mode === "area");
    toggleVisibility(heightGroup, true);
    toggleVisibility(rebarGroup, mode === "dimensions" && includeRebar);
  }

  function initScreedForm(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator !== "screed") {
      return;
    }

    const modeSelect = form.querySelector('[name="mode"]');
    const includeRebar = form.querySelector('[name="includeReinforcement"]');

    syncScreedFormGroups(form);
    [modeSelect, includeRebar].forEach((node) => {
      if (!node) {
        return;
      }
      node.addEventListener("change", () => {
        clearErrors(form);
        markResultStale(form);
        syncScreedFormGroups(form);
      });
    });
  }

  function syncMixtureBlock(block) {
    if (!(block instanceof Element)) {
      return;
    }

    const select = block.querySelector("[data-mixture-type-select]");
    const type = select instanceof HTMLSelectElement ? select.value : "ready";
    const panels = block.querySelectorAll("[data-mixture-panel]");
    panels.forEach((panel) => {
      const panelType = panel.getAttribute("data-mixture-panel");
      const isVisible = panelType === type;
      toggleVisibility(panel, isVisible);
      panel
        .querySelectorAll("input, select")
        .forEach((control) => {
          if (
            control instanceof HTMLInputElement ||
            control instanceof HTMLSelectElement
          ) {
            control.disabled = !isVisible;
          }
        });
    });
  }

  function syncMixtureUnitFields(block, materialKey) {
    if (!(block instanceof Element)) {
      return;
    }

    const unitSelect = block.querySelector(`[name$="${materialKey}PurchaseUnit"]`);
    const weightLabel = block.querySelector(
      `[data-mixture-unit-label="${materialKey}"]`
    );
    const priceLabel = block.querySelector(
      `[data-mixture-price-label="${materialKey}"]`
    );
    const weightInput = block.querySelector(
      `[data-mixture-unit-input="${materialKey}"]`
    );

    if (!(unitSelect instanceof HTMLSelectElement)) {
      return;
    }

    const isTonne = unitSelect.value === "tonne";
    const unitText = isTonne ? "т" : "кг";
    const purchaseText = isTonne ? "тонны" : "мешка";

    if (weightLabel) {
      weightLabel.textContent = `Вес единицы ${materialKey === "cement" ? "цемента" : materialKey === "sand" ? "песка" : "щебня"} (${unitText})`;
    }
    if (priceLabel) {
      priceLabel.textContent = `Цена ${purchaseText} ${
        materialKey === "cement" ? "цемента" : materialKey === "sand" ? "песка" : "щебня"
      }`;
    }
    if (weightInput instanceof HTMLInputElement) {
      const previousUnit = weightInput.dataset.displayUnit || unitText;
      const currentValue = Number(weightInput.value);
      if (Number.isFinite(currentValue) && currentValue > 0 && previousUnit !== unitText) {
        weightInput.value = previousUnit === "кг" && unitText === "т"
          ? String(currentValue / 1000)
          : previousUnit === "т" && unitText === "кг"
            ? String(currentValue * 1000)
            : weightInput.value;
      }
      if (isTonne) {
        weightInput.step = "0.001";
        weightInput.min = "0.001";
      } else {
        weightInput.step = "1";
        weightInput.min = "1";
      }
      weightInput.dataset.displayUnit = unitText;
    }
  }

  function syncPileMixtureBlocks(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator !== "pile_foundation") {
      return;
    }

    const useUnified =
      form.querySelector('[name="useUnifiedConcreteMixtureSettings"]')?.checked !==
      false;
    const sharedBlock = form.querySelector('[data-pile-mixture-block="shared"]');
    const pileBlock = form.querySelector('[data-pile-mixture-block="pile"]');
    const grillageBlock = form.querySelector(
      '[data-pile-mixture-block="grillage"]'
    );

    toggleVisibility(sharedBlock, useUnified);
    toggleVisibility(pileBlock, !useUnified);
    toggleVisibility(grillageBlock, !useUnified);
  }

  function initMixtureFields(form) {
    const mixtureBlocks = form.querySelectorAll("[data-mixture-scope]");
    mixtureBlocks.forEach((block) => {
      syncMixtureBlock(block);
      ["cement", "sand", "gravel"].forEach((materialKey) => {
        syncMixtureUnitFields(block, materialKey);
        const unitSelect = block.querySelector(
          `[name$="${materialKey}PurchaseUnit"]`
        );
        if (unitSelect) {
          unitSelect.addEventListener("change", () => {
            clearErrors(form);
            markResultStale(form);
            syncMixtureUnitFields(block, materialKey);
          });
        }
      });
      const select = block.querySelector("[data-mixture-type-select]");
      if (select) {
        select.addEventListener("change", () => {
          clearErrors(form);
          markResultStale(form);
          syncMixtureBlock(block);
        });
      }
    });

    const pileUnifiedToggle = form.querySelector(
      '[name="useUnifiedConcreteMixtureSettings"]'
    );
    if (pileUnifiedToggle) {
      syncPileMixtureBlocks(form);
      pileUnifiedToggle.addEventListener("change", () => {
        clearErrors(form);
        markResultStale(form);
        syncPileMixtureBlocks(form);
      });
    }
  }

  function initSlabFoundationForm(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator !== "slab_foundation") {
      return;
    }

    const modeSelect = form.querySelector('[name="mode"]');
    const includeReinforcement = form.querySelector('[name="includeReinforcement"]');
    const includeFormwork = form.querySelector('[name="includeFormwork"]');

    syncSlabFoundationGroups(form);
    [modeSelect, includeReinforcement, includeFormwork].forEach((node) => {
      if (!node) {
        return;
      }
      node.addEventListener("change", () => {
        clearErrors(form);
        markResultStale(form);
        syncSlabFoundationGroups(form);
      });
    });
  }

  function initStripFoundationForm(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator !== "strip_foundation" && calculator !== "pile_foundation") {
      return;
    }

    const modeSelect = form.querySelector('[name="mode"]');
    const includeReinforcement = form.querySelector('[name="includeReinforcement"]');
    const includeFormwork = form.querySelector('[name="includeFormwork"]');
    const includePiles = form.querySelector('[name="includePiles"]');
    const includeGrillage = form.querySelector('[name="includeGrillage"]');
    const pileType = form.querySelector('[name="pileType"]');
    const includePileBase = form.querySelector('[name="includePileBase"]');
    const includePileReinforcement = form.querySelector('[name="includePileReinforcement"]');
    const addSegmentButton = form.querySelector("[data-strip-add-segment]");
    const segmentsList = form.querySelector("[data-strip-segments-list]");

    const refresh = () => {
      reindexStripSegments(form);
      syncStripFoundationGroups(form);
    };

    [modeSelect, includeReinforcement, includeFormwork, includePiles, includeGrillage, pileType, includePileBase, includePileReinforcement].forEach((node) => {
      if (!node) {
        return;
      }
      node.addEventListener("change", () => {
        clearErrors(form);
        markResultStale(form);
        refresh();
      });
    });

    if (addSegmentButton && segmentsList) {
      addSegmentButton.addEventListener("click", () => {
        clearErrors(form);
        markResultStale(form);
        const nextIndex = segmentsList.querySelectorAll("[data-strip-segment-item]").length;
        segmentsList.insertAdjacentHTML("beforeend", createStripSegmentMarkup(nextIndex));
        refresh();
        initTooltips(form);
      });

      segmentsList.addEventListener("click", (event) => {
        const target = event.target;
        if (!(target instanceof Element)) {
          return;
        }
        const removeButton = target.closest("[data-strip-remove-segment]");
        if (!removeButton) {
          return;
        }
        const segmentNodes = segmentsList.querySelectorAll("[data-strip-segment-item]");
        if (segmentNodes.length <= 1) {
          return;
        }
        const segmentNode = removeButton.closest("[data-strip-segment-item]");
        if (!segmentNode) {
          return;
        }
        segmentNode.remove();
        clearErrors(form);
        markResultStale(form);
        refresh();
      });

      segmentsList.addEventListener("change", (event) => {
        const target = event.target;
        if (!(target instanceof Element)) {
          return;
        }
        if (
          target.matches("[data-segment-include-reinforcement]") ||
          target.matches("[data-segment-use-global-rebar]") ||
          target.matches("[data-segment-include-formwork]") ||
          target.matches("[data-segment-use-global-formwork]")
        ) {
          clearErrors(form);
          markResultStale(form);
          refresh();
        }
      });
    }

    refresh();
  }

  function normalizePagePath() {
    const path = window.location.pathname || "/";
    if (path === "/") {
      return "/";
    }
    return path.endsWith("/") ? path : `${path}/`;
  }

  function buildMetrikaBaseParams(form, payload) {
    const calculator_type = String(
      form.querySelector('[name="calculator"]')?.value || "",
    );
    const page_path = normalizePagePath();
    const params = { calculator_type, page_path };
    if (payload && payload.mode != null && String(payload.mode) !== "") {
      params.mode = String(payload.mode);
    }
    return params;
  }

  function safeReachGoal(goalId, params) {
    const cfg = window.brigmasterEstimateFormData;
    if (!cfg?.metrikaEnabled || !cfg?.metrikaCounterId) {
      return;
    }
    if (typeof ym !== "function") {
      return;
    }
    try {
      ym(cfg.metrikaCounterId, "reachGoal", goalId, params || {});
    } catch (_err) {
      /* ignore */
    }
  }

  async function onSubmit(event) {
    event.preventDefault();

    const form = event.currentTarget;
    const endpoint = window.brigmasterEstimateFormData?.endpoint;
    const networkErrorMessage =
      window.brigmasterEstimateFormData?.networkErrorMessage ||
      "Не удалось выполнить запрос.";

    clearErrors(form);

    if (!endpoint) {
      safeReachGoal("brigmaster_calc_fail_config", {
        ...buildMetrikaBaseParams(form, {}),
        error_kind: "config",
      });
      setFieldError(form, "general", "Не настроен endpoint для расчета.");
      return;
    }

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const formData = new FormData(form);
    const { isValid, payload } = buildPayload(form, formData);
    const submitButton = form.querySelector('button[type="submit"]');
    let requestSucceeded = false;

    if (!isValid) {
      updateValidationSummary(form);
      safeReachGoal("brigmaster_calc_fail_client", {
        ...buildMetrikaBaseParams(form, payload),
        error_kind: "client_validation",
      });
      return;
    }

    form._lastRequestPayload = payload;
    form.dataset.suspendStaleTracking = "1";
    setLoadingState(form, submitButton, true);

    const baseParams = buildMetrikaBaseParams(form, payload);
    safeReachGoal("brigmaster_calc_request", { ...baseParams });

    try {
      const response = await fetch(endpoint, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
      });

      let data;
      try {
        data = await response.json();
      } catch (_parseError) {
        safeReachGoal("brigmaster_calc_fail_api", {
          ...baseParams,
          error_kind: "api_other",
          http_status: response.status,
        });
        setFieldError(form, "general", networkErrorMessage);
        return;
      }

      if (response.ok) {
        clearResult(form);
        showResult(form, data);
        requestSucceeded = true;
        safeReachGoal("brigmaster_calc_success", { ...baseParams });
        return;
      }

      const validationErrors = data?.errors || data?.validation_error?.errors;
      handleValidationErrors(form, validationErrors);
      const hasValidationErrors =
        validationErrors &&
        typeof validationErrors === "object" &&
        Object.keys(validationErrors).length > 0;
      const failApiParams = {
        ...baseParams,
        error_kind: hasValidationErrors ? "api_validation" : "api_other",
        http_status: response.status,
      };
      if (typeof data?.code === "string" && data.code !== "") {
        failApiParams.api_error_code = data.code;
      }
      safeReachGoal("brigmaster_calc_fail_api", failApiParams);
    } catch (_error) {
      safeReachGoal("brigmaster_calc_fail_network", {
        ...baseParams,
        error_kind: "network",
      });
      setFieldError(form, "general", networkErrorMessage);
    } finally {
      setLoadingState(form, submitButton, false);
      if (!requestSucceeded) {
        delete form.dataset.suspendStaleTracking;
      }
    }
  }

  function initForm(form) {
    initScreedForm(form);
    initSlabFoundationForm(form);
    initStripFoundationForm(form);
    initModeScenarioUi(form);
    initStaleOnFormChange(form);
    initMixtureFields(form);
    initTooltips(form);
    form.addEventListener("submit", onSubmit);
  }

  document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll(".brigmaster-estimate-form");
    forms.forEach((form) => initForm(form));
  });
})();
