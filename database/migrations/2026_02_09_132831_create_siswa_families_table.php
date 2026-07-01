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
    Schema::create('siswa_families', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke tabel Siswa (Jika siswa dihapus, data keluarga ikut terhapus)
        $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');

        // Penanda ini data siapa (Ayah/Ibu/Wali)
        $table->enum('hubungan', ['ayah', 'ibu', 'wali']);

        // Data Rinci (Sesuai list yang kamu kasih tadi)
        $table->string('nama')->nullable();
        $table->string('nik', 20)->nullable();
        $table->year('tahun_lahir')->nullable();
        $table->string('jenjang_pendidikan', 50)->nullable(); // SD/SMP/SMA/S1
        $table->string('pekerjaan', 100)->nullable();
        $table->string('penghasilan', 100)->nullable(); // < 500rb, 1-2 Juta, dll
        
        // Data tambahan wali (opsional jika dibutuhkan)
        $table->string('no_hp', 20)->nullable(); 
        $table->string('email', 50)->nullable();

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_families');
    }
};
