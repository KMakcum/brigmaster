import { setFieldError } from "./form-state.js";


    export function isPositiveNumber(value) {
        const numericValue = Number(value);
        return Number.isFinite(numericValue) && numericValue > 0;
    }


    export function isPositiveInteger(value) {
        const numericValue = Number(value);
        return Number.isInteger(numericValue) && numericValue > 0;
    }


    export function validateBaseFields(form, payload) {
        if (!payload.mode) {
            setFieldError(form, "mode", "Выберите режим расчета.");
            return false;
        }

        return true;
    }


    export function validatePositiveField(form, payload, fieldName, message) {
        if (!isPositiveNumber(payload[fieldName])) {
            setFieldError(form, fieldName, message);
            return false;
        }

        return true;
    }


    export function validatePositiveValue(form, errorKey, value, message) {
        if (!isPositiveNumber(value)) {
            setFieldError(form, errorKey, message);
            return false;
        }

        return true;
    }


    export function validateSelectedValue(form, errorKey, value, allowedValues, message) {
        if (!allowedValues.includes(value)) {
            setFieldError(form, errorKey, message);
            return false;
        }

        return true;
    }
