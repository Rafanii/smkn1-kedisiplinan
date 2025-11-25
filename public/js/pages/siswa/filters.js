/**
 * Siswa Index - Filter Module
 * Location: public/js/pages/siswa/filters.js
 * Purpose: Handle all filter interactions, state management, and validation
 * Pattern: IIFE Module
 * 
 * Features:
 * - Auto-submit on filter change
 * - Live search with debounce
 * - Reset functionality
 * - Sticky filter effect
 * - Filter state tracking
 * - Mobile responsive
 */

const SiswaFilterModule = (() => {
    'use strict';

    // ========== CONFIG ==========
    const CONFIG = {
        formId: 'filterForm',
        filterId: 'stickyFilter',
        searchId: 'liveSearch',
        debounceDelay: 800,
        stickyOffset: 50,
        selectors: {
            filterSelect: '.filter-select',
            filterSearch: '.filter-search',
            resetBtn: '.filter-reset-btn',
            filterForm: '#filterForm'
        }
    };

    // ========== STATE ==========
    const state = {
        debounceTimer: null,
        isSearching: false,
        hasActiveFilters: false,
        stickyActive: false,
        filterValues: {}
    };

    /**
     * Initialize filter module
     */
    function init() {
        setupFilterHandlers();
        setupStickyEffect();
        captureInitialState();
        
        console.log('[SiswaFilter] Module initialized');
    }

    /**
     * Setup filter event handlers
     */
    function setupFilterHandlers() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return;

        // Use event delegation on the form to reliably catch select/input changes
        // This is more robust across dynamic DOM updates and avoids missing binds.
        form.addEventListener('change', function(e) {
            const t = e.target;
            if (!t) return;
            if (t.matches && t.matches('select[data-filter], select.filter-select')) {
                handleSelectChange({ target: t });
            }
        });

        // input delegation for search field
        form.addEventListener('input', function(e) {
            const t = e.target;
            if (!t) return;
            if (t.matches && (t.matches('input[data-filter="search"]') || t.matches('.filter-search'))) {
                handleSearchInput({ target: t });
            }
        });

        // ========== FORM SUBMISSION ==========
        form.addEventListener('submit', handleFormSubmit);

        // ========== RESET HANDLERS ==========
        const resetBtns = document.querySelectorAll(CONFIG.selectors.resetBtn);
        resetBtns.forEach(btn => {
            btn.addEventListener('click', handleReset);
        });
    }

    /**
     * Handle select filter change - auto submit
     */
    function handleSelectChange(event) {
        const select = event.target;
        const filterName = select.getAttribute('data-filter');
        const filterValue = select.value;

        console.log(`[SiswaFilter] Select changed: ${filterName} = ${filterValue}`);

        // Update state
        state.filterValues[filterName] = filterValue;
        updateHasActiveFilters();

        // Auto-submit form after brief delay
        clearTimeout(state.debounceTimer);
        // submit quickly for select changes to make filter feel live
        state.debounceTimer = setTimeout(() => submitForm(), 180);
    }

    /**
     * Handle search input with debounce
     */
    function handleSearchInput(event) {
        const input = event.target;
        const searchValue = input.value.trim();

        console.log(`[SiswaFilter] Search input: "${searchValue}"`);

        // Clear previous debounce
        clearTimeout(state.debounceTimer);

        // Always debounce and submit regardless of empty or not so clearing search
        // auto-refreshes the page. Use smaller delay for snappier UX when typing.
        const delay = searchValue.length === 0 ? 180 : CONFIG.debounceDelay;
        state.debounceTimer = setTimeout(() => {
            state.isSearching = searchValue.length > 0;
            console.log(`[SiswaFilter] Submitting search after debounce (len=${searchValue.length})`);
            submitForm();
        }, delay);
    }

    /**
     * Handle form submission with validation
     */
    function handleFormSubmit(event) {
        // Let default form behavior handle submission
        console.log('[SiswaFilter] Form submitted');
    }

    /**
     * Submit form via GET
     */
    function submitForm() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return;

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const url = `${form.action}?${params.toString()}`;

        console.log(`[SiswaFilter] Navigating to: ${url}`);
        window.location.href = url;
    }

    /**
     * Handle reset button
     */
    function handleReset(event) {
        event.preventDefault();

        console.log('[SiswaFilter] Reset triggered');

        // Clear all filter inputs
        const form = document.getElementById(CONFIG.formId);
        if (form) {
            // Reset selects
            form.querySelectorAll(CONFIG.selectors.filterSelect).forEach(select => {
                select.value = '';
            });

            // Reset search
            const searchInput = form.querySelector(CONFIG.selectors.filterSearch);
            if (searchInput) {
                searchInput.value = '';
            }
        }

        // Reset state
        state.filterValues = {};
        state.isSearching = false;
        updateHasActiveFilters();

        // Navigate to clean URL
        const baseUrl = form.action;
        console.log(`[SiswaFilter] Navigating to base: ${baseUrl}`);
        window.location.href = baseUrl;
    }

    /**
     * Setup sticky filter effect
     * Filter sticks to top when scrolling down
     */
    function setupStickyEffect() {
        const filterElement = document.getElementById(CONFIG.filterId);
        if (!filterElement) return;

        window.addEventListener('scroll', handleScroll);
        window.addEventListener('resize', handleScroll);
    }

    /**
     * Handle scroll for sticky effect
     */
    function handleScroll() {
        const filterElement = document.getElementById(CONFIG.filterId);
        if (!filterElement) return;

        const scrollPosition = window.scrollY;
        const elementTop = filterElement.offsetTop;

        if (scrollPosition > elementTop - CONFIG.stickyOffset) {
            if (!state.stickyActive) {
                filterElement.classList.add('sticky');
                state.stickyActive = true;
            }
        } else {
            if (state.stickyActive) {
                filterElement.classList.remove('sticky');
                state.stickyActive = false;
            }
        }
    }

    /**
     * Capture initial filter state from URL
     */
    function captureInitialState() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return;

        // Get all filter inputs
        form.querySelectorAll('[data-filter]').forEach(input => {
            const filterName = input.getAttribute('data-filter');
            const filterValue = input.value;
            if (filterValue) {
                state.filterValues[filterName] = filterValue;
            }
        });

        updateHasActiveFilters();
        console.log('[SiswaFilter] Initial state:', state.filterValues);
    }

    /**
     * Update hasActiveFilters flag
     */
    function updateHasActiveFilters() {
        state.hasActiveFilters = Object.values(state.filterValues).some(val => val !== '');
        console.log(`[SiswaFilter] Has active filters: ${state.hasActiveFilters}`);
    }

    /**
     * Get current filter values
     */
    function getFilterValues() {
        return { ...state.filterValues };
    }

    /**
     * Get filter status
     */
    function getStatus() {
        return {
            hasActiveFilters: state.hasActiveFilters,
            filterCount: Object.keys(state.filterValues).length,
            isSearching: state.isSearching,
            filters: getFilterValues()
        };
    }

    /**
     * Debug: Log current state
     */
    function debugLog() {
        console.group('[SiswaFilter] Module State');
        console.log('Config:', CONFIG);
        console.log('State:', state);
        console.log('Status:', getStatus());
        console.groupEnd();
    }

    // ========== PUBLIC API ==========
    return {
        init: init,
        getFilterValues: getFilterValues,
        getStatus: getStatus,
        submitForm: submitForm,
        resetFilters: handleReset,
        debugLog: debugLog
    };
})();

// Expose module to global scope so inline fallbacks can detect it
if (typeof window !== 'undefined' && !window.SiswaFilterModule) {
    window.SiswaFilterModule = SiswaFilterModule;
}

// ========== AUTO INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    // Defensive: if module already exposed and has init, use it
    try {
        if (window.SiswaFilterModule && typeof window.SiswaFilterModule.init === 'function') {
            window.SiswaFilterModule.init();
        } else {
            SiswaFilterModule.init();
        }
    } catch (err) {
        console.error('[SiswaFilter] Init error:', err);
    }
});
