<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lms_access_logs', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // PERBAIKAN DI SINI:
            // 1. Ubah nama kolom jadi lms_structure_id (supaya rapi)
            // 2. Arahkan constrained ke tabel 'lms_structures' (bukan lms_items)
            $table->foreignId('lms_structure_id')
                  ->constrained('lms_structures')
                  ->onDelete('cascade');
            
            $table->string('action_type')->default('view'); // view, download
            
            // Data Tambahan
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable(); // Browser/Device
            
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('lms_access_logs');
    }
};
