document.addEventListener('DOMContentLoaded', function() {
    initFormValidation();
    initPrintButton();
});

function initFormValidation() {
    const form = document.querySelector('form[action*="kasus.update"]');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const tanggalInput = form.querySelector('input[name="tanggal_tindak_lanjut"]');
        if (tanggalInput && !tanggalInput.value) {
            e.preventDefault();
            alert('Tanggal penanganan harus diisi!');
        }
    });
}

function initPrintButton() {
    const printBtn = document.querySelector('.btn-print');
    if (!printBtn) return;
    
    printBtn.addEventListener('click', function(e) {
        console.log('Print button clicked');
    });
}
