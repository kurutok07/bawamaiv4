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
    Schema::create('kelas_siswa', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
        $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
        // Pastikan tabel tahun_ajarans sudah ada, atau sesuaikan nama tabelnya
        $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade'); 
        
        $table->timestamps();

        // Mencegah duplikasi: 1 Siswa hanya boleh ada di 1 Kelas pada Tahun Ajaran yang sama
        $table->unique(['siswa_id', 'tahun_ajaran_id']); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_siswa');
    }
};
