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
        Schema::create('raport_files', function (Blueprint $table) {
            $table->id();
            
            // Perbaikan Relasi Siswa
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            
            // Perbaikan Relasi Kategori
            $table->foreignId('raport_category_id')->constrained('raport_categories')->onDelete('cascade'); 
            
            // Perbaikan Relasi Kelas
            // Pastikan nama tabel kelas benar (biasanya 'kelas' karena kita sering ubah manual, atau 'kelas' jamaknya tetap 'kelas'?)
            // Jika error lagi disini, ganti jadi constrained('classes') atau sesuai nama tabel di database phpmyadmin
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade'); 

            // --- BAGIAN YANG ERROR TADI ---
            // Coba ganti 'tahun_ajaran' menjadi 'tahun_ajarans' (jamak/plural)
            // Atau hapus parameter stringnya biar Laravel nebak sendiri
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade'); 
            // ------------------------------
            
            $table->foreignId('uploaded_by')->constrained('users'); 
            
            $table->string('file_path'); 
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raport_files');
    }
};
