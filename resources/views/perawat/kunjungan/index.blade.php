@extends('layouts.app')
@section('title', 'Kunjungan Luar Negeri – DIKSERA')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* Paste CSS dari file index.blade.php (seperti di Tanda Jasa Index) ke sini */
    :root { --primary-blue: #2563eb; --primary-hover: #1d4ed8; --text-dark: #0f172a; --text-gray: #64748b; --bg-light: #f1f5f9; }
    body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: var(--text-dark); }
    .page-header { margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; }
    .page-title { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0; letter-spacing: -0.5px; }
    .page-subtitle { color: var(--text-gray); font-size: 0.9rem; margin-top: 4px; }
    .btn-blue { background-color: var(--primary-blue); color: white; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; border: 1px solid var(--primary-blue); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .btn-white { background: white; color: var(--text-gray); border: 1px solid #e2e8f0; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .table-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden; }
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table thead th { background-color: #f1f5f9; color: #475569; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    .custom-table tbody tr { border-bottom: 1px solid #f1f5f9; }
    .custom-table td { padding: 20px 24px; vertical-align: middle; font-size: 0.95rem; color: var(--text-gray); }
    .data-title { font-weight: 600; color: var(--text-dark); display: block; margin-bottom: 4px; font-size: 1rem; }
    .data-sub { font-size: 0.85rem; color: var(--text-gray); }
    .action-btn { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; background: transparent; border: 1px solid transparent; }
    .action-btn:hover { background: #dbeafe; color: var(--primary-blue); }
    .action-btn.delete:hover { background: #fee2e2; color: #ef4444; }
    .alert-blue { background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; border-radius: 8px; padding: 12px 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="page-header">
        <div>
            <h1 class="page-title">Kunjungan ke Luar Negeri</h1>
            <p class="page-subtitle">Daftar pengalaman kunjungan dinas/belajar ke luar negeri.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('perawat.drh') }}" class="btn-white"><i class="bi bi-arrow-left"></i> Kembali</a>
            <a href="{{ route('perawat.kunjungan.create') }}" class="btn-blue"><i class="bi bi-plus-lg"></i> Tambah Kunjungan</a>
        </div>
    </div>

    @if(session('swal'))
        <div class="alert-blue"><i class="bi bi-check-circle-fill"></i> {{ session('swal')['text'] }}</div>
    @endif

    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="20%">Negara</th>
                        <th width="35%">Tujuan Kunjungan</th>
                        <th width="15%">Lamanya</th>
                        <th width="20%">Yang Membiayai</th>
                        <th width="10%" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kunjungan as $row)
                        <tr>
                            <td><span class="data-title">{{ $row->negara }}</span></td>
                            <td><span class="d-block text-dark mb-1">{{ $row->tujuan_kunjungan }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ $row->lamanya ?? '-' }}</span></td>
                            <td><span class="data-sub">{{ $row->yang_membiayai ?? '-' }}</span></td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('perawat.kunjungan.edit', $row->id) }}" class="action-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('perawat.kunjungan.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data kunjungan ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-airplane fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data kunjungan ke luar negeri.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection