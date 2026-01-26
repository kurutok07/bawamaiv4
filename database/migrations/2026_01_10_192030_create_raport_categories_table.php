<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_raport_categories_table.php
public function up()
{
    Schema::create('raport_categories', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kategori'); // Contoh: "Semester Ganjil", "Semester Genap", "Ijazah"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raport_categories');
    }
};
