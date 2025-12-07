# Changelog: Frequency-Based Point System

## Version 2.0.0 - Frequency-Based Point System (Planned)

### 🎯 Major Changes

#### 1. Point Accumulation Logic - BREAKING CHANGE
**Before:**
- Poin diberikan SETIAP KALI pelanggaran tercatat
- Contoh: Alfa 4x = 4 × 25 = 100 poin

**After:**
- Poin diberikan HANYA saat threshold frekuensi tercapai
- Contoh: Alfa 4x = 25 poin (threshold 1-3) + 25 poin (threshold 4) = 50 poin

**Impact:**
- ✅ Akumulasi poin lebih akurat sesuai tata tertib
- ⚠️ Siswa dengan pelanggaran berulang akan punya poin lebih rendah dari sebelumnya
- ⚠️ Perlu recalculate poin untuk data existing (optional)

---

#### 2. Surat Pemanggilan Logic - BREAKING CHANGE
**Before:**
- Surat ditentukan berdasarkan akumulasi poin:
  - Surat 1: Frekuensi spesifik (Atribut 10x, Alfa 4x)
  - Surat 2: Poin 100-500
  - Surat 3: Poin > 500

**After:**
- Surat ditentukan berdasarkan pembina yang terlibat:
  - Surat 1: Wali Kelas
  - Surat 2: Wali Kelas + Kaprodi
  - Surat 3: Wali Kelas + Kaprodi + Waka
  - Surat 4: Wali Kelas + Kaprodi + Waka + Kepsek
- Surat HANYA trigger jika sanksi mencantumkan "Panggilan orang tua"

**Impact:**
- ✅ Surat pemanggilan lebih sesuai dengan hierarki pembinaan
- ✅ Pembinaan internal (berdasarkan akumulasi poin) TIDAK trigger surat otomatis
- ⚠️ Logic surat pemanggilan berubah total

---

#### 3. Pembinaan Internal - NEW CONCEPT
**Before:**
- Tidak ada konsep pembinaan internal yang jelas
- Semua pembinaan trigger surat pemanggilan

**After:**
- Pembinaan internal adalah **rekomendasi konseling** berdasarkan akumulasi poin:
  - 0-50: Wali Kelas (konseling ringan)
  - 55-100: Wali Kelas + Kaprodi (monitoring ketat)
  - 105-300: Wali Kelas + Kaprodi + Waka (pembinaan intensif)
  - 305-500: Wali Kelas + Kaprodi + Waka + Kepsek (pembinaan kritis)
  - >500: Dikembalikan kepada orang tua
- **PENTING**: Pembinaan internal TIDAK trigger surat pemanggilan otomatis
- Surat pemanggilan HANYA trigger dari pelanggaran dengan sanksi "Panggilan orang tua"

**Impact:**
- ✅ Pembinaan lebih terstruktur dan bertahap
- ✅ Tidak semua pembinaan perlu melibatkan orang tua
- ✅ Fokus pada konseling internal dulu sebelum eskalasi
- ✅ Sistem pembinaan internal dan surat pemanggilan berjalan independen

---

#### 4. Pembina "Semua Guru & Staff" - NEW OPTION
**Use Case:**
- Untuk pelanggaran yang bisa ditindaklanjuti oleh **siapa saja yang melihat**
- Contoh: Atribut tidak lengkap, kerapian, kebersihan
- Pembinaan dilakukan ditempat oleh guru/staff yang kedapatan melihat

**Behavior:**
- Pembina "Semua Guru & Staff" TIDAK trigger surat pemanggilan formal
- Fokus pada pembinaan langsung dan spontan
- Cocok untuk pelanggaran ringan dengan frekuensi rendah

**Example:**
```
Pelanggaran: Atribut/Seragam Tidak Lengkap
Rule 1: Frekuensi 1-9x, Poin 0, Sanksi "Pembinaan ditempat", Pembina "Semua Guru & Staff"
Rule 2: Frekuensi 10+x, Poin 5, Sanksi "Panggilan orang tua", Pembina "Wali Kelas", Trigger Surat
```

**Impact:**
- ✅ Pembinaan lebih fleksibel dan responsif
- ✅ Tidak perlu menunggu Wali Kelas untuk pelanggaran ringan
- ✅ Semua guru/staff bisa berkontribusi dalam pembinaan
- ✅ Mengurangi beban administratif untuk pelanggaran ringan

---

### 🆕 New Features

