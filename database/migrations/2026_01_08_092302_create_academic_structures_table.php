<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Tahun Ajar
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: 2024/2025 Ganjil
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 2. Tabel Kelas
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            // Wali Kelas (ambil dari users yang role=guru)
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name'); // Contoh: 1 Abu Bakar
            $table->string('level'); // Contoh: 1, 2, 3... 6 (Untuk sorting)
            $table->timestamps();
        });

        // 3. Pivot Table: Siswa masuk kelas mana
        Schema::create('class_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_student');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('academic_years');
    }
};