<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class MassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID Role yang dibutuhkan
        $roleWali = Role::where('nama_role', 'Wali Kelas')->first()->id;
        $roleOrtu = Role::where('nama_role', 'Wali Murid')->first()->id;

        // 2. Ambil semua Jurusan yang sudah dibuat di JurusanSeeder
        $jurusanList = Jurusan::all();

        echo "Memulai proses seeding massal (kaprodi, kelas, wali kelas, siswa, wali murid)...\n";

        // Disable foreign key checks, then truncate kelas and siswa to avoid duplicates
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('siswa')->truncate();
        \Illuminate\Support\Facades\DB::table('kelas')->truncate();

        // Roles
        $roleKaprodi = Role::where('nama_role', 'Kaprodi')->first()?->id;
        $roleWali = Role::where('nama_role', 'Wali Kelas')->first()?->id;
        $roleOrtu = Role::where('nama_role', 'Wali Murid')->first()?->id;

        // Untuk setiap jurusan buat 6 kelas: X {kode} 1, X {kode} 2, XI {kode} 1, XI {kode} 2, XII {kode} 1, XII {kode} 2
        $tingkats = ['X', 'XI', 'XII'];
        $nomors = [1, 2];

        foreach ($jurusanList as $jurusan) {
            // dapatkan kode jurusan (harapkan disimpan di kolom kode_jurusan)
            $kodeJurusanRaw = $jurusan->kode_jurusan ?? preg_replace('/[^A-Z0-9]/', '', strtoupper($jurusan->nama_jurusan));
            $kodeJurusan = strtoupper($kodeJurusanRaw);
            $kodeLower = strtolower($kodeJurusan);

            // --- A. Buat atau update Kaprodi untuk jurusan ini ---
            if ($roleKaprodi) {
                $usernameKaprodi = 'kaprodi.' . $kodeLower;
                $passwordKaprodi = 'smkn1.kaprodi.' . $kodeLower;
                $kaprodiUser = User::updateOrCreate(
                    ['username' => $usernameKaprodi],
                    [
                        'role_id' => $roleKaprodi,
                        'nama' => 'Kaprodi ' . $jurusan->nama_jurusan,
                        'email' => $usernameKaprodi . '@smkn1.sch.id',
                        'password' => \Illuminate\Support\Facades\Hash::make($passwordKaprodi),
                        'email_verified_at' => now(),
                    ]
                );

                // hubungkan kaprodi ke jurusan
                $jurusan->kaprodi_user_id = $kaprodiUser->id;
                $jurusan->save();
            }

            foreach ($tingkats as $tingkat) {
                foreach ($nomors as $nomor) {
                    $namaKelas = trim("{$tingkat} {$kodeJurusan} {$nomor}");

                    // build wali kelas username like: walikelas.{tingkat}.{kode}{nomor} (tingkat lower)
                    $tingkatLower = strtolower($tingkat);
                    $kodeSafe = preg_replace('/[^a-z0-9]+/i', '', (string) $kodeLower);
                    $baseWaliUsername = "walikelas.{$tingkatLower}.{$kodeSafe}{$nomor}";

                    // create or update wali kelas user
                    $usernameWali = $baseWaliUsername;
                    $passwordWali = 'smkn1.walikelas.' . $tingkatLower . $kodeSafe . $nomor;

                    $waliUser = null;
                    if ($roleWali) {
                        $waliUser = User::updateOrCreate(
                            ['username' => $usernameWali],
                            [
                                'role_id' => $roleWali,
                                'nama' => "Wali Kelas {$namaKelas}",
                                'email' => $usernameWali . '@smkn1.sch.id',
                                'password' => \Illuminate\Support\Facades\Hash::make($passwordWali),
                                'email_verified_at' => now(),
                            ]
                        );
                    }

                    // --- B. Buat Kelas ---
                    $kelas = Kelas::create([
                        'jurusan_id' => $jurusan->id,
                        'wali_kelas_user_id' => $waliUser?->id,
                        'nama_kelas' => $namaKelas,
                    ]);

                    echo "  -> Membuat Kelas: $kelas->nama_kelas (Wali: " . ($waliUser?->username ?? 'none') . ")\n";

                    // --- C. Buat 20 SISWA PER KELAS ---
                    for ($i = 1; $i <= 20; $i++) {
                        $s = Siswa::factory()->create([
                            'kelas_id' => $kelas->id,
                            'wali_murid_user_id' => null,
                        ]);

                        // Buat akun Wali Murid sesuai aturan: username 'wali.{nisn}', password 'smkn1.walimurid.{nisn}'
                        if ($roleOrtu) {
                            $nisn = $s->nisn;
                            $usernameOrtu = 'wali.' . $nisn;
                            $passwordOrtu = 'smkn1.walimurid.' . $nisn;

                            $ortuUser = User::updateOrCreate(
                                ['username' => $usernameOrtu],
                                [
                                    'role_id' => $roleOrtu,
                                    'nama' => 'Wali Murid ' . $s->nama_siswa,
                                    'email' => $usernameOrtu . '@smkn1.sch.id',
                                    'password' => \Illuminate\Support\Facades\Hash::make($passwordOrtu),
                                    'email_verified_at' => now(),
                                ]
                            );

                            // hubungkan siswa ke wali murid
                            $s->wali_murid_user_id = $ortuUser->id;
                            $s->save();
                        }
                    }
                }
            }
        }

        echo "SELESAI! Kaprodi, kelas, wali kelas, siswa, dan wali murid telah dibuat oleh MassSeeder.\n";
        // Re-enable foreign key checks
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}