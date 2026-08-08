@extends('layouts.app')

@section('title', 'Data Uji Kompetensi')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .content-card { background: #ffffff; border-radius: 16px; border: 1px solid var(--border-soft, #e2e8f0); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02); padding: 24px; }
        .bulk-action-bar { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(150%); background: #1e293b; color: white; padding: 10px 20px; border-radius: 50px; box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25); z-index: 1050; display: flex; align-items: center; gap: 15px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); max-width: 95%; width: max-content; }
        .bulk-action-bar.active { transform: translateX(-50%) translateY(0); }
        .bulk-separator { width: 1px; height: 20px; background: #475569; margin: 0 5px; }
        .search-input { border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px; padding-left: 12px; height: 40px; transition: all 0.2s; }
        .search-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .table-custom th { background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding: 14px 16px; vertical-align: middle; }
        .table-custom td { vertical-align: middle; padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #334155; }
        .avatar-initial { width: 38px; height: 38px; background: #e0f2fe; color: #0ea5e9; font-weight: 700; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; border: 1px solid #bae6fd; }
        .badge-soft { padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd;}
        .badge-blue { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .btn-icon { width: 32px; height: 32px; padding: 0; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; border: 1px solid transparent; }
        .btn-icon:hover { transform: translateY(-2px); }
        .btn-icon-primary { color: #0ea5e9; background: #e0f2fe; }
        .btn-icon-primary:hover { background: #0ea5e9; color: #fff; }
        .btn-icon-danger { color: #ef4444; background: #fef2f2; }
        .btn-icon-danger:hover { background: #ef4444; color: #fff; }
        .btn-icon-dark { color: #475569; background: #f1f5f9; }
        .btn-icon-dark:hover { background: #1e293b; color: #fff; }
        .w-fit { width: fit-content; }
        .pagination { display: flex; justify-content: flex-end; gap: 6px; margin-bottom: 0; flex-wrap: wrap; }
        .page-item .page-link { border-radius: 8px !important; border: 1px solid var(--border-soft, #e2e8f0); color: #475569; padding: 8px 14px; font-size: 13px; font-weight: 500; transition: all 0.2s ease-in-out; background-color: #ffffff; display: flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; }
        .page-item:not(.active):not(.disabled) .page-link:hover { background-color: #e0f2fe; color: #0ea5e9; border-color: #bae6fd; transform: translateY(-2px); }
        .page-item.active .page-link { background-color: #0ea5e9; border-color: #0ea5e9; color: #ffffff; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3); z-index: 1; }
        .page-item.disabled .page-link { color: #94a3b8; background-color: #f8fafc; border-color: #e2e8f0; cursor: not-allowed; opacity: 0.7; }
        .page-link svg { width: 14px; height: 14px; }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Data Uji Kompetensi</h4>
            <p class="text-muted small mb-0">Monitor kompetensi dan masa berlaku untuk Sertifikat Kompetensi (UKOM).</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm px-3" style="border-radius: 8px;"><i class="bi bi-arrow-left"></i> Kembali</a>
            <a href="{{ route('admin.lisensi_pg_interview.create') }}" class="btn btn-info text-white px-3 shadow-sm" style="border-radius: 8px; background-color: #0ea5e9; border:none;"><i class="bi bi-plus-lg me-1"></i> Tambah Lisensi</a>
        </div>
    </div>

    <div class="content-card">
        <form action="{{ route('admin.lisensi_pg_interview.index') }}" method="GET" class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <div class="d-flex gap-2 w-100 w-md-auto">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input" placeholder="Cari nama, nomor, atau bidang..." style="min-width: 300px;">
                <button class="btn btn-light border" type="submit"><i class="bi bi-search"></i></button>
            </div>
            <div class="d-flex gap-2 w-100 w-md-auto">
                <select name="status" class="form-select form-select-sm" style="border-radius: 8px; width: 150px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center"><input type="checkbox" id="checkAll" class="form-check-input" style="cursor: pointer;"></th>
                        <th width="5%" class="text-center">No</th>
                        <th>Pemilik Lisensi</th>
                        <th width="25%">Detail & Kompetensi</th>
                        <th width="20%">Pelaksanaan</th>
                        <th>Masa Berlaku</th>
                        <th class="text-center">File</th>
                        <th class="text-center" width="120">Aksi</th>
                    </tr>
                </thead>
               <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="ids[]" value="{{ $item->lisensi_lama_id }}" class="form-check-input check-item" style="cursor: pointer;">
                            </td>
                            <td class="text-center text-muted">{{ $loop->iteration + $data->firstItem() - 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        $name = $item->user->name ?? 'Unknown';
                                        $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                                    @endphp
                                    <div class="avatar-initial">{{ $initials }}</div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $name }}</div>
                                        <div class="text-muted small" style="font-size: 11px;">{{ $item->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $item->lisensiLama->nama ?? 'Sertifikat Kompetensi' }}</div>
                                <div class="d-flex flex-column gap-1">
                                    <span class="text-muted small"><i class="bi bi-building me-1"></i> {{ $item->lisensiLama->lembaga ?? 'Komite Keperawatan' }}</span>
                                    <span class="text-info small fw-bold" style="font-size: 11px; color: #0ea5e9 !important;">No: {{ $item->lisensiLama->nomor ?? '-' }}</span>
                                    <div class="mt-1">
                                        <div class="text-dark small fw-semibold mb-1" style="font-size: 11px;"><i class="bi bi-briefcase me-1"></i> {{ $item->lisensiLama->bidang ?? 'Keperawatan' }}</div>
                                        <div class="d-flex flex-wrap gap-1">
                                            @php
                                                $kfks = $item->kfk;
                                                if (is_string($kfks)) {
                                                    $kfks = json_decode($kfks, true);
                                                    if (!is_array($kfks)) $kfks = [$item->kfk];
                                                }
                                                if (empty($kfks)) $kfks = [];
                                            @endphp
                                            @foreach ($kfks as $pk)
                                                <span class="badge-soft badge-blue" style="padding: 2px 6px; font-size: 10px;">{{ $pk }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="mb-2">
                                    <span class="badge-soft badge-info"><i class="bi bi-mic"></i> Uji Kompetensi + Uji Skill</span>
                                </div>
                                <div class="text-muted small d-flex flex-column" style="font-size: 11px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar3 text-secondary"></i>
                                        @php
                                            $tglAwal = $item->lisensiLama ? \Carbon\Carbon::parse($item->lisensiLama->tgl_mulai)->format('d M') : \Carbon\Carbon::parse($item->tgl_wawancara)->subDays(1)->format('d M');
                                            $tglAkhir = $item->lisensiLama ? \Carbon\Carbon::parse($item->lisensiLama->tgl_diselenggarakan)->format('d M Y') : \Carbon\Carbon::parse($item->tgl_wawancara)->format('d M Y');
                                        @endphp
                                        <span>{{ $tglAwal }} s/d {{ $tglAkhir }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $expired = \Carbon\Carbon::parse($item->tgl_akhir_berlaku);
                                    $today = \Carbon\Carbon::now();
                                    $diff = $today->diffInDays($expired, false);
                                @endphp
                                <div class="d-flex flex-column gap-1">
                                    @if ($diff < 0)
                                        <span class="badge-soft badge-danger w-fit"><i class="bi bi-x-circle"></i> Expired</span>
                                    @elseif($diff < 90)
                                        <span class="badge-soft badge-warning w-fit"><i class="bi bi-exclamation-triangle"></i> Segera Habis</span>
                                    @else
                                        <span class="badge-soft badge-active w-fit"><i class="bi bi-check-circle"></i> Aktif</span>
                                    @endif
                                    <div class="text-muted small mt-1" style="font-size: 11px; white-space: nowrap;"><i class="bi bi-calendar-check me-1"></i> {{ \Carbon\Carbon::parse($item->tgl_mulai_berlaku)->format('d M Y') }} - {{ $expired->format('d M Y') }}</div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if ($item->lisensiLama && $item->lisensiLama->file_path)
                                    <a href="{{ Storage::url($item->lisensiLama->file_path) }}" target="_blank" class="btn-icon btn-icon-dark" data-bs-toggle="tooltip" title="Lihat Dokumen"><i class="bi bi-file-earmark-pdf"></i></a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.pengajuan.cetak_serkom', $item->id) }}" target="_blank" class="btn-icon text-white" style="background-color: #10b981;" data-bs-toggle="tooltip" title="Cetak SERKOM">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    @if($item->lisensi_lama_id)
                                        <a href="{{ route('admin.lisensi_pg_interview.edit', $item->lisensi_lama_id) }}" class="btn-icon btn-icon-primary" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('admin.lisensi_pg_interview.destroy', $item->lisensi_lama_id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-icon-danger" data-bs-toggle="tooltip" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="bi bi-card-checklist display-6 opacity-25"></i></div>
                                <span class="text-muted small">Belum ada data lisensi Uji Kompetensi + Uji Skill ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <div class="text-muted small">Menampilkan {{ $data->firstItem() ?? 0 }} sampai {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} data</div>
            <div>{{ $data->withQueryString()->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>

    {{-- BULK ACTIONS BAR --}}
    <div class="bulk-action-bar" id="bulkActionBar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-dark rounded-pill px-2 py-1" id="selectedCountBadge">0</span>
            <span class="small fw-bold">Terpilih</span>
        </div>
        <div class="bulk-separator"></div>
        <div class="d-flex gap-2">
            <form id="formBulkDelete" action="{{ route('admin.lisensi_pg_interview.bulk_destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div id="bulkDeleteInputs"></div>
                <button type="button" class="btn btn-danger btn-sm fw-bold rounded-pill px-3" onclick="submitBulkDelete()"><i class="bi bi-trash me-1"></i> Hapus Terpilih</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkAll = document.getElementById('checkAll');
            const checkItems = document.querySelectorAll('.check-item');
            const bulkActionBar = document.getElementById('bulkActionBar');
            const selectedCountBadge = document.getElementById('selectedCountBadge');

            function updateBulkBar() {
                const count = document.querySelectorAll('.check-item:checked').length;
                selectedCountBadge.innerText = count;
                bulkActionBar.classList.toggle('active', count > 0);
            }

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checkItems.forEach(item => { item.checked = this.checked; });
                    updateBulkBar();
                });
            }
            checkItems.forEach(item => item.addEventListener('change', updateBulkBar));

            window.submitBulkDelete = function() {
                Swal.fire({ title: 'Hapus Lisensi Terpilih?', text: "Data lisensi yang dicentang akan dihapus secara permanen.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus Semua!', cancelButtonText: 'Batal' }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('formBulkDelete');
                        const container = document.getElementById('bulkDeleteInputs');
                        container.innerHTML = ''; 
                        document.querySelectorAll('.check-item:checked').forEach(item => {
                            const input = document.createElement('input');
                            input.type = 'hidden'; 
                            input.name = 'ids[]'; 
                            input.value = item.value;
                            container.appendChild(input);
                        });
                        form.submit();
                    }
                });
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });

            @if (session('swal'))
                var swalData = {!! json_encode(session('swal')) !!};
                Swal.fire({ icon: swalData.icon, title: swalData.title, text: swalData.text, showConfirmButton: false, timer: 2000 });
            @endif

            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({ title: 'Hapus Lisensi?', text: "Data lisensi dan pengajuan terkait ini akan dihapus permanen.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' }).then((result) => {
                        if (result.isConfirmed) { this.submit(); }
                    });
                });
            });
        });
    </script>
@endpush