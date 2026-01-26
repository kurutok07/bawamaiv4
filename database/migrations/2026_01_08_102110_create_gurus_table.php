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
    Schema::create('gurus', function (Blueprint $table) {
        $table->id();
        $table->string('nip', 20)->unique();
        $table->string('nama_lengkap');
        $table->string('gelar_depan')->nullable(); // Bisa kosong (Contoh: Drs.)
        $table->string('gelar_belakang')->nullable(); // Bisa kosong (Contoh: S.Pd)
        $table->enum('jenis_kelamin', ['L', 'P']);
        $table->string('no_hp', 15)->nullable();
        $table->string('email')->unique()->nullable();
        $table->string('foto')->nullable(); // Untuk path foto
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
