<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lms_access_logs', function (Blueprint $table) {
            // Cek apakah kolom lama masih ada? Jika ada, hapus.
            if (Schema::hasColumn('lms_access_logs', 'lms_structure_id')) {
                // Hapus Foreign Key dulu (biasanya format: nama_tabel_nama_kolom_foreign)
                // Kita bungkus try-catch agar jika key tidak ada, tidak error
                try {
                    $table->dropForeign(['lms_structure_id']); 
                } catch (\Exception $e) {
                    // Lanjut saja kalau foreign key gak ketemu
                }
                
                // Hapus Kolomnya
                $table->dropColumn('lms_structure_id');
            }
        });
    }

    public function down()
    {
        // Tidak perlu logic down karena ini pembersihan kolom sampah
    }
};