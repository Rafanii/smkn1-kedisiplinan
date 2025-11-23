/**
 * Selection Cards Component
 * Handles interactive selection cards for students and violations
 */

const SelectionCardsComponent = {
    /**
     * Initialize selection cards with single selection (radio)
     * @param {string} containerSelector - CSS selector for cards container
     * @param {Object} options - Configuration options
     */
    initSingleSelection(containerSelector, options = {}) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        const cards = container.querySelectorAll('.selection-card');
        const onSelect = options.onSelect || null;

        cards.forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            
            card.addEventListener('click', (e) => {
                if (e.target === radio || e.target.closest('input[type="radio"]')) {
                    return;
                }

                e.preventDefault();
                
                cards.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                
                if (radio) {
                    radio.checked = true;
                    if (onSelect) {
                        onSelect(radio.value, card);
                    }
                }
            });

            radio?.addEventListener('change', () => {
                cards.forEach(c => c.classList.remove('selected'));
                if (radio.checked) {
                    card.classList.add('selected');
                    if (onSelect) {
                        onSelect(radio.value, card);
                    }
                }
            });

            if (radio?.checked) {
                card.classList.add('selected');
            }
        });
    },

    /**
     * Initialize selection cards with multiple selection (checkboxes)
     * @param {string} containerSelector - CSS selector for cards container
     * @param {Object} options - Configuration options
     */
    initMultipleSelection(containerSelector, options = {}) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        const cards = container.querySelectorAll('.selection-card');
        const onSelect = options.onSelect || null;
        const maxSelect = options.maxSelect || null;

        cards.forEach(card => {
            const checkbox = card.querySelector('input[type="checkbox"]');
            
            card.addEventListener('click', (e) => {
                if (e.target === checkbox || e.target.closest('input[type="checkbox"]')) {
                    return;
                }

                e.preventDefault();

                if (maxSelect && !checkbox?.checked) {
                    const checkedCount = container.querySelectorAll('input[type="checkbox"]:checked').length;
                    if (checkedCount >= maxSelect) {
                        return;
                    }
                }

                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    card.classList.toggle('selected');
                    
                    if (onSelect) {
                        const selected = Array.from(cards)
                            .filter(c => c.classList.contains('selected'))
                            .map(c => c.querySelector('input[type="checkbox"]')?.value);
                        onSelect(selected);
                    }
                }
            });

            checkbox?.addEventListener('change', () => {
                card.classList.toggle('selected');
                if (onSelect) {
                    const selected = Array.from(cards)
                        .filter(c => c.classList.contains('selected'))
                        .map(c => c.querySelector('input[type="checkbox"]')?.value);
                    onSelect(selected);
                }
            });

            if (checkbox?.checked) {
                card.classList.add('selected');
            }
        });
    },

    /**
     * Get selected value(s)
     * @param {string} containerSelector - CSS selector for cards container
     * @returns {string|Array} Selected value or array of values
     */
    getSelected(containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return null;

        const radio = container.querySelector('input[type="radio"]:checked');
        if (radio) return radio.value;

        const checkboxes = container.querySelectorAll('input[type="checkbox"]:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    },

    /**
     * Clear all selections
     * @param {string} containerSelector - CSS selector for cards container
     */
    clearSelection(containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        container.querySelectorAll('.selection-card').forEach(card => {
            card.classList.remove('selected');
        });

        container.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(input => {
            input.checked = false;
        });
    },

    /**
     * Select specific card(s)
     * @param {string} containerSelector - CSS selector for cards container
     * @param {string|Array} values - Value(s) to select
     */
    selectByValue(containerSelector, values) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        const valuesArray = Array.isArray(values) ? values : [values];
        
        container.querySelectorAll('.selection-card').forEach(card => {
            const input = card.querySelector('input[type="radio"], input[type="checkbox"]');
            if (input && valuesArray.includes(input.value)) {
                input.checked = true;
                card.classList.add('selected');
            } else {
                input.checked = false;
                card.classList.remove('selected');
            }
        });
    }
};

export default SelectionCardsComponent;
