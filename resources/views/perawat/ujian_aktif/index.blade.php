@extends('layouts.app')

@section('title', 'Daftar Ujian & Form Aktif')

@push('styles')
    <style>
        /* --- 1. Card Styling --- */
        .exam-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            background: #fff;
            overflow: hidden;
            position: relative;
        }

        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.06);
            border-color: #dbeafe;
        }

        /* --- 2. Custom Soft Badges --- */
        .badge-soft {
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Varian Warna Badge */
        .bg-soft-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .bg-soft-warning {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .bg-soft-secondary {
            background-color: #f1f5f9;
            color: #475569;
        }

        .bg-soft-primary {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .bg-soft-purple {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        .bg-soft-blue {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .bg-soft-info {
            background-color: #e0f7fa;
            color: #006064;
        }

        /* --- 3. Info Box Waktu --- */
        .time-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #f1f5f9;
        }

        .icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .icon-wrap-blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .icon-wrap-red {
            background: #fef2f2;
            color: #ef4444;
        }

        /* --- 4. Utils --- */
        .text-limit {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 40px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Daftar Ujian & Survei</h4>
                <p class="text-muted small mb-0">Pilih form yang tersedia di bawah ini untuk mulai mengerjakan.</p>
            </div>

            {{-- Date Badge --}}
            <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border">
                <i class="bi bi-calendar-event text-primary"></i>
                <span class="fw-bold text-dark small">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>

   @if ($forms->isEmpty())
    {{-- Empty State --}}
    <div class="d-flex flex-column align-items-center justify-content-center py-5 bg-white rounded-4 border border-dashed text-center" style="min-height: 350px;">
        <div class="bg-light rounded-circle p-4 mb-3 text-muted">
            <i class="bi bi-clipboard-x display-3 opacity-25"></i>
        </div>
        
        @php
            $isPending = \App\Models\PengajuanSertifikat::where('user_id', auth()->id())->where('status', 'pending')->exists();
        @endphp

        @if($isPending)
            <h5 class="fw-bold text-dark">Pengajuan Masih Menunggu Verifikasi</h5>
            <p class="text-muted small px-3" style="max-width: 450px;">
                Daftar ujian akan muncul secara otomatis setelah berkas pengajuan sertifikasi Anda 
                <strong>diverifikasi oleh Admin/Komite</strong>.
            </p>
        @else
            <h5 class="fw-bold text-dark">Belum Ada Ujian Tersedia</h5>
            <p class="text-muted small px-3" style="max-width: 450px;">
                Saat ini belum ada jadwal ujian atau form survei yang dipublikasikan untuk akun Anda.
            </p>
        @endif
        
        <button onclick="location.reload()" class="btn btn-outline-primary rounded-pill px-4 mt-2">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Halaman
        </button>
    </div>
@else
            <div class="row g-4">
                @foreach ($forms as $form)
                   @php
    // --- 1. INISIALISASI DATA DASAR ---
    $user = auth()->user();
    $allResults = $form->examResults; // Sudah di-eager load dari controller
    $attemptCount = $allResults->count();
    $maxAttempts = 3;

    // Ambil info pengajuan aktif user
    $pengajuanAktif = \App\Models\PengajuanSertifikat::where('user_id', $user->id)
        ->whereIn('status', ['method_selected', 'exam_passed', 'interview_scheduled'])
        ->latest()
        ->first();

    $isKredensialing = $pengajuanAktif && $pengajuanAktif->metode == 'interview_only';

    // Ambil hasil terakhir (jika ada)
    $lastResult = $allResults->sortByDesc('id')->first();
    $isSubmitted = $lastResult !== null;
    
    // Cek kelulusan (Nilai ambang batas 75 atau flag remidi)
    $isLulus = $lastResult && ($lastResult->total_nilai >= 75 && !$lastResult->remidi);
    $isRemidiLast = $lastResult && ($lastResult->remidi || $lastResult->total_nilai < 75);

    $isStarted = $now->greaterThanOrEqualTo($form->waktu_mulai);
    $isEnded = $now->greaterThan($form->waktu_selesai);

    // --- 2. LOGIKA PENENTUAN STATUS & UI ---
    
    // Default values
    $opacityClass = '';
    $isClickable = true;

    if ($isKredensialing) {
        // JIKA JALUR KREDENSIALING (SKIP UJIAN)
        $badgeClass = 'bg-soft-info';
        $badgeIcon = 'bi-info-circle';
        $statusLabel = 'Kredensialing';
        $btnClass = 'btn-light text-muted border';
        $btnLabel = 'Tidak Perlu Ujian';
        $btnIcon = 'bi-slash-circle';
        $linkRoute = '#';
        $isClickable = false;
        $opacityClass = 'opacity-75';

    } elseif ($isLulus) {
        // JIKA SUDAH LULUS
        $badgeClass = 'bg-soft-primary';
        $badgeIcon = 'bi-check-all';
        $statusLabel = 'Lulus / Selesai';
        $btnClass = 'btn-outline-primary';
        $btnLabel = 'Lihat Nilai Saya';
        $btnIcon = 'bi-trophy';
      $linkRoute = '#';

    } elseif ($attemptCount >= $maxAttempts) {
        // JIKA SUDAH MENCOBA 3 KALI (LIMIT REACHED)
        $badgeClass = 'bg-soft-secondary';
        $badgeIcon = 'bi-exclamation-octagon';
        $statusLabel = 'Batas Remidi Habis';
        $btnClass = 'btn-light text-danger border';
        $btnLabel = 'Kesempatan Habis (3/3)';
        $btnIcon = 'bi-x-circle';
        $linkRoute = '#';
        $isClickable = false;
        $opacityClass = 'opacity-75';

    } elseif ($isEnded) {
        // JIKA WAKTU HABIS
        $badgeClass = 'bg-soft-secondary';
        $badgeIcon = 'bi-clock-history';
        $statusLabel = 'Waktu Habis';
        $btnClass = 'btn-light text-muted border';
        $btnLabel = 'Sudah Ditutup';
        $btnIcon = 'bi-lock';
        $linkRoute = '#';
        $isClickable = false;
        $opacityClass = 'opacity-75 grayscale';

    } elseif (!$isStarted) {
        // JIKA BELUM MULAI
        $badgeClass = 'bg-soft-warning';
        $badgeIcon = 'bi-hourglass-split';
        $statusLabel = 'Belum Dimulai';
        $btnClass = 'btn-light text-muted border';
        $btnLabel = 'Menunggu Jadwal';
        $btnIcon = 'bi-calendar';
        $linkRoute = '#';
        $isClickable = false;

    } elseif ($isRemidiLast) {
        // JIKA GAGAL TAPI MASIH ADA SISA PERCOBAAN
        $badgeClass = 'bg-soft-warning';
        $badgeIcon = 'bi-arrow-repeat';
        $statusLabel = 'Remidi (' . $attemptCount . '/' . $maxAttempts . ')';
        $btnClass = 'btn-danger shadow-sm';
        $btnLabel = 'Kerjakan Lagi';
        $btnIcon = 'bi-arrow-repeat';
        $linkRoute = route('perawat.ujian.kerjakan', $form->slug);

    } else {
        // KONDISI NORMAL (PERTAMA KALI MENGERJAKAN)
        $badgeClass = 'bg-soft-success';
        $badgeIcon = 'bi-play-circle-fill';
        $statusLabel = 'Tersedia';
        $btnClass = 'btn-primary shadow-sm';
        $btnLabel = 'Kerjakan Sekarang';
        $btnIcon = 'bi-arrow-right-circle';
        $linkRoute = route('perawat.ujian.kerjakan', $form->slug);
    }
    
     $lastResult = $allResults->sortByDesc('id')->first();
    
    // BAGIAN KRUSIAL: Bikin link hanya jika $lastResult ada
    // Jika tidak ada, kasih '#' saja supaya tidak error Missing Parameter
    $resultLink = ($lastResult) ? route('perawat.ujian.hasil', ['result_id' => $lastResult->id]) : '#';
@endphp

@if(session('trigger_hard_reset'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Batas Percobaan Habis!',
            text: "{{ session('trigger_hard_reset')['message'] }}",
            icon: 'error',
            confirmButtonText: 'Saya Mengerti & Ajukan Ulang',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Lakukan penghapusan via AJAX setelah klik OK
                fetch("{{ route('perawat.ujian.hard_reset') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        form_id: "{{ session('trigger_hard_reset')['form_id'] }}"
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload(); // Reload untuk membersihkan tampilan
                    } else {
                        Swal.fire('Error', 'Gagal mereset data: ' + data.message, 'error');
                    }
                });
            }
        });
    });
