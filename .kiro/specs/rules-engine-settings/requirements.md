# Requirements Document: Rules Engine Settings Management

## Introduction

Fitur ini memungkinkan Operator Sekolah untuk mengubah threshold poin dan frekuensi pelanggaran yang digunakan oleh Rules Engine tanpa perlu mengedit code. Ini memberikan fleksibilitas kepada sekolah untuk menyesuaikan aturan sesuai kebijakan yang berlaku.

## Glossary

- **Rules Engine**: Service yang mengevaluasi poin dan frekuensi pelanggaran untuk menentukan tindak lanjut (Surat 1, 2, atau 3)
- **Threshold**: Batas nilai poin atau frekuensi yang memicu tindak lanjut tertentu
- **Surat 1/2/3**: Tingkat eskalasi pemanggilan wali murid (1 = ringan, 3 = berat)
- **Frekuensi Spesifik**: Jumlah pelanggaran jenis tertentu (atribut, alfa) yang memicu tindak lanjut
- **Akumulasi Poin**: Total poin dari semua pelanggaran siswa yang terakumulasi
- **Operator Sekolah**: Role dengan akses penuh ke master data dan konfigurasi sistem

## Requirements

### Requirement 1: Halaman Settings Rules Engine

**User Story:** Sebagai Operator Sekolah, saya ingin mengakses halaman settings untuk mengubah threshold Rules Engine, sehingga sistem dapat disesuaikan dengan kebijakan sekolah.

#### Acceptance Criteria

1. WHEN Operator Sekolah mengakses menu "Pengaturan Rules Engine" THEN sistem SHALL menampilkan halaman settings dengan form yang terorganisir
2. WHEN halaman settings ditampilkan THEN sistem SHALL menampilkan nilai threshold saat ini untuk semua parameter
3. WHEN halaman settings ditampilkan THEN sistem SHALL menampilkan penjelasan singkat untuk setiap parameter
4. WHEN halaman settings ditampilkan THEN sistem SHALL menampilkan contoh kasus untuk membantu pemahaman
5. WHEN role selain Operator Sekolah mencoba akses THEN sistem SHALL menolak akses dengan pesan error 403

---

### Requirement 2: Konfigurasi Threshold Poin untuk Surat

**User Story:** Sebagai Operator Sekolah, saya ingin mengubah threshold poin untuk Surat 1, 2, dan 3, sehingga eskalasi pelanggaran sesuai dengan kebijakan sekolah.

#### Acceptance Criteria

1. WHEN Operator mengisi threshold poin Surat 2 minimum THEN sistem SHALL memvalidasi bahwa nilai harus berupa angka positif
2. WHEN Operator mengisi threshold poin Surat 2 maksimum THEN sistem SHALL memvalidasi bahwa nilai harus lebih besar dari minimum
3. WHEN Operator mengisi threshold poin Surat 3 minimum THEN sistem SHALL memvalidasi bahwa nilai harus lebih besar dari Surat 2 maksimum
4. WHEN Operator menyimpan settings THEN sistem SHALL memvalidasi bahwa tidak ada overlap range antara Surat 2 dan Surat 3
5. WHEN validasi gagal THEN sistem SHALL menampilkan pesan error yang spesifik dan jelas

---

### Requirement 3: Konfigurasi Threshold Akumulasi Poin

**User Story:** Sebagai Operator Sekolah, saya ingin mengubah threshold akumulasi poin, sehingga eskalasi berdasarkan total poin siswa dapat disesuaikan.

#### Acceptance Criteria

1. WHEN Operator mengisi threshold akumulasi sedang minimum THEN sistem SHALL memvalidasi bahwa nilai harus berupa angka positif
2. WHEN Operator mengisi threshold akumulasi sedang maksimum THEN sistem SHALL memvalidasi bahwa nilai harus lebih besar dari minimum
3. WHEN Operator mengisi threshold akumulasi kritis THEN sistem SHALL memvalidasi bahwa nilai harus lebih besar dari akumulasi sedang maksimum
4. WHEN Operator menyimpan settings THEN sistem SHALL memvalidasi konsistensi dengan threshold poin surat
5. WHEN validasi gagal THEN sistem SHALL menampilkan pesan error yang menjelaskan hubungan antar threshold

---

### Requirement 4: Konfigurasi Frekuensi Spesifik

