import { escapeHtml, formatNumber, hasMeaningfulNumber } from "../core/formatters.js";
import { clearErrors, closeAllTooltips, finalizeSuccessfulResult, getEstimatorShell, initTooltips, isMobileTooltipViewport, markResultStale, openTooltip, positionTooltipWithinViewport, readTrimmed, setFieldError, setModeLockState, setTooltipBackdropVisible, toggleTooltip, toggleVisibility } from "../core/form-state.js";
import { isPositiveInteger, isPositiveNumber, validateBaseFields, validatePositiveField, validateSelectedValue } from "../core/validation.js";
import { buildMixturePayload, syncPileMixtureBlocks } from "../core/mixture.js";
import { buildPileReinforcementColumnsHtml, renderMixtureCard, renderStripReinforcementCard, syncResultGridLayout } from "../ui/result-panel.js";
import { initEstimateForms } from "../core/bootstrap.js";


    export function showTileResult(form, payload) {
        const resultNode = getEstimatorShell(form)?.querySelector("[data-result]");
        if (!resultNode) {
            return;
        }

        const summaryCard = resultNode.querySelector('[data-result-card="tile-summary"]');
        const layoutCard = resultNode.querySelector('[data-result-card="tile-layout"]');
        const adhesiveCard = resultNode.querySelector('[data-result-card="tile-adhesive"]');
        const groutCard = resultNode.querySelector('[data-result-card="tile-grout"]');
        const costsCard = resultNode.querySelector('[data-result-card="tile-costs"]');
        const geometry = payload?.geometry || {};
        const tile = payload?.tile || {};
        const layout = payload?.layout || {};
        const cutouts = payload?.cutouts || {};
        const openings = payload?.openings || {};
        const adhesive = payload?.adhesive || {};
        const grout = payload?.grout || {};
        const costs = payload?.costs || {};

        if (summaryCard) {
            const openingsRow =
                Number(openings.count) > 0 || Number(geometry.openingsAreaM2) > 0
                    ? `<p><strong>Проёмы:</strong> ${formatNumber(geometry.openingsAreaM2)} м²</p>`
                    : "";
            const cutoutsRow =
                Number(cutouts.count) > 0 || Number(geometry.cutoutsAreaM2) > 0
                    ? `<p><strong>Вырезы и отверстия:</strong> ${formatNumber(geometry.cutoutsAreaM2)} м²</p>`
                    : "";
            const cutoutWasteRow =
                Number(tile.countCutoutWaste) > 0
                    ? `<p><strong>Потери на вырезы:</strong> ${formatNumber(tile.countCutoutWaste)} шт</p>`
                    : "";
            summaryCard.innerHTML = `
        <h3>Плитка</h3>
        <p><strong>Общая площадь:</strong> ${formatNumber(geometry.grossAreaM2)} м²</p>
        ${openingsRow}
        ${cutoutsRow}
        <p><strong>Чистая площадь:</strong> ${formatNumber(geometry.netAreaM2)} м²</p>
        <p><strong>Плиток без запаса:</strong> ${formatNumber(tile.countBase)} шт</p>
        ${cutoutWasteRow}
        <p><strong>Плиток с запасом:</strong> ${formatNumber(tile.countWithReserve)} шт</p>
        <p><strong>К покупке:</strong> ${formatNumber(tile.countToBuy)} шт</p>
      `;
        }

        if (layoutCard) {
            if (layout.canRender) {
                const warning = layout.hasNarrowCutWarning
                    ? `<p class="brigmaster-estimator__result-note">${escapeHtml(layout.warningText || "")}</p>`
                    : "";
                layoutCard.innerHTML = `
          <h3>Раскладка</h3>
          <p><strong>Плиток по длине:</strong> ${formatNumber(layout.tilesAlongLength)}</p>
          <p><strong>Рядов:</strong> ${formatNumber(layout.rowsCount)}</p>
          <p><strong>Остаток по длине:</strong> ${formatNumber(layout.remainderLengthM)} м</p>
          <p><strong>Остаток по ширине:</strong> ${formatNumber(layout.remainderWidthM)} м</p>
          <p><strong>Крайняя подрезка по длине:</strong> ${formatNumber(layout.edgeTrimLengthMm)} мм</p>
          <p><strong>Крайняя подрезка по ширине:</strong> ${formatNumber(layout.edgeTrimWidthMm)} мм</p>
          ${warning}
        `;
            } else {
                layoutCard.innerHTML = `
          <h3>Раскладка</h3>
          <p class="brigmaster-estimator__result-note">${escapeHtml(
                    layout.note ||
                    "Для ориентировочной раскладки нужны размеры прямоугольной зоны. В режиме по площади показываем только ориентир по материалам."
                )}</p>
        `;
            }
        }

        if (adhesiveCard) {
            if (adhesive.enabled) {
                adhesiveCard.hidden = false;
                const costText = hasMeaningfulNumber(adhesive.costExact)
                    ? `<p><strong>Стоимость:</strong> ${formatNumber(adhesive.costExact)} / ${formatNumber(adhesive.costRounded)} руб</p>`
                    : "";
                adhesiveCard.innerHTML = `
          <h3>Клей</h3>
          <p><strong>Расход:</strong> ${formatNumber(adhesive.requiredKg)} кг</p>
          <p><strong>Нужно мешков:</strong> ${formatNumber(adhesive.requiredBags)}</p>
          <p><strong>К покупке:</strong> ${formatNumber(adhesive.bagsToBuy)} меш.</p>
          ${costText}
        `;
            } else {
                adhesiveCard.hidden = true;
            }
        }

        if (groutCard) {
            if (grout.enabled) {
                groutCard.hidden = false;
                const costText = hasMeaningfulNumber(grout.costExact)
                    ? `<p><strong>Стоимость:</strong> ${formatNumber(grout.costExact)} / ${formatNumber(grout.costRounded)} руб</p>`
                    : "";
                groutCard.innerHTML = `
          <h3>Затирка</h3>
          <p><strong>Расход:</strong> ${formatNumber(grout.requiredKg)} кг</p>
          <p><strong>Нужно упаковок:</strong> ${formatNumber(grout.requiredPacks)}</p>
          <p><strong>К покупке:</strong> ${formatNumber(grout.packsToBuy)} уп.</p>
          ${costText}
        `;
            } else {
                groutCard.hidden = true;
            }
        }

        if (costsCard) {
            const hasCosts = [
                costs.tileCostExact,
                costs.adhesiveCostExact,
                costs.groutCostExact,
                costs.totalExact,
            ].some((value) => hasMeaningfulNumber(value));
            if (hasCosts) {
                const rows = [];
                if (hasMeaningfulNumber(costs.tileCostExact)) {
                    rows.push(`<p><strong>Плитка:</strong> ${formatNumber(costs.tileCostExact)} руб</p>`);
                }
                if (hasMeaningfulNumber(costs.adhesiveCostExact)) {
                    rows.push(`<p><strong>Клей:</strong> ${formatNumber(costs.adhesiveCostExact)} / ${formatNumber(costs.adhesiveCostRounded)} руб</p>`);
                }
                if (hasMeaningfulNumber(costs.groutCostExact)) {
                    rows.push(`<p><strong>Затирка:</strong> ${formatNumber(costs.groutCostExact)} / ${formatNumber(costs.groutCostRounded)} руб</p>`);
                }
                if (hasMeaningfulNumber(costs.totalExact)) {
                    rows.push(`<p><strong>Итого:</strong> ${formatNumber(costs.totalExact)} / ${formatNumber(costs.totalRounded)} руб</p>`);
                }
                costsCard.hidden = false;
                costsCard.innerHTML = `<h3>Стоимость</h3>${rows.join("")}<p class="brigmaster-estimator__result-note">Если есть упаковка, сначала показывается точная оценка, затем ориентир к покупке.</p>`;
            } else {
                costsCard.hidden = true;
            }
        }

        syncResultGridLayout(resultNode);
        resultNode.hidden = false;
        resultNode.classList.add("is-success");
        finalizeSuccessfulResult(form);
    }


    export function buildTileRepeatItems(form, groupName) {
        const list = form.querySelector(`[data-tile-repeat-list="${groupName}"]`);
        if (!list) {
            return [];
        }

        return Array.from(list.querySelectorAll("[data-tile-repeat-item]")).map((itemNode) => {
            const type = itemNode.getAttribute("data-tile-item-type") || "opening";
            if (type === "cutout") {
                const shape =
                    String(itemNode.querySelector('[data-tile-repeat-input="shape"]')?.value || "").trim() ||
                    "circle";

                return {
                    shape,
                    diameterMm: String(itemNode.querySelector('[data-tile-repeat-input="diameterMm"]')?.value || "").trim(),
                    widthMm: String(itemNode.querySelector('[data-tile-repeat-input="widthMm"]')?.value || "").trim(),
                    heightMm: String(itemNode.querySelector('[data-tile-repeat-input="heightMm"]')?.value || "").trim(),
                    count: String(itemNode.querySelector('[data-tile-repeat-input="count"]')?.value || "").trim(),
                };
            }

            return {
                type: String(itemNode.querySelector('[data-tile-repeat-input="type"]')?.value || "").trim(),
                widthM: String(itemNode.querySelector('[data-tile-repeat-input="widthM"]')?.value || "").trim(),
                heightM: String(itemNode.querySelector('[data-tile-repeat-input="heightM"]')?.value || "").trim(),
                count: String(itemNode.querySelector('[data-tile-repeat-input="count"]')?.value || "").trim(),
            };
        });
    }


    export function validateTileOpenings(form, items) {
        let isValid = true;
        items.forEach((item, index) => {
            if (!isPositiveNumber(item.widthM)) {
                setFieldError(form, `tileOpenings.${index}.widthM`, "Проём: ширина должна быть больше 0.");
                isValid = false;
            }
            if (!isPositiveNumber(item.heightM)) {
                setFieldError(form, `tileOpenings.${index}.heightM`, "Проём: высота должна быть больше 0.");
                isValid = false;
            }
            if (!isPositiveInteger(item.count)) {
                setFieldError(form, `tileOpenings.${index}.count`, "Проём: количество должно быть целым числом больше 0.");
                isValid = false;
            }
        });
        return isValid;
    }


    export function validateTileCutouts(form, items) {
        let isValid = true;
        items.forEach((item, index) => {
            if (!isPositiveInteger(item.count)) {
                setFieldError(form, `tileCutouts.${index}.count`, "Вырез: количество должно быть целым числом больше 0.");
                isValid = false;
            }
            if (item.shape === "circle") {
                if (!isPositiveNumber(item.diameterMm)) {
                    setFieldError(form, `tileCutouts.${index}.diameterMm`, "Вырез: диаметр должен быть больше 0.");
                    isValid = false;
                }
                return;
            }
            if (!isPositiveNumber(item.widthMm)) {
                setFieldError(form, `tileCutouts.${index}.widthMm`, "Вырез: ширина должна быть больше 0.");
                isValid = false;
            }
            if (!isPositiveNumber(item.heightMm)) {
                setFieldError(form, `tileCutouts.${index}.heightMm`, "Вырез: высота должна быть больше 0.");
                isValid = false;
            }
        });
        return isValid;
    }


    export function createTileRepeatMarkup(groupName, type, index) {
        if (type === "cutout") {
            return `
        <article class="brigmaster-estimator__segment-card" data-tile-repeat-item data-tile-item-type="cutout" data-tile-group="${escapeHtml(
                groupName
            )}">
          <div class="brigmaster-estimator__segment-head">
            <h4 class="brigmaster-estimator__segment-title">Вырез или отверстие ${index + 1}</h4>
            <button type="button" class="brigmaster-estimator__segment-remove" data-tile-remove-item>Удалить</button>
          </div>
          <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four">
            <div class="brigmaster-estimator__field">
              <label>Что это за элемент</label>
              <select data-tile-repeat-input="shape">
                <option value="circle">Круглое отверстие</option>
                <option value="rect">Прямоугольный вырез</option>
              </select>
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${index}.shape" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field" data-tile-shape-circle>
              <label>Диаметр отверстия (мм)</label>
              <input type="number" min="1" step="1" value="80" data-tile-repeat-input="diameterMm">
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${index}.diameterMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field brigmaster-estimator__field-group--hidden" data-tile-shape-rect>
              <label>Ширина выреза (мм)</label>
              <input type="number" min="1" step="1" value="150" data-tile-repeat-input="widthMm">
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${index}.widthMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field brigmaster-estimator__field-group--hidden" data-tile-shape-rect>
              <label>Высота выреза (мм)</label>
              <input type="number" min="1" step="1" value="150" data-tile-repeat-input="heightMm">
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${index}.heightMm" aria-live="polite"></div>
            </div>
            <div class="brigmaster-estimator__field">
              <label>Количество</label>
              <input type="number" min="1" step="1" value="1" data-tile-repeat-input="count">
              <div class="brigmaster-estimator__error" data-field-error="tileCutouts.${index}.count" aria-live="polite"></div>
            </div>
          </div>
        </article>
      `;
        }

        return `
      <article class="brigmaster-estimator__segment-card" data-tile-repeat-item data-tile-item-type="opening" data-tile-group="${escapeHtml(
            groupName
        )}">
        <div class="brigmaster-estimator__segment-head">
          <h4 class="brigmaster-estimator__segment-title">Окно или дверь ${index + 1}</h4>
          <button type="button" class="brigmaster-estimator__segment-remove" data-tile-remove-item>Удалить</button>
        </div>
        <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four">
          <div class="brigmaster-estimator__field">
            <label>Что вычитаем</label>
            <select data-tile-repeat-input="type">
              <option value="window">Окно</option>
              <option value="door">Дверь</option>
            </select>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Ширина (м)</label>
            <input type="number" min="0.01" step="0.01" value="1.2" data-tile-repeat-input="widthM">
            <div class="brigmaster-estimator__error" data-field-error="tileOpenings.${index}.widthM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Высота (м)</label>
            <input type="number" min="0.01" step="0.01" value="1.4" data-tile-repeat-input="heightM">
            <div class="brigmaster-estimator__error" data-field-error="tileOpenings.${index}.heightM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Количество</label>
            <input type="number" min="1" step="1" value="1" data-tile-repeat-input="count">
            <div class="brigmaster-estimator__error" data-field-error="tileOpenings.${index}.count" aria-live="polite"></div>
          </div>
        </div>
      </article>
    `;
    }


    export function syncTileCutoutCard(itemNode) {
        if (!itemNode) {
            return;
        }
        const shape = String(
            itemNode.querySelector('[data-tile-repeat-input="shape"]')?.value || "circle"
        ).trim();
        itemNode
            .querySelectorAll("[data-tile-shape-circle]")
            .forEach((node) =>
                node.classList.toggle("brigmaster-estimator__field-group--hidden", shape !== "circle")
            );
        itemNode
            .querySelectorAll("[data-tile-shape-rect]")
            .forEach((node) =>
                node.classList.toggle("brigmaster-estimator__field-group--hidden", shape !== "rect")
            );
    }


    export function reindexTileRepeatList(listNode) {
        const groupName = listNode.getAttribute("data-tile-repeat-list") || "";
        const type = listNode.getAttribute("data-tile-item-type") || "opening";
        const listId = listNode.id || groupName || "tile-repeat";
        const items = listNode.querySelectorAll("[data-tile-repeat-item]");
        items.forEach((itemNode, index) => {
            const baseId = `${listId}-${index}`;
            const titleNode = itemNode.querySelector(".brigmaster-estimator__segment-title");
            if (titleNode) {
                titleNode.textContent = `${
                    type === "cutout" ? "Вырез или отверстие" : "Окно или дверь"
                } ${index + 1}`;
            }
            const removeButton = itemNode.querySelector("[data-tile-remove-item]");
            if (removeButton) {
                removeButton.disabled = items.length === 1;
            }
            itemNode.querySelectorAll("[data-tile-repeat-input]").forEach((inputNode) => {
                const fieldKey = inputNode.getAttribute("data-tile-repeat-input") || "value";
                const inputId = `${baseId}-${fieldKey}`;
                inputNode.id = inputId;
                inputNode.name = `${groupName}[${index}][${fieldKey}]`;
                const fieldNode = inputNode.closest(".brigmaster-estimator__field");
                const labelNode = fieldNode?.querySelector("label");
                if (labelNode) {
                    labelNode.setAttribute("for", inputId);
                }
            });
            syncTileCutoutCard(type === "cutout" ? itemNode : null);
        });
    }


    export function syncTileFormGroups(form) {
        const calculator = form.querySelector('[name="calculator"]')?.value;
        if (calculator !== "tile") {
            return;
        }

        const target = form.querySelector('[name="tileTarget"]')?.value || "floor";
        const mode = form.querySelector('[name="mode"]')?.value || "dimensions";
        const pattern = form.querySelector('[name="tileLayingPattern"]')?.value || "direct";
        const includeOpenings = form.querySelector('[name="tileIncludeOpenings"]')?.checked;
        const includeCutouts = form.querySelector('[name="tileIncludeCutouts"]')?.checked;
        const includeAdhesive = form.querySelector('[name="tileIncludeAdhesive"]')?.checked;
        const includeGrout = form.querySelector('[name="tileIncludeGrout"]')?.checked;
        const thicknessInput = form.querySelector("[data-tile-thickness-input]");
        const reserveInput = form.querySelector("[data-tile-reserve-input]");

        toggleVisibility(form.querySelector('[data-field-group="tile-dimensions"]'), mode === "dimensions");
        toggleVisibility(form.querySelector('[data-field-group="tile-area"]'), mode === "area");
        toggleVisibility(form.querySelector('[data-field-group="tile-wall-height"]'), target === "wall" && mode === "dimensions");
        toggleVisibility(form.querySelector('[data-field-group="tile-openings-toggle"]'), target === "wall");
        toggleVisibility(form.querySelector("[data-tile-openings-root]"), target === "wall" && includeOpenings);
        toggleVisibility(form.querySelector("[data-tile-cutouts-root]"), !!includeCutouts);
        toggleVisibility(form.querySelector("[data-tile-adhesive-fields]"), !!includeAdhesive);
        toggleVisibility(form.querySelector("[data-tile-grout-fields]"), !!includeGrout);
        toggleVisibility(form.querySelector('[data-field-group="tile-offset"]'), pattern === "offset");

        form.querySelectorAll("[data-tile-length-label]").forEach((node) => {
            node.textContent = target === "wall" ? "Длина комнаты (м)" : "Длина помещения (м)";
        });
        form.querySelectorAll("[data-tile-width-label]").forEach((node) => {
            node.textContent = target === "wall" ? "Ширина комнаты (м)" : "Ширина помещения (м)";
        });

        if (thicknessInput && !thicknessInput.dataset.userChanged) {
            thicknessInput.value = target === "wall" ? "8" : "9";
        }

        if (reserveInput && !reserveInput.dataset.userChanged) {
            reserveInput.value =
                pattern === "diagonal" ? "10" : pattern === "offset" ? "7" : "5";
        }
    }


    export function initTileForm(form) {
        const calculator = form.querySelector('[name="calculator"]')?.value;
        if (calculator !== "tile") {
            return;
        }

        const targetSelect = form.querySelector('[name="tileTarget"]');
        const modeSelect = form.querySelector('[name="mode"]');
        const patternSelect = form.querySelector('[name="tileLayingPattern"]');
        const openingsToggle = form.querySelector('[name="tileIncludeOpenings"]');
        const cutoutsToggle = form.querySelector('[name="tileIncludeCutouts"]');
        const adhesiveToggle = form.querySelector('[name="tileIncludeAdhesive"]');
        const groutToggle = form.querySelector('[name="tileIncludeGrout"]');
        const reserveInput = form.querySelector("[data-tile-reserve-input]");
        const thicknessInput = form.querySelector("[data-tile-thickness-input]");

        [reserveInput, thicknessInput].forEach((input) => {
            input?.addEventListener("input", () => {
                input.dataset.userChanged = "1";
            });
        });

        const refresh = () => {
            form.querySelectorAll("[data-tile-repeat-list]").forEach((listNode) => {
                reindexTileRepeatList(listNode);
            });
            syncTileFormGroups(form);
        };

        [targetSelect, modeSelect, patternSelect, openingsToggle, cutoutsToggle, adhesiveToggle, groutToggle].forEach(
            (node) => {
                node?.addEventListener("change", () => {
                    clearErrors(form);
                    markResultStale(form);
                    refresh();
                });
            }
        );

        form.querySelectorAll("[data-tile-add-item]").forEach((button) => {
            button.addEventListener("click", () => {
                const groupName = button.getAttribute("data-tile-add-item");
                const listNode = groupName
                    ? form.querySelector(`[data-tile-repeat-list="${groupName}"]`)
                    : null;
                const type = listNode?.getAttribute("data-tile-item-type") || "opening";
                if (!listNode) {
                    return;
                }
                const nextIndex = listNode.querySelectorAll("[data-tile-repeat-item]").length;
                listNode.insertAdjacentHTML(
                    "beforeend",
                    createTileRepeatMarkup(groupName, type, nextIndex)
                );
                clearErrors(form);
                markResultStale(form);
                refresh();
                initTooltips(form);
            });
        });

        form.querySelectorAll("[data-tile-repeat-list]").forEach((listNode) => {
            if (!listNode.querySelector("[data-tile-repeat-item]")) {
                const type = listNode.getAttribute("data-tile-item-type") || "opening";
                const groupName = listNode.getAttribute("data-tile-repeat-list") || "";
                listNode.insertAdjacentHTML("beforeend", createTileRepeatMarkup(groupName, type, 0));
            }

            listNode.addEventListener("click", (event) => {
                const target = event.target;
                if (!(target instanceof Element)) {
                    return;
                }
                const removeButton = target.closest("[data-tile-remove-item]");
                if (!removeButton) {
                    return;
                }
                const items = listNode.querySelectorAll("[data-tile-repeat-item]");
                if (items.length <= 1) {
                    return;
                }
                const itemNode = removeButton.closest("[data-tile-repeat-item]");
                if (!itemNode) {
                    return;
                }
                itemNode.remove();
                clearErrors(form);
                markResultStale(form);
                refresh();
            });

            listNode.addEventListener("change", (event) => {
                const target = event.target;
                if (!(target instanceof Element)) {
                    return;
                }
                if (target.matches('[data-tile-repeat-input="shape"]')) {
                    syncTileCutoutCard(target.closest("[data-tile-repeat-item]"));
                }
            });
        });

        refresh();
    }

