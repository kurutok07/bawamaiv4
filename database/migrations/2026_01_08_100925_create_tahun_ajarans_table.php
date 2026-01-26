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
    Schema::create('tahun_ajarans', function (Blueprint $table) {
        $table->id();
        $table->string('tahun'); // Contoh: "2025/2026"
        $table->enum('semester', ['Ganjil', 'Genap']);
        $table->boolean('is_active')->default(false); // Status Aktif/Tidak
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajarans');
    }
};