**User Story:** Sebagai Operator Sekolah, saya ingin mengubah frekuensi pelanggaran spesifik (atribut, alfa), sehingga trigger Surat 1 dapat disesuaikan dengan kebijakan sekolah.

#### Acceptance Criteria

1. WHEN Operator mengisi frekuensi atribut THEN sistem SHALL memvalidasi bahwa nilai harus berupa angka positif minimal 1
2. WHEN Operator mengisi frekuensi alfa THEN sistem SHALL memvalidasi bahwa nilai harus berupa angka positif minimal 1
3. WHEN Operator menyimpan settings THEN sistem SHALL menyimpan nilai frekuensi untuk kedua jenis pelanggaran
4. WHEN frekuensi diubah THEN sistem SHALL menggunakan nilai baru untuk evaluasi pelanggaran berikutnya (tidak retroaktif)
5. WHEN halaman settings ditampilkan THEN sistem SHALL menampilkan warning bahwa perubahan tidak berlaku retroaktif

---

### Requirement 5: Validasi Konsistensi Settings

**User Story:** Sebagai Operator Sekolah, saya ingin sistem memvalidasi konsistensi antar threshold, sehingga tidak terjadi konfigurasi yang bertentangan.

#### Acceptance Criteria

1. WHEN Operator menyimpan settings THEN sistem SHALL memvalidasi bahwa threshold Surat 3 > Surat 2 > Surat 1
2. WHEN Operator menyimpan settings THEN sistem SHALL memvalidasi bahwa akumulasi kritis > akumulasi sedang
3. WHEN Operator menyimpan settings THEN sistem SHALL memvalidasi bahwa tidak ada gap atau overlap antar range
4. WHEN validasi gagal THEN sistem SHALL menampilkan semua error sekaligus (tidak satu per satu)
5. WHEN validasi berhasil THEN sistem SHALL menampilkan preview perubahan sebelum menyimpan

---

### Requirement 6: Preview dan Konfirmasi Perubahan

**User Story:** Sebagai Operator Sekolah, saya ingin melihat preview perubahan sebelum menyimpan, sehingga saya dapat memastikan konfigurasi sudah benar.

#### Acceptance Criteria

1. WHEN Operator klik tombol "Preview Perubahan" THEN sistem SHALL menampilkan tabel perbandingan nilai lama vs baru
2. WHEN preview ditampilkan THEN sistem SHALL menampilkan contoh kasus dengan nilai lama dan baru
3. WHEN preview ditampilkan THEN sistem SHALL menampilkan warning jika ada perubahan signifikan (>50% dari nilai lama)
4. WHEN Operator konfirmasi perubahan THEN sistem SHALL menyimpan settings ke database
5. WHEN Operator batal THEN sistem SHALL kembali ke form tanpa menyimpan

---

### Requirement 7: Audit Trail Perubahan Settings

**User Story:** Sebagai Kepala Sekolah, saya ingin melihat history perubahan settings Rules Engine, sehingga ada transparansi dan akuntabilitas.

#### Acceptance Criteria

1. WHEN Operator menyimpan perubahan settings THEN sistem SHALL mencatat log dengan detail: operator_id, timestamp, nilai lama, nilai baru
2. WHEN Operator atau Kepala Sekolah akses halaman settings THEN sistem SHALL menampilkan tab "History Perubahan"
3. WHEN history ditampilkan THEN sistem SHALL menampilkan dalam format timeline dengan filter tanggal
4. WHEN history ditampilkan THEN sistem SHALL menampilkan username operator yang melakukan perubahan
5. WHEN history ditampilkan THEN sistem SHALL menampilkan detail perubahan dalam format yang mudah dibaca

---

### Requirement 8: Reset ke Default

**User Story:** Sebagai Operator Sekolah, saya ingin mereset settings ke nilai default, sehingga saya dapat kembali ke konfigurasi awal jika terjadi kesalahan.

#### Acceptance Criteria

1. WHEN Operator klik tombol "Reset ke Default" THEN sistem SHALL menampilkan dialog konfirmasi dengan warning
2. WHEN Operator konfirmasi reset THEN sistem SHALL mengembalikan semua threshold ke nilai default yang di-hardcode
3. WHEN reset berhasil THEN sistem SHALL mencatat log aktivitas reset
4. WHEN reset berhasil THEN sistem SHALL menampilkan pesan sukses dengan nilai default yang digunakan
5. WHEN Operator batal reset THEN sistem SHALL membatalkan aksi tanpa mengubah settings

