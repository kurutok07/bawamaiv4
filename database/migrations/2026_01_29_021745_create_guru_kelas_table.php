<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guru_kelas', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Guru
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            
            // Relasi ke Kelas
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            
            // Relasi ke Tahun Ajaran (Penting agar history terjaga)
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
            
            // Opsional: Mata Pelajaran (Jika ingin spesifik guru mapel apa)
            // $table->string('mata_pelajaran')->nullable(); 

            $table->timestamps();

            // Mencegah duplikasi: 1 Guru di 1 Kelas pada 1 Tahun Ajaran yang sama cukup sekali
            $table->unique(['guru_id', 'kelas_id', 'tahun_ajaran_id'], 'unique_guru_kelas_ta');
        });
    }

    public function down()
    {
        Schema::dropIfExists('guru_kelas');
    }
};