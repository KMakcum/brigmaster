

export function formatNumber(value) {
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

    const MODE_HINTS_SCREED = {
        dimensions:
            "Ввод по длине и ширине — нужен для опций арматуры.",
        area:
            "Ввод по площади; арматура доступна только в режиме по длине и ширине.",
    };

    const MODE_HINTS_STRIP = {
        perimeter:
            "Считаем объём по одной общей длине и размерам сечения.",
        house:
            "Сначала получаем длину по габаритам дома, затем считаем объём по сечению.",
        segments:
            "Длина складывается из участков, объём — по суммарной длине и сечению.",
    };

    const MODE_HINTS_BRICK = {
        dimensions:
            "Ввод по общей длине и средней высоте стен. Подходит, когда геометрия дома известна.",
        area:
            "Ввод по площади стен без вычета проёмов. Ниже можно дополнительно вычесть окна и двери.",
    };

    const MODE_HINTS_TILE = {
        dimensions:
            "Расчёт по размерам прямоугольной зоны. В этом режиме доступны ориентировочные подрезки и раскладка.",
        area:
            "Расчёт по общей площади. Материалы считаются корректно, а ориентировочная раскладка не строится, потому что геометрия не задана.",
    };

    const MODE_HINTS_DRYWALL = {
        dimensions:
            "Ввод по размерам нужен для расчёта профилей, подвесов и крепежа по каркасу.",
        area:
            "Ввод по площади считает листы и отделку, но не считает каркас, потому что геометрия конструкции не задана.",
    };


    export function escapeHtml(text) {
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    export function hasMeaningfulNumber(value) {
        return value !== null && value !== "" && Number.isFinite(Number(value));
    }


    export function normalizePurchaseWeight(rawValue, purchaseUnit) {
        const numericValue = Number(rawValue);
        if (!Number.isFinite(numericValue) || numericValue <= 0) {
            return rawValue;
        }

        return purchaseUnit === "tonne"
            ? String(numericValue * 1000)
            : String(numericValue);
    }
