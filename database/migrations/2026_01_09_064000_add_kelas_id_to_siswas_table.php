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
    Schema::table('siswas', function (Blueprint $table) {
        // Tambah kolom kelas_id, boleh null jika siswa belum dapat kelas
        $table->foreignId('kelas_id')->nullable()->after('id')->constrained('kelas')->onDelete('set null');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            //
        });
    }
};
