/**
 * Search Utility Module
 * Handles live search and autocomplete functionality
 * Works both in module and non-module environments
 */

(function(window) {
    const SearchModule = {
        /**
         * Initialize live search with debouncing
         * @param {string} searchInputId - ID of search input element
         * @param {string} formId - ID of form to submit
         * @param {number} debounceMs - Debounce delay in milliseconds
         */
        initLiveSearch(searchInputId, formId, debounceMs = 800) {
            let timeout = null;
            const searchInput = document.getElementById(searchInputId);
            const form = document.getElementById(formId);

            if (!searchInput || !form) return;

            searchInput.addEventListener('keyup', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    form.submit();
                }, debounceMs);
            });

            // Restore focus & cursor position
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('cari') || urlParams.has('cari_siswa')) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        },

        /**
         * Clear search input and reset form
         * @param {string} searchInputId - ID of search input element
         * @param {string} formId - ID of form to submit
         */
        clearSearch(searchInputId, formId) {
            const searchInput = document.getElementById(searchInputId);
            const form = document.getElementById(formId);

            if (!searchInput) return;

            searchInput.value = '';
            if (form) form.submit();
        },

        /**
         * Get current search value
         * @param {string} searchInputId - ID of search input element
         * @returns {string} Current search value
         */
        getSearchValue(searchInputId) {
            const searchInput = document.getElementById(searchInputId);
            return searchInput ? searchInput.value : '';
        },

        /**
         * Set search value
         * @param {string} searchInputId - ID of search input element
         * @param {string} value - Search value to set
         */
        setSearchValue(searchInputId, value) {
            const searchInput = document.getElementById(searchInputId);
            if (searchInput) {
                searchInput.value = value;
            }
        }
    };

    window.SearchModule = SearchModule;
})(window);
