/**
 * Siswa Edit Page - Edit Student
 * Handles form submission, validation, and deletion
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize form handlers
    initFormHandlers();
    
    // Initialize input interactions
    initInputInteractions();

    // Initialize delete confirmation
    initDeleteConfirmation();
});

/**
 * Initialize form handlers
 */
function initFormHandlers() {
    const form = document.getElementById('editSiswaForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
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

        // Handle readonly fields
        if (this.hasAttribute('readonly')) {
            this.style.cursor = 'not-allowed';
        }

        // Handle focus styles
        if (!input.hasAttribute('readonly')) {
            input.addEventListener('focus', function() {
                this.style.borderColor = '#80bdff';
            });

            input.addEventListener('blur', function() {
                this.style.borderColor = '';
            });
        }
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
 * Initialize delete confirmation
 */
function initDeleteConfirmation() {
    const deleteBtn = document.querySelector('.btn-danger[data-confirm]');
    if (!deleteBtn) return;

    deleteBtn.addEventListener('click', function(e) {
        const confirmed = confirm(
            'Apakah Anda yakin ingin menghapus data siswa ini? Tindakan ini tidak dapat dibatalkan.'
        );

        if (!confirmed) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

/**
 * Mark form as dirty (detect changes)
 */
function markFormDirty() {
    const form = document.getElementById('editSiswaForm');
    if (!form) return;

    let isDirty = false;
    const originalValues = new FormData(form);

    form.addEventListener('change', function() {
        isDirty = true;
        console.log('Form has unsaved changes');
    });

    form.addEventListener('submit', function() {
        isDirty = false;
    });

    window.addEventListener('beforeunload', function(e) {
        if (isDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
}
