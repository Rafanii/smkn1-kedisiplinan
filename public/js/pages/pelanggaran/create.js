/**
 * Pelanggaran Create Page - Record Violation
 * Manages student selection, violation selection, and form submission
 */

let currentFilterTopic = 'all';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize filters
    initStudentFilters();
    initViolationSearch();
    initFilterPills();
    initFileInput();
});

/**
 * Initialize student filters
 */
function initStudentFilters() {
    const filterTingkat = document.getElementById('filterTingkat');
    const filterJurusan = document.getElementById('filterJurusan');
    const filterKelas = document.getElementById('filterKelas');
    const searchInput = document.getElementById('searchSiswa');
    
    if (filterTingkat) filterTingkat.addEventListener('change', applyStudentFilters);
    if (filterJurusan) filterJurusan.addEventListener('change', applyStudentFilters);
    if (filterKelas) filterKelas.addEventListener('change', applyStudentFilters);
    if (searchInput) searchInput.addEventListener('keyup', applyStudentFilters);
}

/**
 * Apply student filters
 */
function applyStudentFilters() {
    const container = document.getElementById('studentListContainer');
    const students = container.querySelectorAll('.student-item');
    
    const tingkat = document.getElementById('filterTingkat').value;
    const jurusan = document.getElementById('filterJurusan').value;
    const kelas = document.getElementById('filterKelas').value;
    const search = document.getElementById('searchSiswa').value.toLowerCase();
    
    let visibleCount = 0;
    
    students.forEach(student => {
        let show = true;
        
        if (tingkat && student.dataset.tingkat !== tingkat) show = false;
        if (jurusan && student.dataset.jurusan !== jurusan) show = false;
        if (kelas && student.dataset.kelas !== kelas) show = false;
        if (search && !student.dataset.search.includes(search)) show = false;
        
        student.style.display = show ? 'flex' : 'none';
        if (show) visibleCount++;
    });
    
    const noMsg = container.querySelector('#noResultMsg');
    if (noMsg) noMsg.style.display = visibleCount === 0 ? 'block' : 'none';
}

/**
 * Select student
 */
function selectStudent(element) {
    const container = document.getElementById('studentListContainer');
    const students = container.querySelectorAll('.student-item');
    
    students.forEach(s => s.classList.remove('selected'));
    element.classList.add('selected');
    
    const radio = element.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
}

/**
 * Reset student filters
 */
function resetFilters() {
    document.getElementById('filterTingkat').value = '';
    document.getElementById('filterJurusan').value = '';
    document.getElementById('filterKelas').value = '';
    document.getElementById('searchSiswa').value = '';
    applyStudentFilters();
}

/**
 * Initialize violation search
 */
function initViolationSearch() {
    const searchInput = document.getElementById('searchPelanggaran');
    if (searchInput) {
        searchInput.addEventListener('keyup', applyViolationSearch);
    }
}

/**
 * Apply violation search
 */
function applyViolationSearch() {
    const violations = document.querySelectorAll('.violation-item');
    if (violations.length === 0) return;
    
    const search = document.getElementById('searchPelanggaran').value.toLowerCase();
    
    let visibleCount = 0;
    
    violations.forEach(violation => {
        const nama = violation.dataset.nama || '';
        const kategori = violation.dataset.kategori || '';
        
        let show = !search || nama.includes(search) || kategori.includes(search);
        
        // Apply category filter if active
        if (currentFilterTopic !== 'all' && currentFilterTopic !== 'berat') {
            show = show && kategori === currentFilterTopic;
        } else if (currentFilterTopic === 'berat') {
            show = show && kategori === 'berat';
        }
        
        violation.style.display = show ? 'flex' : 'none';
        if (show) visibleCount++;
    });
    
    const noMsg = document.getElementById('noViolationMsg');
    if (noMsg) noMsg.style.display = visibleCount === 0 ? 'block' : 'none';
}

/**
 * Select violation
 */
function selectViolation(element) {
    const violations = document.querySelectorAll('.violation-item');
    
    violations.forEach(v => v.classList.remove('selected'));
    element.classList.add('selected');
    
    const radio = element.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
}

/**
 * Initialize filter pills
 */
function initFilterPills() {
    // Filter pills will be handled by setFilterTopic function
    // which is called directly from onclick in the blade template
}

/**
 * Set filter topic
 */
function setFilterTopic(topic, element) {
    const container = document.querySelector('.filter-pills');
    const buttons = container.querySelectorAll('.btn');
    
    buttons.forEach(b => b.classList.remove('active'));
    element.classList.add('active');
    
    currentFilterTopic = topic;
    applyViolationSearch();
}

/**
 * Initialize file input handler
 */
function initFileInput() {
    const fileInput = document.getElementById('customFile');
    if (fileInput && typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }
}
