/**
 * Form Validation Component
 * Handles client-side form validation
 */

const FormValidationComponent = {
    /**
     * Initialize form validation
     * @param {string} formId - ID of form to validate
     * @param {Object} rules - Validation rules
     */
    init(formId, rules = {}) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', (e) => {
            if (!this.validate(form, rules)) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    },

    /**
     * Validate form
     * @param {HTMLFormElement} form - Form to validate
     * @param {Object} rules - Validation rules
     * @returns {boolean} True if form is valid
     */
    validate(form, rules = {}) {
        let isValid = true;

        // Bootstrap validation
        if (form.checkValidity() === false) {
            isValid = false;
        }

        // Custom validation rules
        Object.entries(rules).forEach(([fieldName, rule]) => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field) return;

            if (rule.required && !this.isRequired(field)) {
                this.setFieldError(field, 'This field is required');
                isValid = false;
            } else if (rule.type === 'email' && !this.isEmail(field.value)) {
                this.setFieldError(field, 'Please enter a valid email');
                isValid = false;
            } else if (rule.type === 'number' && !this.isNumber(field.value)) {
                this.setFieldError(field, 'Please enter a valid number');
                isValid = false;
            } else if (rule.min && field.value.length < rule.min) {
                this.setFieldError(field, `Minimum ${rule.min} characters required`);
                isValid = false;
            } else if (rule.max && field.value.length > rule.max) {
                this.setFieldError(field, `Maximum ${rule.max} characters allowed`);
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });

        return isValid;
    },

    /**
     * Validate single field
     * @param {string} fieldName - Name of field to validate
     * @param {Object} rule - Validation rule
     * @returns {boolean} True if field is valid
     */
    validateField(fieldName, rule = {}) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return false;

        if (rule.required && !this.isRequired(field)) {
            this.setFieldError(field, 'This field is required');
            return false;
        }

        if (rule.type === 'email' && !this.isEmail(field.value)) {
            this.setFieldError(field, 'Please enter a valid email');
            return false;
        }

        if (rule.type === 'number' && !this.isNumber(field.value)) {
            this.setFieldError(field, 'Please enter a valid number');
            return false;
        }

        if (rule.min && field.value.length < rule.min) {
            this.setFieldError(field, `Minimum ${rule.min} characters required`);
            return false;
        }

        if (rule.max && field.value.length > rule.max) {
            this.setFieldError(field, `Maximum ${rule.max} characters allowed`);
            return false;
        }

        this.clearFieldError(field);
        return true;
    },

    /**
     * Check if field is required and filled
     * @param {HTMLElement} field - Form field
     * @returns {boolean} True if field is filled
     */
    isRequired(field) {
        if (field.type === 'checkbox' || field.type === 'radio') {
            return field.checked;
        }
        return field.value.trim() !== '';
    },

    /**
     * Check if value is valid email
     * @param {string} value - Email value
     * @returns {boolean} True if valid email
     */
    isEmail(value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(value);
    },

    /**
     * Check if value is valid number
     * @param {string} value - Number value
     * @returns {boolean} True if valid number
     */
    isNumber(value) {
        return !isNaN(value) && value !== '';
    },

    /**
     * Set field error state
     * @param {HTMLElement} field - Form field
     * @param {string} message - Error message
     */
    setFieldError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');

        let feedback = field.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.insertBefore(feedback, field.nextSibling);
        }
        feedback.textContent = message;
        feedback.style.display = 'block';
    },

    /**
     * Clear field error state
     * @param {HTMLElement} field - Form field
     */
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');

        const feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.style.display = 'none';
        }
    }
};

export default FormValidationComponent;
