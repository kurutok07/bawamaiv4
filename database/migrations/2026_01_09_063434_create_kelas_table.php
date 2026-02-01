<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();

            // Relasi Tahun Ajaran
            $table->foreignId('tahun_ajaran_id')
                  ->constrained('tahun_ajarans')
                  ->onDelete('cascade');
            
            // Relasi Wali Kelas
            // Harus UnsignedBigInteger agar jodoh dengan $table->id() di tabel gurus
            $table->unsignedBigInteger('wali_kelas_id')->nullable(); 
            
            $table->foreign('wali_kelas_id')
                  ->references('id')
                  ->on('gurus') // Pastikan nama tabel di database benar 'gurus'
                  ->onDelete('set null');

            $table->string('nama_kelas'); 
            $table->integer('tingkat');   
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kelas');
    }
};