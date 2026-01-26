<?php

namespace Database\Seeders;

use App\Models\LmsItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LmsMenuSeeder extends Seeder
{
    public function run()
    {
        // --- LEVEL 1: MENU UTAMA ---
        $qurana = LmsItem::create([
            'title' => "QUR'ANA",
            'slug' => Str::slug("QUR'ANA"),
            'type' => 'folder',
            'cover_image' => 'assets/dashboard/qurana/quran.png',
            'order' => 1
        ]);

        $smartLearning = LmsItem::create([
            'title' => 'SMART LEARNING',
            'slug' => Str::slug('SMART LEARNING'),
            'type' => 'folder',
            'cover_image' => 'assets/dashboard/smartlearning/book (1).png',
            'order' => 2
        ]);

        // --- LEVEL 2: SUB-MENU (Contoh di dalam Qur'ana) ---
        $jilid1 = LmsItem::create([
            'parent_id' => $qurana->id,
            'title' => 'Qurana Jilid 1',
            'slug' => Str::slug('Qurana Jilid 1'),
            'type' => 'folder',
            'cover_image' => 'assets/dashboard/qurana/quran.png',
            'order' => 1
        ]);

        // --- LEVEL 3: KONTEN AKHIR (Contoh Materi di Jilid 1) ---
        LmsItem::create([
            'parent_id' => $jilid1->id,
            'title' => 'Materi Hijaiyah (Video)',
            'slug' => Str::slug('Materi Hijaiyah Video'),
            'type' => 'video',
            'content' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'order' => 1
        ]);
        
        LmsItem::create([
            'parent_id' => $jilid1->id,
            'title' => 'Modul Jilid 1 (PDF)',
            'slug' => Str::slug('Modul Jilid 1 PDF'),
            'type' => 'file',
            'content' => 'assets/pdf/jilid1.pdf',
            'order' => 2
        ]);
    }
}