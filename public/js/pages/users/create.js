document.addEventListener('DOMContentLoaded', function() {
    initRoleToggle();
    initFiltering();
});

function initRoleToggle() {
    const roleSelect = document.getElementById('roleSelect');
    const siswaSection = document.getElementById('siswaSection');

    if (!roleSelect || !siswaSection) return;

    roleSelect.addEventListener('change', function() {
        const selectedText = this.options[this.selectedIndex].text;
        if (selectedText.includes('Wali Murid')) {
            siswaSection.style.display = 'block';
        } else {
            siswaSection.style.display = 'none';
        }
    });

    roleSelect.dispatchEvent(new Event('change'));
}

function initFiltering() {
    const filterTingkat = document.getElementById('filterTingkat');
    const filterJurusan = document.getElementById('filterJurusan');
    const filterKelas = document.getElementById('filterKelas');
    const searchSiswa = document.getElementById('searchSiswa');
    const resetBtn = document.querySelector('button[onclick="resetFilters()"]');

    if (!filterTingkat || !searchSiswa) return;

    function applyFilter() {
        const tingkat = filterTingkat.value;
        const jurusan = filterJurusan.value;
        const kelas = filterKelas.value;
        const searchText = searchSiswa.value.toLowerCase();

        const studentItems = document.querySelectorAll('.student-item');
        let visibleCount = 0;

        studentItems.forEach(item => {
            const itemTingkat = item.dataset.tingkat;
            const itemJurusan = item.dataset.jurusan;
            const itemKelas = item.dataset.kelas;
            const itemSearch = item.dataset.search;

            let match = true;

            if (tingkat && itemTingkat !== tingkat) match = false;
            if (jurusan && itemJurusan !== jurusan) match = false;
            if (kelas && itemKelas !== kelas) match = false;
            if (searchText && !itemSearch.includes(searchText)) match = false;

            if (match) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const noResultMsg = document.getElementById('noResultMsg');
        if (noResultMsg) {
            noResultMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    filterTingkat.addEventListener('change', applyFilter);
    filterJurusan.addEventListener('change', applyFilter);
    filterKelas.addEventListener('change', applyFilter);
    searchSiswa.addEventListener('keyup', applyFilter);

    if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
    }
}

function resetFilters() {
    document.getElementById('filterTingkat').value = '';
    document.getElementById('filterJurusan').value = '';
    document.getElementById('filterKelas').value = '';
    document.getElementById('searchSiswa').value = '';

    const studentItems = document.querySelectorAll('.student-item');
    studentItems.forEach(item => item.style.display = 'block');

    const noResultMsg = document.getElementById('noResultMsg');
    if (noResultMsg) {
        noResultMsg.style.display = 'none';
    }
}
