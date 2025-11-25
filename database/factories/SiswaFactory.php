<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Siswa>
 */
class SiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use Indonesian locale to generate Indonesian names where possible
        $fakerId = \Faker\Factory::create('id_ID');

        return [
            // Generate NISN acak 10 digit
            'nisn' => $fakerId->unique()->numerify('##########'),
            'nama_siswa' => $fakerId->name(), // Nama Indonesia acak
            'nomor_hp_wali_murid' => $fakerId->phoneNumber(),
            // kelas_id dan wali_murid_user_id akan kita isi manual saat dipanggil di Seeder
        ];
    }
}