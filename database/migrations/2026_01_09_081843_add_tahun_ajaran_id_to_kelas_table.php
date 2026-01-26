<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('kelas', function (Blueprint $table) {
        // Menambahkan kolom relasi ke tahun_ajarans
        // nullable() dipakai agar data lama tidak error saat dimigrate
        $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->onDelete('cascade')->after('id');
    });
}

public function down()
{
    Schema::table('kelas', function (Blueprint $table) {
        $table->dropForeign(['tahun_ajaran_id']);
        $table->dropColumn('tahun_ajaran_id');
    });
}};
