import { escapeHtml, formatNumber, hasMeaningfulNumber } from "../core/formatters.js";
import { clearErrors, closeAllTooltips, finalizeSuccessfulResult, getEstimatorShell, initTooltips, isMobileTooltipViewport, markResultStale, openTooltip, positionTooltipWithinViewport, readTrimmed, setFieldError, setModeLockState, setTooltipBackdropVisible, toggleTooltip, toggleVisibility } from "../core/form-state.js";
import { isPositiveInteger, isPositiveNumber, validateBaseFields, validatePositiveField, validateSelectedValue } from "../core/validation.js";
import { buildMixturePayload, syncPileMixtureBlocks } from "../core/mixture.js";
import { buildPileReinforcementColumnsHtml, renderMixtureCard, renderStripReinforcementCard, syncResultGridLayout } from "../ui/result-panel.js";
import { initEstimateForms } from "../core/bootstrap.js";


    export function renderSlabScheme(form, responsePayload, requestPayload) {
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


    export function showSlabResult(form, payload, requestPayload) {
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


    export function syncSlabFoundationGroups(form) {
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


    export function initSlabFoundationForm(form) {
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

export function buildPayload(form, formData) {
    const calculator = readTrimmed(formData, "calculator") || "slab_foundation";
    const mode = readTrimmed(formData, "mode");
    const payload = {
      calculator,
      mode,
    };

    let isValid = validateBaseFields(form, payload);


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

    return {
      isValid,
      payload,
    };
  }

const calculatorModule = {
  calculator: "slab_foundation",
  init: initSlabFoundationForm,
  buildPayload,
  showResult(form, payload) {
    showSlabResult(form, payload, form._lastRequestPayload || {});
  },
};

initEstimateForms(calculatorModule);
