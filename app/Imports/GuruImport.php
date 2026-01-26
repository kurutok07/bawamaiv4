<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Validasi dasar
        if (!isset($row['nip']) || $row['nip'] == null) {
            return null;
        }

        // 2. LOGIC: Cari User dulu, kalau gak ada baru Buat (firstOrCreate)
        // Kita simpan hasilnya ke variabel $user agar bisa diambil ID-nya
        $user = User::firstOrCreate(
            ['username' => $row['nip']], // Cek berdasarkan NIP
            [
                'name'      => $row['nama_lengkap'],
                'email'     => $row['email'] ?? $row['nip'].'@bawamai.sch.id',
                'password'  => Hash::make($row['nip']), // Default Password = NIP
                'role'      => 'guru',
            ]
        );

        // 3. Return Data Guru (Sekarang sudah bawa user_id)
        // Gunakan updateOrCreate agar kalau upload ulang data gak duplikat/error
        return Guru::updateOrCreate(
            ['nip' => $row['nip']], // Kunci pencarian (NIP)
            [
                'user_id'        => $user->id, // <--- INI YG PENTING (Jembatannya)
                'nama_lengkap'   => $row['nama_lengkap'],
                'gelar_depan'    => $row['gelar_depan'] ?? null,
                'gelar_belakang' => $row['gelar_belakang'] ?? null,
                'jenis_kelamin'  => isset($row['jenis_kelamin']) ? strtoupper($row['jenis_kelamin']) : null,
                'no_hp'          => $row['no_hp'] ?? null,
                'email'          => $row['email'] ?? null,
                'foto'           => null,
            ]
        );
    }
}