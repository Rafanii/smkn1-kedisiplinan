<?php

namespace App\Services;

use App\Models\JenisPelanggaran;
use App\Models\RiwayatPelanggaran;
use App\Models\Siswa;
use App\Models\TindakLanjut;

/**
 * Service untuk Rules Engine Pelanggaran (v2.0 - Frequency-Based)
 *
 * Tanggung jawab:
 * - Mengevaluasi poin berdasarkan threshold frekuensi
 * - Menentukan jenis surat berdasarkan pembina yang terlibat
 * - Membuat/update TindakLanjut dan SuratPanggilan otomatis
 * - Memberikan rekomendasi pembinaan internal (TIDAK trigger surat)
 */
class PelanggaranRulesEngine
{
    /**
     * Konstanta tipe surat (eskalasi levels)
     */
    const SURAT_1 = 'Surat 1';
    const SURAT_2 = 'Surat 2';
    const SURAT_3 = 'Surat 3';
    const SURAT_4 = 'Surat 4';

    /**
     * Proses batch pelanggaran untuk satu siswa.
     * Dipanggil saat pencatatan multiple pelanggaran.
     *
     * @param int $siswaId
     * @param array $pelanggaranIds Array ID jenis pelanggaran
     * @return void
     */
    public function processBatch(int $siswaId, array $pelanggaranIds): void
    {
        $siswa = Siswa::find($siswaId);
        if (!$siswa) return;

        // Eager load frequency rules
        $pelanggaranObjs = JenisPelanggaran::with('frequencyRules')
            ->whereIn('id', $pelanggaranIds)
            ->get();

        if ($pelanggaranObjs->isEmpty()) return;

        $totalPoinBaru = 0;
        $suratTypes = [];
        $sanksiList = [];

        // Evaluasi setiap pelanggaran
        foreach ($pelanggaranObjs as $pelanggaran) {
            if ($pelanggaran->usesFrequencyRules()) {
                // Gunakan frequency-based evaluation
                $result = $this->evaluateFrequencyRules($siswaId, $pelanggaran);
                $totalPoinBaru += $result['poin_ditambahkan'];

                if ($result['surat_type']) {
                    $suratTypes[] = $result['surat_type'];
                }

                $sanksiList[] = $result['sanksi'];
            } else {
                // Fallback: immediate accumulation (backward compatibility)
                $totalPoinBaru += $pelanggaran->poin;

                // Untuk pelanggaran berat, tentukan surat berdasarkan poin
                if ($pelanggaran->poin >= 100) {
                    // Pelanggaran berat langsung trigger surat
                    if ($pelanggaran->poin >= 200) {
                        $suratTypes[] = self::SURAT_3;
                    } elseif ($pelanggaran->poin > 500) {
                        $suratTypes[] = self::SURAT_4;
                    } else {
                        $suratTypes[] = self::SURAT_2;
                    }
                }
            }
        }

        // Tentukan tipe surat tertinggi (HANYA dari frequency rules)
        $tipeSurat = $this->tentukanTipeSuratTertinggi($suratTypes);

        // Buat/update TindakLanjut jika diperlukan
        if ($tipeSurat) {
            $pemicu = implode(', ', array_unique(array_filter($sanksiList)));
            $status = in_array($tipeSurat, [self::SURAT_3, self::SURAT_4])
                ? 'Menunggu Persetujuan'
                : 'Baru';

            $this->buatAtauUpdateTindakLanjut($siswaId, $tipeSurat, $pemicu, $status);
        }
    }

