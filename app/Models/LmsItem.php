<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // 1. Relasi ke Children (Sub-Menu)
    // Digunakan saat membuka folder: $item->children
    public function children()
    {
        return $this->hasMany(LmsItem::class, 'parent_id')->orderBy('order', 'asc');
    }

    // 2. Relasi ke Parent (Menu Induk)
    // Digunakan untuk breadcrumb: Home > Qur'ana > Jilid 1
    public function parent()
    {
        return $this->belongsTo(LmsItem::class, 'parent_id');
    }

    // 3. Relasi ke Logs (Analytics)
    public function logs()
    {
        return $this->hasMany(LmsAccessLog::class);
    }
    
    // Helper: Cek apakah ini folder?
    public function isFolder()
    {
        return $this->type === 'folder';
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}