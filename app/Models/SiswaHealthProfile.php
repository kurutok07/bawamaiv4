<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaHealthProfile extends Model
{
    use HasFactory;

    protected $table = 'siswa_health_profiles';

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'siswa_id',
        'golongan_darah',
        'riwayat_alergi',
        'penyakit_bawaan',
        'catatan_khusus',
        'file_psikotest',
    ];

    // Relasi balik ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}   