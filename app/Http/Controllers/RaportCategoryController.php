<?php

namespace App\Http\Controllers;

use App\Models\RaportCategory;
use Illuminate\Http\Request;

class RaportCategoryController extends Controller
{
    public function index()
    {
        $categories = RaportCategory::all();
        return view('admin.raport.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|max:50']);
        
        RaportCategory::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return back()->with('success', 'Kategori Raport berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama_kategori' => 'required|string|max:50']);
        
        $category = RaportCategory::findOrFail($id);
        $category->update(['nama_kategori' => $request->nama_kategori]);

        return back()->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $category = RaportCategory::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus');
    }
}