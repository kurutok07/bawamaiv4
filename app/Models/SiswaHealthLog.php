<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaHealthLog extends Model
{
    use HasFactory;

    protected $table = 'siswa_health_logs';

    protected $fillable = [
        'siswa_id',
        'tanggal_periksa',
        'keluhan',
        'diagnosa',
        'tindakan',
        'obat_diberikan',
        'keterangan',
        'petugas_pencatat'
    ];

    // Relasi balik ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}