<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaportFile extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke Siswa
public function siswa()
    {
        // belongsTo(ModelTujuan, nama_foreign_key)
        return $this->belongsTo(Siswa::class, 'student_id');
    }
    // Relasi ke Kategori (Ganjil/Genap)
    public function category() {
        return $this->belongsTo(RaportCategory::class, 'raport_category_id');
    }

    // Relasi ke Kelas
    public function kelas() {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke Tahun Ajaran
    public function tahunAjaran() {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}