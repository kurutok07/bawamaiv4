<?php

namespace App\Http\Controllers;

use App\Models\LmsItem;

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil menu utama untuk ditampilkan di Landing Page
        $menus = LmsItem::whereNull('parent_id')
                        ->where('is_active', true)
                        ->orderBy('order', 'asc')
                        ->get();

        return view('welcome', compact('menus')); 
    }
}