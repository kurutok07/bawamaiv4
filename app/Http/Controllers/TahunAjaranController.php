<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahun_ajaran = TahunAjaran::orderBy('created_at', 'desc')->get();
        return view('admin.tahun_ajaran.index', compact('tahun_ajaran'));
    }

    public function store(Request $request)
{
    // Hapus validasi semester
    $request->validate([
        'tahun' => 'required|string|max:20', // Contoh: 2024/2025
    ]);

    TahunAjaran::create([
        'tahun' => $request->tahun,
        'is_active' => false // Default tidak aktif
    ]);

    return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun Ajaran berhasil ditambahkan');
}

    public function activate($id)
    {
        // Nonaktifkan semua
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        
        // Aktifkan yang dipilih
        $ta = TahunAjaran::findOrFail($id);
        $ta->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Tahun Ajaran aktif berhasil diubah');
    }
    
    public function destroy($id)
    {
        TahunAjaran::destroy($id);
        return redirect()->back()->with('success', 'Data dihapus');
    }
}