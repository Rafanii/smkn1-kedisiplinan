/**
 * Selection Utility Module
 * Handles item selection (students, violations, etc.)
 */

const SelectionModule = {
    /**
     * Initialize selection cards
     * @param {string} containerSelector - CSS selector for selection container
     * @param {string} inputSelector - CSS selector for hidden input element
     * @param {string} cardClass - CSS class for selection card
     */
    initSelectionCards(containerSelector, inputSelector, cardClass = 'selection-card') {
        const container = document.querySelector(containerSelector);
        const input = document.querySelector(inputSelector);

        if (!container || !input) return;

        const cards = container.querySelectorAll(`.${cardClass}`);
        const radio = container.querySelector('input[type="radio"]');

        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        this.classList.add('selected');
                        cards.forEach(c => {
                            if (c !== this) c.classList.remove('selected');
                        });
                    }
                }
            });

            const radio = card.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                card.classList.add('selected');
            }
        });
    },

    /**
     * Initialize multiple selection cards (checkboxes)
     * @param {string} containerSelector - CSS selector for selection container
     * @param {string} cardClass - CSS class for selection card
     */
    initMultipleSelection(containerSelector, cardClass = 'selection-card') {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        const cards = container.querySelectorAll(`.${cardClass}`);

        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        this.classList.toggle('selected');
                    }
                }
            });

            const checkbox = card.querySelector('input[type="checkbox"]');
            if (checkbox && checkbox.checked) {
                card.classList.add('selected');
            }
        });
    },

    /**
     * Get selected value
     * @param {string} containerSelector - CSS selector for selection container
     * @returns {string} Selected value or empty string
     */
    getSelectedValue(containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return '';

        const radio = container.querySelector('input[type="radio"]:checked');
        return radio ? radio.value : '';
    },

    /**
     * Get all selected values (multiple selection)
     * @param {string} containerSelector - CSS selector for selection container
     * @returns {Array} Array of selected values
     */
    getSelectedValues(containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return [];

        const checkboxes = container.querySelectorAll('input[type="checkbox"]:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    },

    /**
     * Clear selection
     * @param {string} containerSelector - CSS selector for selection container
     */
    clearSelection(containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        const inputs = container.querySelectorAll('input[type="radio"], input[type="checkbox"]');
        inputs.forEach(input => {
            input.checked = false;
        });

        const cards = container.querySelectorAll('.selection-card');
        cards.forEach(card => {
            card.classList.remove('selected');
        });
    }
};

export default SelectionModule;
