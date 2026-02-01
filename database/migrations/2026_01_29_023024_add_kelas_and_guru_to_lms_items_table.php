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
    Schema::table('lms_items', function (Blueprint $table) {
        // Jika NULL = Materi Umum (Bisa dilihat semua siswa)
        // Jika TERISI = Materi Khusus Kelas tersebut
        $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');

        // Untuk mencatat Guru siapa yang upload (keperluan Analytics)
        $table->foreignId('guru_id')->nullable()->constrained('gurus')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('lms_items', function (Blueprint $table) {
        $table->dropForeign(['kelas_id']);
        $table->dropColumn('kelas_id');
        $table->dropForeign(['guru_id']);
        $table->dropColumn('guru_id');
    });
}
};
