@extends('layouts.app')

@section('title', 'Arsip Dokumen')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
        }

        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: var(--text-dark); }
        .page-header { margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; }
        .page-title { font-size: 1.5rem; font-weight: 700; margin: 0; letter-spacing: -0.5px; }
        .page-subtitle { color: var(--text-gray); font-size: 0.9rem; margin-top: 4px; }
        
        .btn-blue { background-color: var(--primary-blue); color: white; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--primary-blue); transition: 0.2s; }
        .btn-blue:hover { background-color: var(--primary-hover); transform: translateY(-2px); color: white; }
        .btn-white { background: white; color: var(--text-gray); border: 1px solid #e2e8f0; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; }
        .btn-white:hover { background-color: #f8fafc; color: var(--text-dark); border-color: #cbd5e1; }
        
        .table-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02); overflow: hidden; }
        .custom-table { width: 100%; border-collapse: collapse; }
        .custom-table thead th { background-color: #f1f5f9; color: #475569; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .custom-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: 0.2s; }
        .custom-table tbody tr:hover { background-color: #eff6ff; }
        .custom-table td { padding: 20px 24px; vertical-align: middle; font-size: 0.95rem; color: var(--text-gray); }
        
        .action-btn { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; background: transparent; border: 1px solid transparent; transition: 0.2s; text-decoration: none; }
        .action-btn:hover { background: #dbeafe; color: var(--primary-blue); }
        .action-btn.delete:hover { background: #fee2e2; color: #ef4444; }

        .list-dokumen { padding-left: 20px; margin-bottom: 0; font-size: 0.85rem; }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="page-header">
            <div>
                <h1 class="page-title">Arsip Dokumen</h1>
                <p class="page-subtitle">Kelola penyimpanan link dokumen eksternal / Google Drive.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('perawat.drh') }}" class="btn-white"><i class="bi bi-arrow-left"></i> Kembali</a>
                <a href="{{ route('perawat.arsip.create') }}" class="btn-blue"><i class="bi bi-plus-lg"></i> Tambah Arsip</a>
            </div>
        </div>

        @if (session('swal'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "{{ session('swal')['icon'] }}",
                        title: "{{ session('swal')['title'] }}",
                        text: "{{ session('swal')['text'] }}",
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif

        <div class="table-card">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Tahun</th>
                            <th width="40%">Isian Dokumen</th>
                            <th width="30%">Link Drive</th>
                            <th width="15%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="text-dark fw-medium">{{ $item->tahun }}</span></td>
                                <td>
                                    @if(is_array($item->isian_dokumen) && count($item->isian_dokumen) > 0)
                                        <ul class="list-dokumen">
                                            @foreach($item->isian_dokumen as $dok)
                                                <li>{{ $dok }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ $item->link_drive }}" target="_blank" class="btn-white btn-sm" style="font-size: 0.8rem; padding: 6px 12px;">
                                        <i class="bi bi-link-45deg"></i> Buka Link
                                    </a>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('perawat.arsip.edit', $item->id) }}" class="action-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('perawat.arsip.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus arsip ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted" style="opacity: 0.6;">
                                        <i class="bi bi-folder-x fs-1 d-block mb-2"></i> Belum ada data Arsip Dokumen.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection