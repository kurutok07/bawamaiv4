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
    Schema::create('siswa_health_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
        $table->date('tanggal_periksa');
        $table->string('keluhan'); // Misal: Demam, Luka gores
        $table->string('diagnosa')->nullable(); // Misal: Flu, Maag
        $table->string('tindakan')->nullable(); // Misal: Istirahat di UKS, Diberi obat
        $table->string('obat_diberikan')->nullable();
        $table->text('keterangan')->nullable();
        $table->string('petugas_pencatat')->nullable(); // Admin/Guru yang input
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_health_logs');
    }
};
