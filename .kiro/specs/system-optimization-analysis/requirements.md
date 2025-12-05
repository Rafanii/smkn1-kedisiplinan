# Analisis dan Optimasi Sistem Kepedulian Sekolah

## Pendahuluan

Dokumen ini berisi analisis mendalam terhadap sistem informasi kepedulian sekolah yang sudah berjalan, mengidentifikasi area yang perlu optimasi, dan mengkonfirmasi hal-hal yang perlu klarifikasi dari user.

## Glossary

- **Sistem**: Sistem Informasi Kepedulian Sekolah (Disciplinary System)
- **Operator**: Operator Sekolah (role dengan akses penuh ke master data)
- **Pencatat**: User yang mencatat pelanggaran (Guru, Wali Kelas, Waka, Kaprodi)
- **Rules Engine**: Service yang mengevaluasi poin dan frekuensi pelanggaran untuk menentukan tindak lanjut
- **Tindak Lanjut**: Kasus yang dibuat otomatis oleh sistem berdasarkan akumulasi pelanggaran
- **Data Scoping**: Pembatasan data yang bisa dilihat user berdasarkan role dan hierarki

---

## BAGIAN 1: ANALISIS ALUR UTAMA SISTEM

### 1.1 Alur Authentication & Authorization

**Status Saat Ini:** ✅ SUDAH BAGUS

**Yang Sudah Ada:**
- Login dengan username/password
- Middleware `auth` untuk proteksi route
- Middleware `profile.completed` untuk memaksa lengkapi profil
- Middleware `role` untuk kontrol akses berbasis role
- Developer impersonation untuk testing (non-production)
- Email verification (opsional, tidak mengunci akses)

**Temuan:**
- ✅ Hierarki role sudah jelas dan konsisten
- ✅ Data scoping per role sudah diterapkan dengan baik
- ✅ Tracking username/password changes sudah ada
- ⚠️ **PERLU KONFIRMASI**: Apakah email verification perlu dibuat wajib untuk role tertentu (misal: Kepala Sekolah, Operator)?

---

### 1.2 Alur Pencatatan Pelanggaran

**Status Saat Ini:** ✅ SUDAH BAGUS dengan beberapa area optimasi

**Yang Sudah Ada:**
- Multi-select siswa dan jenis pelanggaran
- Upload bukti foto (opsional)
- Auto-trigger Rules Engine setelah pencatatan
- Pencatat bisa edit/hapus riwayat mereka sendiri (max 3 hari)
- Operator bisa edit/hapus semua riwayat tanpa batasan

**Temuan:**
- ✅ Alur pencatatan sudah efisien
- ✅ Rules Engine sudah otomatis
- ⚠️ **PERLU KONFIRMASI**: 
  1. Apakah perlu notifikasi ke Wali Murid saat pelanggaran dicatat?
  2. Apakah perlu validasi tanggal kejadian tidak boleh masa depan?
  3. Apakah perlu konfirmasi sebelum hapus riwayat (untuk mencegah hapus tidak sengaja)?

---

### 1.3 Alur Rules Engine & Tindak Lanjut

**Status Saat Ini:** ✅ SUDAH BAGUS dengan logika bisnis yang kompleks

**Yang Sudah Ada:**
- Threshold poin untuk Surat 1, 2, 3
- Frekuensi spesifik (atribut 10x, alfa 4x)
- Akumulasi poin untuk eskalasi
- Auto-create/update TindakLanjut dan SuratPanggilan
- Rekonsiliasi saat edit/hapus riwayat
- Approval workflow untuk Surat 3 (Kepala Sekolah)

**Temuan:**
- ✅ Logika bisnis sudah lengkap dan terstruktur
- ✅ Rekonsiliasi sudah handle edge cases
- ⚠️ **PERLU KONFIRMASI**:
  1. Apakah threshold poin dan frekuensi sudah sesuai dengan kebijakan sekolah?
  2. Apakah perlu fitur untuk mengubah threshold tanpa edit code (misal: settings table)?
  3. Apakah perlu history log saat tindak lanjut di-escalate?

---

### 1.4 Alur Manajemen User

**Status Saat Ini:** ✅ SUDAH BAGUS setelah update terbaru

**Yang Sudah Ada:**
- CRUD user oleh Operator
- Auto-generate nama, username, password (fleksibel)
- Manual edit nama, username, password oleh Operator
- Tracking perubahan username/password
- Bulk create siswa dengan auto-create akun Wali Murid
- Download credentials CSV setelah bulk create

