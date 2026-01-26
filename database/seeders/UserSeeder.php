<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Akun ADMIN
        DB::table('users')->insert([
            'name'      => 'Super Admin',
            'username'  => 'admin',
            'email'     => 'admin@bawamai.sch.id',
            'password'  => Hash::make('password123'),
            'role'      => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Akun GURU
        DB::table('users')->insert([
            'name'      => 'Budi Guru',
            'username'  => 'guru01',
            'email'     => 'guru01@bawamai.sch.id',
            'password'  => Hash::make('password123'), // password sama biar gampang diingat
            'role'      => 'guru', // pastikan database kolom role tipe datanya string/enum yang support ini
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Akun SISWA
        DB::table('users')->insert([
            'name'      => 'Ani Siswa',
            'username'  => 'siswa01',
            'email'     => 'siswa01@bawamai.sch.id',
            'password'  => Hash::make('password123'),
            'role'      => 'siswa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}