</script>
@endif
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 exam-card {{ $opacityClass }}">
                            <div class="card-body p-4 d-flex flex-column">

                                {{-- Badges Header --}}
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    {{-- Badge Target --}}
                                    @if ($form->target_peserta == 'khusus')
                                        <span class="badge-soft bg-soft-purple" title="Undangan Khusus">
                                            <i class="bi bi-star-fill"></i> Khusus
                                        </span>
                                    @else
                                        <span class="badge-soft bg-soft-blue">
                                            <i class="bi bi-globe"></i> Public
                                        </span>
                                    @endif

                                    {{-- Badge Status --}}
                                    <span class="badge-soft {{ $badgeClass }}">
                                        <i class="bi {{ $badgeIcon }}"></i> {{ $statusLabel }}
                                    </span>
                                </div>

                                {{-- Judul --}}
                               {{-- Judul & Deskripsi --}}
<h5 class="fw-bold text-dark mb-2 text-truncate" title="{{ $form->judul }}">
    {{ $form->judul }}
</h5>

{{-- TAMBAHKAN INDIKATOR PERCOBAAN DI SINI --}}
<div class="d-flex align-items-center mb-1">
    <div class="progress flex-grow-1 bg-light" style="height: 6px; border-radius: 10px;">
        @php
            // Hitung lebar progress bar (max 3 kali)
            $progressWidth = ($attemptCount / $maxAttempts) * 100;
            $progressColor = $attemptCount >= $maxAttempts ? 'bg-danger' : ($attemptCount == 2 ? 'bg-warning' : 'bg-success');
        @endphp
        <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $progressWidth }}%; border-radius: 10px;"></div>
    </div>
    <span class="ms-2 badge bg-light text-dark border extra-small fw-bold">
        {{ $attemptCount }}/{{ $maxAttempts }} Percobaan
    </span>
