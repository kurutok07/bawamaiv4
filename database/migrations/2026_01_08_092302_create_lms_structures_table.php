<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Item LMS (Hierarki Dinamis)
        Schema::create('lms_items', function (Blueprint $table) {
            $table->id();
            // Self-referencing untuk sub-menu
            $table->foreignId('parent_id')->nullable()->constrained('lms_items')->onDelete('cascade');
            
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon_image')->nullable(); // Path icon card
            
            // Tipe konten
            $table->enum('type', ['folder', 'file', 'video', 'link'])->default('folder');
            
            // Isi konten (diisi salah satu sesuai tipe)
            $table->string('content_file')->nullable(); // Path PDF/PPT
            $table->string('content_url')->nullable();  // Link Youtube/Drive
            
            $table->integer('order')->default(0); // Urutan tampilan
            $table->integer('depth_level')->default(1); // Level 1-4
            $table->timestamps();
        });

        // 2. Tabel Analytics / Log Akses
        Schema::create('lms_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lms_item_id')->constrained()->onDelete('cascade');
            $table->timestamp('accessed_at');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable(); // Info Browser/Device
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_access_logs');
        Schema::dropIfExists('lms_items');
    }
};