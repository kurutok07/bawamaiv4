<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Panggil seeder yang tadi dibuat
        $this->call([
            UserSeeder::class,
            // Tambahkan seeder lain di sini jika ada (misal: ProductSeeder::class)
        ]);
    }
}