**Temuan:**
- ✅ Sistem sudah fleksibel dan user-friendly
- ✅ Operator bisa membantu user yang lupa kredensial
- ⚠️ **PERLU KONFIRMASI**:
  1. Apakah perlu fitur "Reset Password" yang mengirim email ke user (bukan manual edit oleh Operator)?
  2. Apakah perlu log aktivitas saat Operator mengubah password user lain?

---

### 1.5 Alur Data Scoping & Hierarki

**Status Saat Ini:** ✅ SUDAH BAGUS dan konsisten

**Yang Sudah Ada:**
- Operator & Waka: Lihat semua data
- Kepala Sekolah: Lihat semua data + approval
- Kaprodi: Lihat data jurusannya
- Wali Kelas: Lihat data kelasnya
- Guru: Lihat riwayat yang dia catat
- Wali Murid: Lihat data anaknya (support multiple children)

**Temuan:**
- ✅ Hierarki sudah jelas dan konsisten
- ✅ Data scoping sudah diterapkan di semua controller
- ✅ Multiple children untuk Wali Murid sudah support
- ✅ Tidak ada temuan yang perlu optimasi

---

## BAGIAN 2: AREA YANG PERLU OPTIMASI

### 2.1 Validasi Input & User Experience

**Requirement 1: Validasi Tanggal Kejadian**

**User Story:** Sebagai pencatat pelanggaran, saya ingin sistem mencegah input tanggal/jam masa depan, sehingga data yang tercatat akurat.

**Acceptance Criteria:**
1. WHEN pencatat mengisi tanggal kejadian THEN sistem SHALL memvalidasi bahwa tanggal tidak boleh lebih dari hari ini
2. WHEN pencatat mengisi jam kejadian THEN sistem SHALL memvalidasi bahwa kombinasi tanggal+jam tidak boleh masa depan
3. WHEN validasi gagal THEN sistem SHALL menampilkan pesan error yang jelas
4. WHEN pencatat mengisi tanggal kemarin atau hari ini THEN sistem SHALL menerima input tersebut

---

**Requirement 2: Konfirmasi Sebelum Hapus**

**User Story:** Sebagai pencatat pelanggaran, saya ingin konfirmasi sebelum menghapus riwayat, sehingga tidak terjadi penghapusan tidak sengaja.

**Acceptance Criteria:**
1. WHEN pencatat klik tombol hapus riwayat THEN sistem SHALL menampilkan dialog konfirmasi
2. WHEN pencatat konfirmasi hapus THEN sistem SHALL menghapus riwayat dan rekalkulasi tindak lanjut
3. WHEN pencatat batal hapus THEN sistem SHALL membatalkan aksi dan tetap di halaman yang sama
4. WHEN Operator hapus riwayat THEN sistem SHALL tetap menampilkan konfirmasi (untuk konsistensi)

---

### 2.2 Notifikasi & Komunikasi

**Requirement 3: Notifikasi Pelanggaran ke Wali Murid**

**User Story:** Sebagai Wali Murid, saya ingin mendapat notifikasi saat anak saya melakukan pelanggaran, sehingga saya bisa segera menindaklanjuti.

**Acceptance Criteria:**
1. WHEN pelanggaran dicatat untuk siswa THEN sistem SHALL mencatat notifikasi untuk Wali Murid siswa tersebut
2. WHEN Wali Murid login THEN sistem SHALL menampilkan badge notifikasi yang belum dibaca
3. WHEN Wali Murid klik notifikasi THEN sistem SHALL menampilkan detail pelanggaran dan menandai notifikasi sebagai dibaca
4. WHEN siswa tidak punya akun Wali Murid THEN sistem SHALL skip notifikasi tanpa error

**CATATAN:** Ini adalah fitur baru yang perlu development. Perlu konfirmasi apakah prioritas tinggi.

---

### 2.3 Audit Trail & Logging

**Requirement 4: Log Perubahan Password oleh Operator**

**User Story:** Sebagai Kepala Sekolah, saya ingin melihat log saat Operator mengubah password user lain, sehingga ada transparansi dan akuntabilitas.

