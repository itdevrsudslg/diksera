@extends('layouts.app')
@section('title', 'Daftar Penilaian OPPE')

@push('styles')
<style>
    .dash-card {
        background: #ffffff; 
        border-radius: 16px; 
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01); 
        padding: 24px;
    }
    .table-custom { border-collapse: separate; border-spacing: 0 8px; }
    .table-custom thead th { 
        background-color: transparent; color: #64748b; font-weight: 600; 
        text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; 
        border-bottom: 2px solid #e2e8f0; padding-bottom: 12px;
    }
    .table-custom tbody tr { transition: all 0.2s ease; }
    .table-custom tbody tr:hover td { background-color: #f8fafc; }
    .table-custom td { vertical-align: middle; color: #334155; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
    .table-custom td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-custom td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    
    .badge-status { 
        padding: 6px 14px; border-radius: 50rem; font-weight: 600; font-size: 0.75rem; 
        letter-spacing: 0.3px; display: inline-flex; align-items: center; gap: 6px;
    }
    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-state-icon {
        width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; margin: 0 auto 20px auto; color: #94a3b8; font-size: 2.5rem;
    }
</style>
@endpush

@section('content')
<div class=" py-4">
    
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Data OPPE</h4>
            <p class="text-muted mb-0 small">Kelola dokumen evaluasi kinerja Anda dan rekan sejawat.</p>
        </div>
        @if(Auth::user()->role !== 'admin')
            <a href="{{ route('perawat.oppe.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i>Buat Pengajuan
            </a>
        @endif
    </div>

 <!-- FILTER & SEARCH -->
    <div class="dash-card mb-4 p-3 border-0 bg-white rounded-4 shadow-sm">
        <form action="{{ route('perawat.oppe.list_evaluasi') }}" method="GET">
            <div class="row g-2 align-items-center">
                
                <!-- 1. Kolom Pencarian (Dibuat Lebih Panjang) -->
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill px-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-start-0 rounded-end-pill py-2" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai / periode...">
                    </div>
                </div>
                
                <!-- 2. Kolom Status -->
                <div class="col-md-4">
                    <select class="form-select bg-light rounded-pill border-0 py-2" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="pending_self" {{ request('status') == 'pending_self' ? 'selected' : '' }}>Asesmen Diri</option>
                        <option value="pending_peer" {{ request('status') == 'pending_peer' ? 'selected' : '' }}>Menunggu Sejawat</option>
                        <option value="pending_karu" {{ request('status') == 'pending_karu' ? 'selected' : '' }}>Menunggu Karu</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                
                <!-- 3. Kolom Tombol (Penuhi Sisa Ruang) -->
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1 shadow-sm py-2">Terapkan</button>
                    
                    <!-- Tombol Reset hanya muncul jika filter aktif -->
                    @if(request('search') || request('status'))
                        <a href="{{ route('perawat.oppe.list_evaluasi') }}" class="btn btn-light rounded-pill flex-grow-1 text-danger shadow-sm py-2" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>
    <!-- DATA TABEL -->
    <div class="dash-card">
        @if($daftarEvaluasi->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Belum Ada Dokumen</h5>
                <p class="text-muted small mb-4">Saat ini tidak ada pengajuan OPPE yang menunggu tindakan atau cocok dengan pencarian Anda.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="25%">Pegawai Dinilai</th>
                            <th width="30%">Tim Penilai</th>
                            <th width="12%">Periode</th>
                            <th width="18%">Status Saat Ini</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarEvaluasi as $index => $oppe)
                        <tr>
                            <td class="text-center fw-medium text-muted">
                                {{ $daftarEvaluasi->firstItem() + $index }}
                            </td>
                            
                            <!-- KOLOM PEGAWAI DINILAI -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; font-weight: bold; flex-shrink: 0;">
                                        {{ substr($oppe->user->profile->nama_lengkap ?? $oppe->user->name, 0, 1) }}
                                    </div>
                                   <div>
                                        <div class="fw-bold text-dark">{{ $oppe->user->profile->nama_lengkap ?? $oppe->user->name }}</div>
                                        <div class="small text-muted">NIP/NIRP. {{ $oppe->user->profile->nip ?? $oppe->user->profile->nirp ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- KOLOM TIM PENILAI -->
                            <td>
                                <div class="small">
                                    <div class="mb-1">
                                        <span class="text-muted" style="font-size: 0.75rem;">Teman Sejawat:</span><br>
                                        <span class="fw-medium text-dark">
                                            <i class="bi bi-person-check text-warning me-1"></i> 
                                            {{ $oppe->peer->profile->nama_lengkap ?? $oppe->peer->name ?? '-' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-muted" style="font-size: 0.75rem;">Atasan / Karu:</span><br>
                                        <span class="fw-medium text-dark">
                                            <i class="bi bi-person-badge text-primary me-1"></i> 
                                            {{ $oppe->evaluator->profile->nama_lengkap ?? $oppe->evaluator->name ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- KOLOM PERIODE -->
                            <td>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-calendar-check me-2 text-primary"></i> 
                                    <span class="fw-medium">{{ $oppe->periode }}</span>
                                </div>
                            </td>

                            <!-- KOLOM STATUS -->
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending_self' => ['label' => 'Asesmen Diri', 'class' => 'bg-info bg-opacity-10 text-info border border-info'],
                                        'pending_peer' => ['label' => 'Menunggu Sejawat', 'class' => 'bg-warning bg-opacity-10 text-warning border border-warning'],
                                        'pending_karu' => ['label' => 'Menunggu Karu', 'class' => 'bg-primary bg-opacity-10 text-primary border border-primary'],
                                        'completed' => ['label' => 'Selesai', 'class' => 'bg-success bg-opacity-10 text-success border border-success']
                                    ];

                                    $currentStatus = $statusConfig[$oppe->status] ?? [
                                        'label' => $oppe->status, 
                                        'class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary'
                                    ];
                                @endphp

                                <span class="badge badge-status {{ $currentStatus['class'] }}">
                                    @if($oppe->status == 'pending_self') <i class="bi bi-person"></i>
                                    @elseif($oppe->status == 'pending_peer') <i class="bi bi-people"></i>
                                    @elseif($oppe->status == 'pending_karu') <i class="bi bi-person-badge"></i>
                                    @elseif($oppe->status == 'completed') <i class="bi bi-check2-circle"></i>
                                    @else <i class="bi bi-info-circle"></i> @endif
                                    
                                    {{ $currentStatus['label'] }}
                                </span>
                            </td>

                            <!-- KOLOM AKSI -->
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    
                                    <!-- JIKA USER ADALAH ADMIN -->
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('perawat.oppe.evaluate', $oppe->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-medium" title="Lihat Progress">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('perawat.oppe.cetak', $oppe->id) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-medium" title="Cetak Dokumen">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <form action="{{ route('perawat.oppe.destroy', $oppe->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengajuan OPPE ini secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm fw-medium" title="Hapus Dokumen">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    <!-- JIKA BUKAN ADMIN (USER BIASA) -->
                                    @else
                                        @if($oppe->status == 'completed')
                                            <a href="{{ route('perawat.oppe.evaluate', $oppe->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-medium" title="Lihat Hasil">
                                                <i class="bi bi-file-earmark-check"></i>
                                            </a>
                                            <a href="{{ route('perawat.oppe.cetak', $oppe->id) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-medium" title="Cetak Dokumen">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('perawat.oppe.evaluate', $oppe->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-medium">
                                                <i class="bi bi-pencil-square me-1"></i> Nilai
                                            </a>
                                        @endif

                                        <!-- Tombol Hapus (Hanya muncul jika milik sendiri) -->
                                        @if($oppe->user_id == Auth::id())
                                            <form action="{{ route('perawat.oppe.destroy', $oppe->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan OPPE ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm fw-medium" title="Hapus Pengajuan">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- PAGINASI BAWAAN LARAVEL -->
            <div class="d-flex justify-content-end mt-4">
                {{ $daftarEvaluasi->links('pagination::bootstrap-4') }}
            </div>
            
        @endif
    </div>
    
</div>
@endsection