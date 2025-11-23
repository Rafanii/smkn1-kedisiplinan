/**
 * Filter Utility Module
 * Handles filter logic and form submissions
 */

const FilterModule = {
    /**
     * Initialize filter buttons
     * @param {string} containerSelector - CSS selector for filter container
     * @param {string} formId - ID of form to submit
     */
    initFilterButtons(containerSelector, formId) {
        const container = document.querySelector(containerSelector);
        const form = document.getElementById(formId);

        if (!container || !form) return;

        const buttons = container.querySelectorAll('.btn');
        buttons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                buttons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                form.submit();
            });
        });
    },

    /**
     * Submit filter form
     * @param {string} formId - ID of form to submit
     */
    submitFilter(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.submit();
        }
    },

    /**
     * Reset all filters
     * @param {string} formId - ID of form to reset
     */
    resetFilters(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.reset();
            form.submit();
        }
    },

    /**
     * Get active filter value
     * @param {string} filterName - Name of filter parameter
     * @returns {string} Filter value or empty string
     */
    getActiveFilter(filterName) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(filterName) || '';
    },

    /**
     * Check if filter is active
     * @param {string} filterName - Name of filter parameter
     * @param {string} filterValue - Filter value to check
     * @returns {boolean} True if filter is active
     */
    isFilterActive(filterName, filterValue) {
        const activeValue = this.getActiveFilter(filterName);
        return activeValue === filterValue;
    }
};

export default FilterModule;
