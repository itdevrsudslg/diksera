{{--@extends('layouts.app')--}}

{{--@section('title', 'Data Lisensi Saya')--}}

{{--@push('styles')--}}
{{--    <style>--}}
{{--        :root {--}}
{{--            --primary-blue: #2563eb;--}}
{{--            --primary-hover: #1d4ed8;--}}
{{--            --text-dark: #1e293b;--}}
{{--            --text-gray: #64748b;--}}
{{--            --bg-light: #f8fafc;--}}
{{--            --border-color: #e2e8f0;--}}
{{--        }--}}

{{--        body {--}}
{{--            background-color: var(--bg-light);--}}
{{--            color: var(--text-dark);--}}
{{--            font-size: 14px;--}}
{{--        }--}}

        /* --- Header --- */
{{--        .page-header {--}}
{{--            display: flex;--}}
{{--            justify-content: space-between;--}}
{{--            align-items: center;--}}
{{--            margin-bottom: 24px;--}}
{{--        }--}}

{{--        .page-title {--}}
{{--            font-size: 1.5rem;--}}
{{--            font-weight: 700;--}}
{{--            margin: 0;--}}
{{--            color: var(--text-dark);--}}
{{--        }--}}

        /* --- Table Card --- */
{{--        .card-table {--}}
{{--            background: white;--}}
{{--            border: 1px solid var(--border-color);--}}
{{--            border-radius: 12px;--}}
{{--            overflow: hidden;--}}
{{--            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);--}}
{{--        }--}}

{{--        .table-custom th {--}}
{{--            background-color: #f1f5f9;--}}
{{--            color: var(--text-gray);--}}
{{--            font-weight: 600;--}}
{{--            font-size: 0.75rem;--}}
{{--            text-transform: uppercase;--}}
{{--            letter-spacing: 0.5px;--}}
{{--            padding: 16px 20px;--}}
{{--            border-bottom: 1px solid var(--border-color);--}}
{{--        }--}}

{{--        .table-custom td {--}}
{{--            padding: 16px 20px;--}}
{{--            vertical-align: middle;--}}
{{--            border-bottom: 1px solid #f1f5f9;--}}
{{--        }--}}

{{--        .table-custom tr:last-child td {--}}
{{--            border-bottom: none;--}}
{{--        }--}}

{{--        .table-custom tr:hover td {--}}
{{--            background-color: #f8fafc;--}}
{{--        }--}}

        /* --- Typography --- */
{{--        .license-name {--}}
{{--            font-weight: 700;--}}
{{--            color: var(--text-dark);--}}
{{--            font-size: 0.95rem;--}}
{{--            display: block;--}}
{{--        }--}}

{{--        .license-sub {--}}
{{--            font-size: 0.8rem;--}}
{{--            color: var(--text-gray);--}}
{{--            display: flex;--}}
{{--            align-items: center;--}}
{{--            gap: 5px;--}}
{{--            margin-top: 4px;--}}
{{--        }--}}

{{--        .license-number {--}}
{{--            font-family: 'Courier New', monospace;--}}
{{--            font-weight: 700;--}}
{{--            background: #f1f5f9;--}}
{{--            color: #475569;--}}
{{--            padding: 4px 8px;--}}
{{--            border-radius: 4px;--}}
{{--            font-size: 0.85rem;--}}
{{--        }--}}

        /* --- Badges --- */
{{--        .badge-metode {--}}
{{--            font-size: 0.7rem;--}}
{{--            padding: 4px 8px;--}}
{{--            border-radius: 50px;--}}
{{--            font-weight: 600;--}}
{{--            text-transform: uppercase;--}}
{{--        }--}}

{{--        .bg-metode-interview {--}}
{{--            background: #e0f2fe;--}}
{{--            color: #0369a1;--}}
{{--            border: 1px solid #bae6fd;--}}
{{--        }--}}

{{--        .bg-metode-pg {--}}
{{--            background: #f0fdf4;--}}
{{--            color: #15803d;--}}
{{--            border: 1px solid #bbf7d0;--}}
{{--        }--}}

        /* Status Badges */
{{--        .badge-status {--}}
{{--            padding: 5px 10px;--}}
{{--            border-radius: 6px;--}}
{{--            font-size: 0.75rem;--}}
{{--            font-weight: 700;--}}
{{--            display: inline-flex;--}}
{{--            align-items: center;--}}
{{--            gap: 6px;--}}
{{--        }--}}

{{--        .bs-active {--}}
{{--            background: #dcfce7;--}}
{{--            color: #166534;--}}
{{--        }--}}

{{--        .bs-warning {--}}
{{--            background: #fef9c3;--}}
{{--            color: #854d0e;--}}
{{--        }--}}

{{--        .bs-danger {--}}
{{--            background: #fee2e2;--}}
{{--            color: #991b1b;--}}
{{--        }--}}

{{--        .bs-pending {--}}
{{--            background: #f3f4f6;--}}
{{--            color: #4b5563;--}}
{{--            border: 1px solid #e5e7eb;--}}
{{--        }--}}

        /* --- Buttons --- */
{{--        .btn-renew {--}}
{{--            margin-top: 8px;--}}
{{--            background-color: white;--}}
{{--            border: 1px solid #cbd5e1;--}}
{{--            color: var(--text-dark);--}}
{{--            font-size: 0.75rem;--}}
{{--            font-weight: 600;--}}
{{--            padding: 6px 12px;--}}
{{--            border-radius: 6px;--}}
{{--            width: 100%;--}}
{{--            transition: 0.2s;--}}
{{--            display: flex;--}}
{{--            justify-content: center;--}}
{{--            align-items: center;--}}
{{--            gap: 5px;--}}
{{--            text-decoration: none;--}}
            /* Tambahan agar link seperti tombol */
{{--        }--}}

{{--        .btn-renew:hover {--}}
{{--            border-color: #f59e0b;--}}
{{--            background-color: #fffbeb;--}}
{{--            color: #b45309;--}}
{{--        }--}}

{{--        .btn-renew.danger-renew:hover {--}}
{{--            border-color: #ef4444;--}}
{{--            background-color: #fef2f2;--}}
{{--            color: #b91c1c;--}}
{{--        }--}}

{{--        .btn-generate {--}}
{{--            color: var(--primary-blue);--}}
{{--            background: #e0f2fe;--}}
{{--            padding: 8px 14px;--}}
{{--            border-radius: 8px;--}}
{{--            font-weight: 600;--}}
{{--            font-size: 0.85rem;--}}
{{--            text-decoration: none;--}}
{{--            transition: 0.2s;--}}
{{--            display: inline-flex;--}}
{{--            align-items: center;--}}
{{--            gap: 6px;--}}
{{--        }--}}

{{--        .btn-generate:hover {--}}
{{--            background: var(--primary-blue);--}}
{{--            color: white;--}}
{{--        }--}}

{{--        .btn-generate.disabled {--}}
{{--            background: #f1f5f9;--}}
{{--            color: #94a3b8;--}}
{{--            cursor: not-allowed;--}}
{{--        }--}}

{{--        .btn-back {--}}
{{--            color: var(--text-gray);--}}
{{--            font-weight: 600;--}}
{{--            text-decoration: none;--}}
{{--            display: inline-flex;--}}
{{--            align-items: center;--}}
{{--            gap: 6px;--}}
{{--            padding: 8px 16px;--}}
{{--            border-radius: 8px;--}}
{{--            border: 1px solid transparent;--}}
{{--            transition: 0.2s;--}}
{{--        }--}}

{{--        .btn-back:hover {--}}
{{--            background: white;--}}
{{--            border-color: #e2e8f0;--}}
{{--            color: var(--text-dark);--}}
{{--        }--}}
{{--    </style>--}}
{{--@endpush--}}

{{--@section('content')--}}
{{--    <div class="container py-4">--}}

<!--        {{-- HEADER --}}-->
{{--        <div class="page-header">--}}
{{--            <div>--}}
{{--                <h1 class="page-title">Data Lisensi Saya</h1>--}}
{{--                <p class="text-muted small mb-0">Riwayat pengajuan Uji Kompetensi dan Kredensialing.</p>--}}
{{--            </div>--}}
{{--            <a href="{{ route('dashboard') }}" class="btn-back">--}}
{{--                <i class="bi bi-arrow-left"></i> Kembali Dashboard--}}
{{--            </a>--}}
{{--        </div>--}}

{{--        @if (session('success'))--}}
{{--            <div--}}
{{--                class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 mb-4 d-flex align-items-center gap-2">--}}
{{--                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}--}}
{{--            </div>--}}
{{--        @endif--}}

{{--        @if (session('swal'))--}}
{{--            <script>--}}
{{--                document.addEventListener("DOMContentLoaded", function() {--}}
{{--                    Swal.fire({--}}
{{--                        icon: "{{ session('swal')['icon'] }}",--}}
{{--                        title: "{{ session('swal')['title'] }}",--}}
{{--                        text: "{{ session('swal')['text'] }}",--}}
{{--                        timer: 3000,--}}
{{--                        showConfirmButton: false--}}
{{--                    });--}}
{{--                });--}}
{{--            </script>--}}
{{--        @endif--}}

<!--        {{-- INFO ALERT --}}-->
{{--        <div class="alert alert-info d-flex align-items-center p-3 mb-4"--}}
{{--            style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; color: #1e40af;">--}}
{{--            <i class="bi bi-info-circle-fill fs-4 me-3"></i>--}}
{{--            <div>--}}
{{--                <strong>Ingin Mengajukan Lisensi Baru?</strong><br>--}}
{{--                Silakan pilih menu <b>"Pengajuan Lisensi"</b> di sidebar sebelah kiri.--}}
{{--            </div>--}}
{{--        </div>--}}

<!--        {{-- TABEL DATA --}}-->
{{--        <div class="card-table">--}}
{{--            <div class="table-responsive">--}}
{{--                <table class="table table-custom mb-0">--}}
{{--                    <thead>--}}
{{--                        <tr>--}}
{{--                            <th width="5%">No</th>--}}
{{--                            <th width="15%">Detail Lisensi</th>--}}
{{--                            <th width="15%">Metode</th>--}}
{{--                            <th width="20%">Nomor & Masa Berlaku</th>--}}
{{--                            <th width="20%">Status & Aksi</th>--}}
{{--                            <th width="12%" class="text-end">Dokumen</th>--}}
{{--                        </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                        @forelse($data as $item)--}}
{{--                            <tr>--}}
{{--                                <td class="text-muted text-center">{{ $loop->iteration }}</td>--}}

<!--                                {{-- Nama & Lembaga --}}-->
{{--                                <td>--}}
{{--                                    <span class="license-name">{{ $item->nama }}</span>--}}
{{--                                    <div class="license-sub">--}}
{{--                                        <i class="bi bi-building"></i> {{ $item->lembaga }}--}}
{{--                                    </div>--}}
{{--                                    <div class="license-sub text-primary">--}}
{{--                                        <i class="bi bi-geo-alt"></i> Unit: {{ $item->unit_kerja_saat_buat }}--}}
{{--                                    </div>--}}
{{--                                </td>--}}

<!--                                {{-- Metode Badge --}}-->
{{--                                <td>--}}
{{--                                    @if ($item->metode == 'interview_only')--}}
{{--                                        <span class="badge-metode bg-metode-interview">Kredensialing</span>--}}
{{--                                    @elseif ($item->metode == 'pg_interview')--}}
{{--                                        <span class="badge-metode bg-metode-pg">Uji Kompetensi</span>--}}
{{--                                    @else--}}
{{--                                        <span class="badge-metode bg-metode-pg">Uji Kompetensi</span>--}}
{{--                                    @endif--}}
{{--                                </td>--}}

<!--                                {{-- Nomor & Tanggal --}}-->
{{--                                <td>--}}
{{--                                    <div class="mb-2">--}}
{{--                                        <span class="license-number">{{ $item->nomor }}</span>--}}
{{--                                    </div>--}}
{{--                                    <div class="d-flex flex-column" style="font-size: 0.8rem; color: #64748b;">--}}
{{--                                        <span>Terbit:--}}
{{--                                            {{ \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') }}</span>--}}
{{--                                        <span--}}
{{--                                            class="fw-bold {{ \Carbon\Carbon::parse($item->tgl_expired)->isPast() ? 'text-danger' : 'text-dark' }}">--}}
{{--                                            Habis: {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d M Y') }}--}}
{{--                                        </span>--}}
{{--                                    </div>--}}
{{--                                </td>--}}

<!--                                {{-- STATUS & AKSI --}}-->
{{--                                <td>--}}
<!--                                    {{-- 1. CEK STATUS PENDING / REJECTED --}}-->
{{--                                    @if ($item->status == 'pending')--}}
{{--                                        <span class="badge-status bs-pending mb-1">--}}
{{--                                            <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi--}}
{{--                                        </span>--}}
{{--                                        <div class="text-muted small fst-italic mt-1">--}}
{{--                                            Pengajuan sedang diperiksa Admin.--}}
{{--                                        </div>--}}
{{--                                    @elseif ($item->status == 'rejected')--}}
{{--                                        <span class="badge-status bs-danger mb-1">--}}
{{--                                            <i class="bi bi-x-circle"></i> Ditolak--}}
{{--                                        </span>--}}
{{--                                        <div class="text-danger small mt-1">--}}
{{--                                            Hubungi Admin untuk info lebih lanjut.--}}
{{--                                        </div>--}}
{{--                                    @else--}}
<!--                                        {{-- 2. STATUS AKTIF / EXPIRED --}}-->
{{--                                        @php--}}
{{--                                            $expired = \Carbon\Carbon::parse($item->tgl_expired);--}}
{{--                                            $today = \Carbon\Carbon::now();--}}
{{--                                            $diff = $today->diffInDays($expired, false);--}}
{{--                                        @endphp--}}

{{--                                        @if ($diff < 0)--}}
<!--                                            {{-- EXPIRED --}}-->
{{--                                            <span class="badge-status bs-danger mb-1"><i class="bi bi-x-circle"></i>--}}
{{--                                                Expired</span>--}}

<!--                                            {{-- [PERBAIKAN] Gunakan Link ke Create dengan Metode item tsb --}}-->
{{--                                            <a href="{{ route('perawat.lisensi.create', $item->metode) }}"--}}
{{--                                                class="btn-renew danger-renew">--}}
{{--                                                <i class="bi bi-arrow-repeat"></i> Ajukan Perpanjangan--}}
{{--                                            </a>--}}
{{--                                        @elseif($diff < 90)--}}
<!--                                            {{-- HAMPIR EXPIRED --}}-->
{{--                                            <span class="badge-status bs-warning mb-1"><i--}}
{{--                                                    class="bi bi-exclamation-triangle"></i> Hampir Expired</span>--}}

<!--                                            {{-- [PERBAIKAN] Gunakan Link ke Create --}}-->
{{--                                            <a href="{{ route('perawat.lisensi.create', $item->metode) }}"--}}
{{--                                                class="btn-renew">--}}
{{--                                                <i class="bi bi-arrow-repeat"></i> Perpanjang--}}
{{--                                            </a>--}}
{{--                                        @else--}}
<!--                                            {{-- AKTIF --}}-->
{{--                                            <span class="badge-status bs-active"><i class="bi bi-check-circle"></i>--}}
{{--                                                Aktif</span>--}}
{{--                                        @endif--}}
{{--                                    @endif--}}
{{--                                </td>--}}

<!--                                {{-- Generate PDF --}}-->
{{--                                <td class="text-end">--}}
{{--                                    @if ($item->status == 'pending' || $item->status == 'rejected')--}}
{{--                                        <button class="btn-generate disabled" disabled title="Dokumen belum aktif">--}}
{{--                                            <i class="bi bi-lock-fill"></i> PDF--}}
{{--                                        </button>--}}
{{--                                    @else--}}
{{--                                        <a href="{{ route('perawat.lisensi.generate', $item->id) }}" class="btn-generate"--}}
{{--                                            title="Unduh PDF">--}}
{{--                                            <i class="bi bi-file-earmark-pdf"></i> PDF--}}
{{--                                        </a>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @empty--}}
{{--                            <tr>--}}
{{--                                <td colspan="6" class="text-center py-5">--}}
{{--                                    <div class="d-flex flex-column align-items-center opacity-50">--}}
{{--                                        <i class="bi bi-clipboard-x display-4 text-muted mb-2"></i>--}}
{{--                                        <h6 class="fw-bold text-muted">Belum Ada Riwayat</h6>--}}
{{--                                        <p class="small text-muted mb-0">Silakan ajukan Uji Kompetensi melalui menu di--}}
{{--                                            sidebar.</p>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @endforelse--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}
