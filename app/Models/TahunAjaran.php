<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    // Karena nama tabelnya jamak bahasa indonesia (pake 's' di belakang),
    // Sebaiknya definisikan secara eksplisit biar aman.
    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'tahun',      // varchar
        'is_active',  // tinyint
    ];
}