export function buildPayload(form, formData) {
    const calculator = readTrimmed(formData, "calculator") || "tile";
    const mode = readTrimmed(formData, "mode");
    const payload = {
      calculator,
      mode,
    };

    let isValid = validateBaseFields(form, payload);


            payload.tileTarget = readTrimmed(formData, "tileTarget") || "floor";
            payload.tileLayingPattern =
                readTrimmed(formData, "tileLayingPattern") || "direct";
            payload.length = readTrimmed(formData, "length");
            payload.width = readTrimmed(formData, "width");
            payload.height = readTrimmed(formData, "height");
            payload.area = readTrimmed(formData, "area");
            payload.tileLengthMm = readTrimmed(formData, "tileLengthMm");
            payload.tileWidthMm = readTrimmed(formData, "tileWidthMm");
            payload.tileThicknessMm = readTrimmed(formData, "tileThicknessMm");
            payload.tileJointMm = readTrimmed(formData, "tileJointMm");
            payload.tileOffsetPercent = readTrimmed(formData, "tileOffsetPercent");
            payload.reservePercent = readTrimmed(formData, "reservePercent");
            payload.tilePricePerM2 = readTrimmed(formData, "tilePricePerM2");
            payload.tileIncludeOpenings =
                payload.tileTarget === "wall" && formData.get("tileIncludeOpenings") !== null;
            payload.tileIncludeCutouts = formData.get("tileIncludeCutouts") !== null;
            payload.tileIncludeAdhesive = formData.get("tileIncludeAdhesive") !== null;
            payload.tileIncludeGrout = formData.get("tileIncludeGrout") !== null;

            isValid =
                validateSelectedValue(
                    form,
                    "tileTarget",
                    payload.tileTarget,
                    ["floor", "wall"],
                    "Выберите, что облицовываем."
                ) && isValid;
            isValid =
                validateSelectedValue(
                    form,
                    "tileLayingPattern",
                    payload.tileLayingPattern,
                    ["direct", "offset", "diagonal"],
                    "Выберите способ укладки."
                ) && isValid;

            if (mode === "dimensions") {
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
                if (payload.tileTarget === "wall") {
                    isValid =
                        validatePositiveField(
                            form,
                            payload,
                            "height",
                            "Высота стен должна быть больше 0."
                        ) && isValid;
                }
            } else {
                isValid =
                    validatePositiveField(
                        form,
                        payload,
                        "area",
                        "Площадь должна быть больше 0."
                    ) && isValid;
            }

            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "tileLengthMm",
                    "Длина плитки должна быть больше 0."
                ) && isValid;
            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "tileWidthMm",
                    "Ширина плитки должна быть больше 0."
                ) && isValid;
            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "tileThicknessMm",
                    "Толщина плитки должна быть больше 0."
                ) && isValid;
            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "tileJointMm",
                    "Ширина шва должна быть больше 0."
                ) && isValid;
            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "reservePercent",
                    "Запас должен быть больше 0."
                ) && isValid;

            if (payload.tileLayingPattern === "offset") {
                isValid =
                    validatePositiveField(
                        form,
                        payload,
                        "tileOffsetPercent",
                        "Смещение должно быть больше 0."
                    ) && isValid;
            }

            if (payload.tileTarget === "wall" && payload.tileIncludeOpenings) {
                payload.tileOpenings = buildTileRepeatItems(form, "tileOpenings");
                isValid = validateTileOpenings(form, payload.tileOpenings) && isValid;
            }

            if (payload.tileIncludeCutouts) {
                payload.tileCutouts = buildTileRepeatItems(form, "tileCutouts");
                isValid = validateTileCutouts(form, payload.tileCutouts) && isValid;
            }

            if (payload.tileIncludeAdhesive) {
                payload.tileAdhesiveConsumptionKgPerM2 = readTrimmed(
                    formData,
                    "tileAdhesiveConsumptionKgPerM2"
                );
                payload.tileAdhesiveLayerMm = readTrimmed(formData, "tileAdhesiveLayerMm");
                payload.tileAdhesiveBagWeightKg = readTrimmed(
                    formData,
                    "tileAdhesiveBagWeightKg"
                );
                payload.tileAdhesiveBagPrice = readTrimmed(formData, "tileAdhesiveBagPrice");

                isValid =
                    validatePositiveField(
                        form,
                        payload,
                        "tileAdhesiveConsumptionKgPerM2",
                        "Расход клея должен быть больше 0."
                    ) && isValid;
                isValid =
                    validatePositiveField(
                        form,
                        payload,
                        "tileAdhesiveLayerMm",
                        "Толщина слоя клея должна быть больше 0."
                    ) && isValid;
                isValid =
                    validatePositiveField(
                        form,
                        payload,
                        "tileAdhesiveBagWeightKg",
                        "Вес мешка клея должен быть больше 0."
                    ) && isValid;
            }

            if (payload.tileIncludeGrout) {
                payload.tileGroutDensityKgPerM3 = readTrimmed(
                    formData,
                    "tileGroutDensityKgPerM3"
                );
                payload.tileGroutPackWeightKg = readTrimmed(
                    formData,
                    "tileGroutPackWeightKg"
                );
                payload.tileGroutPackPrice = readTrimmed(formData, "tileGroutPackPrice");

                isValid =
                    validatePositiveField(
                        form,
                        payload,
                        "tileGroutDensityKgPerM3",
                        "Плотность затирки должна быть больше 0."
                    ) && isValid;
                isValid =
                    validatePositiveField(
                        form,
                        payload,
                        "tileGroutPackWeightKg",
                        "Вес упаковки затирки должен быть больше 0."
                    ) && isValid;
            }

    return {
      isValid,
      payload,
    };
  }

const calculatorModule = {
  calculator: "tile",
  init: initTileForm,
  buildPayload,
  showResult: showTileResult,
};

initEstimateForms(calculatorModule);
