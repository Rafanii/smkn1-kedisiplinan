<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisPelanggaran;
use App\Models\PelanggaranFrequencyRule;
use Illuminate\Support\Facades\DB;

class FrequencyRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing frequency rules
        DB::table('pelanggaran_frequency_rules')->truncate();

        // =====================================================================
        // PELANGGARAN RINGAN - Frequency Rules
        // =====================================================================

        // 1. Atribut/seragam tidak lengkap
        $this->createFrequencyRules('Atribut/seragam tidak lengkap', [
            [
                'frequency_min' => 1,
                'frequency_max' => 9,
                'poin' => 0,
                'sanksi_description' => 'Pembinaan ditempat',
                'trigger_surat' => false,
                'pembina_roles' => ['Guru'],
                'display_order' => 1,
            ],
            [
                'frequency_min' => 10,
                'frequency_max' => null,
                'poin' => 5,
                'sanksi_description' => 'Panggilan orang tua dan denda membawa kebutuhan ringan jurusan',
                'trigger_surat' => true,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 2,
            ],
        ]);

        // 2. Terlambat apel pagi
        $this->createFrequencyRules('Terlambat apel pagi', [
            [
                'frequency_min' => 1,
                'frequency_max' => 9,
                'poin' => 0,
                'sanksi_description' => 'Pembinaan ditempat',
                'trigger_surat' => false,
                'pembina_roles' => ['Guru'],
                'display_order' => 1,
            ],
            [
                'frequency_min' => 10,
                'frequency_max' => null,
                'poin' => 5,
                'sanksi_description' => 'Panggilan orang tua dan denda membawa kebutuhan ringan jurusan',
                'trigger_surat' => true,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 2,
            ],
        ]);

        // 3. Terlambat tidak apel pagi
        $this->createFrequencyRules('Terlambat tidak apel pagi', [
            [
                'frequency_min' => 1,
                'frequency_max' => 3,
                'poin' => 0,
                'sanksi_description' => 'Pembinaan ditempat',
                'trigger_surat' => false,
                'pembina_roles' => ['Guru'],
                'display_order' => 1,
            ],
            [
                'frequency_min' => 4,
                'frequency_max' => null,
                'poin' => 10,
                'sanksi_description' => 'Panggilan orang tua dan denda membawa kebutuhan ringan jurusan',
                'trigger_surat' => true,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 2,
            ],
        ]);

        // 4. Tidak melaksanakan sholat dhuhur dan Ashar
        $this->createFrequencyRules('Tidak melaksanakan sholat', [
            [
                'frequency_min' => 1,
                'frequency_max' => 4,
                'poin' => 0,
                'sanksi_description' => 'Pembinaan ditempat',
                'trigger_surat' => false,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 1,
            ],
            [
                'frequency_min' => 5,
                'frequency_max' => null,
                'poin' => 10,
                'sanksi_description' => 'Denda membawa sendal (Berlaku kelipatan)',
                'trigger_surat' => false,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 2,
            ],
        ]);

        // =====================================================================
        // PELANGGARAN SEDANG - Frequency Rules
        // =====================================================================

        // 5. Alfa (absen tanpa keterangan)
        $this->createFrequencyRules('Alfa', [
            [
                'frequency_min' => 1,
                'frequency_max' => 3,
                'poin' => 25,
                'sanksi_description' => 'Pembinaan',
                'trigger_surat' => false,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 1,
            ],
            [
                'frequency_min' => 4,
                'frequency_max' => null,
                'poin' => 25,
                'sanksi_description' => 'Panggilan orang tua dan denda membawa 1 buah pot bunga diameter 30 cm (Berlaku kelipatan)',
                'trigger_surat' => true,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 2,
            ],
        ]);

        // 6. Cabut keluar sekolah
        $this->createFrequencyRules('Cabut keluar sekolah', [
            [
                'frequency_min' => 1,
                'frequency_max' => 1,
                'poin' => 25,
                'sanksi_description' => 'Pembinaan',
                'trigger_surat' => false,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 1,
            ],
            [
                'frequency_min' => 2,
                'frequency_max' => null,
                'poin' => 25,
                'sanksi_description' => 'Panggilan orang tua dan denda membawa 2 buah pot bunga diameter 30 cm (Berlaku kelipatan)',
                'trigger_surat' => true,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 2,
            ],
        ]);

        // 7. Tidak mengikuti kegiatan hari besar
        $this->createFrequencyRules('Tidak mengikuti kegiatan hari besar', [
            [
                'frequency_min' => 1,
                'frequency_max' => null,
                'poin' => 25,
                'sanksi_description' => 'Denda membawa 1 pot bunga diameter 30 cm (Berlaku kelipatan)',
                'trigger_surat' => false,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 1,
            ],
        ]);

        // 8. Membawa HP/Elektronik tanpa izin
        $this->createFrequencyRules('Membawa HP', [
            [
                'frequency_min' => 1,
                'frequency_max' => 2,
                'poin' => 0,
                'sanksi_description' => 'Pembinaan ditempat',
                'trigger_surat' => false,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 1,
            ],
            [
                'frequency_min' => 3,
                'frequency_max' => null,
                'poin' => 25,
                'sanksi_description' => 'Panggilan orang tua dan disita oleh pihak sekolah selama 1 bulan',
                'trigger_surat' => true,
                'pembina_roles' => ['Wali Kelas'],
                'display_order' => 2,
            ],
        ]);

        // 9. Mencoret/merusak fasilitas sekolah
        $this->createFrequencyRules('merusak fasilitas', [
            [
                'frequency_min' => 1,
                'frequency_max' => null,
                'poin' => 50,
                'sanksi_description' => 'Pembinaan ditempat dan memperbaiki fasilitas yang dirusak',
                'trigger_surat' => false,
                'pembina_roles' => ['Wali Kelas', 'Waka Sarana'],
                'display_order' => 1,
            ],
        ]);

        $this->command->info('Frequency rules seeded successfully!');
    }

    /**
     * Helper method untuk create frequency rules untuk satu jenis pelanggaran.
     *
     * @param string $namaPelanggaran Nama pelanggaran (partial match)
     * @param array $rules Array of frequency rules
     * @return void
     */
    private function createFrequencyRules(string $namaPelanggaran, array $rules): void
    {
        // Find jenis pelanggaran by partial name match
        $jenisPelanggaran = JenisPelanggaran::where('nama_pelanggaran', 'LIKE', "%{$namaPelanggaran}%")
            ->first();

        if (!$jenisPelanggaran) {
            $this->command->warn("Jenis pelanggaran '{$namaPelanggaran}' tidak ditemukan. Skipping...");
            return;
        }

        // Update flag has_frequency_rules
        $jenisPelanggaran->update(['has_frequency_rules' => true]);

        // Create frequency rules
        foreach ($rules as $rule) {
            PelanggaranFrequencyRule::create([
                'jenis_pelanggaran_id' => $jenisPelanggaran->id,
                'frequency_min' => $rule['frequency_min'],
                'frequency_max' => $rule['frequency_max'],
                'poin' => $rule['poin'],
                'sanksi_description' => $rule['sanksi_description'],
                'trigger_surat' => $rule['trigger_surat'],
                'pembina_roles' => $rule['pembina_roles'],
                'display_order' => $rule['display_order'],
            ]);
        }

        $this->command->info("✓ Frequency rules untuk '{$jenisPelanggaran->nama_pelanggaran}' berhasil dibuat.");
    }
}
