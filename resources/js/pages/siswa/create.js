document.addEventListener('DOMContentLoaded', function () {
    const nisnInput = document.querySelector('input[name="nisn"]');
    const createWaliCheckbox = document.getElementById('create_wali');
    const waliSelect = document.querySelector('select[name="wali_murid_user_id"]');
    const previewBox = document.getElementById('wali-preview');
    const previewUsername = document.getElementById('wali-preview-username');

    function computeUsername() {
        let nisn = (nisnInput && nisnInput.value) ? nisnInput.value.replace(/\D+/g, '') : '';
        if (!nisn) {
            previewUsername.textContent = '- (masukkan NISN untuk melihat preview)';
            return;
        }
        previewUsername.textContent = 'wali.' + nisn;
    }

    function updatePreviewVisibility() {
        // hide preview if an existing wali is selected
        const hasExistingWali = waliSelect && waliSelect.value;
        if (createWaliCheckbox && createWaliCheckbox.checked && !hasExistingWali) {
            previewBox.classList.remove('d-none');
            computeUsername();
        } else {
            previewBox.classList.add('d-none');
        }
    }

    if (nisnInput) {
        nisnInput.addEventListener('input', function () {
            if (previewBox && !previewBox.classList.contains('d-none')) {
                computeUsername();
            }
        });
    }

    if (createWaliCheckbox) {
        createWaliCheckbox.addEventListener('change', updatePreviewVisibility);
    }

    if (waliSelect) {
        waliSelect.addEventListener('change', updatePreviewVisibility);
    }

    // initial state
    updatePreviewVisibility();
});
