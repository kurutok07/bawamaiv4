<?php

namespace App\Http\Controllers;

use App\Models\LmsItem;
use App\Models\Carousel; // <--- 1. Tambahkan Model Carousel

class LandingPageController extends Controller
{
    public function index()
    {
        // 1. Ambil Menu LMS (Logic Lama Anda)
        $menus = LmsItem::whereNull('parent_id')
                        ->where('is_active', true)
                        ->orderBy('order', 'asc')
                        ->get();

        // 2. Ambil Data Carousel (Logic Baru)
        $carousels = Carousel::where('is_active', 1)
                             ->orderBy('urutan', 'asc')
                             ->get();

        // 3. Kirim KEDUANYA ke view
        return view('welcome', compact('menus', 'carousels')); 
    }
}