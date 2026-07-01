<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run()
    {
        // Data awal biar maintenance mati (0) saat pertama kali deploy
        DB::table('settings')->updateOrInsert(
            ['key' => 'maintenance_mode'],
            ['value' => '0']
        );
    }
}