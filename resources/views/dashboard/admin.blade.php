@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- TOP STATS CARDS --}}
    <div class="row g-4 mb-4">
        {{-- Card 1 --}}
        <div class="col-xl-3 col-sm-6">
            <div class="dash-card p-4 h-100 shadow-sm border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1">Total Perawat</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_perawat'] }}</h3>
                        <small class="text-success fw-bold"><i class="bi bi-arrow-up"></i> Active Users</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card 2 --}}
        <div class="col-xl-3 col-sm-6">
            <div class="dash-card p-4 h-100 shadow-sm border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1">Pending Verif</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $stats['pending_verif'] }}</h3>
                        <small class="text-muted">Butuh Review</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-clipboard-data fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card 3 --}}
        <div class="col-xl-3 col-sm-6">
            <div class="dash-card p-4 h-100 shadow-sm border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1">Lulus Sertifikasi</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $stats['lulus_total'] }}</h3>
                        <small class="text-success">Verified</small>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-patch-check-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card 4 --}}
        <div class="col-xl-3 col-sm-6">
            <div class="dash-card p-4 h-100 shadow-sm border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase fw-bold text-muted small mb-1">STR Expired</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $stats['expired_str'] }}</h3>
                        <small class="text-danger">Non-Active</small>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                        <i class="bi bi-exclamation-octagon-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS SECTION --}}
    <div class="row g-4 mb-4">
        {{-- Chart Trend Bulanan --}}
        <div class="col-lg-8">
            <div class="dash-card p-4 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 text-dark">Tren Pengajuan Sertifikasi ({{ date('Y') }})</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">Filter</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Tahun Ini</a></li>
                            <li><a class="dropdown-item" href="#">Bulan Ini</a></li>
                        </ul>
                    </div>
                </div>
                <div style="height: 300px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Chart Demografi --}}
        <div class="col-lg-4">
            <div class="dash-card p-4 h-100 shadow-sm">
                <h6 class="fw-bold mb-4 text-dark">Demografi Perawat</h6>
                
                <ul class="nav nav-tabs mb-3" id="demoTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active py-1 px-2 small" id="gender-tab" data-bs-toggle="tab" data-bs-target="#gender" type="button">Gender</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-1 px-2 small" id="edu-tab" data-bs-toggle="tab" data-bs-target="#edu" type="button">Pendidikan</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="demoTabContent">
                    <div class="tab-pane fade show active" id="gender" role="tabpanel">
                        <div style="height: 200px; position: relative;">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <div class="text-center mt-3 small text-muted">
                            Total: {{ $stats['total_perawat'] }} Data
                        </div>
                    </div>
                    <div class="tab-pane fade" id="edu" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless small">
                                <thead>
                                    <tr class="text-muted border-bottom">
                                        <th>Jenjang</th>
                                        <th class="text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($eduStats as $jenjang => $count)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $jenjang }}</td>
                                        <td class="text-end">{{ $count }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLES SECTION --}}
    <div class="row g-4">
        {{-- Pengajuan Terbaru --}}
        <div class="col-lg-8">
            <div class="dash-card p-4 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Pengajuan Terbaru Masuk</h6>
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light small text-uppercase text-muted">
                            <tr>
                                <th class="ps-3 border-0 rounded-start">Perawat</th>
                                <th class="border-0">Tanggal</th>
                                <th class="border-0">Tipe</th>
                                <th class="border-0 text-center">Status</th>
                                {{-- <th class="pe-3 border-0 text-end rounded-end">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPengajuans as $p)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:12px;">
                                            {{ substr($p->user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-bold text-dark" style="font-size: 13px;">{{ $p->user->profile->nama_lengkap ?? $p->user->name }}</span>
                                            <span class="d-block text-muted" style="font-size: 11px;">{{ $p->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-muted">{{ $p->created_at->format('d M Y') }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $p->tipe_sertifikat }}</span></td>
                                <td class="text-center">
                                    @if($p->status == 'pending') <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($p->status == 'disetujui') <span class="badge bg-success">Approved</span>
                                    @else <span class="badge bg-danger">Rejected</span> @endif
                                </td>
                                {{-- <td class="text-end pe-3">
                                    <a href="#" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a>
                                </td> --}}
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Jadwal Interview --}}
        <div class="col-lg-4">
            <div class="dash-card p-4 h-100 shadow-sm bg-primary text-white" style="background: linear-gradient(135deg, #2563eb, #1e40af);">
                <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-25 pb-2">Jadwal Interview</h6>
                
                <div class="list-group list-group-flush">
                    @forelse($upcomingInterviews as $jadwal)
                        <div class="d-flex align-items-center mb-3 p-2 rounded bg-white bg-opacity-10">
                            <div class="bg-white text-primary rounded p-2 text-center me-3" style="min-width: 50px;">
                                <span class="d-block fw-bold h5 mb-0">{{ $jadwal->waktu_wawancara->format('d') }}</span>
                                <small class="d-block text-uppercase" style="font-size: 10px;">{{ $jadwal->waktu_wawancara->format('M') }}</small>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="mb-0 fw-bold text-truncate">{{ $jadwal->pengajuan->user->profile->nama_lengkap ?? 'User' }}</h6>
                                <small class="text-white-50"><i class="bi bi-clock me-1"></i> {{ $jadwal->waktu_wawancara->format('H:i') }} WIB</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-white-50">
                            <i class="bi bi-calendar-x fs-1 opacity-50"></i>
                            <p class="mt-2 small">Tidak ada jadwal interview.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
<!-- KOTAK PROGRESS JENJANG KLINIS -->
<div class="row mt-4">
    <div class="col-12">
        <div class="dash-card p-4 shadow-sm">
            <h6 class="fw-bold mb-4">Progress Kredensialing per Jenjang Klinis</h6>
            
            <div class="row g-4">
                @foreach($progressKlinis as $jenjang => $data)
                <div class="col-md-6">
                    <div class="border rounded-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-primary mb-0">{{ $data['nama_jenjang'] }}</h6>
                            <span class="badge {{ $data['persen'] == 100 ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $data['persen'] }}%
                            </span>
                        </div>
                        
                        <p class="small text-muted mb-2">
                            Total Pengajuan: <strong>{{ $data['total'] }}</strong> | 
                            Selesai: <strong>{{ $data['selesai'] }}</strong> | 
                            <a href="#" class="text-decoration-none text-danger" data-bs-toggle="modal" data-bs-target="#modalBelum-{{ str_replace(' ', '', $jenjang) }}">
                                Kurang: <strong>{{ count($data['list_belum']) }}</strong>
                            </a>
                        </p>

                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar {{ $data['persen'] == 100 ? 'bg-success' : 'bg-primary' }}" 
                                 role="progressbar" style="width: {{ $data['persen'] }}%;"></div>
                        </div>

                        <!-- TOMBOL UNDUH PAKSA SEMUA (MASSAL) -->
                        @if($data['selesai'] > 0)
                            <a href="{{ route('admin.dashboard.cetak_spk_massal', ['jenjang' => $jenjang]) }}" 
                               target="_blank" 
                               class="btn btn-sm {{ $data['persen'] == 100 ? 'btn-success' : 'btn-outline-primary' }} w-100 fw-bold rounded-pill">
                                <i class="bi bi-download me-1"></i> Unduh SPK Kolektif ({{ $data['selesai'] }} Nama)
                            </a>
                        @else
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 fw-bold rounded-pill" disabled>
                                <i class="bi bi-download me-1"></i> Unduh SPK Kolektif (Belum ada data)
                            </button>
                        @endif

                    </div>
                </div>

                <!-- MODAL LIST ORANG YANG BELUM SELESAI -->
                <div class="modal fade" id="modalBelum-{{ str_replace(' ', '', $jenjang) }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                            <div class="modal-header bg-light border-0">
                                <h6 class="modal-title fw-bold">Belum Selesai - {{ $jenjang }}</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <ul class="list-group list-group-flush">
                                    @forelse($data['list_belum'] as $belum)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 mb-2">
                                            <div>
                                                <h6 class="mb-0 fw-bold small">{{ $belum->user->profile->nama_lengkap ?? $belum->user->name }}</h6>
                                            </div>
                                            <span class="badge bg-secondary rounded-pill" style="font-size: 10px;">{{ str_replace('_', ' ', $belum->status) }}</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-center text-success fw-bold border-0">
                                            <i class="bi bi-check-circle-fill me-2"></i>Semua sudah selesai!
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>

@push('styles')
<style>
    .dash-card {
        background: white; border-radius: 12px; border: none;
    }
    .avatar { flex-shrink: 0; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. CHART TREND
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartTrend['labels']) !!},
            datasets: [
                {
                    label: 'Total Pengajuan',
                    data: {!! json_encode($chartTrend['total']) !!},
                    borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4, fill: true
                },
                {
                    label: 'Disetujui',
                    data: {!! json_encode($chartTrend['approved']) !!},
                    borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.0)',
                    tension: 0.4, borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, grid: { borderDash: [2, 2] } }, x: { grid: { display: false } } }
        }
    });

    // 2. CHART GENDER
    const ctxGender = document.getElementById('genderChart').getContext('2d');
    new Chart(ctxGender, {
        type: 'doughnut',
        data: {
            labels: ['Laki-laki', 'Perempuan'],
            datasets: [{
                data: [{{ $chartGender['Laki-laki'] }}, {{ $chartGender['Perempuan'] }}],
                backgroundColor: ['#3b82f6', '#ec4899'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'right', labels: { boxWidth: 10 } } }
        }
    });
</script>
@endpush
@endsection