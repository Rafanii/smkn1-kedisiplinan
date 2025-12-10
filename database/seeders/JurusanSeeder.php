<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // <-- 1. IMPORT USER
use App\Models\Jurusan; // <-- 2. IMPORT JURUSAN
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel dulu
        DB::table('jurusan')->truncate();

        // 2. Buat 5 Jurusan (kode_jurusan diset eksplisit)
        $jurusan = [
            ['kode_jurusan' => 'ATP', 'nama_jurusan' => 'Agribisnis Tanaman Perkebunan (ATP)'],
            ['kode_jurusan' => 'APHP', 'nama_jurusan' => 'Agribisnis Pengolahan Hasil Pertanian (APHP)'],
            ['kode_jurusan' => 'ATU', 'nama_jurusan' => 'Agribisnis Ternak Unggas (ATU)'],
            ['kode_jurusan' => 'TEB', 'nama_jurusan' => 'Teknik Energi Biomassa (TEB)'],
            ['kode_jurusan' => 'AKL', 'nama_jurusan' => 'Akuntansi dan Keuangan Lembaga (AKL)'],
        ];

        // 3. Masukkan ke database (tanpa membuat user Kaprodi di seeder)
        foreach ($jurusan as $j) {
            Jurusan::create($j);
        }
    }
}