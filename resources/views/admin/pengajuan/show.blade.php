@extends('layouts.app')

@section('title', 'Detail Verifikasi - ' . $pengajuan->user->name)

@push('styles')
    <style>
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: white;
            transition: transform 0.2s;
        }

        .header-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .info-group {
            padding: 12px;
            background-color: #f8fafc;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.2s;
        }

        .file-item:hover {
            border-color: #cbd5e1;
            background-color: #f8fafc;
        }

        .status-card {
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            background: #f0f9ff;
            border: 1px dashed #bae6fd;
            color: #0369a1;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">

    {{-- HEADER NAVIGATION --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Detail Verifikasi Berkas</h1>
            <p class="text-muted small mb-0">Verifikasi dokumen pengajuan dan tentukan metode evaluasi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.perawat.timeline', $pengajuan->user_id) }}" class="btn btn-outline-primary px-4 rounded-pill">
                <i class="bi bi-clock-history me-2"></i>Riwayat Peserta
            </a>
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- KOLOM KIRI: INFO USER & BERKAS --}}
        <div class="col-lg-8">

            {{-- 1. KARTU PROFIL USER --}}
            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="bi bi-person-bounding-box fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-dark mb-1">{{ $pengajuan->user->name }}</h5>
                       <h5 class="fw-bold text-dark mb-1">
    {{ is_array($pengajuan->kfk) ? implode(', ', $pengajuan->kfk) : $pengajuan->kfk }}
</h5>
                            <div class="text-muted small mb-2"><i class="bi bi-envelope me-1"></i>
                                {{ $pengajuan->user->email }}</div>

                            <div class="text-muted small">
                                <i class="bi bi-building me-1"></i> Unit Kerja:
                                {{ $pengajuan->user->unit_kerja ?? 'RSUD Simpang Lima Gumul' }}
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="header-label">JENIS PENGAJUAN</span>
                            @if ($pengajuan->jenis_pengajuan == 'baru')
                                <span class="badge bg-info text-dark px-3 py-2 rounded-pill"><i
                                        class="bi bi-stars me-1"></i> BARU</span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i
                                        class="bi bi-arrow-repeat me-1"></i> PERPANJANGAN</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. KARTU DOKUMEN --}}
            <div class="card card-custom mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold m-0"><i class="bi bi-folder2-open me-2 text-primary"></i>Kelengkapan Dokumen</h6>
                </div>
                <div class="card-body pt-0">
                    {{-- LINK GDRIVE --}}
                    <div class="file-item">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success p-2 rounded me-3">
                                <i class="bi bi-google fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Google Drive</div>
                                <div class="small text-muted">Berkas pendukung lengkap</div>
                            </div>
                        </div>
                        @if ($pengajuan->link_gdrive)
                            <a href="{{ $pengajuan->link_gdrive }}" target="_blank"
                                class="btn btn-sm btn-outline-success fw-bold">
                                Buka Link <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </a>
                        @else
                            <span class="badge bg-secondary">Tidak Ada</span>
                        @endif
                    </div>

                    @if ($pengajuan->jenis_pengajuan == 'baru')
                        <div class="file-item">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded me-3">
                                    <i class="bi bi-file-earmark-pdf fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Dokumen Pengajuan</div>
                                    <div class="small text-muted">Format PDF</div>
                                </div>
                            </div>
                            @if ($pengajuan->file_dokumen_baru)
                                <a href="{{ asset('storage/' . $pengajuan->file_dokumen_baru) }}" target="_blank"
                                    class="btn btn-sm btn-outline-danger fw-bold">
                                    <i class="bi bi-download me-1"></i> Unduh
                                </a>
                            @else
                                <span class="badge bg-secondary">Belum Upload</span>
                            @endif
                        </div>
                    @else
                        <div class="file-item">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded me-3">
                                    <i class="bi bi-award fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Sertifikat Lama</div>
                                    <div class="small text-muted">Bukti lisensi sebelumnya</div>
                                </div>
                            </div>
                            @if ($pengajuan->file_sertifikat_lama)
                                <a href="{{ asset('storage/' . $pengajuan->file_sertifikat_lama) }}" target="_blank"
                                    class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-eye me-1"></i> Lihat
                                </a>
                            @else
                                <span class="badge bg-secondary">Belum Upload</span>
                            @endif
                        </div>

                        <div class="file-item">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded me-3">
                                    <i class="bi bi-envelope-paper-heart fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Surat Rekomendasi</div>
                                    <div class="small text-muted">Dari Karu/Atasan</div>
                                </div>
                            </div>
                            @if ($pengajuan->file_surat_rekomendasi)
                                <a href="{{ asset('storage/' . $pengajuan->file_surat_rekomendasi) }}" target="_blank"
                                    class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-eye me-1"></i> Lihat
                                </a>
                            @else
                                <span class="badge bg-secondary">Belum Upload</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. KARTU HASIL EVALUASI (BARU: HASIL UJIAN & WAWANCARA) --}}
            <div class="card card-custom">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold m-0"><i class="bi bi-clipboard-check me-2 text-primary"></i>Hasil Evaluasi Peserta</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        {{-- HASIL UJIAN TULIS --}}
                        <div class="col-md-6">
                            <div class="info-group">
                                <span class="header-label">Hasil Ujian Tulis (CBT)</span>
                                @php $exam = $pengajuan->user->examResult; @endphp
                                @if ($exam)
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h3 class="fw-bold mb-0 {{ $exam->total_nilai >= 75 ? 'text-success' : 'text-danger' }}">
                                                {{ $exam->total_nilai }}
                                            </h3>
                                            <small class="text-muted">Skor minimal Lulus: 75</small>
                                        </div>
                                        <div class="text-end">
                                            {{--<span class="badge {{ $exam->lulus ? 'bg-success' : 'bg-warning' }} rounded-pill">--}}
                                            {{--    {{ $exam->lulus ? 'Disetujui Admin' : 'Menunggu Validasi' }}--}}
                                            {{--</span>--}}
                                        </div>
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        Benar: {{ $exam->total_benar }} | Salah: {{ $exam->total_salah }}
                                    </div>
                                @else
                                    <div class="text-muted italic py-2">Belum ada data ujian tulis.</div>
                                @endif
                            </div>
                        </div>

                        {{-- HASIL WAWANCARA & SKILL --}}
                        <div class="col-md-6">
                            <div class="info-group">
                                <span class="header-label">Hasil Uji Skill & Wawancara</span>
                                @php $jadwal = $pengajuan->jadwalWawancara; @endphp
                                @if ($jadwal && $jadwal->penilaian)
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h3 class="fw-bold mb-0 text-primary">
                                                {{ $jadwal->penilaian->nilai_wawancara }}
                                            </h3>
                                            <small class="text-muted">Oleh: {{ $jadwal->pewawancara->nama ?? 'Pewawancara' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info text-dark rounded-pill">Status: {{ ucfirst($jadwal->status) }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-2 small text-dark fw-bold">
                                        Catatan: <span class="fw-normal">{{ $jadwal->penilaian->catatan ?? '-' }}</span>
                                    </div>
                                    @if($jadwal->penilaian->file_hasil)
                                        <a href="{{ asset('storage/' . $jadwal->penilaian->file_hasil) }}" target="_blank" class="btn btn-sm btn-link p-0 mt-1 text-decoration-none">
                                            <i class="bi bi-file-earmark-check"></i> Lihat Berkas Penilaian
                                        </a>
                                    @endif
                                @else
                                    <div class="text-muted italic py-2">Belum ada data penilaian wawancara.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: AKSI & TANGGAL --}}
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0 text-dark"><i class="bi bi-shield-check me-2"></i>Verifikasi & Validasi</h6>
                </div>
                <div class="card-body">
                    
                    {{-- STATUS DISPLAY JIKA SUDAH DIPROSES --}}
                    @if ($pengajuan->status != 'pending')
                        <div class="status-card mb-3">
                            <div class="text-uppercase small fw-bold mb-1 opacity-75">Status Saat Ini</div>
                            <h5 class="fw-bold mb-0 text-primary">
                                {{ strtoupper(str_replace('_', ' ', $pengajuan->status)) }}
                            </h5>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">BERLAKU SEJAK</label>
                            <input type="text" class="form-control bg-light" value="{{ $pengajuan->tgl_mulai_berlaku }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">BERAKHIR PADA</label>
                            <input type="text" class="form-control bg-light" value="{{ $pengajuan->tgl_akhir_berlaku }}" readonly>
                        </div>
                    @endif

                    {{-- ALUR PROSES LANJUTAN --}}
                    <div class="d-grid gap-2">
                        {{-- 1. Validasi Nilai Ujian (Jika status method_selected & sudah ujian) --}}
                        @if ($pengajuan->status == 'method_selected' && $pengajuan->user->examResult)
                            <form action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}" method="POST">
                                @csrf
                               <a href="{{ route('admin.pengajuan.approve_score', $pengajuan->id) }}" class="btn btn-primary w-100 fw-bold py-2 rounded-pil" onclick="return confirm('Verifikasi Nilai?')" title="Acc Nilai">VALIDASI</a>

                            </form>
                        @endif

                        {{-- 2. Selesaikan Proses (Jika status interview_scheduled & jadwal disetujui/completed) --}}
                        @php $jadwal = $pengajuan->jadwalWawancara; @endphp
                        @if ($pengajuan->status == 'interview_scheduled' && $jadwal && $jadwal->status == 'approved' && $jadwal->penilaian)
                            <a href="{{ route('admin.pengajuan.complete', $pengajuan->id) }}"
                                class="btn btn-success w-100 fw-bold py-2 rounded-pill"
                                onclick="return confirm('Terbitkan lisensi untuk pengajuan ini?')">
                                <i class="bi bi-patch-check me-1"></i> TERBITKAN LISENSI
                            </a>
                        @endif
                    </div>

                    {{-- FORM APPROVAL AWAL (HANYA MUNCUL JIKA PENDING) --}}
                    @if ($pengajuan->status == 'pending')
                        <form action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}" method="POST">
                            @csrf
                            <div class="alert alert-info small mb-3">
                                <i class="bi bi-info-circle me-1"></i> 
                                Metode evaluasi akan <strong>terdeteksi otomatis</strong> berdasarkan pilihan user.
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">TANGGAL TERBIT <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_mulai_berlaku" class="form-control" 
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">TANGGAL EXPIRED (1 TH) <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_akhir_berlaku" class="form-control" 
                                    value="{{ date('Y-m-d', strtotime('+1 years')) }}" required>
                            </div>

                            <hr>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success fw-bold py-2" 
                                    onclick="return confirm('Tanggal sudah benar? Lanjutkan proses?')">
                                    <i class="bi bi-check-lg me-2"></i> SETUJUI & PROSES
                                </button>

                                <button type="button" class="btn btn-outline-danger py-2" 
                                    onclick="if(confirm('Tolak berkas ini?')){document.getElementById('form-reject').submit();}">
                                    <i class="bi bi-x-lg me-2"></i> TOLAK BERKAS
                                </button>
                            </div>
                        </form>

                        <form id="form-reject" action="{{ route('admin.pengajuan.reject', $pengajuan->id) }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endif
                </div>
            </div>
{{--            <div class="card border-0 shadow-sm rounded-4 mt-4">--}}
{{--    <div class="card-header bg-white border-0 pt-4 px-4">--}}
{{--        <h6 class="fw-bold mb-0"><i class="bi bi-send-check text-primary me-2"></i>Penerbitan Dokumen SPK & SPKK</h6>--}}
{{--    </div>--}}
{{--    <div class="card-body p-4">--}}
{{--        @if($pengajuan->status == 'disetujui')--}}
{{--            <div class="alert alert-success border-0 mb-0">--}}
{{--                <i class="bi bi-check-circle-fill me-2"></i> Dokumen SPK & SPKK sudah diterbitkan dan terkirim ke Dashboard Perawat.--}}
{{--            </div>--}}
            
            {{-- Tombol untuk Admin ngintip PDF-nya --}}
{{--            <div class="mt-3">--}}
{{--                <a href="{{ route('pengajuan.print_spk', $pengajuan->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">Lihat SPK</a>--}}
{{--                <a href="{{ route('pengajuan.print_spkk', $pengajuan->id) }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-3">Lihat SPKK</a>--}}
{{--            </div>--}}
{{--        @else--}}
{{--            <p class="small text-muted mb-3">Pastikan semua tahapan kredensialing sudah selesai sebelum menerbitkan dokumen ini ke perawat bersangkutan.</p>--}}
            
{{--            <form action="{{ route('admin.pengajuan.terbitkan_spk', $pengajuan->id) }}" method="POST">--}}
{{--                @csrf--}}
{{--                <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm" onclick="return confirm('Terbitkan SPK dan kirim ke dashboard perawat sekarang?')">--}}
{{--                    <i class="bi bi-send-fill me-2"></i> Terbitkan & Kirim SPK--}}
{{--                </button>--}}
{{--            </form>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
        </div>
    </div>
</div>
@endsection
