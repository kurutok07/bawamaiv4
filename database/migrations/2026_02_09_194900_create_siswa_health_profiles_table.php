<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up() {
    Schema::create('siswa_health_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
        $table->string('golongan_darah', 5)->nullable();
        $table->text('riwayat_alergi')->nullable(); // Makanan, obat, debu
        $table->text('penyakit_bawaan')->nullable(); // Asma, jantung, dll
        $table->text('catatan_khusus')->nullable(); 
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_health_profiles');
    }
};
