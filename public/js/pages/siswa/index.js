/**
 * Siswa Index Page - Student List
 * Manages sticky filters, search, and table interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sticky filter using global component
    if (typeof StickyFilterComponent !== 'undefined') {
        StickyFilterComponent.init('stickyFilter', 20);
    }

    // Initialize live search using global component
    if (typeof SearchModule !== 'undefined') {
        SearchModule.initLiveSearch('liveSearch', 'filterForm', 800);
    }

    // Initialize table interactions
    initTableInteractions();
});

/**
 * Initialize table interactions
 */
function initTableInteractions() {
    const table = document.querySelector('.table-premium');
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        row.addEventListener('click', function() {
            this.classList.toggle('active');
        });

        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });

        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.backgroundColor = '';
            }
        });
    });
}
