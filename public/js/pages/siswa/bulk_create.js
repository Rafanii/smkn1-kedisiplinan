document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('bulkTable');
    const addRowBtn = document.getElementById('addRowBtn');
    const hiddenBulk = document.getElementById('bulk_data');
    const fileInput = document.querySelector('input[name="bulk_file"]');
    const form = document.getElementById('bulkCreateForm');
    const LARGE_THRESHOLD = 100; // warn if more than this many rows

    function addRow(nisn = '', nama = '', hp = '') {
        const tbody = table.querySelector('tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm bulk-nisn" value="${escapeHtml(nisn)}"></td>
            <td><input type="text" class="form-control form-control-sm bulk-nama" value="${escapeHtml(nama)}"></td>
            <td><input type="text" class="form-control form-control-sm bulk-hp" value="${escapeHtml(hp)}"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row">-</button></td>
        `;
        tbody.appendChild(tr);
        tr.querySelector('.remove-row').addEventListener('click', function () { tr.remove(); });
        return tr;
    }

    function escapeHtml(s) { return (s+'').replace(/[&<>\"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }

    // remove handlers for existing remove buttons
    table.querySelectorAll('.remove-row').forEach(btn => btn.addEventListener('click', function () { this.closest('tr').remove(); }));

    if (addRowBtn) addRowBtn.addEventListener('click', function () { addRow(); });

    // paste handling: support paste from spreadsheet (multi-row, multi-col)
    table.addEventListener('paste', function (e) {
        const target = e.target;
        const closestInput = target.closest('input[class*="bulk-"]');
        if (!closestInput) return; // only handle when pasting into bulk input cells
        
        e.preventDefault();
        
        const clipboard = (e.clipboardData || window.clipboardData).getData('text');
        if (!clipboard) return;
        
        // determine start position in table
        const startTr = closestInput.closest('tr');
        const tbody = table.querySelector('tbody');
        const allRows = Array.from(tbody.querySelectorAll('tr'));
        const startRowIndex = allRows.indexOf(startTr);
        
        if (startRowIndex < 0) return;
        
        // determine which column paste started in (0=nisn, 1=nama, 2=hp)
        let startColIndex = 0;
        if (closestInput.classList.contains('bulk-nama')) startColIndex = 1;
        else if (closestInput.classList.contains('bulk-hp')) startColIndex = 2;
        
        // parse clipboard: rows separated by newline, cols by tab/semicolon
        const clipRows = clipboard.split(/\r\n|\r|\n/).map(r => r.trim()).filter(r => r !== '');
        
        clipRows.forEach((clipRow, rowIdx) => {
            const clipCols = clipRow.split(/\t|;|,/).map(c => c.trim());
            let rowElem = allRows[startRowIndex + rowIdx];
            
            // create rows if needed
            if (!rowElem) {
                addRow();
                rowElem = tbody.querySelector('tr:last-child');
            }
            
            // fill cells starting from startColIndex
            const inputs = [rowElem.querySelector('.bulk-nisn'), rowElem.querySelector('.bulk-nama'), rowElem.querySelector('.bulk-hp')];
            clipCols.forEach((val, colIdx) => {
                const targetIdx = startColIndex + colIdx;
                if (targetIdx < 3 && inputs[targetIdx]) {
                    inputs[targetIdx].value = val;
                }
            });
        });
    });

    // file input change: show basic confirm (detailed preview handled by server)
    if (fileInput) fileInput.addEventListener('change', function () {
        if (!this.files || this.files.length === 0) return;
        // nothing else here — server will parse file
    });

    function serializeRows() {
        const lines = [];
        const errors = [];
        table.querySelectorAll('tbody tr').forEach((tr, idx) => {
            const nisn = tr.querySelector('.bulk-nisn').value.trim();
            const nama = tr.querySelector('.bulk-nama').value.trim();
            const hp = tr.querySelector('.bulk-hp').value.trim();
            if (nisn === '' && nama === '' && hp === '') return; // skip fully empty rows
            
            // validation: NISN dan Nama wajib (HP opsional)
            if (nisn === '') {
                errors.push(`Baris ${idx + 1}: NISN wajib diisi.`);
                tr.classList.add('table-danger');
                return;
            }
            if (nama === '') {
                errors.push(`Baris ${idx + 1}: Nama wajib diisi.`);
                tr.classList.add('table-danger');
                return;
            }
            if (!/^\d+$/.test(nisn) || nisn.length < 8) {
                errors.push(`Baris ${idx + 1}: NISN harus numeric minimal 8 digit.`);
                tr.classList.add('table-danger');
                return;
            }
            
            tr.classList.remove('table-danger');
            lines.push(`${nisn};${nama};${hp}`);
        });
        return { lines, errors };
    }

    if (form) {
        form.addEventListener('submit', function (ev) {
            // if file provided, skip serialization
            if (fileInput && fileInput.files.length > 0) {
                if (!confirm('Anda mengunggah file CSV. Lanjutkan proses bulk create?')) { ev.preventDefault(); }
                return;
            }

            const result = serializeRows();
            const rows = result.lines;
            const validationErrors = result.errors;
            
            if (validationErrors.length > 0) {
                ev.preventDefault();
                alert('Validasi gagal:\n\n' + validationErrors.join('\n'));
                return;
            }
            
            if (rows.length === 0) { ev.preventDefault(); alert('Tidak ada baris siswa yang diisi.'); return; }
            if (rows.length >= LARGE_THRESHOLD) {
                if (!confirm('Anda akan membuat ' + rows.length + ' siswa sekaligus. Proses ini akan membuat banyak akun jika opsi pembuatan wali dicentang. Lanjutkan?')) { ev.preventDefault(); return; }
            }
            // put into hidden textarea for backend parsing
            hiddenBulk.value = rows.join('\n');
        });
    }
});