---

### Requirement 9: Help & Documentation

**User Story:** Sebagai Operator Sekolah, saya ingin melihat dokumentasi dan contoh kasus, sehingga saya dapat memahami dampak perubahan settings.

#### Acceptance Criteria

1. WHEN halaman settings ditampilkan THEN sistem SHALL menampilkan panel "Bantuan" yang dapat di-collapse
2. WHEN panel bantuan dibuka THEN sistem SHALL menampilkan penjelasan setiap parameter dengan contoh
3. WHEN panel bantuan dibuka THEN sistem SHALL menampilkan diagram alur Rules Engine
4. WHEN panel bantuan dibuka THEN sistem SHALL menampilkan FAQ tentang perubahan settings
5. WHEN Operator hover pada label parameter THEN sistem SHALL menampilkan tooltip dengan penjelasan singkat

---

### Requirement 10: Integrasi dengan Rules Engine

**User Story:** Sebagai sistem, saya ingin Rules Engine menggunakan settings dari database, sehingga perubahan threshold langsung berlaku untuk evaluasi pelanggaran berikutnya.

#### Acceptance Criteria

1. WHEN Rules Engine mengevaluasi pelanggaran THEN sistem SHALL membaca threshold dari tabel settings (bukan konstanta di code)
2. WHEN settings tidak ditemukan di database THEN sistem SHALL menggunakan nilai default yang di-hardcode sebagai fallback
3. WHEN Rules Engine membaca settings THEN sistem SHALL menggunakan caching untuk performa (cache 5 menit)
4. WHEN settings diubah THEN sistem SHALL clear cache agar nilai baru langsung digunakan
5. WHEN terjadi error saat membaca settings THEN sistem SHALL log error dan menggunakan nilai default tanpa mengganggu proses pencatatan pelanggaran

---

## Data Requirements

### Settings Table Structure

Tabel `rules_engine_settings` harus menyimpan:
- `id` (primary key)
- `key` (string, unique) - Nama parameter (misal: 'surat_2_min_poin')
- `value` (integer) - Nilai threshold
- `description` (text) - Penjelasan parameter
- `category` (string) - Kategori (poin_surat, akumulasi, frekuensi)
- `updated_by` (foreign key ke users) - Operator yang terakhir update
- `updated_at` (timestamp)
- `created_at` (timestamp)

### Settings History Table Structure

Tabel `rules_engine_settings_history` harus menyimpan:
- `id` (primary key)
- `setting_key` (string) - Nama parameter yang diubah
- `old_value` (integer) - Nilai lama
- `new_value` (integer) - Nilai baru
- `changed_by` (foreign key ke users) - Operator yang melakukan perubahan
- `changed_at` (timestamp)
- `notes` (text, nullable) - Catatan opsional

---

## Business Rules

### BR-1: Hierarki Threshold
- Threshold Surat 3 minimum HARUS lebih besar dari Surat 2 maksimum
- Threshold Surat 2 minimum HARUS lebih kecil dari Surat 2 maksimum
- Threshold akumulasi kritis HARUS lebih besar dari akumulasi sedang maksimum

### BR-2: Non-Retroactive
- Perubahan settings TIDAK berlaku untuk tindak lanjut yang sudah dibuat
- Perubahan settings HANYA berlaku untuk evaluasi pelanggaran baru setelah perubahan disimpan

### BR-3: Fallback Mechanism
- Jika tabel settings kosong atau error, sistem HARUS menggunakan nilai default dari konstanta di code
- Sistem TIDAK BOLEH crash atau error jika settings tidak ditemukan

### BR-4: Audit Trail
- Setiap perubahan settings HARUS dicatat di history table
- Log HARUS mencatat siapa, kapan, dan apa yang diubah

### BR-5: Access Control
- HANYA Operator Sekolah yang boleh mengubah settings
- Kepala Sekolah boleh melihat settings dan history (read-only)
- Role lain TIDAK boleh akses halaman settings

---

## UI/UX Requirements

### Layout
- Form settings harus terorganisir dalam card/section berdasarkan kategori
- Setiap input harus memiliki label yang jelas dan tooltip
- Validasi error harus ditampilkan inline di bawah input yang bermasalah
- Preview perubahan harus ditampilkan dalam modal atau panel terpisah

