/**
 * Sticky Filter Component
 * Handles sticky filter behavior on scroll
 * Works both in module and non-module environments
 */

(function(window) {
    const StickyFilterComponent = {
        /**
         * Initialize sticky filter
         * @param {string} filterId - ID of sticky filter element
         * @param {number} headerThreshold - Scroll distance before header hides (default: 20)
         */
        init(filterId, headerThreshold = 20) {
            const stickyFilter = document.getElementById(filterId);
            if (!stickyFilter) return;

            let lastScrollY = 0;

            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;

                if (currentScrollY > headerThreshold) {
                    stickyFilter.classList.add('compact-mode');
                    
                    if (currentScrollY > lastScrollY) {
                        stickyFilter.classList.add('header-hidden');
                    } else {
                        stickyFilter.classList.remove('header-hidden');
                    }
                } else {
                    stickyFilter.classList.remove('compact-mode');
                    stickyFilter.classList.remove('header-hidden');
                }

                lastScrollY = currentScrollY;
            }, { passive: true });
        },

        /**
         * Toggle filter header visibility
         * @param {string} filterId - ID of sticky filter element
         */
        toggleHeader(filterId) {
            const stickyFilter = document.getElementById(filterId);
            if (stickyFilter) {
                stickyFilter.classList.toggle('header-hidden');
            }
        },

        /**
         * Show filter header
         * @param {string} filterId - ID of sticky filter element
         */
        showHeader(filterId) {
            const stickyFilter = document.getElementById(filterId);
            if (stickyFilter) {
                stickyFilter.classList.remove('header-hidden');
            }
        },

        /**
         * Hide filter header
         * @param {string} filterId - ID of sticky filter element
         */
        hideHeader(filterId) {
            const stickyFilter = document.getElementById(filterId);
            if (stickyFilter) {
                stickyFilter.classList.add('header-hidden');
            }
        }
    };

    window.StickyFilterComponent = StickyFilterComponent;
})(window);
