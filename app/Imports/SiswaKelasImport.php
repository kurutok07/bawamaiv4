<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas; // Tambahkan
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaKelasImport implements ToCollection, WithHeadingRow
{
    protected $kelas_id;
    protected $tahun_ajaran_id;

    public function __construct($kelas_id, $tahun_ajaran_id)
    {
        $this->kelas_id = $kelas_id;
        $this->tahun_ajaran_id = $tahun_ajaran_id;
    }

public function collection(Collection $rows)
    {
        $kelas = Kelas::find($this->kelas_id);
        if(!$kelas) return;

        foreach ($rows as $row) {
            if (!isset($row['nisn'])) continue;

            $siswa = Siswa::where('nisn', $row['nisn'])->first();

            if ($siswa) {
                // PERBAIKAN: Ganti allSiswas() jadi siswas()
                $exists = $kelas->siswas()
                                ->where('siswa_id', $siswa->id)
                                ->wherePivot('tahun_ajaran_id', $this->tahun_ajaran_id)
                                ->exists();
                
                if (!$exists) {
                    // PERBAIKAN: Ganti allSiswas() jadi siswas()
                    $kelas->siswas()->attach($siswa->id, [
                        'tahun_ajaran_id' => $this->tahun_ajaran_id
                    ]);
                }
            }
        }
    }
}