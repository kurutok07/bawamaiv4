<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsAccessLog extends Model
{
    use HasFactory;

    // --- TAMBAHKAN INI (FILLABLE) ---
    // Daftar kolom yang boleh diisi oleh script LmsController
    protected $fillable = [
        'user_id',
        'lms_item_id',
        'action_type',
        'ip_address',
        'user_agent',
    ];

    // --- RELASI (Penting untuk Analytics nanti) ---
    
    // Log milik siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Log melihat materi apa?
    public function lmsItem()
    {
        return $this->belongsTo(LmsItem::class, 'lms_item_id');
    }
}