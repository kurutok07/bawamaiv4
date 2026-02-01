<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;       
use App\Models\TahunAjaran; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Tambahkan DB Facade
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class SiswaImport implements ToModel, WithHeadingRow
{
    private $activeTa;

    public function __construct()
    {
        $this->activeTa = TahunAjaran::where('is_active', 1)->first();
    }

    private function transformDate($value)
    {
        if (!$value) return null;
        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function model(array $row)
    {
        // 1. Cek Data Wajib
        if (empty($row['nisn']) || empty($row['nama_lengkap'])) {
            return null;
        }
        $emailUser = !empty($row['email']) ? $row['email'] : $row['nisn'] . '@siswa.bawamai.id';
        // 2. LOGIC USER (Create or Ignore)
        // Kita pakai firstOrCreate agar password tidak ter-reset kalau user sudah ada.
        $user = User::firstOrCreate(
            ['username' => $row['nisn']], 
            [
                'name'     => $row['nama_lengkap'],
                'email'    => $emailUser, 
                'password' => Hash::make($row['nisn']), 
                'role'     => 'siswa',
            ]
        );

        // Jika nama di Excel beda dengan di DB, update nama User juga
        if ($user->name !== $row['nama_lengkap']) {
            $user->update(['name' => $row['nama_lengkap']]);
        }
        if (!empty($row['email']) && $user->email !== $row['email']) {
             $user->update(['email' => $row['email']]);
        }

        // 3. LOGIC SISWA (Update or Create)
        $siswa = Siswa::updateOrCreate(
            ['nisn' => $row['nisn']], 
            [
                'user_id'        => $user->id,
                'nik'            => $row['nik'] ?? null,   
                'nama_lengkap'   => $row['nama_lengkap'],
                'jenis_kelamin'  => isset($row['jenis_kelamin']) ? strtoupper($row['jenis_kelamin']) : 'L',
                'tempat_lahir'   => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'  => $this->transformDate($row['tanggal_lahir'] ?? null),
                'nama_wali'      => $row['nama_wali'] ?? null,
                'no_hp_wali'     => $row['no_hp_wali'] ?? null,
                'alamat'         => $row['alamat'] ?? null,
            ]
        );

        // 4. LOGIC MASUK KELAS OTOMATIS
        if (!empty($row['rombel']) && $this->activeTa) {
            
            $namaKelasExcel = trim($row['rombel']); 

            // Cari Kelas di Tahun Ajaran Aktif
            // Penting: Pastikan kelas yang dicari adalah kelas Tahun INI.
            $kelas = Kelas::where('tahun_ajaran_id', $this->activeTa->id)
                          ->where(function($q) use ($namaKelasExcel) {
                              $q->where('nama_kelas', $namaKelasExcel)
                                ->orWhere('nama_kelas', 'LIKE', '%'.$namaKelasExcel.'%');
                          })->first();

            if ($kelas) {
                // --- PERBAIKAN LOGIC (MENCEGAH DUPLIKAT KELAS DI TAHUN SAMA) ---
                
                // Cek apakah siswa sudah punya kelas LAIN di tahun ini?
                $existingRecord = DB::table('kelas_siswa')
                                    ->where('siswa_id', $siswa->id)
                                    ->where('tahun_ajaran_id', $this->activeTa->id)
                                    ->first();

                if ($existingRecord) {
                    // Jika sudah ada, tapi beda kelas -> UPDATE (Pindah Kelas)
                    if ($existingRecord->kelas_id != $kelas->id) {
                        DB::table('kelas_siswa')
                            ->where('id', $existingRecord->id)
                            ->update([
                                'kelas_id' => $kelas->id,
                                'updated_at' => now()
                            ]);
                    }
                    // Jika kelasnya sama, biarkan (Skip)
                } else {
                    // Jika belum ada di tahun ini -> INSERT BARU (Naik Kelas / Siswa Baru)
                    $siswa->kelas()->attach($kelas->id, ['tahun_ajaran_id' => $this->activeTa->id]);
                }
            }
        }

        return $siswa;
    }
}