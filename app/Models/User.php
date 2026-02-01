<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Siswa; 
use App\Models\Guru;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username', 
        'role',  
        'foto_profil',   // Wajib ada untuk Admin & Kepsek
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // --- RELASI ---
    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    // --- AKSESOR PINTAR: getFotoProfilAttribute ---
    // Dipanggil via: Auth::user()->foto_profil
    // Parameter $value adalah isi asli kolom 'foto_profil' di tabel users
    public function getFotoProfilAttribute($value)
    {
        // 1. Jika Role SISWA -> Ambil dari tabel siswas
        if ($this->role === 'siswa' && $this->siswa) {
            return $this->siswa->foto;
        }
        
        // 2. Jika Role GURU -> Ambil dari tabel gurus
        if ($this->role === 'guru' && $this->guru) {
            return $this->guru->foto;
        }

        // 3. Jika Role KEPALA SEKOLAH / ADMIN -> Ambil dari tabel users ($value)
        // Ini akan mengembalikan path yang kita upload lewat controller tadi
        return $value;
    }
}