</div>


                                <p class="text-muted small mb-4 text-limit">
                                    {{ $form->deskripsi ?? 'Silakan kerjakan ujian ini dengan teliti.' }}
                                </p>

                                {{-- Info Waktu --}}
                                <div class="time-box mb-2 mt-auto">
                                    {{-- Mulai --}}
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-wrap icon-wrap-blue me-3">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-secondary"
                                                style="font-size: 10px; font-weight: 600;">MULAI</span>
                                            <span class="fw-bold text-dark" style="font-size: 12px;">
                                                {{ $form->waktu_mulai->format('d M Y, H:i') }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Selesai --}}
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrap icon-wrap-red me-3">
                                            <i class="bi bi-calendar-x"></i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-secondary" style="font-size: 10px; font-weight: 600;">BATAS
                                                AKHIR</span>
                                            <span class="fw-bold text-dark" style="font-size: 12px;">
                                                {{ $form->waktu_selesai->format('d M Y, H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                    {{-- Durasi --}}
        <div class="d-flex align-items-center pt-2 mt-2">
            <div class="icon-wrap me-3" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-stopwatch"></i>
            </div>
            <div class="d-flex flex-column">
                <span class="text-secondary" style="font-size: 10px; font-weight: 600;">DURASI UJIAN</span>
                <span class="fw-bold text-dark" style="font-size: 12px;">
                    {{ $form->durasi ?? 90 }} Menit
                </span>
            </div>
        </div>
                                </div>

                                {{-- Alert Khusus Kredensialing --}}
                                @if ($isKredensialing)
                                    <div
                                        class="alert alert-info py-2 px-3 small border-0 bg-opacity-10 bg-info text-info mb-3">
                                        <i class="bi bi-info-circle me-1"></i> Jalur Kredensialing tidak perlu ujian.
                                    </div>
                                @endif


{{-- TAMPILKAN RIWAYAT NILAI --}}
<div class="mb-4 p-2 bg-light rounded-3 ">
    <small class="text-muted d-block mb-1 fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">RIWAYAT SKOR</small>
    
    <div class="d-flex flex-wrap gap-2">
        @forelse($allResults->sortBy('id') as $index => $res)
            <div class="d-flex align-items-center">
                <span class="badge {{ $res->total_nilai >= 75 ? 'bg-success' : 'bg-danger' }} rounded-circle p-1 me-1" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 8px;">
                    {{ $index + 1 }}
                </span>
                <span class="fw-bold {{ $res->total_nilai >= 75 ? 'text-success' : 'text-danger' }}" style="font-size: 12px;">
                    {{ $res->total_nilai }}
                </span>
                @if(!$loop->last)
                    <span class="text-muted mx-1">|</span>
                @endif
            </div>
        @empty
            <span class="text-muted italic" style="font-size: 11px;">Belum ada data nilai.</span>
        @endforelse
    </div>

    {{-- Info Kelulusan Singkat --}}
    @if($isLulus)
        <div class="mt-1">
            <small class="text-success fw-bold" style="font-size: 10px;">
                <i class="bi bi-patch-check-fill"></i> Selamat! Anda Lulus.
            </small>
        </div>
    @elseif($isRemidiLast && $attemptCount < $maxAttempts)
        <div class="mt-1">
            <small class="text-warning fw-bold" style="font-size: 10px;">
                <i class="bi bi-info-circle-fill"></i> Nilai belum mencapai 75.
            </small>
        </div>
    @endif
</div>
{{-- 1. Tombol Detail (Munculin Modal) --}}
<button type="button" class="btn btn-outline-secondary w-100 rounded-pill py-2 mb-2 small fw-bold" 
        data-bs-toggle="modal" data-bs-target="#detailModal{{ $form->id }}">
    <i class="bi bi-info-circle me-1"></i> Detail Ujian
</button>

<a href="{{ $isLulus ? $resultLink : $linkRoute }}"
   class="btn {{ $btnClass }} w-100 rounded-pill py-2 fw-bold {{ (!$isClickable && !$isLulus) ? 'disabled' : '' }}">
    
    @if ($isLulus)
        {{-- Jika sudah lulus, tampilkan tombol Lihat Nilai --}}
        <i class="bi bi-trophy me-1"></i> Lihat Nilai Saya
    @elseif ($isSubmitted)
        {{-- Jika sudah pernah ngerjakan tapi belum lulus (Remidi) --}}
        <i class="bi {{ $btnIcon }} me-1"></i> {{ $btnLabel }}
    @else
        {{-- Jika benar-benar baru pertama kali --}}
        {{ $btnLabel }} <i class="bi {{ $btnIcon }} ms-1"></i>
    @endif
</a>


                            </div>
                        </div>
                    </div>
                       
    <div class="modal fade" id="detailModal{{ $form->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Detail Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3 mb-2">
                        <i class="bi bi-file-earmark-text fs-3"></i>
                    </div>
                    <h6 class="fw-bold mb-0">{{ $form->judul }}</h6>
                    <span class="badge-soft {{ $badgeClass }} mt-2">{{ $statusLabel }}</span>
                </div>

                <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                    <div class="list-group-item d-flex justify-content-between small">
                        <span class="text-muted">Target Peserta</span>
                        <span class="fw-bold">{{ ucfirst($form->target_peserta) }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between small">
                        <span class="text-muted">Total Soal</span>
                        <span class="fw-bold text-primary">{{ $form->questions_count ?? $form->questions->count() }} Soal</span>
                    </div>
                    {{-- TAMBAHAN DURASI MODAL --}}
    <div class="list-group-item d-flex justify-content-between small">
        <span class="text-muted">Durasi Waktu</span>
        <span class="fw-bold text-warning"><i class="bi bi-stopwatch"></i> {{ $form->durasi ?? 90 }} Menit</span>
    </div>
                    <div class="list-group-item d-flex justify-content-between small">
                        <span class="text-muted">Batas Percobaan</span>
                        <span class="fw-bold">3 Kali (Sisa: {{ 3 - $attemptCount }})</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between small">
                        <span class="text-muted">Ambang Batas (Passing Grade)</span>
                        <span class="fw-bold text-success">75</span>
                    </div>
                </div>

<div class="mt-4">
    <p class="small text-muted fw-bold mb-2">
        <i class="bi bi-info-circle-fill me-1"></i> Instruksi & Deskripsi:
    </p>
    <div class="p-3 bg-light border rounded-3 text-secondary shadow-sm" 
         style="white-space: pre-line; font-size: 13px; line-height: 1.6; max-height: 400px; overflow-y: auto;">
        {{ $form->deskripsi ?? 'Tidak ada deskripsi tambahan.' }}
    </div>
</div>

<style>
    .description-box {
        background-image: radial-gradient(#e9ecef 1px, transparent 1px);
        background-size: 20px 20px; /* Memberi efek titik-titik halus di background */
    }
</style>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
                @endforeach
            </div>
        @endif
    </div>
    
 
@endsection