#### 1. Frequency Rules Management (Operator)
- Operator dapat mengelola threshold frekuensi untuk setiap jenis pelanggaran
- UI untuk tambah/edit/hapus frequency rules
- Validasi threshold overlap
- Preview dampak perubahan rules

#### 2. Real-time Frequency Preview (Guru)
- Guru dapat melihat frekuensi saat ini saat mencatat pelanggaran
- Warning jika threshold akan tercapai
- Preview poin yang akan ditambahkan
- Preview sanksi yang akan ditrigger

#### 3. Role Waka Sarana
- Dashboard khusus untuk monitor pelanggaran fasilitas
- Statistik pelanggaran fasilitas (total, bulan ini)
- Riwayat pelanggaran fasilitas terbaru
- Riwayat yang dicatat oleh Waka Sarana sendiri
- Access control: bisa catat semua pelanggaran, hanya bisa edit/hapus riwayat sendiri

#### 4. Audit Trail for Point Changes
- Tracking perubahan poin siswa dengan alasan
- History: siswa, jenis pelanggaran, frekuensi, threshold, poin ditambahkan, timestamp
- Filter by siswa, tanggal, jenis pelanggaran

---

### 🗄️ Database Changes

#### New Tables
1. **pelanggaran_frequency_rules**
   - Menyimpan aturan threshold frekuensi
   - Kolom: id, jenis_pelanggaran_id, frequency_min, frequency_max, poin, sanksi_description, trigger_surat, pembina_roles, display_order, timestamps
   - Foreign key ke `jenis_pelanggaran` dengan ON DELETE CASCADE
   - Indexes: jenis_pelanggaran_id, display_order

#### Updated Tables
1. **jenis_pelanggaran**
   - Tambah kolom: `has_frequency_rules` BOOLEAN DEFAULT FALSE
   - Flag untuk menandai pelanggaran yang menggunakan frequency rules

2. **roles**
   - Tambah role: `Waka Sarana`

---

### 🔧 Service Layer Changes

#### PelanggaranRulesEngine
**New Methods:**
- `evaluateFrequencyRules(int $siswaId, JenisPelanggaran $pelanggaran): array`
- `tentukanTipeSuratTertinggi(array $suratTypes): ?string`
- `getPembinaanInternalRekomendasi(int $totalPoin): array`

**Updated Methods:**
- `processBatch()`: Support frequency-based evaluation
- `reconcileForSiswa()`: Support frequency-based recalculation

**Removed Methods:**
- `cekFrekuensiSpesifik()`: Replaced by frequency rules
- `tentukanBerdasarkanPoin()`: Replaced by frequency-based evaluation

---

### 🎨 UI Changes

#### New Pages
1. **Frequency Rules Management** (`/frequency-rules`)
   - List semua jenis pelanggaran dengan toggle frequency rules
   - Detail frequency rules untuk setiap pelanggaran
   - Form tambah/edit rule dengan validasi

2. **Waka Sarana Dashboard** (`/dashboard/waka-sarana`)
   - Statistik pelanggaran fasilitas
   - Riwayat pelanggaran fasilitas terbaru
   - Riwayat yang dicatat sendiri

#### Updated Pages
1. **Pencatatan Pelanggaran** (`/pelanggaran/catat`)
   - Tambah section preview frequency
   - Display current frequency
   - Warning jika threshold akan tercapai
   - Preview poin dan sanksi

2. **Sidebar Navigation**
   - Tambah link "Kelola Frequency Rules" untuk Operator
   - Tambah link "Dashboard" untuk Waka Sarana

---

### 🔒 Security & Access Control

#### New Permissions
- **Operator Sekolah**: Manage frequency rules
- **Waka Sarana**: Catat pelanggaran, edit/hapus riwayat sendiri, view dashboard

#### Updated Permissions
- **Guru/Wali Kelas/Waka/Kaprodi**: View real-time frequency preview

---

### 📊 Performance Improvements

#### Caching
- Cache frequency rules per jenis pelanggaran (TTL: 1 jam)
- Cache frekuensi siswa per pelanggaran (invalidate on new record)
- Cache total poin akumulasi siswa (invalidate on change)

#### Query Optimization
- Eager load `frequencyRules` saat evaluasi batch
- Use `whereIn()` untuk batch processing
- Minimize database queries dengan caching

#### Indexing
- Index pada `jenis_pelanggaran_id` di `pelanggaran_frequency_rules`
- Index pada `display_order` untuk sorting

---

### 🔄 Backward Compatibility

