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
    Schema::table('siswas', function (Blueprint $table) {
        // Menambahkan kolom tahun_ajaran_id, boleh null dulu buat data lama
        $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->onDelete('cascade'); 
    });
}

public function down()
{
    Schema::table('siswas', function (Blueprint $table) {
        $table->dropForeign(['tahun_ajaran_id']);
        $table->dropColumn('tahun_ajaran_id');
    });
}
};
