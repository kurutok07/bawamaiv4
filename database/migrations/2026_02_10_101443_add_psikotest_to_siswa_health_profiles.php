<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswa_health_profiles', function (Blueprint $table) {
            // Tambahkan kolom untuk menyimpan path file psikotest
            // Nullable karena tidak semua siswa wajib/punya hasil psikotest
            $table->string('file_psikotest')->nullable()->after('catatan_khusus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_health_profiles', function (Blueprint $table) {
            $table->dropColumn('file_psikotest');
        });
    }
};  