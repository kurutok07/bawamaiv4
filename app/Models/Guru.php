<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Guru extends Model
{
    use HasFactory;

    // --- BAGIAN INI YANG PALING PENTING ---
    // Semua nama kolom database WAJIB ditulis di sini agar bisa disimpan.
protected $fillable = [
    // Identitas
    'user_id', 
    'niy', 
    'nuptk',            
    'nama_lengkap', 
    'gelar_depan', 
    'gelar_belakang',
    'jenis_kelamin', 
    'status_kepegawaian', 
    'tugas_tambahan',      // <--- BARU (Tugas Utama/Tambahan)
    
    // Biodata
    'tempat_lahir',
    'tanggal_lahir',
    'alamat',
    'no_hp', 
    'email', 
    'foto',

    // Karir & Pendidikan
    'pendidikan_terakhir',
    'tahun_lulus',
    'tmt_sekolah',
    'masa_kerja_sd',       // <--- BARU (Di SD Bawamai)
    'masa_kerja_total',    // <--- BARU (Keseluruhan)
];
    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Kelas (Wali Kelas)
public function kelas()
    {
        // 1. Ambil ID Tahun Ajaran Aktif
        $activeTa = TahunAjaran::where('is_active', 1)->first();
        $activeTaId = $activeTa ? $activeTa->id : null;

        // 2. Return relasi HANYA JIKA tahun ajarannya cocok
        return $this->hasOne(Kelas::class, 'wali_kelas_id')
                    ->where('tahun_ajaran_id', $activeTaId);
    }    // Relasi ke Kelas yang Diajar (Many-to-Many)
public function kelasAjar()
    {
        return $this->belongsToMany(Kelas::class, 'guru_kelas', 'guru_id', 'kelas_id')
                    ->withPivot('tahun_ajaran_id')
                    ->withTimestamps();
    }public function lmsItems()
{
    return $this->hasMany(LmsItem::class, 'guru_id');
}
    // Logic Foto URL (Tetap pertahankan yang working)
    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->foto) {
                    return asset('img/no-image.png');
                }
                
                $path = storage_path('app/public/' . $this->foto);

                if (file_exists($path)) {
                    return asset('storage/' . $this->foto);
                }
                
                return asset('img/no-image.png');
            }
        );
    }
}