    /**
     * Evaluasi frequency rules untuk satu siswa dan satu jenis pelanggaran.
     *
     * @param int $siswaId
     * @param JenisPelanggaran $pelanggaran
     * @return array ['poin_ditambahkan' => int, 'surat_type' => string|null, 'sanksi' => string]
     */
    private function evaluateFrequencyRules(int $siswaId, JenisPelanggaran $pelanggaran): array
    {
        // Hitung frekuensi total pelanggaran ini untuk siswa
        $currentFrequency = RiwayatPelanggaran::where('siswa_id', $siswaId)
            ->where('jenis_pelanggaran_id', $pelanggaran->id)
            ->count();

        // Ambil semua frequency rules untuk pelanggaran ini
        $rules = $pelanggaran->frequencyRules;

        if ($rules->isEmpty()) {
            // Fallback: tidak ada rules, gunakan poin langsung
            return [
                'poin_ditambahkan' => $pelanggaran->poin,
                'surat_type' => null,
                'sanksi' => 'Pembinaan',
            ];
        }

        // Cari rule yang match dengan frekuensi saat ini
        $matchedRule = $rules->first(function ($rule) use ($currentFrequency) {
            return $rule->matchesFrequency($currentFrequency);
        });

        if (!$matchedRule) {
            // Tidak ada rule yang match, tidak ada poin
            return [
                'poin_ditambahkan' => 0,
                'surat_type' => null,
                'sanksi' => 'Belum mencapai threshold',
            ];
        }

        // Cek apakah threshold ini sudah pernah tercapai sebelumnya
        $previousFrequency = $currentFrequency - 1;
        $previousRule = $rules->first(function ($rule) use ($previousFrequency) {
            return $rule->matchesFrequency($previousFrequency);
        });

        // Jika rule sekarang sama dengan rule sebelumnya, berarti masih di range yang sama
        // Tidak perlu tambah poin lagi
        if ($previousRule && $previousRule->id === $matchedRule->id) {
            return [
                'poin_ditambahkan' => 0,
                'surat_type' => null,
                'sanksi' => $matchedRule->sanksi_description,
            ];
        }

        // Threshold baru tercapai! Tambahkan poin
        return [
            'poin_ditambahkan' => $matchedRule->poin,
            'surat_type' => $matchedRule->getSuratType(),
            'sanksi' => $matchedRule->sanksi_description,
        ];
    }

    /**
     * Tentukan tipe surat tertinggi dari array surat types.
     * Prioritas: Surat 4 > Surat 3 > Surat 2 > Surat 1
     *
     * CATATAN PENTING: Akumulasi poin TIDAK trigger surat otomatis!
     * Surat HANYA dari frequency rules yang memiliki trigger_surat = TRUE
     *
     * @param array $suratTypes
     * @return string|null
     */
    private function tentukanTipeSuratTertinggi(array $suratTypes): ?string
    {
        if (empty($suratTypes)) {
            // Tidak ada surat dari frequency rules
            return null;
        }

        // Extract level dari surat types
        $levels = array_map(function ($surat) {
            return (int) filter_var($surat, FILTER_SANITIZE_NUMBER_INT);
        }, $suratTypes);

        $maxLevel = max($levels);

        return $maxLevel > 0 ? "Surat {$maxLevel}" : null;
    }

    /**
     * Tentukan rekomendasi pembina untuk pembinaan internal berdasarkan akumulasi poin.
     * CATATAN: Ini HANYA rekomendasi konseling, TIDAK trigger surat pemanggilan.
     *
     * @param int $totalPoin Total poin akumulasi siswa
     * @return array ['pembina_roles' => array, 'keterangan' => string]
     */
    private function getPembinaanInternalRekomendasi(int $totalPoin): array
    {
        // 0-50: Wali Kelas (konseling ringan)
        if ($totalPoin >= 0 && $totalPoin <= 50) {
            return [
                'pembina_roles' => ['Wali Kelas'],
                'keterangan' => 'Pembinaan ringan, konseling',
            ];
        }

        // 55-100: Wali Kelas + Kaprodi (monitoring ketat)
        if ($totalPoin >= 55 && $totalPoin <= 100) {
            return [
                'pembina_roles' => ['Wali Kelas', 'Kaprodi'],
                'keterangan' => 'Pembinaan sedang, monitoring ketat',
            ];
        }

        // 105-300: Wali Kelas + Kaprodi + Waka (pembinaan intensif)
        if ($totalPoin >= 105 && $totalPoin <= 300) {
            return [
                'pembina_roles' => ['Wali Kelas', 'Kaprodi', 'Waka Kesiswaan'],
                'keterangan' => 'Pembinaan intensif, evaluasi berkala',
            ];
        }

        // 305-500: Wali Kelas + Kaprodi + Waka + Kepsek (pembinaan kritis)
        if ($totalPoin >= 305 && $totalPoin <= 500) {
            return [
                'pembina_roles' => ['Wali Kelas', 'Kaprodi', 'Waka Kesiswaan', 'Kepala Sekolah'],
                'keterangan' => 'Pembinaan kritis, pertemuan dengan orang tua',
            ];
        }

        // >500: Dikembalikan kepada orang tua
        if ($totalPoin > 500) {
            return [
                'pembina_roles' => ['Kepala Sekolah'],
                'keterangan' => 'Dikembalikan kepada orang tua, siswa tidak dapat melanjutkan',
            ];
        }

        return [
            'pembina_roles' => [],
            'keterangan' => 'Tidak ada pembinaan',
        ];
    }