### Visual Indicators
- Input yang diubah harus diberi highlight (misal: border kuning)
- Nilai yang berbeda signifikan (>50%) harus diberi warning icon
- Tombol "Simpan" harus disabled sampai validasi berhasil
- Loading indicator harus ditampilkan saat menyimpan

### Responsive Design
- Form harus responsive dan mudah digunakan di tablet
- Tabel history harus scrollable horizontal di mobile
- Modal preview harus adaptif dengan ukuran layar

---

## Non-Functional Requirements

### Performance
- Halaman settings harus load dalam <2 detik
- Validasi form harus real-time (tidak perlu submit untuk validasi)
- Cache settings di Rules Engine harus expire dalam 5 menit

### Security
- Semua input harus divalidasi di server-side (tidak hanya client-side)
- SQL injection harus dicegah dengan prepared statements
- CSRF token harus divalidasi untuk setiap form submission

### Reliability
- Sistem harus tetap berjalan normal jika settings table kosong
- Error saat membaca settings tidak boleh mengganggu pencatatan pelanggaran
- Rollback otomatis jika terjadi error saat menyimpan settings

### Maintainability
- Code harus modular dan mudah di-extend untuk parameter baru
- Konstanta default harus didefinisikan di satu tempat (single source of truth)
- Dokumentasi inline harus jelas untuk setiap method

---

## Acceptance Testing Scenarios

### Scenario 1: Happy Path - Ubah Threshold Berhasil
1. Operator login dan akses halaman settings
2. Operator ubah threshold Surat 2 minimum dari 100 menjadi 150
3. Operator klik "Preview Perubahan"
4. Sistem tampilkan preview dengan perbandingan nilai
5. Operator konfirmasi perubahan
6. Sistem simpan settings dan tampilkan pesan sukses
7. Sistem catat log di history table
8. Rules Engine gunakan nilai baru (150) untuk evaluasi berikutnya

### Scenario 2: Validasi Error - Threshold Tidak Konsisten
1. Operator login dan akses halaman settings
2. Operator ubah threshold Surat 3 minimum menjadi 400 (lebih kecil dari Surat 2 max 500)
3. Operator klik "Simpan"
4. Sistem tampilkan error: "Threshold Surat 3 minimum (400) harus lebih besar dari Surat 2 maksimum (500)"
5. Operator perbaiki nilai menjadi 501
6. Sistem validasi berhasil dan simpan settings

### Scenario 3: Reset ke Default
1. Operator login dan akses halaman settings
2. Operator klik "Reset ke Default"
3. Sistem tampilkan dialog konfirmasi dengan warning
4. Operator konfirmasi reset
5. Sistem kembalikan semua threshold ke nilai default
6. Sistem catat log reset di history
7. Sistem tampilkan pesan sukses dengan nilai default

### Scenario 4: View History
1. Kepala Sekolah login dan akses halaman settings
2. Kepala Sekolah klik tab "History Perubahan"
3. Sistem tampilkan timeline perubahan dengan filter tanggal
4. Kepala Sekolah filter history bulan ini
5. Sistem tampilkan semua perubahan bulan ini dengan detail operator dan nilai

---

## Out of Scope

Fitur-fitur berikut TIDAK termasuk dalam requirement ini:
- Notifikasi email saat settings diubah
- Approval workflow untuk perubahan settings (langsung apply)
- Export/import settings dalam format JSON/CSV
- Versioning settings (hanya history, tidak bisa rollback ke versi lama)
- Multi-language support untuk label dan help text

---

## Dependencies

Fitur ini bergantung pada:
- Tabel `users` (untuk foreign key updated_by dan changed_by)
- Service `PelanggaranRulesEngine` (harus diupdate untuk baca dari database)
- Middleware `role:Operator Sekolah` (untuk access control)
- Cache system Laravel (untuk caching settings)

---

## Success Criteria

Fitur ini dianggap berhasil jika:
1. ✅ Operator dapat mengubah semua threshold tanpa edit code
2. ✅ Validasi mencegah konfigurasi yang tidak konsisten
3. ✅ Rules Engine menggunakan settings dari database
4. ✅ History perubahan tercatat dengan lengkap
5. ✅ Sistem tetap berjalan normal jika settings error (fallback ke default)
6. ✅ UI user-friendly dan mudah dipahami
7. ✅ Performa tidak terpengaruh (load time <2 detik)
8. ✅ Semua acceptance criteria terpenuhi
