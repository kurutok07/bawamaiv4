<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User; 
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Gunakan File Facade untuk hapus foto
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    // Helper
    private function getActiveTahunAjaran()
    {
        return TahunAjaran::where('is_active', 1)->first(); 
    }

    public function index(Request $request)
    {
        $activeTa = $this->getActiveTahunAjaran();
        
        $query = Siswa::with(['kelas' => function($q) use ($activeTa) {
            if($activeTa) $q->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
        }]);

        // Filter Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter Kelas
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            $query->whereHas('kelas', function($q) use ($request, $activeTa) {
                $q->where('kelas_id', $request->kelas_id);
                if($activeTa) $q->where('kelas_siswa.tahun_ajaran_id', $activeTa->id);
            });
        }

        $siswas = $query->latest()->paginate(9)->withQueryString(); 
        $daftarKelas = $activeTa ? Kelas::where('tahun_ajaran_id', $activeTa->id)->orderBy('nama_kelas')->get() : [];

        return view('admin.siswa.index', compact('siswas', 'daftarKelas', 'activeTa'));
    }

    // --- TAMBAHAN KEMBALI: CREATE ---
    public function create()
    {
        $activeTa = $this->getActiveTahunAjaran();
        $daftarKelas = $activeTa ? Kelas::where('tahun_ajaran_id', $activeTa->id)->get() : [];
        return view('admin.siswa.create', compact('daftarKelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'nisn'          => 'required|unique:siswas,nisn|unique:users,username',
            'nama_wali'     => 'required|string',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'foto'          => 'nullable|image|max:2048',
        ]);

        $activeTa = $this->getActiveTahunAjaran();

        // LOGIC UPLOAD FOTO
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'siswa_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/siswa'), $filename);
            $fotoPath = 'uploads/siswa/' . $filename;
        }

        DB::transaction(function () use ($request, $fotoPath, $activeTa) {
            // 1. Buat User
            $user = User::create([
                'name'     => $request->nama_lengkap,
                'email'    => $request->nisn . '@siswa.bawamai.id',
                'username' => $request->nisn,
                'password' => Hash::make($request->nisn),
                'role'     => 'siswa',
                'avatar'   => $fotoPath, 
            ]);

            // 2. Buat Siswa
            $siswa = Siswa::create([
                'user_id'         => $user->id,
                'nisn'            => $request->nisn,
                'nik'             => $request->nik,
                'nama_lengkap'    => $request->nama_lengkap,
                'jenis_kelamin'   => $request->jenis_kelamin,
                'tempat_lahir'    => $request->tempat_lahir,
                'tanggal_lahir'   => $request->tanggal_lahir,
                'nama_wali'       => $request->nama_wali,
                'no_hp_wali'      => $request->no_hp_wali,
                'alamat'          => $request->alamat,
                'foto'            => $fotoPath,
            ]);

            // 3. Masukkan ke Kelas
            if ($request->kelas_id && $activeTa) {
                $siswa->kelas()->attach($request->kelas_id, ['tahun_ajaran_id' => $activeTa->id]);
            }
        });

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil disimpan!');
    }

    // --- TAMBAHAN KEMBALI: EDIT ---
    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $activeTa = $this->getActiveTahunAjaran();
        
        $daftarKelas = $activeTa ? Kelas::where('tahun_ajaran_id', $activeTa->id)->orderBy('nama_kelas')->get() : [];
        
        // Cek kelas siswa saat ini
        $currentKelasID = null;
        if($activeTa) {
            $currentClass = $siswa->kelas()->wherePivot('tahun_ajaran_id', $activeTa->id)->first();
            $currentKelasID = $currentClass ? $currentClass->id : null;
        }

        return view('admin.siswa.edit', compact('siswa', 'daftarKelas', 'currentKelasID'));
    }

public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $activeTa = $this->getActiveTahunAjaran();

        $request->validate([
            'nama_lengkap' => 'required',
            'nisn'         => 'required|unique:siswas,nisn,' . $siswa->id,
            'kelas_id'     => 'nullable|exists:kelas,id',
        ]);

        $data = $request->except(['foto', 'password', 'kelas_id']); 
        
        // LOGIC FOTO (Tetap sama)
        if ($request->hasFile('foto')) {
            if ($siswa->foto && File::exists(public_path($siswa->foto))) {
                File::delete(public_path($siswa->foto));
            }
            $file = $request->file('foto');
            $filename = 'siswa_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/siswa'), $filename);
            $data['foto'] = 'uploads/siswa/' . $filename;
        }

        $siswa->update($data);

        // UPDATE USER (Tetap sama)
        if ($siswa->user_id) {
            $user = User::find($siswa->user_id);
            if($user) {
                $updateUser = [
                    'name' => $request->nama_lengkap, 
                    'username' => $request->nisn,
                    'email' => $request->nisn.'@siswa.bawamai.id'
                ];
                if(isset($data['foto'])) $updateUser['avatar'] = $data['foto'];
                $user->update($updateUser);
            }
        }

        // --- PERBAIKAN LOGIC KELAS (HISTORY AMAN) ---
        if ($activeTa) {
            // Kita cek dulu apakah ada perubahan kelas?
            // Ambil kelas siswa SAAT INI di tahun aktif
            $currentClassRecord = \Illuminate\Support\Facades\DB::table('kelas_siswa')
                                    ->where('siswa_id', $siswa->id)
                                    ->where('tahun_ajaran_id', $activeTa->id)
                                    ->first();

            $newKelasId = $request->kelas_id;

            if ($currentClassRecord) {
                // KASUS 1: SUDAH ADA KELAS DI TAHUN INI
                
                if ($newKelasId && $newKelasId != $currentClassRecord->kelas_id) {
                    // Jika user memilih kelas baru yang BEDA -> UPDATE
                    \Illuminate\Support\Facades\DB::table('kelas_siswa')
                        ->where('id', $currentClassRecord->id)
                        ->update([
                            'kelas_id' => $newKelasId,
                            'updated_at' => now()
                        ]);
                } elseif (!$newKelasId) {
                    // Jika user memilih "Kosongkan Kelas" -> HAPUS Record tahun ini
                    \Illuminate\Support\Facades\DB::table('kelas_siswa')
                        ->where('id', $currentClassRecord->id)
                        ->delete();
                }
                // Jika kelasnya sama, tidak ngapa-ngapain.

            } else {
                // KASUS 2: BELUM ADA KELAS DI TAHUN INI (Baru Naik Kelas / Siswa Baru)
                if ($newKelasId) {
                    // INSERT BARU (Attach)
                    $siswa->kelas()->attach($newKelasId, [
                        'tahun_ajaran_id' => $activeTa->id
                    ]);
                }
            }
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa diperbarui!');
    }
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        if ($siswa->foto && File::exists(public_path($siswa->foto))) {
            File::delete(public_path($siswa->foto));
        }
        
        if ($siswa->user_id) User::where('id', $siswa->user_id)->delete();
        
        $siswa->delete();
        
        return redirect()->back()->with('success', 'Data siswa dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new SiswaImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Data Siswa Berhasil!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }
}