**Acceptance Criteria:**
1. WHEN Operator mengubah password user lain THEN sistem SHALL mencatat log aktivitas dengan detail: operator_id, target_user_id, timestamp
2. WHEN Kepala Sekolah akses activity log THEN sistem SHALL menampilkan log perubahan password dengan filter
3. WHEN user mengubah password sendiri THEN sistem SHALL mencatat log dengan flag "self_change"
4. WHEN log ditampilkan THEN sistem SHALL menampilkan username operator dan target user (bukan ID)

---

**Requirement 5: History Log Eskalasi Tindak Lanjut**

**User Story:** Sebagai Waka Kesiswaan, saya ingin melihat history eskalasi tindak lanjut, sehingga saya bisa tracking perkembangan kasus.

**Acceptance Criteria:**
1. WHEN tindak lanjut di-escalate (Surat 1 → 2 → 3) THEN sistem SHALL mencatat history dengan timestamp dan pemicu
2. WHEN user akses detail tindak lanjut THEN sistem SHALL menampilkan timeline eskalasi
3. WHEN tindak lanjut dibatalkan karena edit/hapus riwayat THEN sistem SHALL mencatat alasan pembatalan di history
4. WHEN history ditampilkan THEN sistem SHALL menampilkan dalam format timeline yang mudah dibaca

---

### 2.4 Konfigurasi & Fleksibilitas

**Requirement 6: Settings untuk Threshold Rules Engine**

**User Story:** Sebagai Operator Sekolah, saya ingin mengubah threshold poin dan frekuensi tanpa edit code, sehingga sistem lebih fleksibel mengikuti kebijakan sekolah.

**Acceptance Criteria:**
1. WHEN Operator akses halaman settings THEN sistem SHALL menampilkan form untuk mengubah threshold poin (Surat 1, 2, 3)
2. WHEN Operator akses halaman settings THEN sistem SHALL menampilkan form untuk mengubah frekuensi spesifik (atribut, alfa)
3. WHEN Operator simpan settings THEN sistem SHALL memvalidasi input (harus angka positif, threshold Surat 3 > Surat 2 > Surat 1)
4. WHEN settings diubah THEN sistem SHALL menggunakan nilai baru untuk evaluasi pelanggaran berikutnya (tidak retroaktif)

**CATATAN:** Ini adalah fitur baru yang memerlukan tabel `settings` dan update Rules Engine. Perlu konfirmasi prioritas.

---

### 2.5 Reporting & Analytics

**Requirement 7: Dashboard Analytics untuk Kepala Sekolah**

**User Story:** Sebagai Kepala Sekolah, saya ingin melihat grafik trend pelanggaran per bulan/semester, sehingga saya bisa membuat keputusan strategis.

**Acceptance Criteria:**
1. WHEN Kepala Sekolah akses dashboard THEN sistem SHALL menampilkan grafik trend pelanggaran per bulan (6 bulan terakhir)
2. WHEN Kepala Sekolah akses dashboard THEN sistem SHALL menampilkan breakdown pelanggaran per kategori (pie chart)
3. WHEN Kepala Sekolah akses dashboard THEN sistem SHALL menampilkan top 10 siswa dengan poin tertinggi
4. WHEN Kepala Sekolah akses dashboard THEN sistem SHALL menampilkan perbandingan pelanggaran per jurusan (bar chart)

**CATATAN:** Ini adalah enhancement untuk dashboard existing. Perlu konfirmasi apakah prioritas tinggi.

---

## BAGIAN 3: PERTANYAAN KONFIRMASI UNTUK USER

### 3.1 Prioritas Fitur

**Pertanyaan 1:** Dari requirement 1-7 di atas, mana yang menjadi prioritas tertinggi untuk diimplementasikan terlebih dahulu?

**Opsi:**
- A. Requirement 1 & 2 (Validasi & Konfirmasi) - Paling cepat, impact langsung ke UX
- B. Requirement 3 (Notifikasi Wali Murid) - Fitur baru, perlu development lebih lama
- C. Requirement 4 & 5 (Audit Trail) - Penting untuk transparansi dan akuntabilitas
- D. Requirement 6 (Settings Threshold) - Fleksibilitas sistem, perlu tabel baru
- E. Requirement 7 (Dashboard Analytics) - Enhancement dashboard, perlu charting library

---

### 3.2 Kebijakan Sekolah

**Pertanyaan 2:** Apakah threshold poin dan frekuensi yang saat ini di-hardcode sudah sesuai dengan kebijakan sekolah?

