<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\SiswaHealthLog;
use App\Models\SiswaHealthProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Jangan lupa import ini
class SiswaHealthController extends Controller
{
    /**
     * Menyimpan Log Riwayat Penyakit (Catatan UKS)
     */
    public function storeLog(Request $request)
    {
        $request->validate([
            'siswa_id'        => 'required|exists:siswas,id',
            'tanggal_periksa' => 'required|date',
            'keluhan'         => 'required|string|max:255',
            'diagnosa'        => 'nullable|string|max:255',
            'tindakan'        => 'nullable|string',
            'obat_diberikan'  => 'nullable|string|max:255',
        ]);

        SiswaHealthLog::create([
            'siswa_id'        => $request->siswa_id,
            'tanggal_periksa' => $request->tanggal_periksa,
            'keluhan'         => $request->keluhan,
            'diagnosa'        => $request->diagnosa,
            'tindakan'        => $request->tindakan,
            'obat_diberikan'  => $request->obat_diberikan,
            'petugas_pencatat'=> auth()->user()->name, // Mengambil nama admin yang login
        ]);

return redirect()->route('siswa.show', $request->siswa_id . '#kesehatan')
                     ->with('success', 'Catatan medis berhasil ditambahkan!');
    }

    /**
     * Mengupdate Profil Kesehatan Statis (Golongan Darah, Alergi, dll)
     */
    public function updateProfile(Request $request, $siswaId)
{
    // 1. Validasi Input (Termasuk File PDF max 5MB)
    $request->validate([
        'golongan_darah' => 'nullable|string|max:5',
        'riwayat_alergi' => 'nullable|string',
        'penyakit_bawaan' => 'nullable|string',
        'file_psikotest' => 'nullable|mimes:pdf|max:5120', // PDF Max 5MB
    ]);

    // 2. Ambil atau Buat Profil Kesehatan
    $profile = SiswaHealthProfile::firstOrNew(['siswa_id' => $siswaId]);
    
    // 3. Simpan Data Text Biasa
    $profile->golongan_darah = $request->golongan_darah;
    $profile->riwayat_alergi = $request->riwayat_alergi;
    $profile->penyakit_bawaan = $request->penyakit_bawaan;

    // 4. Logic Upload File Psikotest
    if ($request->hasFile('file_psikotest')) {
        // Hapus file lama jika ada
        if ($profile->file_psikotest && Storage::disk('public')->exists($profile->file_psikotest)) {
            Storage::disk('public')->delete($profile->file_psikotest);
        }

        // Upload file baru
        $path = $request->file('file_psikotest')->store('psikotest', 'public');
        $profile->file_psikotest = $path;
    }

    $profile->save();

    return redirect()->route('siswa.show', $siswaId . '#kesehatan')
                     ->with('success', 'Profil kesehatan siswa diperbarui!');
}

    /**
     * Mengupdate Log Kesehatan
     */
    public function updateLog(Request $request, $id)
    {
        $request->validate([
            'tanggal_periksa' => 'required|date',
            'keluhan'         => 'required|string|max:255',
            'diagnosa'        => 'nullable|string|max:255',
            'tindakan'        => 'nullable|string',
            'obat_diberikan'  => 'nullable|string|max:255',
        ]);

        $log = SiswaHealthLog::findOrFail($id);
        $log->update($request->all());

return redirect()->route('siswa.show', $log->siswa_id . '#kesehatan')
                     ->with('success', 'Catatan medis diperbarui!');    }


    /**
     * Menghapus Log Kesehatan
     */
    public function destroyLog($id)
    {
        $log = SiswaHealthLog::findOrFail($id);
        $log->delete();

return redirect()->route('siswa.show', $siswaId . '#kesehatan')
                     ->with('success', 'Catatan medis dihapus!');    }
}