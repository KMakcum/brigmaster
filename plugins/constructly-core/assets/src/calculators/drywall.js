import { escapeHtml, formatNumber, hasMeaningfulNumber } from "../core/formatters.js";
import { clearErrors, closeAllTooltips, finalizeSuccessfulResult, getEstimatorShell, initTooltips, isMobileTooltipViewport, markResultStale, openTooltip, positionTooltipWithinViewport, readTrimmed, setFieldError, setModeLockState, setTooltipBackdropVisible, toggleTooltip, toggleVisibility } from "../core/form-state.js";
import { isPositiveInteger, isPositiveNumber, validateBaseFields, validatePositiveField, validateSelectedValue } from "../core/validation.js";
import { buildMixturePayload, syncPileMixtureBlocks } from "../core/mixture.js";
import { buildPileReinforcementColumnsHtml, renderMixtureCard, renderStripReinforcementCard, syncResultGridLayout } from "../ui/result-panel.js";
import { initEstimateForms } from "../core/bootstrap.js";


    export function showDrywallResult(form, payload) {
        const resultNode = getEstimatorShell(form)?.querySelector("[data-result]");
        if (!resultNode) {
            return;
        }

        const geometryCard = resultNode.querySelector('[data-result-card="drywall-geometry"]');
        const sheetsCard = resultNode.querySelector('[data-result-card="drywall-sheets"]');
        const profilesCard = resultNode.querySelector('[data-result-card="drywall-profiles"]');
        const fastenersCard = resultNode.querySelector('[data-result-card="drywall-fasteners"]');
        const finishingCard = resultNode.querySelector('[data-result-card="drywall-finishing"]');
        const costsCard = resultNode.querySelector('[data-result-card="drywall-costs"]');
        const notesCard = resultNode.querySelector('[data-result-card="drywall-notes"]');

        const geometry = payload?.geometry || {};
        const sheets = payload?.sheets || {};
        const profiles = payload?.profiles || {};
        const fasteners = payload?.fasteners || {};
        const finishing = payload?.finishing || {};
        const costs = payload?.costs || {};
        const notes = payload?.notes || {};

        if (geometryCard) {
            const openingsRow =
                Number(geometry.openingsAreaM2) > 0
                    ? `<p><strong>Проёмы:</strong> ${formatNumber(geometry.openingsAreaM2)} м²</p>`
                    : "";
            const partitionRow =
                Number(geometry.partitionThicknessMm) > 0
                    ? `<p><strong>Толщина перегородки:</strong> ${formatNumber(geometry.partitionThicknessMm)} мм</p>`
                    : "";
            const endCladdingRow =
                Number(geometry.endCladdingAreaM2) > 0
                    ? `<p><strong>Торцы проёмов:</strong> ${formatNumber(geometry.endCladdingAreaM2)} м²</p>`
                    : "";
            geometryCard.innerHTML = `
        <h3>Геометрия</h3>
        <p><strong>Общая площадь:</strong> ${formatNumber(geometry.grossAreaM2)} м²</p>
        ${openingsRow}
        <p><strong>Чистая площадь:</strong> ${formatNumber(geometry.netAreaM2)} м²</p>
        <p><strong>Площадь обшивки без запаса:</strong> ${formatNumber(geometry.boardAreaExactM2)} м²</p>
        <p><strong>Площадь обшивки с запасом:</strong> ${formatNumber(geometry.boardAreaWithReserveM2)} м²</p>
        ${partitionRow}
        ${endCladdingRow}
      `;
        }

        if (sheetsCard) {
            sheetsCard.innerHTML = `
        <h3>Листы ГКЛ</h3>
        <p><strong>Формат листа:</strong> ${formatNumber(sheets.sheetLengthMm)}×${formatNumber(sheets.sheetWidthMm)} мм</p>
        <p><strong>Толщина:</strong> ${formatNumber(sheets.sheetThicknessMm)} мм</p>
        <p><strong>Слоёв обшивки:</strong> ${formatNumber(sheets.layers)}</p>
        <p><strong>Листов без запаса:</strong> ${formatNumber(sheets.countExact)} шт</p>
        <p><strong>Листов с запасом:</strong> ${formatNumber(sheets.countWithReserve)} шт</p>
        <p><strong>К покупке:</strong> ${formatNumber(sheets.countToBuy)} шт</p>
      `;
        }

        if (profilesCard) {
            const rows = [];
            if (profiles.enabled) {
                [
                    profiles.guide,
                    profiles.main,
                    profiles.cross,
                ].forEach((item) => {
                    if (!item || Number(item.lengthM) <= 0) {
                        return;
                    }
                    rows.push(
                        `<p><strong>${escapeHtml(item.label || "Профиль")}:</strong> ${formatNumber(
                            item.lengthM
                        )} м, к покупке ${formatNumber(item.lengthToBuyM)} м</p>`
                    );
                });
                if (!rows.length) {
                    rows.push('<p class="brigmaster-estimator__result-note">По заданной геометрии профиль не требуется.</p>');
                }
            } else {
                rows.push(
                    `<p class="brigmaster-estimator__result-note">${escapeHtml(
                        notes.profilesAreaMode ||
                        "Для профилей и крепежа нужен режим по размерам."
                    )}</p>`
                );
            }
            profilesCard.innerHTML = `<h3>Профили</h3>${rows.join("")}<p class="brigmaster-estimator__result-note">Сначала показан расчётный метраж, затем округление вверх для закупки.</p>`;
        }

        if (fastenersCard) {
            const rows = [];
            [
                fasteners.boardScrews,
                fasteners.connectorScrews,
                fasteners.dowels,
                fasteners.hangers,
                fasteners.crabs,
            ].forEach((item) => {
                if (!item || Number(item.countBase) <= 0) {
                    return;
                }
                rows.push(
                    `<p><strong>${escapeHtml(item.label || "Позиция")}:</strong> по расчёту ${formatNumber(
                        item.countBase
                    )} шт, с запасом ${formatNumber(item.countWithReserve)} шт</p>`
                );
            });

            if (!rows.length) {
                rows.push(
                    `<p class="brigmaster-estimator__result-note">${escapeHtml(
                        notes.profilesAreaMode ||
                        "Крепёж по каркасу считается только в режиме по размерам."
                    )}</p>`
                );
            }

            fastenersCard.innerHTML = `<h3>Метизы и крепёж</h3>${rows.join("")}<p class="brigmaster-estimator__result-note">Для всех штучных позиций показаны базовое количество и количество с запасом.</p>`;
        }

        if (finishingCard) {
            if (finishing.enabled) {
                finishingCard.hidden = false;
                finishingCard.innerHTML = `
          <h3>Отделка</h3>
          <p><strong>Грунтовка:</strong> ${formatNumber(finishing.primerKg)} кг</p>
          <p><strong>Шпатлёвка для швов:</strong> ${formatNumber(finishing.jointPuttyKg)} кг</p>
          <p><strong>Финишная шпатлёвка:</strong> ${formatNumber(finishing.finishPuttyKg)} кг</p>
          <p><strong>Армирующая лента:</strong> ${formatNumber(finishing.tapeLm)} м</p>
        `;
            } else {
                finishingCard.hidden = true;
            }
        }

        if (costsCard) {
            const costRows = [];
            if (hasMeaningfulNumber(costs.sheetCost)) {
                costRows.push(`<p><strong>Листы ГКЛ:</strong> ${formatNumber(costs.sheetCost)} руб</p>`);
            }
            if (hasMeaningfulNumber(costs.profileCost)) {
                costRows.push(`<p><strong>Профили:</strong> ${formatNumber(costs.profileCost)} руб</p>`);
            }
            if (hasMeaningfulNumber(costs.fastenersCost)) {
                costRows.push(`<p><strong>Метизы:</strong> ${formatNumber(costs.fastenersCost)} руб</p>`);
            }
            if (hasMeaningfulNumber(costs.primerCost)) {
                costRows.push(`<p><strong>Грунтовка:</strong> ${formatNumber(costs.primerCost)} руб</p>`);
            }
            if (hasMeaningfulNumber(costs.jointPuttyCost)) {
                costRows.push(`<p><strong>Шпатлёвка для швов:</strong> ${formatNumber(costs.jointPuttyCost)} руб</p>`);
            }
            if (hasMeaningfulNumber(costs.finishPuttyCost)) {
                costRows.push(`<p><strong>Финишная шпатлёвка:</strong> ${formatNumber(costs.finishPuttyCost)} руб</p>`);
            }
            if (hasMeaningfulNumber(costs.tapeCost)) {
                costRows.push(`<p><strong>Лента:</strong> ${formatNumber(costs.tapeCost)} руб</p>`);
            }
            if (hasMeaningfulNumber(costs.total)) {
                costRows.push(`<p><strong>Итого:</strong> ${formatNumber(costs.total)} руб</p>`);
            }

            if (costRows.length) {
                costsCard.hidden = false;
                costsCard.innerHTML = `<h3>Стоимость</h3>${costRows.join("")}`;
            } else {
                costsCard.hidden = true;
            }
        }

        if (notesCard) {
            const noteParts = [];
            if (notes.method) {
                noteParts.push(`<p>${escapeHtml(notes.method)}</p>`);
            }
            if (notes.profilesAreaMode) {
                noteParts.push(`<p class="brigmaster-estimator__result-note">${escapeHtml(notes.profilesAreaMode)}</p>`);
            }
            notesCard.innerHTML = `<h3>Примечания</h3>${noteParts.join("")}`;
        }

        syncResultGridLayout(resultNode);
        resultNode.hidden = false;
        resultNode.classList.add("is-success");
        finalizeSuccessfulResult(form);
    }


    export function buildDrywallRepeatItems(form) {
        const list = form.querySelector('[data-drywall-repeat-list="drywallOpenings"]');
        if (!list) {
            return [];
        }

        return Array.from(list.querySelectorAll("[data-drywall-repeat-item]")).map((itemNode) => ({
            type: String(itemNode.querySelector('[data-drywall-repeat-input="type"]')?.value || "").trim(),
            widthM: String(itemNode.querySelector('[data-drywall-repeat-input="widthM"]')?.value || "").trim(),
            heightM: String(itemNode.querySelector('[data-drywall-repeat-input="heightM"]')?.value || "").trim(),
            count: String(itemNode.querySelector('[data-drywall-repeat-input="count"]')?.value || "").trim(),
        }));
    }


    export function validateDrywallOpenings(form, items) {
        let isValid = true;

        items.forEach((item, index) => {
            if (!["window", "door"].includes(item.type)) {
                setFieldError(form, `drywallOpenings.${index}.type`, "Выберите тип проёма.");
                isValid = false;
            }
            if (!isPositiveNumber(item.widthM)) {
                setFieldError(form, `drywallOpenings.${index}.widthM`, "Проём: ширина должна быть больше 0.");
                isValid = false;
            }
            if (!isPositiveNumber(item.heightM)) {
                setFieldError(form, `drywallOpenings.${index}.heightM`, "Проём: высота должна быть больше 0.");
                isValid = false;
            }
            if (!isPositiveInteger(item.count)) {
                setFieldError(form, `drywallOpenings.${index}.count`, "Проём: количество должно быть целым числом больше 0.");
                isValid = false;
            }
        });

        return isValid;
    }


    export function splitDrywallOpenings(items) {
        const windows = [];
        const doors = [];

        items.forEach((item) => {
            const normalizedItem = {
                widthM: item.widthM,
                heightM: item.heightM,
                count: item.count,
            };

            if (item.type === "door") {
                doors.push(normalizedItem);
            } else {
                windows.push(normalizedItem);
            }
        });

        return { windows, doors };
    }


    export function createDrywallRepeatMarkup(index) {
        return `
      <article class="brigmaster-estimator__segment-card" data-drywall-repeat-item>
        <div class="brigmaster-estimator__segment-head">
          <h4 class="brigmaster-estimator__segment-title">Проём ${index + 1}</h4>
          <button type="button" class="brigmaster-estimator__segment-remove" data-drywall-remove-item>Удалить</button>
        </div>
        <div class="brigmaster-estimator__field-grid brigmaster-estimator__field-grid--four">
          <div class="brigmaster-estimator__field">
            <label>Тип проёма</label>
            <select data-drywall-repeat-input="type">
              <option value="window">Окно</option>
              <option value="door">Дверь</option>
            </select>
            <div class="brigmaster-estimator__error" data-field-error="drywallOpenings.${index}.type" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Ширина (м)</label>
            <input type="number" min="0.01" step="0.01" value="0.9" data-drywall-repeat-input="widthM">
            <div class="brigmaster-estimator__error" data-field-error="drywallOpenings.${index}.widthM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Высота (м)</label>
            <input type="number" min="0.01" step="0.01" value="2.1" data-drywall-repeat-input="heightM">
            <div class="brigmaster-estimator__error" data-field-error="drywallOpenings.${index}.heightM" aria-live="polite"></div>
          </div>
          <div class="brigmaster-estimator__field">
            <label>Количество</label>
            <input type="number" min="1" step="1" value="1" data-drywall-repeat-input="count">
            <div class="brigmaster-estimator__error" data-field-error="drywallOpenings.${index}.count" aria-live="polite"></div>
          </div>
        </div>
      </article>
    `;
    }


    export function reindexDrywallRepeatList(listNode) {
        const listId = listNode.id || "drywall-openings";
        const items = listNode.querySelectorAll("[data-drywall-repeat-item]");
        items.forEach((itemNode, index) => {
            const baseId = `${listId}-${index}`;
            const titleNode = itemNode.querySelector(".brigmaster-estimator__segment-title");
            if (titleNode) {
                titleNode.textContent = `Проём ${index + 1}`;
            }
            const removeButton = itemNode.querySelector("[data-drywall-remove-item]");
            if (removeButton) {
                removeButton.disabled = items.length === 1;
            }
            itemNode.querySelectorAll("[data-drywall-repeat-input]").forEach((inputNode) => {
                const fieldKey = inputNode.getAttribute("data-drywall-repeat-input") || "value";
                const inputId = `${baseId}-${fieldKey}`;
                inputNode.id = inputId;
                const fieldNode = inputNode.closest(".brigmaster-estimator__field");
                const labelNode = fieldNode?.querySelector("label");
                if (labelNode) {
                    labelNode.setAttribute("for", inputId);
                }
            });
        });
    }


    export function syncDrywallSheetFormat(form) {
        const formatSelect = form.querySelector("[data-drywall-sheet-format-select]");
        const lengthInput = form.querySelector("[data-drywall-sheet-length]");
        const widthInput = form.querySelector("[data-drywall-sheet-width]");
        if (!formatSelect || !lengthInput || !widthInput) {
            return;
        }

        const selected = formatSelect.options[formatSelect.selectedIndex];
        const isCustom = formatSelect.value === "custom";
        lengthInput.readOnly = !isCustom;
        widthInput.readOnly = !isCustom;

        if (isCustom || !selected) {
            return;
        }

        const length = selected.getAttribute("data-sheet-length");
        const width = selected.getAttribute("data-sheet-width");
        if (length) {
            lengthInput.value = length;
        }
        if (width) {
            widthInput.value = width;
        }
    }


    export function syncDrywallFormGroups(form) {
        const calculator = form.querySelector('[name="calculator"]')?.value;
        if (calculator !== "drywall") {
            return;
        }

        const mode = form.querySelector('[name="mode"]')?.value || "dimensions";
        const target = form.querySelector('[name="drywallTarget"]')?.value || "wall";
        const includeOpenings = form.querySelector('[name="includeOpenings"]')?.checked;
        const includeFinishing = form.querySelector('[name="drywallIncludeFinishing"]')?.checked;
        const includeCosts = form.querySelector('[name="drywallIncludeCosts"]')?.checked;

        toggleVisibility(
            form.querySelector('[data-field-group="drywall-wall-dimensions"]'),
            mode === "dimensions" && target !== "ceiling"
        );
        toggleVisibility(
            form.querySelector('[data-field-group="drywall-ceiling-dimensions"]'),
            mode === "dimensions" && target === "ceiling"
        );
        toggleVisibility(
            form.querySelector('[data-field-group="drywall-area"]'),
            mode === "area"
        );
        toggleVisibility(
            form.querySelector('[data-field-group="drywall-openings-toggle"]'),
            target !== "ceiling"
        );
        toggleVisibility(
            form.querySelector("[data-drywall-openings-root]"),
            target !== "ceiling" && !!includeOpenings
        );
        toggleVisibility(
            form.querySelector('[data-field-group="drywall-profile-width"]'),
            target === "partition"
        );
        toggleVisibility(
            form.querySelector('[data-field-group="drywall-end-cladding-toggle"]'),
            target === "partition" && !!includeOpenings
        );
        toggleVisibility(
            form.querySelector("[data-drywall-costs-root]"),
            !!includeCosts
        );
        toggleVisibility(
            form.querySelector("[data-drywall-finishing-costs-root]"),
            !!includeCosts && !!includeFinishing
        );

        form.querySelectorAll("[data-drywall-length-label]").forEach((node) => {
            node.textContent =
                target === "partition" ? "Длина перегородки (м)" : "Длина стены (м)";
        });
    }


    export function initDrywallForm(form) {
        const calculator = form.querySelector('[name="calculator"]')?.value;
        if (calculator !== "drywall") {
            return;
        }

        const modeSelect = form.querySelector('[name="mode"]');
        const targetSelect = form.querySelector('[name="drywallTarget"]');
        const openingsToggle = form.querySelector('[name="includeOpenings"]');
        const finishingToggle = form.querySelector('[name="drywallIncludeFinishing"]');
        const costsToggle = form.querySelector('[name="drywallIncludeCosts"]');
        const formatSelect = form.querySelector("[data-drywall-sheet-format-select]");

        const refresh = () => {
            form.querySelectorAll("[data-drywall-repeat-list]").forEach((listNode) => {
                reindexDrywallRepeatList(listNode);
            });
            syncDrywallSheetFormat(form);
            syncDrywallFormGroups(form);
        };

        [modeSelect, targetSelect, openingsToggle, finishingToggle, costsToggle, formatSelect].forEach(
            (node) => {
                node?.addEventListener("change", () => {
                    clearErrors(form);
                    markResultStale(form);
                    refresh();
                });
            }
        );

        form.querySelectorAll("[data-drywall-add-item]").forEach((button) => {
            button.addEventListener("click", () => {
                const listNode = form.querySelector('[data-drywall-repeat-list="drywallOpenings"]');
                if (!listNode) {
                    return;
                }
                const nextIndex = listNode.querySelectorAll("[data-drywall-repeat-item]").length;
                listNode.insertAdjacentHTML("beforeend", createDrywallRepeatMarkup(nextIndex));
                clearErrors(form);
                markResultStale(form);
                refresh();
            });
        });

        form.querySelectorAll("[data-drywall-repeat-list]").forEach((listNode) => {
            if (!listNode.querySelector("[data-drywall-repeat-item]")) {
                listNode.insertAdjacentHTML("beforeend", createDrywallRepeatMarkup(0));
            }

            listNode.addEventListener("click", (event) => {
                const target = event.target;
                if (!(target instanceof Element)) {
                    return;
                }
                const removeButton = target.closest("[data-drywall-remove-item]");
                if (!removeButton) {
                    return;
                }
                const items = listNode.querySelectorAll("[data-drywall-repeat-item]");
                if (items.length <= 1) {
                    return;
                }
                const itemNode = removeButton.closest("[data-drywall-repeat-item]");
                if (!itemNode) {
                    return;
                }
                itemNode.remove();
                clearErrors(form);
                markResultStale(form);
                refresh();
            });
        });

        refresh();
    }

