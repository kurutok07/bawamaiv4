<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gurus', function (Blueprint $table) {
            // 1. Ubah 'nuptk' (kredensial lama) menjadi 'niy'
            $table->renameColumn('nuptk', 'niy');
            
            // 2. Ubah 'nip' (opsional lama) menjadi 'nuptk'
            $table->renameColumn('nip', 'nuptk');
        });
    }

    public function down()
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Balikin lagi kalau rollback
            $table->renameColumn('niy', 'nuptk');
            $table->renameColumn('nuptk', 'nip');
        });
    }
};