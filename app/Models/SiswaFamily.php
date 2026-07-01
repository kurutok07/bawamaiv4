<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaFamily extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'siswa_families';

    // Guarded id artinya: "Semua kolom boleh diisi massal KECUALI id"
    // Ini lebih praktis daripada ngetik $fillable satu-satu
    protected $guarded = ['id'];

    // Relasi balik ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}