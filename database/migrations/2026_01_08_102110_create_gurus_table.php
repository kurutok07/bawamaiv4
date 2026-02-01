<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            // INI KUNCINYA: $table->id() otomatis membuat 'Unsigned Big Integer'
            // Jangan diganti jadi $table->integer() atau lainnya.
            $table->id(); 
            
            // Relasi ke User (Pastikan ini ada jika kamu pakai user_id di controller)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('nip')->unique()->nullable();
            $table->string('nuptk')->unique()->nullable();
            $table->string('nama_lengkap');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('status_kepegawaian')->nullable();
            $table->string('tugas_tambahan')->nullable();
            
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('foto')->nullable();
            
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('tahun_lulus')->nullable();
            $table->date('tmt_sekolah')->nullable();
            $table->string('masa_kerja_sd')->nullable();
            $table->string('masa_kerja_total')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};