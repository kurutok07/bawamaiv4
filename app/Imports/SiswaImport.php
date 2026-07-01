<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\SiswaFamily;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class SiswaImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private $activeTa;
    public $successCount = 0;
    public $failCount = 0;

    public function __construct()
    {
        $this->activeTa = TahunAjaran::where('is_active', 1)->first();
    }

    private function cleanText($text)
    {
        if (empty($text)) return null;
        $text = str_replace(["\r\n", "\r", "\n"], ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
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
        // 1. VALIDASI DASAR (NISN & NAMA WAJIB)
        $nisnRaw = $row['nisn'] ?? null;
        $namaRaw = $this->cleanText($row['nama_lengkap'] ?? $row['nama'] ?? null);

        if (empty($nisnRaw) || empty($namaRaw)) {
            $this->failCount++;
            return null;
        }

        $nisn = trim((string) $nisnRaw); // Pastikan string angka bersih
        $emailUser = !empty($row['email']) ? $this->cleanText($row['email']) : $nisn . '@siswa.bawamai.id';

        // 2. MAPPING DATA (Sesuaikan dengan nama kolom Excel & Database)
        $dataSiswa = [
            'nipd'                 => $this->cleanText($row['nipd'] ?? null),
            'nik'                  => $row['nik'] ?? null,
            'nama_lengkap'         => $namaRaw,
            'jenis_kelamin'        => isset($row['jk']) ? strtoupper(trim($row['jk'])) : (isset($row['jenis_kelamin']) ? strtoupper(trim($row['jenis_kelamin'])) : 'L'),
            'tempat_lahir'         => $this->cleanText($row['tempat_lahir'] ?? null),
            'tanggal_lahir'        => $this->transformDate($row['tanggal_lahir'] ?? null),
            'agama'                => $this->cleanText($row['agama'] ?? null),
            
            // Alamat & Koordinat
            'alamat'               => $this->cleanText($row['alamat'] ?? $row['alamat_jalan'] ?? null),
            'rt'                   => $row['rt'] ?? null,
            'rw'                   => $row['rw'] ?? null,
            'dusun'                => $this->cleanText($row['dusun'] ?? null),
            'kelurahan'            => $this->cleanText($row['kelurahan'] ?? $row['desa'] ?? null),
            'kecamatan'            => $this->cleanText($row['kecamatan'] ?? null),
            'kode_pos'             => $row['kode_pos'] ?? null,
            'jenis_tinggal'        => $this->cleanText($row['jenis_tinggal'] ?? null),
            'alat_transportasi'    => $this->cleanText($row['alat_transportasi'] ?? null),
            'lintang'              => $row['lintang'] ?? null,
            'bujur'                => $row['bujur'] ?? null,
            'jarak_ke_sekolah'     => $row['jarak_ke_sekolah'] ?? null, // Dalam KM/Meter

            // Kontak
            'hp'                   => $row['hp'] ?? $row['nomor_hp'] ?? null,
            'email'                => $this->cleanText($row['email_pribadi'] ?? null),

            // Akademik & Legalitas
            'skhun'                => $row['skhun'] ?? null,
            'no_peserta_un'        => $row['no_peserta_un'] ?? null,
            'no_seri_ijazah'       => $row['no_seri_ijazah'] ?? null,
            'sekolah_asal'         => $this->cleanText($row['sekolah_asal'] ?? null),
            'no_kk'                => $row['no_kk'] ?? null,
            'no_registrasi_akta_lahir' => $row['no_reg_akta'] ?? $row['no_registrasi_akta_lahir'] ?? null,

            // Fisik
            'tinggi_badan'         => is_numeric($row['tinggi_badan'] ?? null) ? $row['tinggi_badan'] : null,
            'berat_badan'          => is_numeric($row['berat_badan'] ?? null) ? $row['berat_badan'] : null,
            'lingkar_kepala'       => is_numeric($row['lingkar_kepala'] ?? null) ? $row['lingkar_kepala'] : null,
            'anak_ke'              => is_numeric($row['anak_ke'] ?? null) ? $row['anak_ke'] : null,
            'jml_saudara_kandung'  => is_numeric($row['jml_saudara'] ?? null) ? $row['jml_saudara'] : null,

            // Kesejahteraan (KIP/KPS)
            'penerima_kip'         => (isset($row['penerima_kip']) && strtolower($row['penerima_kip']) == 'ya') ? 1 : 0,
            'no_kip'               => $row['no_kip'] ?? null,
            'nama_di_kip'          => $row['nama_di_kip'] ?? $namaRaw, // Default nama sendiri jika kosong
            'penerima_kps'         => (isset($row['penerima_kps']) && strtolower($row['penerima_kps']) == 'ya') ? 1 : 0,
            'no_kps'               => $row['no_kps'] ?? null,
            'no_kks'               => $row['no_kks'] ?? null, // Kartu Keluarga Sejahtera
            'bank'                 => $this->cleanText($row['bank'] ?? null),
            'no_rekening_bank'     => $row['no_rekening_bank'] ?? null,
            'rekening_atas_nama'   => $this->cleanText($row['rekening_atas_nama'] ?? null),
        ];

        try {
            DB::beginTransaction();

            // 3. LOGIC CEK: UPDATE ATAU CREATE?
            $siswa = Siswa::where('nisn', $nisn)->first();

            if ($siswa) {
                // --- UPDATE ---
                
                // Update User Login (hanya nama & email)
                if ($siswa->user) {
                    $siswa->user->update([
                        'name'  => $namaRaw,
                        'email' => $emailUser
                    ]);
                }

                // Update Biodata Siswa
                $siswa->update($dataSiswa);
                
                Log::info("[IMPORT UPDATE] NISN $nisn berhasil diperbarui.");

            } else {
                // --- CREATE BARU ---

                // Buat User Login
                $user = User::firstOrCreate(
                    ['username' => $nisn],
                    [
                        'name'     => $namaRaw,
                        'email'    => $emailUser,
                        'password' => Hash::make($nisn), // Password = NISN
                        'role'     => 'siswa',
                    ]
                );

                // Inject ID User & NISN
                $dataSiswa['nisn'] = $nisn;
                $dataSiswa['user_id'] = $user->id;
                
                $siswa = Siswa::create($dataSiswa);

                Log::info("[IMPORT NEW] Siswa Baru NISN $nisn ditambahkan.");
            }

            // 4. PROSES KELUARGA (Update/Create)
            $this->processFamily($siswa->id, 'ayah', $row, 'ayah');
            $this->processFamily($siswa->id, 'ibu', $row, 'ibu');
            $this->processFamily($siswa->id, 'wali', $row, 'wali');

            // 5. PROSES KELAS (ROMBEL)
            $this->processKelas($siswa, $row, $namaRaw);

            DB::commit();
            $this->successCount++; // Tambah counter berhasil
            
            return $siswa;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[IMPORT ERROR] Baris NISN $nisn: " . $e->getMessage());
            $this->failCount++; // Tambah counter gagal
            return null;
        }
    }

    // Helper Keluarga
    private function processFamily($siswaId, $hubungan, $row, $suffix)
    {
        $namaKey = "nama_" . $suffix;
        if (!empty($row[$namaKey])) {
            SiswaFamily::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'hubungan' => $hubungan
                ],
                [
                    'nama'               => $this->cleanText($row[$namaKey]),
                    'nik'                => $row["nik_" . $suffix] ?? null,
                    'tahun_lahir'        => is_numeric($row["tahun_lahir_" . $suffix] ?? null) ? $row["tahun_lahir_" . $suffix] : null,
                    'jenjang_pendidikan' => $this->cleanText($row["pendidikan_" . $suffix] ?? null),
                    'pekerjaan'          => $this->cleanText($row["pekerjaan_" . $suffix] ?? null),
                    'penghasilan'        => $this->cleanText($row["penghasilan_" . $suffix] ?? null),
                    'no_hp'              => ($hubungan == 'wali') ? ($row['no_hp_wali'] ?? null) : null,
                ]
            );
        }
    }

    // Helper Kelas
    private function processKelas($siswa, $row, $namaSiswa)
    {
        $namaKelasExcel = $row['rombel'] ?? $row['kelas'] ?? null;
        
        if (!empty($namaKelasExcel) && $this->activeTa) {
            $namaKelasExcel = trim($namaKelasExcel);
            
            // Cari kelas berdasarkan Nama (Misal: "1A" atau "Kelas 1A")
            $kelas = Kelas::where('tahun_ajaran_id', $this->activeTa->id)
                          ->where(function($q) use ($namaKelasExcel) {
                              $q->where('nama_kelas', $namaKelasExcel)
                                ->orWhere('nama_kelas', 'LIKE', '%'.$namaKelasExcel.'%');
                          })->first();

            if ($kelas) {
                // Cek data pivot di tahun ajaran ini
                $existing = DB::table('kelas_siswa')
                    ->where('siswa_id', $siswa->id)
                    ->where('tahun_ajaran_id', $this->activeTa->id)
                    ->first();

                if ($existing) {
                    // Jika sudah punya kelas tp beda, update (pindah kelas)
                    if ($existing->kelas_id != $kelas->id) {
                        DB::table('kelas_siswa')
                            ->where('id', $existing->id)
                            ->update(['kelas_id' => $kelas->id, 'updated_at' => now()]);
                    }
                } else {
                    // Jika belum punya kelas, masukkan
                    $siswa->kelas()->attach($kelas->id, ['tahun_ajaran_id' => $this->activeTa->id]);
                }
            }
        }
    }

    // Handler Error Fatal Excel
    public function onError(\Throwable $e)
    {
        $this->failCount++;
        Log::error("[IMPORT FATAL ERROR] " . $e->getMessage());
    }
}