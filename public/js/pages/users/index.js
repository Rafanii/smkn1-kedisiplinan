document.addEventListener('DOMContentLoaded', function() {
    initTableInteractions();
    initFormSubmit();
});

function initTableInteractions() {
    const deleteButtons = document.querySelectorAll('form[onsubmit*="confirm"]');
    
    deleteButtons.forEach(form => {
        form.addEventListener('submit', function(e) {
            const userName = this.getAttribute('onsubmit').match(/{{ (.*?) }}/)?.[1] || 'user';
            if (!confirm('Yakin ingin menghapus pengguna ini?')) {
                e.preventDefault();
            }
        });
    });
}

function initFormSubmit() {
    const filterForm = document.querySelector('form[action*="users.index"]');
    if (!filterForm) return;
    
    const inputs = filterForm.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            filterForm.submit();
        });
    });
}
