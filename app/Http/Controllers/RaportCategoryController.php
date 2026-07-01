<?php

namespace App\Http\Controllers;

use App\Models\RaportCategory;
use Illuminate\Http\Request;

class RaportCategoryController extends Controller
{
    public function index()
    {
        // 1. Ambil Kategori Utama (Root) beserta isinya (Children)
        $categories = RaportCategory::whereNull('parent_id')
                                    ->with('children')
                                    ->orderBy('type', 'desc') // Folder dulu
                                    ->orderBy('nama_kategori', 'asc')
                                    ->get();

        // 2. Ambil List Folder untuk Dropdown di Modal Tambah
        // (Kita butuh daftar semua folder agar user bisa milih mau simpan file dimana)
        $folders = RaportCategory::where('type', 'folder')->orderBy('nama_kategori')->get();

        return view('admin.raport.categories', compact('categories', 'folders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'type'          => 'required|in:folder,file',
            'parent_id'     => 'nullable|exists:raport_categories,id'
        ]);

        RaportCategory::create([
            'nama_kategori' => $request->nama_kategori,
            'type'          => $request->type,
            'parent_id'     => $request->parent_id // Bisa null kalau dia Folder Utama
        ]);

        return back()->with('success', 'Berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $category = RaportCategory::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:100',
        ]);

        $category->update([
            'nama_kategori' => $request->nama_kategori,
            // parent_id bisa diupdate jika mau memindahkan folder/file (opsional)
            'parent_id'     => $request->parent_id ?? $category->parent_id
        ]);

        return back()->with('success', 'Berhasil diperbarui');
    }

    public function destroy($id)
    {
        $category = RaportCategory::findOrFail($id);
        
        // Cek Safety: Jika folder masih punya isi, jangan dihapus dulu
        if ($category->type === 'folder' && $category->children()->count() > 0) {
            return back()->with('error', 'Gagal! Folder tidak kosong. Hapus isinya terlebih dahulu.');
        }

        $category->delete();
        return back()->with('success', 'Berhasil dihapus');
    }
}