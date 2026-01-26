@extends('layouts.admin')

@section('title', 'Tahun Ajaran')

@section('content')

{{-- Tambahkan Style Khusus di sini --}}
<style>
    /* Default Desktop Styles */
    .page-header-responsive {
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 30px;
    }
    
    .admin-grid-responsive {
        display: grid;
        grid-template-columns: 1fr 2fr; /* Kiri kecil, Kanan besar */
        gap: 20px;
        align-items: start;
    }

    /* Style untuk Table Wrapper agar bisa discroll di HP */
    .table-responsive-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* === MOBILE RESPONSIVE (Max width 768px - Tablet/HP) === */
    @media (max-width: 768px) {
        /* Header jadi tumpuk ke bawah */
        .page-header-responsive {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .page-header-responsive .btn {
            width: 100%; /* Tombol jadi lebar penuh */
            justify-content: center;
        }

        /* Grid jadi 1 kolom (Form di atas, Tabel di bawah) */
        .admin-grid-responsive {
            grid-template-columns: 1fr; 
        }

        /* Form Card */
        .admin-card {
            width: 100%;
        }
    }
</style>

{{-- Header Section --}}
<div class="page-header-responsive">
    <div style="text-align: left;"> 
        <h1 class="page-title" style="margin: 0;">Tahun Ajaran</h1>
        <p class="page-subtitle" style="margin: 5px 0 0;">Kelola tahun akademik dan semester aktif.</p>
    </div>
    
    <a href="{{ route('dashboard') }}" class="btn" style="background-color: #6b7280; color: white; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

{{-- Grid Content --}}
<div class="admin-grid-responsive">
    
    {{-- Kolom Kiri: Form Tambah --}}
    <div class="admin-card" style="align-items: flex-start; text-align: left;">
        <h3 style="margin-bottom: 20px;">Tambah Baru</h3>
        
        <form action="{{ route('tahun-ajaran.store') }}" method="POST" style="width: 100%;">
            @csrf
            <div class="form-group">
                <label class="form-label">Tahun (Contoh: 2025/2026)</label>
                <input type="text" name="tahun" class="form-control" placeholder="2025/2026" required>
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px; margin-bottom: 15px;">
                <input type="checkbox" name="is_active" id="activeCheck">
                <label for="activeCheck" style="cursor: pointer; margin-bottom: 0;">Set sebagai Aktif?</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Data</button>
        </form>
    </div>

    {{-- Kolom Kanan: Tabel Data --}}
    <div class="table-container">
        {{-- Bungkus table dengan div responsive --}}
        <div class="table-responsive-wrapper">
            <table style="width: 100%; min-width: 500px;"> {{-- min-width memastikan tabel tidak gepeng --}}
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tahun_ajaran as $ta)
                    <tr>
                        <td style="font-weight: bold;">{{ $ta->tahun }}</td>
                        <td>
                            @if($ta->is_active)
                                <span class="badge badge-success">AKTIF</span>
                            @else
                                <span class="badge badge-secondary">NON-AKTIF</span>
                            @endif
                        </td>
                        <td>
                            @if(!$ta->is_active)
                            <form action="{{ route('tahun-ajaran.activate', $ta->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-sm btn-primary" title="Aktifkan"><i class="fa fa-check"></i></button>
                            </form>
                            
                            <form action="{{ route('tahun-ajaran.destroy', $ta->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus data ini?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection