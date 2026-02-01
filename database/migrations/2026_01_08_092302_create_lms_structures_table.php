<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Item LMS (Hierarki Dinamis)
        Schema::create('lms_structures', function (Blueprint $table) {
            $table->id();

            // --- PERBAIKAN DI SINI ---
            // Ganti 'lms_items' menjadi 'lms_structures' (Self Referencing)
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('lms_structures') // <--- INI YANG BIKIN ERROR TADI
                  ->onDelete('cascade');
            
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon_image')->nullable(); 
            
            // Tipe konten
            $table->enum('type', ['folder', 'file', 'video', 'link'])->default('folder');
            
            // Isi konten 
            $table->string('content_file')->nullable(); 
            $table->string('content_url')->nullable(); 
            
            $table->integer('order')->default(0); 
            $table->integer('depth_level')->default(1); 
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('lms_access_logs');
        // --- PERBAIKAN DI SINI ---
        // Hapus tabel yang benar (lms_structures), bukan lms_items
        Schema::dropIfExists('lms_structures');
    }
};