    /**
     * Hitung total poin akumulasi siswa dari semua riwayat pelanggaran.
     *
     * @param int $siswaId
     * @return int
     */
    private function hitungTotalPoinAkumulasi(int $siswaId): int
    {
        return RiwayatPelanggaran::where('siswa_id', $siswaId)
            ->join('jenis_pelanggaran', 'riwayat_pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.id')
            ->sum('jenis_pelanggaran.poin');
    }

    /**
     * Buat atau update TindakLanjut dan SuratPanggilan untuk siswa.
     *
     * @param int $siswaId
     * @param string $tipeSurat
     * @param string $pemicu
     * @param string $status
     * @return void
     */
    private function buatAtauUpdateTindakLanjut(
        int $siswaId,
        string $tipeSurat,
        string $pemicu,
        string $status
    ): void {
        $sanksi = "Pemanggilan Wali Murid ({$tipeSurat})";

        // Cari kasus aktif siswa
        $kasusAktif = TindakLanjut::with('suratPanggilan')
            ->where('siswa_id', $siswaId)
            ->whereIn('status', ['Baru', 'Menunggu Persetujuan', 'Disetujui', 'Ditangani'])
            ->latest()
            ->first();

        if (!$kasusAktif) {
            // Buat TindakLanjut baru
            $tl = TindakLanjut::create([
                'siswa_id' => $siswaId,
                'pemicu' => $pemicu,
                'sanksi_deskripsi' => $sanksi,
                'status' => $status,
            ]);

            // Buat SuratPanggilan
            $tl->suratPanggilan()->create([
                'nomor_surat' => 'DRAFT/' . rand(100, 999),
                'tipe_surat' => $tipeSurat,
                'tanggal_surat' => now(),
            ]);
        } else {
            // Update jika eskalasi diperlukan
            $this->eskalasiBilaPerluan($kasusAktif, $tipeSurat, $pemicu, $status);
        }
    }

