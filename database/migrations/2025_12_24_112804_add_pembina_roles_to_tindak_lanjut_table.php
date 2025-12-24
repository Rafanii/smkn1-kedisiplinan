<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan field pembina_roles ke tindak_lanjut untuk filtering dashboard.
     * Field ini disalin dari frequency_rule saat kasus dibuat.
     */
    public function up(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            // Field pembina_roles (JSON array) untuk filtering
            // Contoh: ["Wali Kelas", "Kaprodi"]
            $table->json('pembina_roles')
                  ->nullable()
                  ->after('sanksi_deskripsi')
                  ->comment('Role pembina yang terlibat (untuk filtering dashboard)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tindak_lanjut', function (Blueprint $table) {
            $table->dropColumn('pembina_roles');
        });
    }
};
