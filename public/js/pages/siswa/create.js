/**
 * Siswa Create Page - Create New Student
 * Handles form submission and validation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize form handlers
    initFormHandlers();
    
    // Initialize input interactions
    initInputInteractions();
});

/**
 * Initialize form handlers
 */
function initFormHandlers() {
    const form = document.getElementById('createSiswaForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        // Form validation is handled by Bootstrap/HTML5
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Handle reset button
    const resetBtn = document.querySelector('[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            form.classList.remove('was-validated');
        });
    }
}

/**
 * Initialize input interactions
 */
function initInputInteractions() {
    const inputs = document.querySelectorAll('.form-control-clean, .form-select');
    
    inputs.forEach(input => {
        // Clear error on input
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                const feedback = this.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.style.display = 'none';
                }
            }
        });

        // Handle focus styles
        input.addEventListener('focus', function() {
            this.style.borderColor = '#80bdff';
        });

        input.addEventListener('blur', function() {
            this.style.borderColor = '';
        });
    });

    // Handle Select2 if present
    initSelect2Handlers();
}

/**
 * Initialize Select2 handlers if Select2 is loaded
 */
function initSelect2Handlers() {
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        return;
    }

    // Handle Select2 change events
    $('.form-select').on('change', function() {
        if ($(this).hasClass('is-invalid')) {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').hide();
        }
    });
}

/**
 * Validate required fields
 * @returns {boolean} True if all required fields are filled
 */
function validateRequiredFields() {
    const requiredInputs = document.querySelectorAll('[required]');
    let isValid = true;

    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    return isValid;
}
