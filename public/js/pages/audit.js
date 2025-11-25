/**
 * Audit Page Module
 * Handles audit form interactions, validation, and UI state management
 * Location: public/js/pages/audit.js (follows project convention)
 */

const AuditModule = (() => {
    'use strict';

    // Private variables
    const FORM_ID = 'auditForm';
    const SUMMARY_CONTAINER = 'summaryContainer';
    const CONFIRM_CONTAINER = 'confirmContainer';

    /**
     * Initialize audit module
     */
    function init() {
        setupFormValidation();
        setupScopeButtons();
        setupCheckboxes();
        setupDeleteConfirmation();
    }

    /**
     * Setup form scope selector buttons
     */
    function setupScopeButtons() {
        const scopeButtons = document.querySelectorAll('[data-scope-btn]');
        
        scopeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                scopeButtons.forEach(b => b.classList.remove('active', 'btn-primary'));
                scopeButtons.forEach(b => b.classList.add('btn-outline-primary'));
                
                this.classList.remove('btn-outline-primary');
                this.classList.add('active', 'btn-primary');
                
                const scopeInput = document.querySelector(`input[name="${this.dataset.scopeInput}"]`);
                if (scopeInput) {
                    scopeInput.value = this.dataset.scopeValue || '';
                    scopeInput.focus();
                }
            });
        });
    }

    /**
     * Setup form validation
     */
    function setupFormValidation() {
        const form = document.getElementById(FORM_ID);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            if (!validateAuditForm()) {
                e.preventDefault();
                showAlert('danger', '❌ Pilih scope terlebih dahulu (Kelas, Jurusan, Tingkat, atau IDs)');
            }
        });
    }

    /**
     * Validate audit form has scope selected
     */
    function validateAuditForm() {
        const kelas = document.querySelector('input[name="kelas"]').value;
        const jurusan = document.querySelector('input[name="jurusan"]').value;
        const tingkat = document.querySelector('input[name="tingkat"]').value;
        const ids = document.querySelector('input[name="ids"]').value;

        return kelas || jurusan || tingkat || ids;
    }

    /**
     * Setup checkbox interactions
     */
    function setupCheckboxes() {
        const dryRunCheck = document.getElementById('dryRunCheck');
        const forceDeleteCheck = document.getElementById('forceDeleteCheck');
        const deleteOrphanedWaliCheck = document.getElementById('deleteOrphanedWaliCheck');

        if (dryRunCheck) {
            dryRunCheck.addEventListener('change', function() {
                const deleteSection = document.querySelector('[data-delete-section]');
                if (deleteSection) {
                    deleteSection.classList.toggle('opacity-50', this.checked);
                }
            });
        }

        if (forceDeleteCheck) {
            forceDeleteCheck.addEventListener('change', function() {
                const warningAlert = document.querySelector('[data-hard-delete-warning]');
                if (warningAlert) {
                    warningAlert.classList.toggle('d-none', !this.checked);
                }
            });
        }

        if (deleteOrphanedWaliCheck) {
            deleteOrphanedWaliCheck.addEventListener('change', function() {
                const waliWarning = document.querySelector('[data-wali-warning]');
                if (waliWarning) {
                    waliWarning.classList.toggle('d-none', !this.checked);
                }
            });
        }
    }

    /**
     * Setup delete confirmation interaction
     */
    function setupDeleteConfirmation() {
        const confirmBtn = document.querySelector('[data-confirm-delete-btn]');
        if (!confirmBtn) return;

        confirmBtn.addEventListener('click', function(e) {
            const deleteOrphanedWali = document.getElementById('deleteOrphanedWaliCheck')?.checked;
            const waliWarning = document.querySelector('[data-wali-warning]');

            if (deleteOrphanedWali && waliWarning && !waliWarning.classList.contains('d-none')) {
                const waliCount = document.querySelector('[data-orphaned-wali-count]')?.textContent || '0';
                const confirmed = confirm(`Anda akan menghapus ${waliCount} akun Wali Murid yang orphaned. Lanjutkan?`);
                
                if (!confirmed) {
                    e.preventDefault();
                }
            }
        });
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        const alertContainer = document.querySelector('[data-alert-container]');
        if (!alertContainer) return;

        const alertId = 'alert-' + Date.now();
        const alertHTML = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        alertContainer.insertAdjacentHTML('beforeend', alertHTML);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) alert.remove();
        }, 5000);
    }

    /**
     * Export public API
     */
    return {
        init: init,
        validateAuditForm: validateAuditForm,
        showAlert: showAlert
    };
})();

// Initialize on document ready
document.addEventListener('DOMContentLoaded', AuditModule.init);