#### Maintained
- ✅ Pelanggaran tanpa frequency rules tetap gunakan logic lama
- ✅ Pelanggaran berat (frekuensi = 1) tetap langsung dapat poin
- ✅ Data existing tidak diubah, hanya logic evaluasi kedepannya

#### Breaking Changes
- ⚠️ Point accumulation logic berubah untuk pelanggaran dengan frequency rules
- ⚠️ Surat pemanggilan logic berubah total
- ⚠️ Pembinaan internal tidak lagi trigger surat otomatis

---

### 🧪 Testing

#### Unit Tests
- `PelanggaranFrequencyRule::matchesFrequency()`
- `PelanggaranFrequencyRule::getSuratType()`
- `PelanggaranRulesEngine::evaluateFrequencyRules()`
- `PelanggaranRulesEngine::tentukanTipeSuratTertinggi()`
- `PelanggaranRulesEngine::getPembinaanInternalRekomendasi()`

#### Integration Tests
- Pencatatan pelanggaran dengan frequency rules
- Threshold tercapai → poin ditambahkan
- Threshold belum tercapai → poin tidak ditambahkan
- Multiple threshold untuk satu pelanggaran
- Eskalasi surat berdasarkan akumulasi poin
- Backward compatibility untuk pelanggaran tanpa frequency rules

#### Manual Testing
- Scenario 1: Alfa 4x → 50 poin (bukan 100 poin)
- Scenario 2: Atribut 10x → 5 poin (bukan 50 poin)
- Scenario 3: Merokok 1x → 100 poin (langsung, trigger Surat 2)
- Scenario 4: Akumulasi poin → Eskalasi surat

---

### 📚 Documentation

#### New Documents
- `FREQUENCY_BASED_POINT_SYSTEM.md`: User documentation
- `TATA_TERTIB_REFERENCE.md`: Reference tata tertib lengkap
- `CHANGELOG.md`: Changelog lengkap (this file)

#### Updated Documents
- `README.md`: Update dengan informasi sistem baru
- API documentation: Tambah endpoint baru
- Database schema documentation: Update dengan tabel baru

---

### 🚀 Deployment

#### Pre-Deployment
1. Backup database
2. Test migration di staging environment
3. Prepare rollback script

#### Deployment Steps
1. Pull latest code
2. Run migrations
3. Run seeders
4. Clear cache
5. Restart services

#### Post-Deployment
1. Verify critical paths
2. Monitor error logs
3. Check performance metrics
4. Collect user feedback

---

### 🔙 Rollback Plan

#### If Issues Occur
1. **Rollback Database:**
   - Drop tabel `pelanggaran_frequency_rules`
   - Remove kolom `has_frequency_rules` dari `jenis_pelanggaran`
   - Remove role `Waka Sarana`

2. **Rollback Code:**
   - Revert `PelanggaranRulesEngine` ke versi sebelumnya
   - Remove `PelanggaranFrequencyRule` model
   - Remove Waka Sarana dashboard & routes

3. **Data Recovery:**
   - Restore dari backup jika diperlukan

---

### 📈 Success Metrics

#### Functional
- ✅ Poin akumulasi akurat sesuai tata tertib
- ✅ Surat pemanggilan trigger sesuai threshold
- ✅ Waka Sarana dapat fokus pada pelanggaran fasilitas

#### Performance
- ✅ Response time pencatatan pelanggaran < 500ms
- ✅ Dashboard load time < 1s
- ✅ Frequency evaluation < 100ms

#### User Experience
- ✅ Operator dapat manage frequency rules dengan mudah
- ✅ Guru dapat melihat preview poin sebelum submit
- ✅ Waka Sarana dapat monitor pelanggaran fasilitas dengan efektif

---

### 🐛 Known Issues

None yet. Will be updated post-deployment.

---

### 🔮 Future Enhancements

1. **Auto-recalculate poin untuk data existing**
   - Script untuk recalculate poin siswa berdasarkan frequency rules baru
   - Optional: bisa dijalankan oleh Operator jika diperlukan

2. **Export frequency rules**
   - Export frequency rules ke CSV/Excel untuk backup
   - Import frequency rules dari CSV/Excel

3. **Notification system**
   - Notifikasi ke Wali Kelas saat siswa mencapai threshold
   - Notifikasi ke Waka Sarana saat ada pelanggaran fasilitas baru

4. **Analytics dashboard**
   - Grafik trend pelanggaran per bulan
   - Heatmap pelanggaran per kelas/jurusan
   - Prediksi siswa yang berisiko tinggi

---

## Version 1.0.0 - Initial Release (Current)

