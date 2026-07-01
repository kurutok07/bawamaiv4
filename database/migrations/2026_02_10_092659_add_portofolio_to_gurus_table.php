<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Kita kasih 'nullable' supaya guru gak wajib upload pas register/awal
            // 'after' opsional, biar posisinya rapi di database (setelah foto)
            $table->string('portofolio')->nullable()->after('foto'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Kalau di-rollback, kolomnya dihapus
            $table->dropColumn('portofolio');
        });
    }
};