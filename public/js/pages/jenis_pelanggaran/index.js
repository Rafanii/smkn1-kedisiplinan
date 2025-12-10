document.addEventListener('DOMContentLoaded', function() {
    initTableInteractions();
});

function initTableInteractions() {
    const deleteButtons = document.querySelectorAll('form[onsubmit*="confirm"]');
    
    deleteButtons.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Yakin ingin menghapus aturan pelanggaran ini?')) {
                e.preventDefault();
            }
        });
    });
}