### Features
- Pencatatan pelanggaran siswa
- Rules engine dengan threshold poin
- Surat pemanggaran otomatis
- Dashboard per role
- Audit trail
- Bulk operations

### Known Issues
- ❌ Poin diberikan setiap kali pelanggaran tercatat (tidak sesuai tata tertib)
- ❌ Surat pemanggilan hanya berdasarkan poin (tidak consider pembina)
- ❌ Tidak ada konsep pembinaan internal yang jelas

---

## Migration Guide: v1.0.0 → v2.0.0

### For Operators
1. Review frequency rules yang di-seed otomatis
2. Adjust frequency rules jika diperlukan
3. Train Guru untuk menggunakan preview frequency
4. Train Waka Sarana untuk menggunakan dashboard baru

### For Developers
1. Backup database sebelum migration
2. Run migrations: `php artisan migrate`
3. Run seeders: `php artisan db:seed --class=FrequencyRulesSeeder`
4. Clear cache: `php artisan cache:clear`
5. Test critical paths
6. Monitor error logs

### For Users
1. **Guru/Wali Kelas**: Perhatikan preview frequency saat mencatat pelanggaran
2. **Operator**: Gunakan halaman "Kelola Frequency Rules" untuk manage threshold
3. **Waka Sarana**: Gunakan dashboard baru untuk monitor pelanggaran fasilitas
4. **Semua**: Poin siswa mungkin berbeda dari sebelumnya (lebih akurat)

---

## Support

Jika ada pertanyaan atau issues setelah deployment, silakan contact:
- **Technical Issues**: Developer team
- **User Questions**: Operator Sekolah
- **Training**: Admin team

---

## Implementation Progress

### ✅ Phase 1: Database & Models (COMPLETED - 2025-12-06)
- Created `pelanggaran_frequency_rules` table
- Added `has_frequency_rules` column to `jenis_pelanggaran`
- Added `Waka Sarana` role
- Created `PelanggaranFrequencyRule` model
- Updated `JenisPelanggaran` model with frequency rules relation

### ✅ Phase 2: Service Layer Refactoring (COMPLETED - 2025-12-06)
- Refactored `PelanggaranRulesEngine` service
- Implemented `evaluateFrequencyRules()` method
- Implemented `tentukanTipeSuratTertinggi()` method
- Implemented `getPembinaanInternalRekomendasi()` method
- Updated `processBatch()` and `reconcileForSiswa()` methods
- Removed old rules engine settings dependencies

### ✅ Phase 3: Role Waka Sarana (COMPLETED - 2025-12-06)
- Created `WakaSaranaDashboardController`
- Created Waka Sarana dashboard view
- Added routes for Waka Sarana
- Updated sidebar navigation
- Added `isWakaSarana()` helper method to User model
- Updated access control for Waka Sarana

### ✅ Phase 4: Data Migration & Seeding (COMPLETED - 2025-12-06)
- Created `FrequencyRulesSeeder` with data from tata tertib
- Seeded 16 frequency rules for 9 jenis pelanggaran
- Updated `DatabaseSeeder` to include frequency rules
- Verified data integrity

### ✅ Phase 5: Frequency Rules Management UI (COMPLETED - 2025-12-07)
- Created `FrequencyRulesController` with CRUD methods
- Created frequency rules index and show views
- Added toggle for is_active status (AJAX)
- Added validation for threshold overlap
- Integrated with jenis pelanggaran create/edit forms
- Added "Semua Guru & Staff" option for pembina (for violations that can be handled by anyone who sees it)
- Updated display to show detailed rules with frequency, points, sanctions, and pembina
- Auto-activate pelanggaran when first rule added, auto-deactivate when last rule deleted

### 🔧 Phase 6: Form Pencatatan Improvements (COMPLETED - 2025-12-07)
- Fixed filter_category not working in pencatatan form (JavaScript mapping issue)
- Added filter to only show active pelanggaran (is_active = true) in pencatatan form
- Updated JavaScript to properly map filter buttons to filter_category values
- Improved user experience with consistent filtering

### ⏳ Phase 7: Real-time Preview UI (NOT STARTED)
- Add frequency preview to pencatatan form
- Add warning when threshold will be reached
- Add poin and sanksi preview

### ⏳ Phase 8: Testing (NOT STARTED)
- Unit tests
- Integration tests
- Manual testing scenarios

---

**Last Updated**: 2025-12-06  
**Status**: Phase 1-4 Completed, Phase 5-7 Pending
