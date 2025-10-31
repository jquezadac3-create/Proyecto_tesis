import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';

/**
 *
 * @returns iti
 */
export function phoneInput(phone, error) {
    const input = document.querySelector(`#${phone}`);
    const errorMsg = document.querySelector(`#${error}`);

    // Here, the index maps to the error code returned from getValidationError - see readme
    const errorMap = ["Número inválido", "Código de país inválido", "Número demasiado corto", "Número demasiado largo", "Número inválido"];

    // Initialise plugin
    const iti = intlTelInput(input, {
        initialCountry: "ec",
        nationalMode: true,
        loadUtils: () => import("intl-tel-input/utils"),
    });

    const reset = () => {
        input.classList.remove("error");
        errorMsg.innerHTML = "";
        errorMsg.classList.add("hidden");
    };

    // On input event: validate
    input.addEventListener('input', () => {
        reset();
        if (input.value.trim()) {
            errorMsg.innerHTML = iti.isValidNumber ? "" : "Número inválido";
        }
    });

    //? On change flag: reset
    input.addEventListener('change', reset);

    return iti;
}

/**
 * This funciton recovery the created instance of intl.
 * 
 * @param {HTMLElement} phone 
 * @returns {intlTelInput}
 */
export function getIntlInput(phone) {
    return intlTelInputGlobals.getInstance(phone) ?? '';
}

/**
 * @description Get the formatted number and country extension
 * @param iti
 * @param {boolean} [getCountry=false] - Default value is false, only if its true get the Object.
 * @returns {Object} - This object can have two keys or one key, but on the param @param {boolean} getCountry
 */
export function getFormattedNumber(iti, getCountry = false) {
    return getCountry ?
        { phone: iti.getNumber(), phone_country: iti.getSelectedCountryData().iso2.toUpperCase() ?? 'US' }
        : iti.getNumber();
}


/**
 *
 * @param {HTMLInputElement} phoneElement
 * @param {string} containerId
 */
export function phoneSpanErrorMessage(phoneElement, containerId) {

    let phoneSpanError = document.querySelector(`#${containerId} .item_display_error`)
    phoneElement.addEventListener('input', deletePhoneError)

    function deletePhoneError() {
        phoneSpanError.innerHTML = ''
        phoneElement.removeEventListener('input', deletePhoneError)
    }
}