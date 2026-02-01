<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('siswas', function (Blueprint $table) {
            // 1. Ubah nama kolom 'nis' menjadi 'nik'
            // Pastikan kamu punya package doctrine/dbal (biasanya sudah ada di Laravel baru)
            $table->renameColumn('nis', 'nik');

            // 2. Tambahkan kolom 'nama_ibu_kandung' (karena di desc tadi belum ada)
            // Kita taruh setelah tanggal_lahir biar rapi
            $table->string('nama_ibu_kandung')->nullable()->after('tanggal_lahir');
            
            // 3. Ubah tipe data NIK biar konsisten (Optional, sesuaikan kebutuhan)
            // Biasanya NIK itu 16 digit angka, jadi string(16) atau string(20) cukup
            $table->string('nik', 20)->change();
        });
    }

    public function down()
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Rollback: Hapus kolom ibu dan kembalikan nik jadi nis
            $table->dropColumn('nama_ibu_kandung');
            $table->renameColumn('nik', 'nis');
        });
    }
};