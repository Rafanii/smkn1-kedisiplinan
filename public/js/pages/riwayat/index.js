/**
 * Riwayat Index Page - Violation History
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

    // Initialize export functionality
    initExportFunctionality();
});

/**
 * Initialize filter buttons
 */
function initFilterButtons() {
    const filterContainer = document.querySelector('.filter-pills');
    const form = document.getElementById('filterForm');

    if (!filterContainer || !form) return;

    const buttons = filterContainer.querySelectorAll('.btn');

    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get filter name and value from button
            const filterName = this.getAttribute('data-filter-name');
            const filterValue = this.getAttribute('data-filter-value');

            if (!filterName) return;

            // Toggle active state
            const isActive = this.classList.contains('active');
            
            buttons.forEach(b => {
                if (b.getAttribute('data-filter-name') === filterName) {
                    b.classList.remove('active');
                }
            });

            if (!isActive) {
                this.classList.add('active');
                
                // Set hidden input and submit
                let input = form.querySelector(`input[name="${filterName}"]`);
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = filterName;
                    form.appendChild(input);
                }
                input.value = filterValue;
            }

            form.submit();
        });

        // Set active button based on URL params
        const filterName = btn.getAttribute('data-filter-name');
        const filterValue = btn.getAttribute('data-filter-value');
        const urlParams = new URLSearchParams(window.location.search);
        
        if (filterName && filterValue && urlParams.get(filterName) === filterValue) {
            btn.classList.add('active');
        }
    });
}

/**
 * Initialize table interactions
 */
function initTableInteractions() {
    const table = document.querySelector('.table-premium');
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        // Add click handler for row
        row.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                return; // Don't interfere with links
            }
            this.classList.toggle('active');
        });

        // Add hover effects
        row.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.opacity = '0.8';
            }
        });

        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.opacity = '1';
            }
        });

        // Initialize action buttons
        initRowActions(row);
    });

    // Initialize table responsiveness
    initTableResponsiveness();
}

/**
 * Initialize row actions
 * @param {HTMLTableRowElement} row - Table row
 */
function initRowActions(row) {
    const editBtn = row.querySelector('.btn-edit');
    const deleteBtn = row.querySelector('.btn-delete');
    const viewBtn = row.querySelector('.btn-view');

    if (editBtn) {
        editBtn.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    }

    if (viewBtn) {
        viewBtn.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

/**
 * Initialize table responsiveness
 */
function initTableResponsiveness() {
    const wrapper = document.querySelector('.table-wrapper');
    if (!wrapper) return;

    // Add data-label attributes for mobile view
    const table = wrapper.querySelector('.table-premium');
    const headers = table.querySelectorAll('thead th');

    table.querySelectorAll('tbody td').forEach((cell, index) => {
        const headerText = headers[index % headers.length]?.textContent || '';
        cell.setAttribute('data-label', headerText.trim());
    });
}

/**
 * Initialize export functionality
 */
function initExportFunctionality() {
    const exportBtn = document.querySelector('[data-action="export"]');
    if (!exportBtn) return;

    exportBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const format = this.getAttribute('data-format') || 'pdf';
        const currentUrl = window.location.href;
        const exportUrl = currentUrl + (currentUrl.includes('?') ? '&' : '?') + `export=${format}`;
        
        window.location.href = exportUrl;
    });
}

/**
 * Reset all filters
 */
function resetFilters() {
    const form = document.getElementById('filterForm');
    if (!form) return;

    form.reset();
    
    const buttons = document.querySelectorAll('.filter-pills .btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    form.submit();
}

/**
 * Get table data as array
 * @returns {Array} Array of row data
 */
function getTableData() {
    const table = document.querySelector('.table-premium');
    if (!table) return [];

    const headers = Array.from(table.querySelectorAll('thead th')).map(h => h.textContent.trim());
    const rows = [];

    table.querySelectorAll('tbody tr').forEach(row => {
        const cells = Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim());
        const rowData = {};
        
        headers.forEach((header, index) => {
            rowData[header] = cells[index] || '';
        });

        rows.push(rowData);
    });

    return rows;
}

/**
 * Filter table by search term
 * @param {string} searchTerm - Term to search
 */
function filterTable(searchTerm) {
    const rows = document.querySelectorAll('.table-premium tbody tr');
    const lowerSearchTerm = searchTerm.toLowerCase();

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(lowerSearchTerm) ? '' : 'none';
    });
}

/**
 * Sort table by column
 * @param {number} columnIndex - Column index to sort
 * @param {string} order - 'asc' or 'desc'
 */
function sortTable(columnIndex, order = 'asc') {
    const table = document.querySelector('.table-premium');
    if (!table) return;

    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const isNumeric = !isNaN(parseFloat(rows[0].cells[columnIndex].textContent));

    rows.sort((a, b) => {
        const aVal = a.cells[columnIndex].textContent;
        const bVal = b.cells[columnIndex].textContent;

        if (isNumeric) {
            return order === 'asc' 
                ? parseFloat(aVal) - parseFloat(bVal)
                : parseFloat(bVal) - parseFloat(aVal);
        } else {
            return order === 'asc'
                ? aVal.localeCompare(bVal)
                : bVal.localeCompare(aVal);
        }
    });

    const tbody = table.querySelector('tbody');
    rows.forEach(row => tbody.appendChild(row));
}
