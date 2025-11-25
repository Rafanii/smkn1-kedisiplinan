/**
 * RIWAYAT PELANGGARAN - FILTER MODULE
 * ===================================
 * Comprehensive filter management for riwayat page
 * Features: Auto-submit, date validation, debounced search, sticky effect
 * Pattern: IIFE Module (no global pollution)
 * Last Updated: November 25, 2025
 */

const RiwayatFilterModule = (() => {
    // ===================================
    // 1. CONFIGURATION
    // ===================================
    const CONFIG = {
        formId: 'filterForm',
        filterId: 'stickyFilter',
        startDateId: 'start_date',
        endDateId: 'end_date',
        searchId: 'liveSearch',
        debounceDelay: 800, // ms
        stickyOffset: 50, // px from top
        debugMode: true,
    };

    // ===================================
    // 2. STATE MANAGEMENT
    // ===================================
    const state = {
        debounceTimer: null,
        isSearching: false,
        hasActiveFilters: false,
        stickyActive: false,
        filterValues: {
            startDate: null,
            endDate: null,
            kelasId: null,
            jenisPelanggaranId: null,
            cariSiswa: null,
        },
        originalScrollPos: 0,
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
        setupStickyEffect();
        updateHasActiveFilters();
        debugLog('✅ RiwayatFilterModule initialized');
    }

    // ===================================
    // 4. CAPTURE INITIAL STATE
    // ===================================
    function captureInitialState() {
        const params = new URLSearchParams(window.location.search);
        state.filterValues = {
            startDate: params.get('start_date') || null,
            endDate: params.get('end_date') || null,
            kelasId: params.get('kelas_id') || null,
            jenisPelanggaranId: params.get('jenis_pelanggaran_id') || null,
            cariSiswa: params.get('cari_siswa') || null,
        };
        debugLog('Initial filter state:', state.filterValues);
    }

    // ===================================
    // 5. SETUP EVENT HANDLERS
    // ===================================
    function setupFilterHandlers() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return;

        // Date range inputs
        const startDateInput = form.querySelector('input[name="start_date"]');
        const endDateInput = form.querySelector('input[name="end_date"]');

        if (startDateInput) {
            startDateInput.addEventListener('change', handleDateChange);
            startDateInput.addEventListener('input', () => {
                state.filterValues.startDate = startDateInput.value;
            });
        }

        if (endDateInput) {
            endDateInput.addEventListener('change', handleDateChange);
            endDateInput.addEventListener('input', () => {
                state.filterValues.endDate = endDateInput.value;
            });
        }

        // Kelas select
        const kelasSelect = form.querySelector('select[name="kelas_id"]');
        if (kelasSelect) {
            kelasSelect.addEventListener('change', () => {
                state.filterValues.kelasId = kelasSelect.value;
                handleSelectChange();
            });
        }

        // Jenis Pelanggaran select
        const jenisPelanggaranSelect = form.querySelector('select[name="jenis_pelanggaran_id"]');
        if (jenisPelanggaranSelect) {
            jenisPelanggaranSelect.addEventListener('change', () => {
                state.filterValues.jenisPelanggaranId = jenisPelanggaranSelect.value;
                handleSelectChange();
            });
        }

        // Live search with debounce
        const searchInput = form.querySelector('input[name="cari_siswa"]');
        if (searchInput) {
            searchInput.addEventListener('input', handleSearchInput);
        }

        debugLog('✅ Event handlers attached');
    }

    // ===================================
    // 6. HANDLE DATE CHANGE
    // ===================================
    function handleDateChange() {
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');

        if (!startDateInput || !endDateInput) return;

        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        debugLog(`📅 Date changed - Start: ${startDate}, End: ${endDate}`);

        // Validate date range
        if (startDate && endDate && startDate > endDate) {
            console.warn('⚠️ Start date is after end date!');
            // Keep form as-is, user can correct
        }

        // Auto-submit form
        submitForm();
    }

    // ===================================
    // 7. HANDLE SELECT CHANGE (Auto-submit)
    // ===================================
    function handleSelectChange() {
        debugLog('Select changed - submitting form');
        submitForm();
    }

    // ===================================
    // 8. HANDLE SEARCH INPUT (Debounced)
    // ===================================
    function handleSearchInput(event) {
        const searchValue = event.target.value;
        state.filterValues.cariSiswa = searchValue;

        // Clear previous timer
        if (state.debounceTimer) {
            clearTimeout(state.debounceTimer);
        }

        // Set new timer
        state.debounceTimer = setTimeout(() => {
            debugLog(`🔍 Search debounce triggered: "${searchValue}"`);
            submitForm();
        }, CONFIG.debounceDelay);
    }

    // ===================================
    // 9. SUBMIT FORM
    // ===================================
    function submitForm() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return;

        debugLog('📤 Submitting filter form');
        form.submit();
    }

    // ===================================
    // 10. STICKY EFFECT
    // ===================================
    function setupStickyEffect() {
        window.addEventListener('scroll', handleScroll);
        debugLog('✅ Sticky effect initialized');
    }

    function handleScroll() {
        const filterElement = document.getElementById(CONFIG.filterId);
        if (!filterElement) return;

        const scrollPos = window.scrollY || window.pageYOffset;
        const filterRect = filterElement.getBoundingClientRect();

        // Check if filter should be sticky
        if (scrollPos > CONFIG.stickyOffset && !state.stickyActive) {
            filterElement.classList.add('sticky');
            state.stickyActive = true;
            debugLog('📌 Sticky mode ON');
        } else if (scrollPos <= CONFIG.stickyOffset && state.stickyActive) {
            filterElement.classList.remove('sticky');
            state.stickyActive = false;
            debugLog('📌 Sticky mode OFF');
        }
    }

    // ===================================
    // 11. UPDATE ACTIVE FILTERS FLAG
    // ===================================
    function updateHasActiveFilters() {
        const hasFilters =
            state.filterValues.startDate ||
            state.filterValues.endDate ||
            state.filterValues.kelasId ||
            state.filterValues.jenisPelanggaranId ||
            state.filterValues.cariSiswa;

        state.hasActiveFilters = hasFilters;
        debugLog(`🔎 Active filters: ${state.hasActiveFilters ? 'YES' : 'NO'}`);
    }

    // ===================================
    // 12. PUBLIC API - GET FILTER VALUES
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
    // 13. PUBLIC API - GET STATUS
    // ===================================
    function getStatus() {
        return {
            hasActiveFilters: state.hasActiveFilters,
            filterCount: Object.values(state.filterValues).filter(v => v).length,
            filters: state.filterValues,
            sticky: state.stickyActive,
        };
    }

    // ===================================
    // 14. PUBLIC API - RESET FILTERS
    // ===================================
    function resetFilters() {
        const form = document.getElementById(CONFIG.formId);
        if (!form) return;

        form.reset();
        state.filterValues = {
            startDate: null,
            endDate: null,
            kelasId: null,
            jenisPelanggaranId: null,
            cariSiswa: null,
        };

        updateHasActiveFilters();
        debugLog('🔄 Filters reset');

        // Redirect to clean URL
        window.location.href = window.location.pathname;
    }

    // ===================================
    // 15. PUBLIC API - DEBUG LOG
    // ===================================
    function debugLog(...args) {
        if (CONFIG.debugMode) {
            console.log('[RiwayatFilter]', ...args);
        }
    }

    // ===================================
    // 16. PUBLIC API
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
// 17. AUTO-INITIALIZATION
// ===================================
document.addEventListener('DOMContentLoaded', () => {
    RiwayatFilterModule.init();
});
