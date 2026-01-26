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
    Schema::table('tahun_ajarans', function (Blueprint $table) {
        $table->dropColumn('semester');
    });
}

public function down()
{
    Schema::table('tahun_ajarans', function (Blueprint $table) {
        $table->enum('semester', ['Ganjil', 'Genap'])->after('tahun');
    });
}

};
