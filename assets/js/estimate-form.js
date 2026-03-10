(() => {
  const CONCRETE_SUBTYPE_FIELD = "subType";
  const CONCRETE_SUBTYPE_SLAB = "slab";
  const CONCRETE_SUBTYPE_STRIP = "strip";

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

    const volumeNode = resultNode.querySelector("[data-result-volume]");
    const materialNode = resultNode.querySelector("[data-result-material]");

    if (volumeNode) {
      volumeNode.textContent = "-";
    }

    if (materialNode) {
      materialNode.textContent = "-";
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

  function showResult(form, payload) {
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
    const subType = readTrimmed(formData, CONCRETE_SUBTYPE_FIELD);

    if (calculator === "concrete") {
      payload.subType = subType || CONCRETE_SUBTYPE_SLAB;

      if (payload.subType === CONCRETE_SUBTYPE_STRIP) {
        payload.length = readTrimmed(formData, "length");
        payload.width = readTrimmed(formData, "width");
        payload.height = readTrimmed(formData, "height");

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
        isValid =
          validatePositiveField(
            form,
            payload,
            "height",
            "Высота должна быть больше 0."
          ) && isValid;
      } else {
        payload.subType = CONCRETE_SUBTYPE_SLAB;
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
      }
    } else if (calculator === "brick") {
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

    Object.entries(errors).forEach(([field, messages]) => {
      if (!Array.isArray(messages) || messages.length === 0) {
        return;
      }

      setFieldError(form, field, String(messages[0]));
    });
  }

  function toggleVisibility(node, isVisible) {
    if (!node) {
      return;
    }

    node.classList.toggle("brigmaster-estimator__field-group--hidden", !isVisible);
  }

  function syncConcreteSubTypeGroups(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator !== "concrete") {
      return;
    }

    const subType =
      form.querySelector(`[name="${CONCRETE_SUBTYPE_FIELD}"]`)?.value ||
      CONCRETE_SUBTYPE_SLAB;

    const slabGroup = form.querySelector('[data-field-group="concrete-slab"]');
    const stripGroup = form.querySelector('[data-field-group="concrete-strip"]');

    toggleVisibility(slabGroup, subType !== CONCRETE_SUBTYPE_STRIP);
    toggleVisibility(stripGroup, subType === CONCRETE_SUBTYPE_STRIP);
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

      handleValidationErrors(form, data?.errors);
    } catch (_error) {
      setFieldError(form, "general", networkErrorMessage);
    } finally {
      setLoadingState(form, submitButton, false);
    }
  }

  function initConcreteSubType(form) {
    const calculator = form.querySelector('[name="calculator"]')?.value;
    if (calculator !== "concrete") {
      return;
    }

    const select = form.querySelector(`[name="${CONCRETE_SUBTYPE_FIELD}"]`);
    if (!select) {
      return;
    }

    syncConcreteSubTypeGroups(form);
    select.addEventListener("change", () => {
      clearErrors(form);
      clearResult(form);
      syncConcreteSubTypeGroups(form);
    });
  }

  function initForm(form) {
    initConcreteSubType(form);
    form.addEventListener("submit", onSubmit);
  }

  document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll(".brigmaster-estimate-form");
    forms.forEach((form) => initForm(form));
  });
})();
