(() => {
  function formatNumber(value) {
    const numericValue = Number(value);
    if (!Number.isFinite(numericValue)) {
      return "-";
    }

    return numericValue.toFixed(3).replace(/\.?0+$/, "");
  }

  function clearErrors(form) {
    const errorNodes = form.querySelectorAll("[data-field-error]");
    errorNodes.forEach((node) => {
      node.textContent = "";
    });
    form.classList.remove("has-errors");
  }

  function clearResult(form) {
    const resultNode = form.parentElement.querySelector("[data-result]");
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
    const schemeNode = form.parentElement.querySelector("[data-slab-scheme]");
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

    const reinforcementLayer = reinforcement
      ? `
      <g class="brigmaster-slab-scheme__rebar">
        <line x1="${slabX}" y1="${slabY}" x2="${slabRight}" y2="${slabY}" />
        <line x1="${slabX}" y1="${slabY + slabHeight * 0.33}" x2="${slabRight}" y2="${slabY + slabHeight * 0.33}" />
        <line x1="${slabX}" y1="${slabY + slabHeight * 0.66}" x2="${slabRight}" y2="${slabY + slabHeight * 0.66}" />
        <line x1="${slabX}" y1="${slabBottom}" x2="${slabRight}" y2="${slabBottom}" />
        <line x1="${slabX}" y1="${slabY}" x2="${slabX}" y2="${slabBottom}" />
        <line x1="${slabX + slabWidth * 0.2}" y1="${slabY}" x2="${slabX + slabWidth * 0.2}" y2="${slabBottom}" />
        <line x1="${slabX + slabWidth * 0.4}" y1="${slabY}" x2="${slabX + slabWidth * 0.4}" y2="${slabBottom}" />
        <line x1="${slabX + slabWidth * 0.6}" y1="${slabY}" x2="${slabX + slabWidth * 0.6}" y2="${slabBottom}" />
        <line x1="${slabX + slabWidth * 0.8}" y1="${slabY}" x2="${slabX + slabWidth * 0.8}" y2="${slabBottom}" />
        <line x1="${slabRight}" y1="${slabY}" x2="${slabRight}" y2="${slabBottom}" />
      </g>`
      : "";

    const formworkLayer = formwork
      ? `
      <rect x="${slabX - 12}" y="${slabY - 12}" width="${slabWidth + 24}" height="${slabHeight + 24}" class="brigmaster-slab-scheme__formwork" />`
      : "";

    const legendItems = [
      `<span><span class="brigmaster-slab-scheme__legend-mark brigmaster-slab-scheme__legend-mark--slab"></span>Серый контур - плита</span>`,
    ];
    if (reinforcement) {
      legendItems.push(
        `<span><span class="brigmaster-slab-scheme__legend-mark brigmaster-slab-scheme__legend-mark--rebar"></span>Сетка - арматура</span>`
      );
    }
    if (formwork) {
      legendItems.push(
        `<span><span class="brigmaster-slab-scheme__legend-mark brigmaster-slab-scheme__legend-mark--formwork"></span>Красный контур - опалубка</span>`
      );
    }
    const legendHtml = `<div class="brigmaster-slab-scheme__legend" aria-label="Легенда схемы">${legendItems.join(
      ""
    )}</div>`;

    schemeNode.innerHTML = `
      <h4 class="brigmaster-estimator__scheme-title">Схема плиты</h4>
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
    const resultNode = form.parentElement.querySelector("[data-result]");
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
          <h4>Арматура</h4>
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
          <h4>Опалубка</h4>
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
    resultNode.hidden = false;
    resultNode.classList.add("is-success");
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
      <h4>${escapeHtml(title)}</h4>
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
    const resultNode = form.parentElement.querySelector("[data-result]");
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

    resultNode.hidden = false;
    resultNode.classList.add("is-success");
  }

  function showPileFoundationResult(form, payload) {
    const resultNode = form.parentElement.querySelector("[data-result]");
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
    const grillageHeaderSection = resultNode.querySelector('[data-result-section="grillage-header"]');
    const concreteCard = resultNode.querySelector('[data-result-card="strip-concrete"]');
    const concreteLengthNode = resultNode.querySelector("[data-result-strip-concrete-length]");
    const concreteVolumeNode = resultNode.querySelector("[data-result-strip-concrete-volume]");
    const reinforcementCard = resultNode.querySelector('[data-result-card="strip-reinforcement"]');
    const formworkCard = resultNode.querySelector('[data-result-card="strip-formwork"]');
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

    resultNode.hidden = false;
    resultNode.classList.add("is-success");
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

    const resultNode = form.parentElement.querySelector("[data-result]");
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

    resultNode.hidden = false;
    resultNode.classList.add("is-success");
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
      payload.area = readTrimmed(formData, "area");
      payload.thickness = readTrimmed(formData, "thickness");

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
          "thickness",
          "Толщина должна быть больше 0."
        ) && isValid;
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
          <h4 class="brigmaster-estimator__segment-title">Участок ${index + 1}</h4>
          <button type="button" class="brigmaster-estimator__segment-remove" data-strip-remove-segment>Удалить</button>
        </div>
        <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--three">
          <div class="brigmaster-estimator__field">
            <label>Длина участка (м)</label>
            <input type="number" min="0.01" step="0.01" value="10" data-segment-input="segmentLengthM">
            <p class="brigmaster-estimator__hint">Длина конкретного участка ленты.</p>
            <div class="brigmaster-estimator__error" data-segment-error-field="segmentLengthM" data-field-error="segments.${index}.segmentLengthM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Ширина участка (м)</label>
            <input type="number" min="0.01" step="0.01" value="0.4" data-segment-input="segmentWidthM">
            <p class="brigmaster-estimator__hint">Ширина сечения именно этого участка.</p>
            <div class="brigmaster-estimator__error" data-segment-error-field="segmentWidthM" data-field-error="segments.${index}.segmentWidthM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Высота участка (м)</label>
            <input type="number" min="0.01" step="0.01" value="1" data-segment-input="segmentHeightM">
            <p class="brigmaster-estimator__hint">Высота сечения именно этого участка.</p>
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
              <label class="brigmaster-estimator__label-row">
                <span>Кол-во продольных стержней</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: количество продольных стержней" aria-expanded="false" aria-controls="segment-${index}-seg-long-bars-tooltip">i</button>
                  <div id="segment-${index}-seg-long-bars-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Число рабочих стержней в сечении этого участка. Обычно 4–6. Больше стержней — выше расход арматуры.
                  </div>
                </span>
              </label>
              <input type="number" min="1" step="1" value="4" data-segment-input="segmentLongitudinalBarsCount">
              <p class="brigmaster-estimator__hint">Обычно 4-6 стержней для частного дома.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentLongitudinalBarsCount" data-field-error="segments.${index}.segmentLongitudinalBarsCount" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label class="brigmaster-estimator__label-row">
                <span>Диаметр продольной (мм)</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр продольной арматуры" aria-expanded="false" aria-controls="segment-${index}-seg-long-diameter-tooltip">i</button>
                  <div id="segment-${index}-seg-long-diameter-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Диаметр рабочих стержней в мм. Типично 10–14 мм. Чем больше диаметр, тем выше масса и прочность.
                  </div>
                </span>
              </label>
              <input type="number" min="1" step="1" value="12" data-segment-input="segmentLongitudinalDiameterMm">
              <p class="brigmaster-estimator__hint">Чаще всего 10-14 мм.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentLongitudinalDiameterMm" data-field-error="segments.${index}.segmentLongitudinalDiameterMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label class="brigmaster-estimator__label-row">
                <span>Диаметр поперечной (мм)</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: диаметр поперечной арматуры" aria-expanded="false" aria-controls="segment-${index}-seg-transverse-diameter-tooltip">i</button>
                  <div id="segment-${index}-seg-transverse-diameter-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Диаметр хомутов в мм. Обычно 6–10 мм. Влияет на массу поперечной арматуры.
                  </div>
                </span>
              </label>
              <input type="number" min="1" step="1" value="8" data-segment-input="segmentTransverseDiameterMm">
              <p class="brigmaster-estimator__hint">Обычно 6-10 мм для хомутов.</p>
              <div class="brigmaster-estimator__error" data-segment-error-field="segmentTransverseDiameterMm" data-field-error="segments.${index}.segmentTransverseDiameterMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label class="brigmaster-estimator__label-row">
                <span>Шаг поперечной (мм)</span>
                <span class="brigmaster-estimator__tooltip-anchor">
                  <button type="button" class="brigmaster-estimator__tooltip-trigger" data-tooltip-trigger aria-label="Подсказка: шаг поперечной арматуры" aria-expanded="false" aria-controls="segment-${index}-seg-transverse-step-tooltip">i</button>
                  <div id="segment-${index}-seg-transverse-step-tooltip" class="brigmaster-estimator__tooltip" role="tooltip" hidden>
                    Расстояние между хомутами в мм. Типично 200–400 мм. Меньший шаг — больше хомутов и расход стали.
                  </div>
                </span>
              </label>
              <input type="number" min="1" step="10" value="300" data-segment-input="segmentTransverseStepMm">
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
              <label>Высота опалубки участка (м)</label>
              <input type="number" min="0.01" step="0.01" value="0.8" data-segment-input="segmentFormworkHeightM">
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

      const rebarSettings = segmentNode.querySelector("[data-segment-rebar-settings]");
      const formworkSettings = segmentNode.querySelector("[data-segment-formwork-settings]");
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
    const backdrop = form.parentElement.querySelector("[data-tooltip-backdrop]");
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
      const backdrop = form.parentElement.querySelector("[data-tooltip-backdrop]");
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
        clearResult(form);
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
        clearResult(form);
        refresh();
      });
    });

    if (addSegmentButton && segmentsList) {
      addSegmentButton.addEventListener("click", () => {
        clearErrors(form);
        clearResult(form);
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
        clearResult(form);
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
          clearResult(form);
          refresh();
        }
      });
    }

    refresh();
  }

  async function onSubmit(event) {
    event.preventDefault();

    const form = event.currentTarget;
    const endpoint = window.brigmasterEstimateFormData?.endpoint;
    const networkErrorMessage =
      window.brigmasterEstimateFormData?.networkErrorMessage ||
      "Не удалось выполнить запрос.";

    clearErrors(form);
    clearResult(form);

    if (!endpoint) {
      setFieldError(form, "general", "Не настроен endpoint для расчета.");
      return;
    }

    const formData = new FormData(form);
    const { isValid, payload } = buildPayload(form, formData);
    const submitButton = form.querySelector('button[type="submit"]');

    if (!isValid) {
      return;
    }

    form._lastRequestPayload = payload;
    setLoadingState(form, submitButton, true);

    try {
      const response = await fetch(endpoint, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json();

      if (response.ok) {
        showResult(form, data);
        return;
      }

      const validationErrors = data?.errors || data?.validation_error?.errors;
      handleValidationErrors(form, validationErrors);
    } catch (_error) {
      setFieldError(form, "general", networkErrorMessage);
    } finally {
      setLoadingState(form, submitButton, false);
    }
  }

  function initForm(form) {
    initSlabFoundationForm(form);
    initStripFoundationForm(form);
    initTooltips(form);
    form.addEventListener("submit", onSubmit);
  }

  document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll(".brigmaster-estimate-form");
    forms.forEach((form) => initForm(form));
  });
})();
