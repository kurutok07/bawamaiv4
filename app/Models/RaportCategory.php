<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaportCategory extends Model
{
    protected $table = 'raport_categories';
    protected $fillable = ['parent_id', 'nama_kategori', 'type'];

    // Relasi untuk mengambil Sub-Kategori (Anak)
    public function children()
    {
        return $this->hasMany(RaportCategory::class, 'parent_id');
    }

    // Relasi untuk mengambil Parent (Induk)
    public function parent()
    {
        return $this->belongsTo(RaportCategory::class, 'parent_id');
    }
    
    // Helper untuk cek apakah ini folder
    public function isFolder()
    {
        return $this->type === 'folder';
    }
}