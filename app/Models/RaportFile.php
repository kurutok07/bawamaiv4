<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class RaportFile extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke Siswa
    public function siswa() {
        return $this->belongsTo(Siswa::class, 'student_id');
    }
    
    // Relasi ke Kategori
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

    /**
     * Accessor: Judul File (Display Name)
     * Jika kolom 'nama_file' diisi (Sertifikat Custom), pakai itu.
     * Jika kosong (Raport Biasa), pakai nama Kategorinya.
     */
    protected function judul(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Cek kolom 'nama_file' dulu
                if (!empty($attributes['nama_file'])) {
                    return $attributes['nama_file'];
                }
                // Fallback ke relasi category -> nama_kategori
                return $this->category->nama_kategori ?? 'Dokumen Tanpa Judul';
            }
        );
    }
}