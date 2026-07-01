<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;
use App\Models\SiswaFamily; // Import Model Family

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    // Daftar semua kolom yang ada di migration 'siswas'
    protected $fillable = [
        'user_id', 
        // Identitas
        'nipd', 'nisn', 'nik', 'nama_lengkap', 'jenis_kelamin', 
        'tempat_lahir', 'tanggal_lahir', 'agama', 'kebutuhan_khusus',
        
        // Alamat & Kontak
        'alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos',
        'jenis_tinggal', 'alat_transportasi', 'telepon', 'hp', 'email',
        
        // Akademik & Legal
        'skhun', 'no_peserta_un', 'no_seri_ijazah', 'sekolah_asal',
        'no_registrasi_akta_lahir', 'no_kk',
        
        // Fisik
        'berat_badan', 'tinggi_badan', 'lingkar_kepala', 
        'anak_ke', 'jml_saudara_kandung',
        
        // Zonasi
        'lintang', 'bujur', 'jarak_ke_sekolah',
        
        // KIP/PIP/KPS
        'penerima_kps', 'no_kps', 
        'penerima_kip', 'no_kip', 'nama_di_kip', 'no_kks',
        'layak_pip', 'alasan_layak_pip',
        
        // Bank
        'bank', 'no_rekening_bank', 'rekening_atas_nama',
        
        'foto' // Tetap di siswa
    ];

    // --- RELASI ---

    // 1. Relasi ke Keluarga (Ayah, Ibu, Wali)
    public function families()
    {
        return $this->hasMany(SiswaFamily::class, 'siswa_id');
    }

    // Helper untuk mempermudah akses di Blade: $siswa->ayah->nama
    public function ayah()
    {
        return $this->hasOne(SiswaFamily::class)->where('hubungan', 'ayah');
    }

    public function ibu()
    {
        return $this->hasOne(SiswaFamily::class)->where('hubungan', 'ibu');
    }

    public function wali()
    {
        return $this->hasOne(SiswaFamily::class)->where('hubungan', 'wali');
    }

    // 2. Relasi Kelas (Tetap Sama)
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswa', 'siswa_id', 'kelas_id')
                    ->withPivot('tahun_ajaran_id')
                    ->withTimestamps();
    }
    public function healthProfile() {
    return $this->hasOne(SiswaHealthProfile::class);
}

public function healthLogs() {
    return $this->hasMany(SiswaHealthLog::class)->orderBy('tanggal_periksa', 'desc');
}

    // Helper Kelas Aktif
    public function getKelasAktifAttribute()
    {
        $activeTa = \App\Models\TahunAjaran::where('is_active', 1)->first();
        if(!$activeTa) return null;

        return $this->kelas()
                    ->wherePivot('tahun_ajaran_id', $activeTa->id)
                    ->first();
    }
    public function riwayatKelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswa', 'siswa_id', 'kelas_id')
                    ->withPivot('tahun_ajaran_id')
                    ->withTimestamps()
                    // Mengurutkan agar kelas terbaru muncul duluan
                    ->orderByPivot('tahun_ajaran_id', 'desc');
    }
}