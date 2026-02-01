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
        Schema::table('lms_access_logs', function (Blueprint $table) {
            // Kita hapus/komentari baris ini karena bikin error (ternyata FK-nya ga ada)
            // $table->dropForeign(['lms_structure_id']); 

            // Langsung sikat hapus kolomnya saja
            if (Schema::hasColumn('lms_access_logs', 'lms_structure_id')) {
                $table->dropColumn('lms_structure_id');
            }
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_access_logs', function (Blueprint $table) {
            //
        });
    }
};
