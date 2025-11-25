/**
 * USERS MANAGEMENT - FILTER MODULE
 * ===============================
 * Comprehensive filter management for users page
 * Features: Collapsible card, search filtering, role filtering
 * Pattern: IIFE Module (no global pollution)
 * Last Updated: November 25, 2025
 */

const UsersFilterModule = (() => {
    // ===================================
    // 1. CONFIGURATION
    // ===================================
    const CONFIG = {
        formId: 'filterForm',
        cardId: 'filterCard',
        searchId: 'cari',
        roleSelectId: 'role_id',
        applyBtnId: 'filterApplyBtn',
        debugMode: true,
    };

    // ===================================
    // 2. STATE MANAGEMENT
    // ===================================
    const state = {
        hasActiveFilters: false,
        filterValues: {
            cari: null,
            roleId: null,
        },
        cardCollapsed: false,
    };

    // ===================================
    // 3. INITIALIZATION
    // ===================================
    function init() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) {
            console.warn('Filter form not found:', CONFIG.formId);
            return;
        }

        captureInitialState();
        setupFilterHandlers();
        expandCardIfFiltersActive();
        updateHasActiveFilters();
        debugLog('✅ UsersFilterModule initialized');
    }

    // ===================================
    // 4. CAPTURE INITIAL STATE
    // ===================================
    function captureInitialState() {
        const params = new URLSearchParams(window.location.search);
        state.filterValues = {
            cari: params.get('cari') || null,
            roleId: params.get('role_id') || null,
        };
        debugLog('Initial filter state:', state.filterValues);
    }

    // ===================================
    // 5. SETUP EVENT HANDLERS
    // ===================================
    function setupFilterHandlers() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return;

        // Search input - with debounce (optional)
        const searchInput = form.querySelector('input[name="cari"]');
        if (searchInput) {
            searchInput.addEventListener('input', (event) => {
                state.filterValues.cari = event.target.value;
                updateHasActiveFilters();
                debugLog('Search input changed:', event.target.value);
            });
        }

        // Role select
        const roleSelect = form.querySelector('select[name="role_id"]');
        if (roleSelect) {
            roleSelect.addEventListener('change', (event) => {
                state.filterValues.roleId = event.target.value;
                updateHasActiveFilters();
                debugLog('Role select changed:', event.target.value);
            });
        }

        debugLog('✅ Event handlers attached');
    }

    // ===================================
    // 6. EXPAND CARD IF FILTERS ACTIVE
    // ===================================
    function expandCardIfFiltersActive() {
        if (state.filterValues.cari || state.filterValues.roleId) {
            // The card should be auto-expanded by Bootstrap's collapse widget
            // if data-card-widget="collapse" is properly configured
            debugLog('📂 Filters are active - card should be expanded');
        }
    }

    // ===================================
    // 7. UPDATE ACTIVE FILTERS FLAG
    // ===================================
    function updateHasActiveFilters() {
        const hasFilters = state.filterValues.cari || state.filterValues.roleId;
        state.hasActiveFilters = hasFilters;
        debugLog(`🔎 Active filters: ${state.hasActiveFilters ? 'YES' : 'NO'}`);
    }

    // ===================================
    // 8. PUBLIC API - GET FILTER VALUES
    // ===================================
    function getFilterValues() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return {};

        const formData = new FormData(form);
        const values = {};

        for (let [key, value] of formData.entries()) {
            values[key] = value;
        }

        return values;
    }

    // ===================================
    // 9. PUBLIC API - GET STATUS
    // ===================================
    function getStatus() {
        return {
            hasActiveFilters: state.hasActiveFilters,
            filterCount: Object.values(state.filterValues).filter(v => v).length,
            filters: state.filterValues,
        };
    }

    // ===================================
    // 10. PUBLIC API - SUBMIT FORM
    // ===================================
    function submitForm() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return;

        debugLog('📤 Submitting filter form');
        form.submit();
    }

    // ===================================
    // 11. PUBLIC API - RESET FILTERS
    // ===================================
    function resetFilters() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return;

        form.reset();
        state.filterValues = {
            cari: null,
            roleId: null,
        };

        updateHasActiveFilters();
        debugLog('🔄 Filters reset');

        // Redirect to clean URL
        window.location.href = window.location.pathname;
    }

    // ===================================
    // 12. PUBLIC API - DEBUG LOG
    // ===================================
    function debugLog(...args) {
        if (CONFIG.debugMode) {
            console.log('[UsersFilter]', ...args);
        }
    }

    // ===================================
    // 13. PUBLIC API
    // ===================================
    return {
        init,
        getFilterValues,
        getStatus,
        submitForm,
        resetFilters,
        debugLog,
    };
})();

// ===================================
// 14. AUTO-INITIALIZATION
// ===================================
document.addEventListener('DOMContentLoaded', () => {
    UsersFilterModule.init();
});
