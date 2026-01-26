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
            
            // Relasi ke User & Item LMS
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lms_item_id')->constrained('lms_items')->onDelete('cascade');
            
            $table->string('action_type')->default('view'); // view, download
            
            // Data Tambahan (Opsional tapi berguna buat security/audit)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable(); // Browser/Device
            
            $table->timestamps(); // created_at mencatat WAKTU AKSES
        });
    }

    public function down()
    {
        Schema::dropIfExists('lms_access_logs');
    }
};