**Threshold Saat Ini:**
- Surat 1: Frekuensi atribut 10x atau alfa 4x
- Surat 2: Poin 100-500 atau akumulasi 55-300
- Surat 3: Poin >501 atau akumulasi >301

**Opsi:**
- A. Sudah sesuai, tidak perlu diubah
- B. Perlu diubah, tapi bisa manual edit code untuk sekarang
- C. Perlu fitur settings agar bisa diubah tanpa edit code (Requirement 6)

---

### 3.3 Email Verification

**Pertanyaan 3:** Apakah email verification perlu dibuat wajib untuk role tertentu?

**Saat Ini:** Email verification opsional untuk semua role.

**Opsi:**
- A. Tetap opsional untuk semua role
- B. Wajib untuk Kepala Sekolah dan Operator saja
- C. Wajib untuk semua role kecuali Wali Murid
- D. Wajib untuk semua role

---

### 3.4 Reset Password

**Pertanyaan 4:** Apakah perlu fitur "Reset Password" yang mengirim email ke user (selain manual edit oleh Operator)?

**Saat Ini:** User yang lupa password harus lapor ke Operator, lalu Operator manual edit password.

**Opsi:**
- A. Tidak perlu, sistem saat ini sudah cukup
- B. Perlu, tapi hanya untuk role staff (bukan Wali Murid)
- C. Perlu untuk semua role

---

### 3.5 Notifikasi

**Pertanyaan 5:** Apakah notifikasi pelanggaran ke Wali Murid (Requirement 3) menjadi prioritas?

**Opsi:**
- A. Ya, prioritas tinggi (implement segera)
- B. Ya, tapi bisa nanti setelah fitur lain selesai
- C. Tidak perlu, Wali Murid bisa cek dashboard secara berkala

---

## BAGIAN 4: REKOMENDASI OPTIMASI CEPAT (Quick Wins)

Berikut adalah optimasi yang bisa dilakukan dengan cepat tanpa perlu development besar:

### 4.1 Validasi Tanggal Kejadian (Requirement 1)
- **Effort:** Low (1-2 jam)
- **Impact:** Medium (mencegah data tidak akurat)
- **Rekomendasi:** Implement segera

### 4.2 Konfirmasi Sebelum Hapus (Requirement 2)
- **Effort:** Low (30 menit)
- **Impact:** Medium (mencegah hapus tidak sengaja)
- **Rekomendasi:** Implement segera

### 4.3 Log Perubahan Password (Requirement 4)
- **Effort:** Low (1 jam)
- **Impact:** High (transparansi dan akuntabilitas)
- **Rekomendasi:** Implement segera

---

## BAGIAN 5: KESIMPULAN ANALISIS

### 5.1 Sistem Sudah Bagus Secara Keseluruhan

**Kekuatan Sistem:**
- ✅ Hierarki role dan data scoping sudah jelas dan konsisten
- ✅ Rules Engine sudah otomatis dan handle edge cases
- ✅ Audit trail (activity log) sudah ada
- ✅ Bulk operations sudah support
- ✅ Multiple children untuk Wali Murid sudah support
- ✅ Developer tools untuk testing sudah ada
- ✅ Code sudah clean dan well-documented

### 5.2 Area yang Perlu Optimasi

**Prioritas Tinggi (Quick Wins):**
1. Validasi tanggal kejadian (Requirement 1)
2. Konfirmasi sebelum hapus (Requirement 2)
3. Log perubahan password (Requirement 4)

**Prioritas Medium (Perlu Development):**
4. History log eskalasi tindak lanjut (Requirement 5)
5. Settings untuk threshold (Requirement 6)

**Prioritas Low (Enhancement):**
6. Notifikasi Wali Murid (Requirement 3)
7. Dashboard analytics (Requirement 7)

### 5.3 Pertanyaan yang Perlu Dijawab User

1. Prioritas fitur mana yang harus diimplementasikan terlebih dahulu?
2. Apakah threshold poin dan frekuensi sudah sesuai?
3. Apakah email verification perlu dibuat wajib?
4. Apakah perlu fitur reset password via email?
5. Apakah notifikasi Wali Murid menjadi prioritas?

---

**NEXT STEPS:**
Setelah user menjawab pertanyaan konfirmasi di atas, saya akan membuat design document dan implementation plan untuk fitur-fitur yang diprioritaskan.