    /**
     * Rekonsiliasi tindak lanjut untuk seorang siswa berdasarkan riwayat saat ini.
     *
     * Jika setelah edit/hapus riwayat poin akumulasi turun sehingga tidak lagi memenuhi
     * threshold, maka kasus aktif akan dibatalkan. Jika masih memenuhi, akan dibuat
     * atau di-escalate sesuai kebutuhan.
     *
     * @param int $siswaId
     * @param bool $deleteIfNoSurat
     * @return void
     */
    public function reconcileForSiswa(int $siswaId, bool $deleteIfNoSurat = false): void
    {
        $siswa = Siswa::find($siswaId);
        if (!$siswa) return;

        // Ambil semua jenis pelanggaran yang pernah dicatat untuk siswa ini
        $jenisIds = RiwayatPelanggaran::where('siswa_id', $siswaId)
            ->pluck('jenis_pelanggaran_id')
            ->unique()
            ->toArray();

        $pelanggaranObjs = JenisPelanggaran::with('frequencyRules')
            ->whereIn('id', $jenisIds)
            ->get();

        $suratTypes = [];

        // Re-evaluasi setiap pelanggaran
        foreach ($pelanggaranObjs as $pelanggaran) {
            if ($pelanggaran->usesFrequencyRules()) {
                $result = $this->evaluateFrequencyRules($siswaId, $pelanggaran);
                if ($result['surat_type']) {
                    $suratTypes[] = $result['surat_type'];
                }
            } else {
                // Backward compatibility
                if ($pelanggaran->poin >= 100) {
                    if ($pelanggaran->poin >= 200) {
                        $suratTypes[] = self::SURAT_3;
                    } elseif ($pelanggaran->poin > 500) {
                        $suratTypes[] = self::SURAT_4;
                    } else {
                        $suratTypes[] = self::SURAT_2;
                    }
                }
            }
        }

        $tipeSurat = $this->tentukanTipeSuratTertinggi($suratTypes);

        // Cari kasus aktif yang mungkin ada
        $kasusAktif = TindakLanjut::with('suratPanggilan')
            ->where('siswa_id', $siswaId)
            ->whereIn('status', ['Baru', 'Menunggu Persetujuan', 'Disetujui', 'Ditangani'])
            ->latest()
            ->first();

        if ($tipeSurat) {
            // Jika masih perlu tindak lanjut: buat baru atau update kasus yang ada
            if (!$kasusAktif) {
                $this->buatAtauUpdateTindakLanjut($siswaId, $tipeSurat, 'Rekonsiliasi', 'Baru');
                return;
            }

            // Jika ada kasus aktif, perbarui agar sesuai dengan tipe surat baru
            $kasusAktif->update([
                'pemicu' => 'Rekonsiliasi',
                'sanksi_deskripsi' => "Pemanggilan Wali Murid ({$tipeSurat})",
                'status' => in_array($tipeSurat, [self::SURAT_3, self::SURAT_4]) ? 'Menunggu Persetujuan' : 'Baru',
            ]);

            if ($kasusAktif->suratPanggilan) {
                $kasusAktif->suratPanggilan()->update(['tipe_surat' => $tipeSurat]);
            } else {
                // jika sebelumnya tidak ada surat, buat satu
                $kasusAktif->suratPanggilan()->create([
                    'nomor_surat' => 'DRAFT/' . rand(100, 999),
                    'tipe_surat' => $tipeSurat,
                    'tanggal_surat' => now(),
                ]);
            }

            return;
        }

        // Jika tidak ada tipe surat lagi namun ada kasus aktif
        if ($kasusAktif) {
            if ($deleteIfNoSurat) {
                // Hapus seluruh kasus (beserta suratnya) jika dipicu oleh penghapusan oleh pelapor
                $kasusAktif->delete();
                return;
            }

            // Jika tidak dihapus, tutup kasus secara rapi (set Selesai) dan hapus surat panggilan
            $kasusAktif->update([
                'status' => 'Selesai',
                'pemicu' => 'Dibatalkan otomatis setelah penyesuaian poin',
                'sanksi_deskripsi' => 'Dibatalkan oleh sistem',
            ]);

            if ($kasusAktif->suratPanggilan) {
                $kasusAktif->suratPanggilan()->delete();
            }
        }
    }

    /**
     * Update TindakLanjut jika diperlukan eskalasi ke level surat lebih tinggi.
     *
     * @param TindakLanjut $kasusAktif
     * @param string $tipeSuratBaru
     * @param string $pemicuBaru
     * @param string $statusBaru
     * @return void
     */
    private function eskalasiBilaPerluan(
        TindakLanjut $kasusAktif,
        string $tipeSuratBaru,
        string $pemicuBaru,
        string $statusBaru
    ): void {
        $existingTipe = $kasusAktif->suratPanggilan?->tipe_surat ?? '0';
        $levelLama = (int) filter_var($existingTipe, FILTER_SANITIZE_NUMBER_INT);
        $levelBaru = (int) filter_var($tipeSuratBaru, FILTER_SANITIZE_NUMBER_INT);

        if ($levelBaru > $levelLama) {
            $kasusAktif->update([
                'pemicu' => $pemicuBaru . ' (Eskalasi)',
                'sanksi_deskripsi' => "Pemanggilan Wali Murid ({$tipeSuratBaru})",
                'status' => $statusBaru,
            ]);

            if ($kasusAktif->suratPanggilan) {
                $kasusAktif->suratPanggilan()->update(['tipe_surat' => $tipeSuratBaru]);
            }
        }
    }
}
