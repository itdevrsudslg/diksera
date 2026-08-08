@extends('layouts.app')

@section('title', 'Riwayat Penilaian')

@push('styles')
    <style>
        :root {
            --primary-blue: #2563eb;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
            --blue-soft: #eff6ff;
            --blue-border: #dbeafe;
        }
.btn-rollback { color: #b45309; background: #fef3c7; border: 1px solid #fde68a; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; transition: 0.2s; }
.btn-rollback:hover { background: #d97706; color: white; border-color: #d97706; }
        body { background-color: var(--bg-light); color: var(--text-dark); font-family: 'Inter', sans-serif; }
        .page-header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: var(--text-dark); margin: 0; }
        .card-custom { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); overflow: hidden; }
        .table-custom th { background-color: #f8fafc; color: var(--text-gray); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; }
        .table-custom td { padding: 16px 24px; vertical-align: middle; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; }
        .badge-status { padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
        .bg-pass { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .bg-fail { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .btn-detail { color: var(--primary-blue); background: var(--blue-soft); border: 1px solid var(--blue-border); padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; transition: 0.2s; }
        .btn-detail:hover { background: var(--primary-blue); color: white; }
/* Style untuk list kompetensi agar lebih modern */
.competency-list {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px;
    max-height: 300px;
    overflow-y: auto;
}
.competency-item-card {
    background: #ffffff;
    border: 1px solid #edf2f7;
    border-left: 4px solid #10b981; /* Garis aksen hijau di kiri */
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.competency-header {
    display: flex;
    align-items: center;
    gap: 8px;
}
.competency-header i {
    font-size: 1.1rem;
}
.competency-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-dark);
}
.competency-badges {
    display: flex;
    gap: 6px;
    padding-left: 24px; /* Menyelaraskan posisi badge agar sejajar dengan teks */
}
.badge-custom {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 50px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-jenis {
    background-color: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}
.badge-kewenangan {
    background-color: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}
        /* Modal */
        .modal-content { border-radius: 20px; border: none; }
        .info-card { background: #f8fafc; border-radius: 12px; padding: 15px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.85rem; }
        .info-row span:last-child { font-weight: 700; color: var(--text-dark); }
        .competency-list { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; max-height: 250px; overflow-y: auto; }
        .competency-item { display: flex; align-items: center; font-size: 0.85rem; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .competency-item i { color: #10b981; margin-right: 10px; font-size: 1.1rem; }
        .file-box { display: flex; align-items: center; justify-content: space-between; background: var(--blue-soft); border: 1px dashed var(--primary-blue); padding: 12px; border-radius: 10px; }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="page-header">
        <div>
            <h2 class="page-title">Riwayat Penilaian</h2>
            <p class="text-muted mb-0 small">Arsip hasil seleksi dan kredensialing (Read-Only).</p>
        </div>
        <a href="{{ route('pewawancara.dashboard') }}" class="btn btn-white border fw-bold shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- FILTER & PENCARIAN --}}
    <form action="{{ route('pewawancara.riwayat') }}" method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama peserta atau email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="metode" class="form-select">
                    <option value="">-- Semua Metode --</option>
                    <option value="interview_only" {{ request('metode') == 'interview_only' ? 'selected' : '' }}>Kredensialing</option>
                    <option value="pg_interview" {{ request('metode') == 'pg_interview' ? 'selected' : '' }}>Uji Skill</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="keputusan" class="form-select">
                    <option value="">-- Semua Keputusan --</option>
                    <option value="lulus" {{ request('keputusan') == 'lulus' ? 'selected' : '' }}>Lulus / Valid</option>
                    <option value="tidak_lulus" {{ request('keputusan') == 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i></button>
            </div>
        </div>
    </form>

    <div class="card card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Nama Peserta</th>
                            <th width="20%">Metode</th>
                            <th width="20%">Tanggal</th>
                            <th width="15%">Keputusan</th>
                            <th width="10%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $index => $jadwal)
                            @php 
                                $isKredensial = ($jadwal->pengajuan->metode ?? '') === 'interview_only';
                                $penilaian = $jadwal->penilaian;
                            @endphp
                            <tr>
                                <td>{{ $riwayat->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold">{{ $jadwal->pengajuan->user->name ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $jadwal->pengajuan->user->email ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $isKredensial ? 'bg-secondary' : 'bg-primary' }}">
                                        {{ $isKredensial ? 'Kredensialing' : 'Uji Skill' }}
                                    </span>
                                </td>
                                <td>{{ $jadwal->waktu_wawancara ? $jadwal->waktu_wawancara->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($penilaian)
                                        @php $pass = in_array($penilaian->keputusan, ['lulus', 'valid']); @endphp
                                        <span class="badge-status {{ $pass ? 'bg-pass' : 'bg-fail' }}">
                                            <i class="bi {{ $pass ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                            {{ strtoupper($penilaian->keputusan) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            <td class="text-end">
    <div class="d-flex justify-content-end gap-2">
        {{-- Tombol Lihat Nilai --}}
        <button class="btn-detail" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $jadwal->id }}">
            <i class="bi bi-eye"></i> Lihat
        </button>

        {{-- Tombol Batal Selesai (Rollback) --}}
        <form action="{{ route('pewawancara.penilaian.batal_selesai', $jadwal->id) }}" method="POST" 
              onsubmit="return confirm('Yakin ingin membatalkan status Selesai?\nPeserta ini akan dikembalikan ke daftar Antrian dan sertifikat yang terlanjur terbit akan ditarik kembali.');">
            @csrf
            <button type="submit" class="btn-rollback" title="Kembalikan ke Antrian">
                <i class="bi bi-arrow-counterclockwise"></i> Batal
            </button>
        </form>
    </div>
</td>
                            </tr>

                            {{-- MODAL DETAIL --}}
                            <div class="modal fade" id="modalDetail{{ $jadwal->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">Detail Penilaian (Read Only)</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($penilaian)
                                                <div class="info-card">
                                                    <div class="info-row"><span>Peserta</span><span>{{ $jadwal->pengajuan->user->name }}</span></div>
                                                    <div class="info-row"><span>Metode</span><span>{{ $isKredensial ? 'Kredensialing' : 'Uji Skill' }}</span></div>
                                                </div>

                                                @if($isKredensial)
                                                   <label class="small text-muted fw-bold mb-2 text-uppercase">Kompetensi yang Disetujui:</label>
<div class="competency-list mb-3">
    @php 
        // 1. Decode data detail_penilaian menjadi array PHP
        $poinRaw = is_string($penilaian->detail_penilaian) ? json_decode($penilaian->detail_penilaian, true) : $penilaian->detail_penilaian; 
        
        // 2. Ambil data kompetensi di dalam 'rkk' jika ada, jika tidak gunakan array utama
        $poin = [];
        if (isset($poinRaw['rkk']) && is_array($poinRaw['rkk'])) {
            $poin = $poinRaw['rkk'];
        } elseif (is_array($poinRaw)) {
            $poin = $poinRaw;
        }
    @endphp
    
    @forelse($poin as $id => $data)
        @php
            // 3. Ambil nilai jenis dan kewenangan dari masing-masing ID kompetensi
            $jenis = is_array($data) ? ($data['jenis'] ?? '-') : '-';
            $kewenangan = is_array($data) ? ($data['kewenangan'] ?? '-') : '-';
        @endphp
        
        <div class="competency-item-card">
            <div class="competency-header">
                <i class="bi bi-check-circle-fill text-success"></i>
                <span class="competency-title">Poin Kompetensi Keperawatan #{{ $id }}</span>
            </div>
            <div class="competency-badges">
                <span class="badge-custom badge-jenis">
                    <i class="bi bi-person-workspace"></i> Jenis: {{ ucfirst($jenis) }}
                </span>
                <span class="badge-custom badge-kewenangan">
                    <i class="bi bi-shield-check"></i> Kewenangan: {{ ucfirst($kewenangan) }}
                </span>
            </div>
        </div>
    @empty
        <div class="text-center text-muted small py-4">Tidak ada poin yang dicentang.</div>
    @endforelse
</div>
                                                @else
                                                    {{-- Tambahan Tampilan Uji Skill --}}
                                                    <label class="small text-muted fw-bold mb-2 text-uppercase">Rincian Uji Skill:</label>
                                                    <div class="info-card bg-white border">
                                                        @php
                                                            $skorLisan = 0;
                                                            $skorSkill = 0;
                                                            $nilaiPert = is_string($penilaian->nilai_pertanyaan) ? json_decode($penilaian->nilai_pertanyaan, true) : $penilaian->nilai_pertanyaan;
                                                            $ujiSkillData = is_string($penilaian->uji_skill) ? json_decode($penilaian->uji_skill, true) : $penilaian->uji_skill;
                                                            
                                                            if(is_array($nilaiPert)) $skorLisan = array_sum(array_column($nilaiPert, 'skor'));
                                                            if(is_array($ujiSkillData) && count($ujiSkillData) > 0) $skorSkill = array_sum(array_column($ujiSkillData, 'skor')) / count($ujiSkillData);
                                                        @endphp
                                                        <div class="info-row"><span>Total Skor Lisan (Min 75)</span><span class="text-primary">{{ $skorLisan }}</span></div>
                                                        <div class="info-row border-top pt-2 mt-2"><span>Rata-Rata Skor Praktek (Min 90)</span><span class="text-info">{{ number_format($skorSkill, 2) }}</span></div>
                                                    </div>
                                                @endif

                                                <label class="small text-muted fw-bold mb-1 text-uppercase">Catatan:</label>
                                                <div class="p-3 bg-light rounded border small italic mb-4">
                                                    "{{ $penilaian->catatan_pewawancara ?? 'Tidak ada catatan.' }}"
                                                </div>

                                                <div class="text-center pt-3 border-top">
                                                    <div class="small text-muted">Keputusan Akhir:</div>
                                                    <h4 class="fw-bold {{ in_array($penilaian->keputusan, ['lulus', 'valid']) ? 'text-success' : 'text-danger' }}">
                                                        {{ strtoupper($penilaian->keputusan) }}
                                                    </h4>
                                                </div>
                                            @else
                                                <div class="text-center py-4">Data penilaian belum diisi/tersedia.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="6" class="text-center py-5">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination jika diperlukan --}}
        @if (method_exists($riwayat, 'hasPages') && $riwayat->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                {{ $riwayat->withQueryString()->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection