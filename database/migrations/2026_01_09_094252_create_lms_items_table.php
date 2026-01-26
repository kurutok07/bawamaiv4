<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lms_items', function (Blueprint $table) {
            $table->id();
            
            // Self-Referencing (Untuk Logic Folder Beranak)
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('lms_items')
                  ->onDelete('cascade'); 

            $table->string('title'); // Nama Menu / Judul Materi
            $table->string('slug'); // Untuk URL cantik (misal: qurana/jilid-1)
            
            // Tipe Item: 'folder' (punya anak), 'file' (pdf/pptx), 'video' (youtube)
            $table->enum('type', ['folder', 'file', 'video', 'link'])->default('folder');
            
            // Isi Konten:
            // Jika type='file', isinya path file (assets/pdf/...)
            // Jika type='video'/'link', isinya URL (https://youtube...)
            $table->text('content')->nullable(); 

            $table->string('cover_image')->nullable(); // Icon/Thumbnail Card
            $table->integer('order')->default(0); // Urutan Tampilan
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lms_items');
    }
};