export function buildPayload(form, formData) {
    const calculator = readTrimmed(formData, "calculator") || "drywall";
    const mode = readTrimmed(formData, "mode");
    const payload = {
      calculator,
      mode,
    };

    let isValid = validateBaseFields(form, payload);


            payload.drywallTarget = readTrimmed(formData, "drywallTarget") || "wall";
            payload.drywallSheetLengthMm = readTrimmed(formData, "drywallSheetLengthMm");
            payload.drywallSheetWidthMm = readTrimmed(formData, "drywallSheetWidthMm");
            payload.drywallSheetThicknessMm = readTrimmed(formData, "drywallSheetThicknessMm");
            payload.drywallLayers = readTrimmed(formData, "drywallLayers");
            payload.drywallFrameStepMm = readTrimmed(formData, "drywallFrameStepMm");
            payload.drywallProfileWidthMm = readTrimmed(formData, "drywallProfileWidthMm");
            payload.reservePercent = readTrimmed(formData, "reservePercent");
            payload.drywallFastenerReservePercent = readTrimmed(
                formData,
                "drywallFastenerReservePercent"
            );
            payload.includeOpenings =
                payload.drywallTarget !== "ceiling" &&
                formData.get("includeOpenings") !== null;
            payload.drywallIncludeEndCladding =
                payload.drywallTarget === "partition" &&
                formData.get("drywallIncludeEndCladding") !== null;
            payload.drywallIncludeFinishing =
                formData.get("drywallIncludeFinishing") !== null;
            payload.drywallIncludeCosts = formData.get("drywallIncludeCosts") !== null;

            isValid =
                validateSelectedValue(
                    form,
                    "drywallTarget",
                    payload.drywallTarget,
                    ["wall", "ceiling", "partition"],
                    "Выберите тип конструкции."
                ) && isValid;
            isValid =
                validateSelectedValue(
                    form,
                    "drywallLayers",
                    payload.drywallLayers,
                    ["1", "2"],
                    "Выберите количество слоёв."
                ) && isValid;
            isValid =
                validateSelectedValue(
                    form,
                    "drywallFrameStepMm",
                    payload.drywallFrameStepMm,
                    ["400", "600"],
                    "Выберите шаг профиля."
                ) && isValid;
            if (payload.drywallTarget === "partition") {
                isValid =
                    validateSelectedValue(
                        form,
                        "drywallProfileWidthMm",
                        payload.drywallProfileWidthMm,
                        ["50", "75", "100"],
                        "Выберите ширину профиля перегородки."
                    ) && isValid;
            }

            if (mode === "dimensions") {
                if (payload.drywallTarget === "ceiling") {
                    payload.length = readTrimmed(formData, "drywallCeilingLength");
                    payload.width = readTrimmed(formData, "drywallCeilingWidth");
                    isValid =
                        validatePositiveField(
                            form,
                            payload,
                            "length",
                            "Длина помещения должна быть больше 0."
                        ) && isValid;
                    isValid =
                        validatePositiveField(
                            form,
                            payload,
                            "width",
                            "Ширина помещения должна быть больше 0."
                        ) && isValid;
                } else {
                    payload.length = readTrimmed(formData, "drywallLength");
                    payload.height = readTrimmed(formData, "drywallHeight");
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
                            "height",
                            "Высота должна быть больше 0."
                        ) && isValid;
                }
            } else if (mode === "area") {
                payload.area = readTrimmed(formData, "drywallArea");
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

            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "drywallSheetLengthMm",
                    "Длина листа должна быть больше 0."
                ) && isValid;
            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "drywallSheetWidthMm",
                    "Ширина листа должна быть больше 0."
                ) && isValid;
            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "drywallSheetThicknessMm",
                    "Толщина листа должна быть больше 0."
                ) && isValid;
            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "reservePercent",
                    "Запас на листы и профиль должен быть больше 0."
                ) && isValid;
            isValid =
                validatePositiveField(
                    form,
                    payload,
                    "drywallFastenerReservePercent",
                    "Запас на метизы должен быть больше 0."
                ) && isValid;

            if (payload.includeOpenings) {
                const openings = buildDrywallRepeatItems(form);
                isValid = validateDrywallOpenings(form, openings) && isValid;
                const split = splitDrywallOpenings(openings);
                payload.windows = split.windows;
                payload.doors = split.doors;
            }

            if (payload.drywallIncludeCosts) {
                payload.drywallSheetPrice = readTrimmed(formData, "drywallSheetPrice");
                payload.drywallProfilePricePerLm = readTrimmed(
                    formData,
                    "drywallProfilePricePerLm"
                );
                payload.drywallFastenerPricePer100 = readTrimmed(
                    formData,
                    "drywallFastenerPricePer100"
                );
                payload.drywallPrimerPricePerKg = readTrimmed(
                    formData,
                    "drywallPrimerPricePerKg"
                );
                payload.drywallJointPuttyPricePerKg = readTrimmed(
                    formData,
                    "drywallJointPuttyPricePerKg"
                );
                payload.drywallFinishPuttyPricePerKg = readTrimmed(
                    formData,
                    "drywallFinishPuttyPricePerKg"
                );
                payload.drywallTapePricePerLm = readTrimmed(formData, "drywallTapePricePerLm");

                [
                    "drywallSheetPrice",
                    "drywallProfilePricePerLm",
                    "drywallFastenerPricePer100",
                    "drywallPrimerPricePerKg",
                    "drywallJointPuttyPricePerKg",
                    "drywallFinishPuttyPricePerKg",
                    "drywallTapePricePerLm",
                ].forEach((field) => {
                    if (payload[field] && !isPositiveNumber(payload[field])) {
                        setFieldError(form, field, "Цена должна быть больше 0.");
                        isValid = false;
                    }
                });
            }

    return {
      isValid,
      payload,
    };
  }

const calculatorModule = {
  calculator: "drywall",
  init: initDrywallForm,
  buildPayload,
  showResult: showDrywallResult,
};

initEstimateForms(calculatorModule);
