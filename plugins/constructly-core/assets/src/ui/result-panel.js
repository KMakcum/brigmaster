import { escapeHtml, formatNumber } from "../core/formatters.js";
import { getEstimatorShell } from "../core/form-state.js";


    export function syncResultGridLayout(resultNode) {
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


    export function buildMixtureResultHtml(mixture, title) {
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


    export function renderMixtureCard(cardNode, mixture, title) {
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


    export function buildStripReinforcementHtml(reinforcement, title = "Арматура") {
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


    export function renderStripReinforcementCard(reinforcementCard, reinforcement, title = "Арматура") {
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

    export function buildPileReinforcementColumnsHtml(pileReinforcement) {
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
