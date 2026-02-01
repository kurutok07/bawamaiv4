<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lms_access_logs', function (Blueprint $table) {
            // Cek dulu biar tidak error jika kolom sudah ada
            if (!Schema::hasColumn('lms_access_logs', 'lms_item_id')) {
                $table->foreignId('lms_item_id')->after('id')->constrained('lms_items')->onDelete('cascade');
            }

            // Sekalian cek kolom wajib lainnya
            if (!Schema::hasColumn('lms_access_logs', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('lms_access_logs', 'action_type')) {
                $table->string('action_type')->default('view'); // view, download
            }
            if (!Schema::hasColumn('lms_access_logs', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }
            if (!Schema::hasColumn('lms_access_logs', 'user_agent')) {
                $table->text('user_agent')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('lms_access_logs', function (Blueprint $table) {
            $table->dropForeign(['lms_item_id']);
            $table->dropColumn('lms_item_id');
            // Drop column lain jika perlu
        });
    }
};