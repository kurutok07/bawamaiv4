<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date; // <--- PENTING UNTUK TANGGAL

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // DEBUGGING: Aktifkan baris ini jika ingin melihat apa yang dibaca Laravel
        // dd($row); 

        // 1. Cek Data Wajib (NIS & Nama). Jika kosong, skip.
        if (!isset($row['nis']) || !isset($row['nama_lengkap'])) {
            return null;
        }

        // 2. LOGIC TANGGAL: Konversi format Excel ke format SQL (Y-m-d)
        $tanggalLahir = null;
        if (isset($row['tanggal_lahir'])) {
            try {
                // Jika Excel mengirim angka (serial number), convert pakai class Date
                if (is_numeric($row['tanggal_lahir'])) {
                    $tanggalLahir = Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
                } 
                // Jika Excel mengirim teks (2001/02/17 atau 17-02-2001)
                else {
                    $tanggalLahir = date('Y-m-d', strtotime($row['tanggal_lahir']));
                }
            } catch (\Exception $e) {
                $tanggalLahir = null; // Jika format kacau, set null biar gak error
            }
        }

        // 3. LOGIC USER
        $user = User::firstOrCreate(
            ['username' => $row['nis']], 
            [
                'name'     => $row['nama_lengkap'],
                'email'    => !empty($row['email']) ? $row['email'] : null,
                'password' => Hash::make($row['nis']),
                'role'     => 'siswa',
            ]
        );

        // 4. LOGIC SISWA
        return Siswa::updateOrCreate(
            ['user_id' => $user->id], // Kunci update: User ID (lebih aman daripada NIS kalau usernya update)
            [
                'nis'           => $row['nis'],
                'nama_lengkap'  => $row['nama_lengkap'],
                'jenis_kelamin' => isset($row['jenis_kelamin']) ? strtoupper($row['jenis_kelamin']) : null,
                'nisn'          => $row['nisn'] ?? null,
                'tempat_lahir'  => $row['tempat_lahir'] ?? null,
                'tanggal_lahir' => $tanggalLahir, // Gunakan variabel hasil konversi di atas
                'nama_wali'     => $row['nama_wali'] ?? null,
                'no_hp_wali'    => $row['no_hp_wali'] ?? null,
                'alamat'        => $row['alamat'] ?? null,
                // Pastikan kolom ini ada di fillable Model Siswa
            ]
        );
    }
}