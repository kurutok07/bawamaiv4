<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date; // Library bawaan Excel untuk convert tanggal
use Carbon\Carbon;

class GuruImport implements ToModel, WithHeadingRow
{
    /**
    * Fungsi Helper untuk mengubah Tanggal Excel (Angka) ke Format MySQL (YYYY-MM-DD)
    */
    private function transformDate($value)
    {
        if (!$value) return null;

        try {
            // 1. Jika formatnya Angka Serial Excel (Contoh: 42186)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            
            // 2. Jika formatnya sudah Teks (Contoh: "1990-05-05" atau "05/05/1990")
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            // Jika format ngaco/error, biarkan null daripada bikin error import
            return null;
        }
    }

    public function model(array $row)
    {
        // 1. Validasi Dasar (Cek NIY wajib ada)
        // Pastikan di Excel headernya bernama 'niy' atau 'nuptk' (sesuaikan)
        // Disini saya asumsikan header di excel tetap 'nuptk' tapi isinya NIY, 
        // ATAU Anda bisa ubah header excel jadi 'niy'.
        
        // Agar aman, kita cek key 'niy' dulu, kalau gak ada coba 'nuptk'
        $niyValue = $row['niy'] ?? $row['nuptk'] ?? null; 

        if (!$niyValue) {
            return null; // Skip baris ini jika tidak ada ID unik
        }

        // 2. Logic User (Login pakai NIY)
        $user = User::firstOrCreate(
            ['username' => $niyValue], 
            [
                'name'      => $row['nama_lengkap'],
                'email'     => $row['email'] ?? $niyValue.'@guru.bawamai.sch.id',
                'password'  => Hash::make($niyValue), // Password default = NIY
                'role'      => 'guru',
            ]
        );

        // 3. Logic Simpan Guru
        $guru = Guru::updateOrCreate(
            ['niy' => $niyValue], // Kunci update sekarang NIY
            [
                'user_id'             => $user->id,
                
                // Mapping Kolom Baru
                'nuptk'               => $row['nuptk'] ?? $row['nuptk_lama'] ?? null, // NIP lama jadi NUPTK
                // 'nip'              => null, // Kolom NIP di DB sudah tidak dipakai/null
                
                'nama_lengkap'        => $row['nama_lengkap'],
                'gelar_depan'         => $row['gelar_depan'] ?? null,
                'gelar_belakang'      => $row['gelar_belakang'] ?? null,
                'jenis_kelamin'       => isset($row['jenis_kelamin']) ? strtoupper($row['jenis_kelamin']) : 'L',
                'status_kepegawaian'  => $row['status_kepegawaian'] ?? 'HONORER',
                'tugas_tambahan'      => $row['tugas_tambahan'] ?? null,
                'tempat_lahir'        => $row['tempat_lahir'] ?? null,
                
                // --- GUNAKAN HELPER transformDate DI SINI ---
                'tanggal_lahir'       => $this->transformDate($row['tanggal_lahir'] ?? null), 
                'tmt_sekolah'         => $this->transformDate($row['tmt_sekolah'] ?? null),
                // --------------------------------------------

                'alamat'              => $row['alamat'] ?? null,
                'pendidikan_terakhir' => $row['pendidikan_terakhir'] ?? null,
                'tahun_lulus'         => $row['tahun_lulus'] ?? null,
                'masa_kerja_sd'       => $row['masa_kerja_sd'] ?? null,
                'masa_kerja_total'    => $row['masa_kerja_total'] ?? null,
                'no_hp'               => $row['no_hp'] ?? null,
                'email'               => $row['email'] ?? null,
            ]
        );

        // 4. Logic Otomatis Set Wali Kelas
        if (isset($row['wali_kelas_di']) && $row['wali_kelas_di'] != null) {
            $kelas = Kelas::where('nama_kelas', $row['wali_kelas_di'])->first();
            if ($kelas) {
                $kelas->update([
                    'wali_kelas_id' => $guru->id
                ]);
            }
        }

        return $guru;
    }
}