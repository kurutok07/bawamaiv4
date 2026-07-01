<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carousel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $carousels = Carousel::orderBy('urutan', 'asc')->get();
        // Ambil value maintenance, default '0' jika tidak ada
        $maintenanceSetting = DB::table('settings')->where('key', 'maintenance_mode')->first();
        $maintenanceMode = $maintenanceSetting ? $maintenanceSetting->value : '0';

        return view('admin.settings.index', compact('carousels', 'maintenanceMode'));
    }

    // --- FITUR 1: UPDATE MAINTENANCE ---
    public function updateMaintenance(Request $request)
    {
        $status = $request->has('maintenance_mode') ? '1' : '0';

        DB::table('settings')->updateOrInsert(
            ['key' => 'maintenance_mode'],
            ['value' => $status]
        );

        // KUNCI: Redirect balik sambil bawa info tab 'general'
        return back()->with('success', 'Status website diperbarui.')
                     ->with('active_tab', 'general'); 
    }

    // --- FITUR 2: CAROUSEL ---
    public function storeCarousel(Request $request)
    {
        if (Carousel::count() >= 10) {
            return back()->with('error', 'Maksimal 10 slide.')
                         ->with('active_tab', 'carousel');
        }

        $request->validate([
            'type' => 'required|in:image,html',
            'image' => 'required_if:type,image|image|max:2048',
            'html_content' => 'required_if:type,html',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('carousel', 'public');
        }

        Carousel::create([
            'type' => $request->type,
            'file_path' => $path,
            'html_content' => $request->html_content,
            'urutan' => Carousel::count() + 1
        ]);

        // KUNCI: Redirect balik sambil bawa info tab 'carousel'
        return back()->with('success', 'Slide berhasil ditambahkan')
                     ->with('active_tab', 'carousel');
    }

    public function destroyCarousel($id)
    {
        $item = Carousel::findOrFail($id);
        if ($item->file_path) {
            Storage::disk('public')->delete($item->file_path);
        }
        $item->delete();

        // KUNCI: Redirect balik sambil bawa info tab 'carousel'
        return back()->with('success', 'Slide dihapus')
                     ->with('active_tab', 'carousel');
    }
}