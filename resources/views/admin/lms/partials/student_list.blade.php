<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th>Nama Siswa</th>
                <th>NIS/Login</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- PERHATIKAN: @forelse HARUS DI SINI (Di dalam tbody) --}}
            @forelse($students as $student)
                <tr>
                    <td style="font-weight: 700; color: #4b5563;">
                        {{ $student->name }}
                    </td>
                    <td class="text-muted">
                        {{ $student->email }}
                    </td>
                    <td class="text-center">
                        {{-- Tombol Detail --}}
                        <button class="btn btn-sm btn-info text-white rounded-pill px-3" 
                                onclick="showStudentDetail({{ $student->id }})">
                            <i class="fas fa-history mr-1"></i> Profiling
                        </button>
                    </td>
                </tr>
            @empty
                {{-- Bagian @empty juga harus menghasilkan <tr> --}}
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                        <i class="fas fa-user-slash mb-2"></i><br>
                        Siswa tidak ditemukan.
                    </td>
                </tr>
            @endforelse
            {{-- Tutup @endforelse SEBELUM tutup </tbody> --}}
        </tbody>
    </table>
</div>

{{-- Pagination Links --}}
<div class="d-flex justify-content-center mt-3 custom-pagination">
    {!! $students->links('pagination::bootstrap-4') !!}
</div>