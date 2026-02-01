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
    Schema::table('kelas', function (Blueprint $table) {
        // Kolom untuk menyimpan path file PDF
        $table->string('file_jadwal')->nullable()->after('nama_kelas');
    });
}

public function down()
{
    Schema::table('kelas', function (Blueprint $table) {
        $table->dropColumn('file_jadwal');
    });
}
};
