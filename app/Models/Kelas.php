<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas'; 
    protected $fillable = ['nama_kelas', 'wali_kelas_id', 'tingkat', 'tahun_ajaran_id', 'file_jadwal'];

    // Relasi ke Guru
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

// App\Models\Kelas.php
public function siswas()
{
    // KEMBALIKAN KE MURNI (Tanpa Filter Session/Tahun disini)
    // Biarkan Controller yang menentukan mau filter tahun berapa
    
    return $this->belongsToMany(Siswa::class, 'kelas_siswa')
                ->withPivot('tahun_ajaran_id')
                ->withTimestamps();
}
public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
    // Relasi ke Guru Pengajar (Many-to-Many)
public function gurus()
{
    return $this->belongsToMany(Guru::class, 'guru_kelas', 'kelas_id', 'guru_id')
                ->withPivot('tahun_ajaran_id')
                ->withTimestamps();
}

}