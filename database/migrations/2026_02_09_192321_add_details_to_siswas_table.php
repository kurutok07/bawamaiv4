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
        // --- IDENTITAS UTAMA (Tambahan) ---
        // 'nama_lengkap', 'nisn', 'nik', 'jenis_kelamin' (Sudah ada di migration lama)
        
        $table->string('nipd', 20)->nullable()->after('nisn');
        $table->string('agama', 20)->nullable()->after('jenis_kelamin');
        $table->string('kebutuhan_khusus')->nullable()->after('agama');
        
        // --- ALAMAT RINCI ---
        // 'alamat' (Sudah ada, tapi kita tambah rinciannya)
        $table->string('rt', 5)->nullable()->after('alamat');
        $table->string('rw', 5)->nullable()->after('rt');
        $table->string('dusun', 50)->nullable()->after('rw');
        $table->string('kelurahan', 50)->nullable()->after('dusun');
        $table->string('kecamatan', 50)->nullable()->after('kelurahan');
        $table->string('kode_pos', 10)->nullable()->after('kecamatan');
        $table->string('jenis_tinggal', 50)->nullable()->after('kode_pos');
        $table->string('alat_transportasi', 50)->nullable()->after('jenis_tinggal');
        
        // --- KONTAK ---
        $table->string('telepon', 20)->nullable()->after('alat_transportasi');
        $table->string('hp', 20)->nullable()->after('telepon');
        $table->string('email', 50)->nullable()->after('hp');

        // --- AKADEMIK & LEGAL ---
        $table->string('skhun', 30)->nullable();
        $table->string('no_peserta_un', 30)->nullable();
        $table->string('no_seri_ijazah', 30)->nullable();
        $table->string('sekolah_asal', 100)->nullable();
        $table->string('no_registrasi_akta_lahir', 50)->nullable();
        $table->string('no_kk', 30)->nullable(); // Kartu Keluarga

        // --- DATA PERIODIK (FISIK) ---
        $table->integer('berat_badan')->nullable(); // kg
        $table->integer('tinggi_badan')->nullable(); // cm
        $table->integer('lingkar_kepala')->nullable(); // cm
        $table->integer('anak_ke')->nullable();
        $table->integer('jml_saudara_kandung')->nullable();

        // --- KOORDINAT (ZONASI) ---
        $table->string('lintang', 20)->nullable();
        $table->string('bujur', 20)->nullable();
        $table->integer('jarak_ke_sekolah')->nullable(); // km

        // --- KESEJAHTERAAN (KIP/KPS/PKH) ---
        $table->boolean('penerima_kps')->default(0);
        $table->string('no_kps', 30)->nullable();
        
        $table->boolean('penerima_kip')->default(0);
        $table->string('no_kip', 30)->nullable();
        $table->string('nama_di_kip', 100)->nullable();
        
        $table->string('no_kks', 30)->nullable(); // Kartu Keluarga Sejahtera

        $table->boolean('layak_pip')->default(0);
        $table->string('alasan_layak_pip')->nullable();

        // --- BANK (PIP) ---
        $table->string('bank', 20)->nullable(); 
        $table->string('no_rekening_bank', 30)->nullable();
        $table->string('rekening_atas_nama', 50)->nullable();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            //
        });
    }
};
