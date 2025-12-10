<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role; // <-- 1. IMPORT MODEL ROLE
use App\Models\User; // <-- 2. IMPORT MODEL USER
use Illuminate\Support\Facades\Hash; // <-- 3. IMPORT HASH UNTUK PASSWORD

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID dari setiap role
        $roleOperator = Role::where('nama_role', 'Operator Sekolah')->first()->id;
        $roleWaka = Role::where('nama_role', 'Waka Kesiswaan')->first()->id;
        $roleKepsek = Role::where('nama_role', 'Kepala Sekolah')->first()->id;
        $roleGuru = Role::where('nama_role', 'Guru')->first()->id;
        $roleKaprodi = Role::where('nama_role', 'Kaprodi')->first()->id;
        $roleWaliKelas = Role::where('nama_role', 'Wali Kelas')->first()->id;

        // 2. Buat Pengguna Operator Sekolah
        User::updateOrCreate(
            ['username' => 'operator'], // Cari berdasarkan username 'operator'
            [
                'role_id' => $roleOperator,
                'nama' => 'Operator Admin',
                'email' => 'operator@smkn1.sch.id',
                'password' => Hash::make('password'), // passwordnya: "password"
                'email_verified_at' => now(),
            ]
        );

        // 3. Buat Pengguna Waka Kesiswaan
        User::updateOrCreate(
            ['username' => 'waka'], // Cari berdasarkan username 'waka'
            [
                'role_id' => $roleWaka,
                'nama' => 'Waka Kesiswaan',
                'email' => 'waka@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 4. Buat Pengguna Kepala Sekolah
        User::updateOrCreate(
            ['username' => 'kepsek'], // Cari berdasarkan username 'kepsek'
            [
                'role_id' => $roleKepsek,
                'nama' => 'Kepala Sekolah',
                'email' => 'kepsek@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 5. Buat Pengguna Guru (Umum)
        User::updateOrCreate(
            ['username' => 'guru'], // Cari berdasarkan username 'guru'
            [
                'role_id' => $roleGuru,
                'nama' => 'Guru Umum',
                'email' => 'guru@smkn1.sch.id',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Note: Kaprodi, Wali Kelas dan Wali Murid will be auto-generated
        // during Jurusan/Kelas/Siswa creation in app flow. Seeder only creates
        // operator/waka/kepsek/guru accounts as requested.
    }
}