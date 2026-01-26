<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nip', 'nama_lengkap', 'gelar_depan', 'gelar_belakang',
        'jenis_kelamin', 'no_hp', 'email', 'foto',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'username', 'nip');
    }
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // 1. Jika database kosong
                if (!$this->foto) {
                    return asset('img/no-image.png');
                }

                // 2. Logic Pengecekan File Fisik
                // Route manual kamu mengambil file dari: storage/app/public/{folder}/{filename}
                // Jadi kita cek keberadaan file di path tersebut.
                
                $path = storage_path('app/public/' . $this->foto);

                if (file_exists($path)) {
                    // Return URL yang akan memicu Route Manual kamu
                    // asset('storage/fotos/abc.jpg') -> /storage/fotos/abc.jpg
                    // Route menangkap: folder=fotos, filename=abc.jpg -> SUKSES
                    return asset('storage/' . $this->foto);
                }
                
                // 3. Jika file fisik hilang
                return asset('img/no-image.png');
            }
        );
    }
}