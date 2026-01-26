<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            
            // Data Identitas Utama
            $table->string('nis', 20)->unique();      // NIS Wajib & Unik
            $table->string('nisn', 20)->nullable();   // NISN (Boleh kosong di awal)
            
            // Data Diri
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            
            // Data Wali & Kontak
            $table->string('nama_wali')->nullable();
            $table->string('no_hp_wali', 15)->nullable();
            $table->text('alamat')->nullable();
            
            // Foto (Default null)
            $table->string('foto')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('siswas');
    }
};