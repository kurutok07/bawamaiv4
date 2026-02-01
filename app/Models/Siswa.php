<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas; // Jangan lupa import

class Siswa extends Model
{
    use HasFactory;

    // Pastikan ini sesuai dengan nama tabel di migration
    protected $table = 'siswas';

    protected $fillable = [
        'user_id',        // <--- INI WAJIB ADA AGAR BISA DISIMPAN
        'nisn',
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'nama_wali',
        'no_hp_wali',
        'alamat',
        'foto',
        'kelas_id',        
        'tahun_ajaran_id', // <--- Tambahkan ini
    ];
public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswa', 'siswa_id', 'kelas_id')
                    ->withPivot('tahun_ajaran_id') // Agar bisa akses/filter tahun ajaran
                    ->withTimestamps();
    }
    // Helper: Ambil Kelas Aktif (Berdasarkan Tahun Ajaran yg sedang aktif)
    // Asumsi: Kamu punya cara menentukan active TA, misal via Session atau Database
    public function getKelasAktifAttribute()
    {
        $activeTaId = session('tahun_ajaran_id'); // Atau logic lain
        return $this->riwayatKelas()
                    ->wherePivot('tahun_ajaran_id', $activeTaId)
                    ->first();
    }

public function riwayatKelas()
{
    // Menggunakan tabel pivot 'kelas_siswa'
    return $this->belongsToMany(Kelas::class, 'kelas_siswa')
                ->withPivot('tahun_ajaran_id')
                ->withTimestamps();
}

// Untuk mengambil kelas siswa di tahun aktif tertentu
public function kelasDiTahun($tahunId)
{
    return $this->belongsToMany(Kelas::class, 'kelas_siswa')
                ->wherePivot('tahun_ajaran_id', $tahunId);
}


}