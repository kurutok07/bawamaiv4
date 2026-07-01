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
    Schema::table('raport_categories', function (Blueprint $table) {
        // Kolom untuk menampung ID induk (jika dia sub-folder)
        $table->unsignedBigInteger('parent_id')->nullable()->after('id');
        
        // Penanda apakah dia Folder (Wadah) atau File (Kategori Upload)
        $table->enum('type', ['folder', 'file'])->default('file')->after('nama_kategori');

        // Relasi Self-Join (Optional, biar aman datanya)
        $table->foreign('parent_id')->references('id')->on('raport_categories')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('raport_categories', function (Blueprint $table) {
        $table->dropForeign(['parent_id']);
        $table->dropColumn(['parent_id', 'type']);
    });
}

};
