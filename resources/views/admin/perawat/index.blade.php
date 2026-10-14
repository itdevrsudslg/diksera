@extends('layouts.app')

@php
    $pageTitle = 'Data Perawat dan Bidan';
    $pageSubtitle = 'Monitor dan kelola seluruh data perawat yang terdaftar dalam sistem.';
@endphp

@section('title', 'Data Perawat dan Bidan – Admin DIKSERA')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        .choices__inner {
            border-radius: 8px !important;
            min-height: 38px !important;
            padding: 4px 10px !important;
            background-color: #fff !important;
        }
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
        }
        .table-custom th {
            background-color: var(--blue-soft-2);
            color: var(--text-main);
            font-weight: 600;
            font-size: 12px;
            border-bottom: 2px solid #dbeafe;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            vertical-align: middle;
        }
        .table-custom td {
            vertical-align: middle;
            padding: 12px 16px;
            border-bottom: 1px solid var(--blue-soft-2);
            font-size: 13px;
        }
        .action-group {
            display: flex;
            gap: 6px;
            justify-content: center;
        }
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .avatar-circle {
            width: 36px;
            height: 36px;
            background-color: var(--blue-soft);
            color: var(--blue-main);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        .avatar-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }
        .search-input {
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            font-size: 13px;
            padding-left: 12px;
        }
        .search-input:focus {
            border-color: var(--blue-main);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }
    </style>
@endpush

@section('content')

    <div class="content-card">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end mb-4 gap-3">
            <div>
                <h6 class="m-0 text-muted" style="font-size: 13px;">
                    Total Data: <strong class="text-dark">{{ $perawat->total() }}</strong> Perawat
                </h6>
            </div>

            <div class="d-flex flex-column flex-lg-row gap-2 align-items-lg-center">
                
                {{-- Form Filter & Search --}}
                <form method="GET" class="d-flex flex-wrap flex-md-nowrap gap-2">
                    
                    {{-- Filter Ruangan --}}
                    <select name="filter_ruangan" class="form-select form-select-sm shadow-none" style="border-radius: 8px; max-width: 160px;">
                        <option value="">-- Semua Ruangan --</option>
                        @foreach($allRuangan as $ruangan)
                            <option value="{{ $ruangan }}" {{ request('filter_ruangan') == $ruangan ? 'selected' : '' }}>
                                {{ $ruangan }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Filter Status Kepegawaian --}}
                    <select name="filter_status" class="form-select form-select-sm shadow-none" style="border-radius: 8px; max-width: 150px;">
                        <option value="">-- Semua Status --</option>
                        @foreach($allStatus as $status)
                            <option value="{{ $status }}" {{ request('filter_status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Filter PK --}}
                    <select name="filter_pk" class="form-select form-select-sm shadow-none" style="border-radius: 8px; min-width: 110px;">
                        <option value="">-- Semua PK --</option>
                        @foreach($allPk as $pk)
                            <option value="{{ $pk }}" {{ request('filter_pk') == $pk ? 'selected' : '' }}>
                                {{ $pk }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Search Input --}}
                    <input type="text" name="search" class="form-control form-control-sm search-input shadow-none"
                        placeholder="Cari nama, NIK..." value="{{ request('search') }}" style="width: 160px;">
                    
                    <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm" style="border-radius: 8px;" title="Terapkan Filter">
                        <i class="bi bi-search"></i>
                    </button>
                    
                    @if (request('search') || request('filter_pk') || request('filter_ruangan') || request('filter_status'))
                        <a href="{{ route('admin.perawat.index') }}" class="btn btn-sm btn-outline-danger px-3 shadow-sm"
                            style="border-radius: 8px;" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>

                <div class="vr d-none d-lg-block mx-1 text-secondary opacity-25"></div>

                {{-- Tombol Export --}}
                <div class="d-flex gap-2">
                    <button onclick="exportToExcel()" class="btn btn-success btn-sm px-3 shadow-sm" style="border-radius: 8px; white-space: nowrap;" title="Export Semua Data Terfilter">
                        <i class="bi bi-file-earmark-excel"></i> Semua
                    </button>
                    <button type="button" class="btn btn-success btn-sm px-3 shadow-sm" style="border-radius: 8px; white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#modalExportKustom">
                        <i class="bi bi-file-earmark-excel"></i> Ekspor Kustom
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm px-3 shadow-sm" style="border-radius: 8px; white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#modalExportPk">
                        <i class="bi bi-funnel"></i> Ekspor By KFK
                    </button>
                </div>

            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
               <thead>
                <tr>
                    <th style="width:50px;" class="text-center">No</th>
                    <th>Identitas Perawat</th>
                    <th>Kontak & NIK</th>
                    <th>Dokumen Expired</th>
                    <th>Status Karir</th>
                    <th style="width:160px;" class="text-center">Aksi</th>
                </tr>
               </thead>
                <tbody>
                    @forelse($perawat as $i => $p)
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration + $perawat->firstItem() - 1 }}</td>
                           <td>
    <div class="d-flex align-items-center gap-3">
        <div class="avatar-wrapper">
            @if (!empty($p->profile->foto_3x4))
                <img src="{{ asset('storage/' . $p->profile->foto_3x4) }}" class="avatar-img"
                    alt="{{ $p->name }}">
            @else
                <div class="avatar-circle">{{ strtoupper(substr($p->name, 0, 1)) }}</div>
            @endif
        </div>
        <div>
            <div class="fw-bold text-dark">{{ $p->name }}</div>
            <div class="text-muted mb-1" style="font-size: 11px;">
                Joined: {{ $p->created_at->format('d M Y') }}
            </div>
@php
    $oppeStatus = $p->profile->oppe_status ?? 'auto';
    $isAutoOpen = false;
    $today = now();
    
    if ($today->month == 7 && $today->day >= 1 && $today->day <= 30) {
        $isAutoOpen = true;
    }

    $rkkActive = $p->lisensis->where('status', 'active')->sortByDesc('tgl_terbit')->first();
    if ($rkkActive && $rkkActive->tgl_terbit) {
        $mTerbit = \Carbon\Carbon::parse($rkkActive->tgl_terbit)->month;
        $mAfter = ($mTerbit % 12) + 1;
        if ($today->month == $mTerbit || $today->month == $mAfter) {
            $isAutoOpen = true;
        }
    }

    $isOppeActive = false;
    if ($oppeStatus === 'on') $isOppeActive = true;
    elseif ($oppeStatus === 'off') $isOppeActive = false;
    else $isOppeActive = $isAutoOpen;
@endphp

@if($oppeStatus === 'on')
    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 mt-1" style="font-size: 9px;">
        <i class="bi bi-unlock-fill me-1"></i> OPPE: MANUAL ON
    </span>
@elseif($oppeStatus === 'off')
    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1 mt-1" style="font-size: 9px;">
        <i class="bi bi-x-circle-fill me-1"></i> OPPE: MANUAL OFF
    </span>
@elseif($isAutoOpen)
    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 mt-1" style="font-size: 9px;">
        <i class="bi bi-calendar-check-fill me-1"></i> OPPE: AUTO ON
    </span>
@else
    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1 mt-1" style="font-size: 9px;">
        <i class="bi bi-lock-fill me-1"></i> OPPE: OFF
    </span>
@endif
        </div>
    </div>
</td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-2 text-dark">
                                        <i class="bi bi-person-vcard text-muted" style="font-size: 14px;"></i>
                                        <span style="font-family: monospace;">{{ $p->profile->nik ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 12px;">
                                        <i class="bi bi-whatsapp text-success" style="font-size: 12px;"></i>
                                        <span>{{ $p->profile->no_hp ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-3 py-2">
                                    {{ $p->expired_docs_count }} dokumen expired
                                </span>
                            </td>
                            <td>
@php
    $rek = $p->rekomendasiPk['rekomendasi'] ?? null;
    
    if ($rek) {
        if ($rek['status'] === 'bisa_upgrade') {
            $badgeClass = 'bg-success';
            $textColor = 'text-success';
            $icon = 'bi-rocket-takeoff';
        } elseif ($rek['status'] === 'maksimal') {
            $badgeClass = 'bg-primary';
            $textColor = 'text-primary';
            $icon = 'bi-award';
        } elseif ($rek['status'] === 'stuck_pendidikan') {
            $badgeClass = 'bg-danger';
            $textColor = 'text-danger';
            $icon = 'bi-exclamation-triangle';
        } else {
            $badgeClass = 'bg-warning';
            $textColor = 'text-warning-emphasis';
            $icon = 'bi-hourglass-split';
        }
    }
@endphp

    <div class="d-flex flex-column align-items-start gap-1">
        @if($rek)
            <span class="badge {{ $badgeClass }} bg-opacity-10 {{ $textColor }} border border-{{ str_replace('bg-', '', $badgeClass) }} px-2 py-1" 
                  data-bs-toggle="tooltip" data-bs-html="true" title="{{ strip_tags($rek['pesan']) }}">
                <i class="bi {{ $icon }} me-1"></i> {{ $rek['target_pk'] ?: 'PK Maksimal' }}
            </span>
        @else
            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1">
                <i class="bi bi-dash-circle me-1"></i> Tidak Tersedia
            </span>
        @endif
        
        <small class="text-muted" style="font-size: 10px;">
            Target PK Selanjutnya
        </small>
    </div>
</td>
                            <td class="text-center">
                                <div class="action-group">
<form action="{{ route('admin.perawat.toggle_oppe', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin merubah status akses OPPE untuk perawat ini?');">
    @csrf
    @method('PATCH')
    <button type="submit" 
            class="btn btn-icon {{ $isOppeActive ? 'btn-success text-white shadow-sm' : 'btn-outline-secondary' }}" 
            title="{{ $isOppeActive ? 'Matikan OPPE' : 'Nyalakan OPPE' }}" 
            data-bs-toggle="tooltip">
        <i class="bi {{ $isOppeActive ? 'bi-toggle-on' : 'bi-toggle-off' }} fs-5"></i>
    </button>
</form>
<button type="button" class="btn btn-icon btn-outline-dark" 
        title="Lihat Data Arsip" data-bs-toggle="tooltip"
        onclick="openArsipModal('{{ addslashes($p->name) }}', {{ $p->arsipDokumens ?? '[]' }})">
    <i class="bi bi-archive"></i>
</button>
                                    <a href="{{ route('admin.perawat.show', $p->id) }}"
                                        class="btn btn-icon btn-outline-primary" title="Detail DRH"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                
                                    <a href="{{ route('admin.perawat.sertifikat', $p->id) }}"
                                        class="btn btn-icon btn-outline-success" title="Lihat Sertifikat & Dokumen"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-patch-check"></i>
                                    </a>

                                    <a href="{{ route('admin.perawat.edit', $p->id) }}"
                                        class="btn btn-icon btn-outline-warning" title="Edit Data" data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>
<button type="button" class="btn btn-icon btn-outline-info position-relative" 
        onclick="openChatModal({{ $p->id }}, '{{ $p->name }}')" 
        title="Kirim Pesan" data-bs-toggle="modal" data-bs-target="#chatModal">
    <i class="bi bi-chat-dots"></i>
    @if(isset($p->unread_messages_count) && $p->unread_messages_count > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 8px;">
            {{ $p->unread_messages_count }}
        </span>
    @endif
</button>
                                    <form action="{{ route('admin.perawat.destroy', $p->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data perawat ini beserta seluruh riwayatnya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-outline-danger" title="Hapus"
                                            data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-search display-6 opacity-25"></i>
                                </div>
                                <span class="text-muted small">Data tidak ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $perawat->links('vendor.pagination.diksera') }}
            </div>
        </div>
    </div>

{{-- MODAL EXPORT BY PK & KFK --}}
<div class="modal fade" id="modalExportPk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-success text-white border-0 py-3 px-4">
                <h6 class="modal-title fw-bold mb-0"><i class="bi bi-file-earmark-excel me-2"></i>Export Excel by PK & KFK</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.perawat.export_pk_kfk') }}" method="GET">
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">Filter data perawat berdasarkan jenjang karir klinis dan KFK, kemudian unduh laporannya beserta informasi ruangan (unit kerja).</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Pilih Jenjang PK / BK</label>
                        <select name="filter_pk" class="form-select">
                            <option value="">-- Semua PK / BK --</option>
                            @foreach($allPk as $pk)
                                <option value="{{ $pk }}">{{ $pk }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Pilih KFK</label>
                        <select name="filter_kfk" class="form-select">
                            <option value="">-- Semua KFK --</option>
                            @foreach($allKfk as $kfk)
                                <option value="{{ $kfk }}">{{ $kfk }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="bi bi-download me-1"></i> Unduh Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL LIHAT ARsip --}}
<div class="modal fade" id="modalArsip" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="bi bi-archive me-2"></i> Arsip Dokumen: <span id="arsipTargetName" class="text-warning"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light" style="max-height: 60vh; overflow-y: auto;">
                <div id="arsipBox"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CHAT --}}
<div class="modal fade" id="chatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-chat-custom">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="avatar-chat me-3">
                        <i class="bi bi-person-circle fs-2"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="chatTargetName">Memuat...</h6>
                        <div class="d-flex align-items-center">
                            <span class="online-indicator me-1"></span>
                            <small class="opacity-75" style="font-size: 10px;">Support System</small>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0 bg-chat-canvas">
                <div id="chatBox" class="chat-container p-4"></div>
            </div>

            <div class="modal-footer border-0 p-3 bg-white">
                <form action="{{ route('messages.send') }}" method="POST" class="w-100" id="chatForm">
                    @csrf
                    <input type="hidden" name="receiver_id" id="chat_receiver_id">
                    <div class="input-group shadow-sm" style="border-radius: 30px; overflow: hidden; border: 1px solid #e0e0e0;">
                        <input name="message" id="chatInput" class="form-control border-0 bg-white px-4 py-2" 
                               placeholder="Tulis pesan..." required autocomplete="off">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EXPORT KUSTOM --}}
<div class="modal fade" id="modalExportKustom" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content border-0 shadow-lg ek-modal" style="border-radius: 16px;">
            {{-- display:contents agar body bisa di-scroll & footer selalu terlihat --}}
            <form action="{{ route('admin.perawat.export_custom') }}" method="GET" id="formEksporKustom" style="display: contents;">
                <div class="modal-header bg-success text-white border-0 py-3 px-4">
                    <h6 class="modal-title fw-bold mb-0"><i class="bi bi-file-earmark-excel me-2"></i>Ekspor Excel Kustom</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        {{-- KIRI: KOLOM DATA --}}
                        <div class="col-lg-5">
                            <div class="ek-card h-100">
                                <div class="ek-title"><span class="ek-step">1</span> Kolom data</div>
                                <div class="small text-muted mb-2">Pilih data yang ingin dimasukkan ke Excel.</div>
                                <div class="row g-0">
                                    @foreach ([
                                        'nama' => 'Nama Lengkap', 'nik' => 'NIK', 'nohp' => 'No. WhatsApp', 'ruangan' => 'Ruangan (Unit Kerja)',
                                        'status_kepegawaian' => 'Status Kepegawaian', 'pk' => 'Jenjang PK', 'kfk' => 'KFK', 'str' => 'No. STR & Expired',
                                        'sip' => 'No. SIP & Expired', 'pendidikan' => 'Pendidikan Terakhir', 'alamat' => 'Alamat',
                                    ] as $val => $lbl)
                                        <div class="col-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="kolom[]" value="{{ $val }}" id="col_{{ $val }}"
                                                    {{ in_array($val, ['nama', 'nik', 'nohp']) ? 'checked' : '' }} {!! $val === 'nama' ? 'onclick="return false;"' : '' !!}>
                                                <label class="form-check-label small {{ $val === 'nama' ? 'fw-bold' : '' }}" for="col_{{ $val }}">{{ $lbl }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <hr class="my-3">
                                <div class="ek-title"><i class="bi bi-mortarboard text-success"></i> Ringkasan pelatihan</div>
                                <div class="row g-0">
                                    @foreach (['pelatihan_semua' => 'Daftar Semua Pelatihan', 'pelatihan_jumlah' => 'Jumlah Pelatihan', 'pelatihan_skp' => 'Total SKP Pelatihan'] as $val => $lbl)
                                        <div class="col-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="kolom[]" value="{{ $val }}" id="col_{{ $val }}">
                                                <label class="form-check-label small" for="col_{{ $val }}">{{ $lbl }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- KANAN: PILIH PELATIHAN --}}
                        <div class="col-lg-7">
                            <div class="ek-card h-100">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="ek-title"><span class="ek-step">2</span> Pilih pelatihan <span class="text-muted fw-normal small">(opsional)</span></div>
                                        <div class="small text-muted mb-2">Centang satu atau beberapa pelatihan. Tiap pelatihan menjadi satu kolom berisi <b>Ya (tanggal)</b> atau <b>-</b>.</div>
                                    </div>
                                    <span class="badge rounded-pill text-bg-success" id="jumlahDipilih" style="display: none;"></span>
                                </div>

                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="inputPelatihan" placeholder="Cari pelatihan, mis. BTCLS, PPGD, PPI…" autocomplete="off">
                                    <button type="button" class="btn btn-outline-success" id="btnTambahPelatihan" title="Pakai kata yang diketik sebagai kolom (mencakup semua variasi nama)">
                                        <i class="bi bi-plus-lg"></i> Pakai kata ini
                                    </button>
                                </div>
                                <div class="ek-list" id="daftarPelatihanPilih"></div>
                                <div class="small text-muted mt-1"><i class="bi bi-lightbulb"></i> Tips: ketik <b>btcls</b> lalu <b>Pakai kata ini</b> untuk mencakup semua penulisan (BTCLS 2024, Pelatihan BTCLS, …).</div>

                                <div id="wrapTerpilih" class="mt-3" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-semibold">Pelatihan terpilih</span>
                                        <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none" id="btnHapusSemua">Hapus semua</button>
                                    </div>
                                    <div id="chipPelatihan" class="d-flex flex-wrap gap-1"></div>

                                    <div class="small fw-semibold mt-3 mb-1">Perawat yang diekspor</div>
                                    <div class="ek-seg" role="group">
                                        <input type="radio" class="btn-check" name="filter_pelatihan" id="fp_semua_perawat" value="" checked>
                                        <label class="btn btn-sm btn-outline-secondary" for="fp_semua_perawat"><i class="bi bi-people"></i> Semua perawat</label>
                                        <input type="radio" class="btn-check" name="filter_pelatihan" id="fp_salah_satu" value="salah_satu">
                                        <label class="btn btn-sm btn-outline-success" for="fp_salah_satu"><i class="bi bi-check2"></i> Punya <b>salah satu</b> (atau)</label>
                                        <input type="radio" class="btn-check" name="filter_pelatihan" id="fp_semua" value="semua">
                                        <label class="btn btn-sm btn-outline-success" for="fp_semua"><i class="bi bi-check2-all"></i> Punya <b>semua</b> (dan)</label>
                                    </div>
                                    <div class="small text-muted mt-1" id="ketFilter"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PREVIEW HASIL --}}
                    <div id="wrapPreview" class="ek-card mt-3" style="display: none;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div class="ek-title mb-0"><i class="bi bi-table text-success"></i> Preview <span id="previewInfo" class="text-muted fw-normal small"></span></div>
                            <div class="input-group input-group-sm" style="max-width: 280px;">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="search" class="form-control" id="cariPreview" placeholder="Cari di preview…">
                            </div>
                        </div>
                        <div class="table-responsive border rounded" style="max-height: 340px;">
                            <table class="table table-sm table-striped table-hover mb-0" style="font-size: 12px;">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 1;" id="previewHead"></thead>
                                <tbody id="previewBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-white p-3 d-flex">
                    <button type="button" class="btn btn-outline-success me-auto" id="btnPreviewEkspor"><i class="bi bi-eye me-1"></i> Lihat Preview</button>
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="bi bi-download me-1"></i> Unduh Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .ek-modal .ek-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 16px 18px; }
    .ek-modal .ek-title { font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
    .ek-modal .ek-step { width: 22px; height: 22px; border-radius: 50%; background: #198754; color: #fff; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; }
    .ek-modal .ek-list { border: 1px solid #e5e7eb; border-radius: 10px; max-height: 260px; overflow-y: auto; }
    .ek-modal .ek-item { display: flex; align-items: flex-start; gap: 10px; padding: 7px 12px; border-bottom: 1px solid #f1f5f9; cursor: pointer; font-size: 13px; margin: 0; }
    .ek-modal .ek-item:hover { background: #f0fdf4; }
    .ek-modal .ek-item.dipilih { background: #dcfce7; }
    .ek-modal .ek-item .form-check-input { margin-top: 3px; flex-shrink: 0; }
    .ek-modal .ek-item .nm { flex: 1; line-height: 1.35; }
    .ek-modal .ek-item .jml { flex-shrink: 0; font-size: 11px; color: #475569; background: #f1f5f9; border-radius: 20px; padding: 1px 8px; }
    .ek-modal .ek-chip { display: inline-flex; align-items: center; gap: 6px; max-width: 100%; background: #198754; color: #fff; border-radius: 20px; padding: 4px 6px 4px 12px; font-size: 12px; }
    .ek-modal .ek-chip .t { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 320px; }
    .ek-modal .ek-chip.kata { background: #0f766e; }
    .ek-modal .ek-chip button { border: 0; background: rgba(255,255,255,.2); color: #fff; border-radius: 50%; width: 20px; height: 20px; line-height: 1; padding: 0; flex-shrink: 0; }
    .ek-modal .ek-seg { display: flex; flex-wrap: wrap; gap: 6px; }
    @media (max-width: 576px) { .ek-modal .ek-chip .t { max-width: 200px; } }
</style>

<style>
    .modal-chat-custom { max-width: 500px; }
    .bg-chat-canvas { background-color: #f0f2f5; background-image: url("https://www.transparenttextures.com/patterns/cubes.png"); }
    .chat-container {
        height: 450px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scroll-behavior: smooth;
    }
    .msg-bubble {
        padding: 10px 15px;
        font-size: 13px;
        line-height: 1.5;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .msg-me {
        align-self: flex-end;
        background: #dcf8c6;
        color: #303030;
        border-radius: 15px 15px 2px 15px;
        max-width: 70%;
        padding: 6px 12px;
        font-size: 11.5px;
    }
    .msg-other {
        align-self: flex-start;
        background: #ffffff;
        color: #303030;
        border-radius: 15px 15px 15px 2px;
        max-width: 85%;
        padding: 10px 15px;
        font-size: 13px;
    }
    .msg-time { font-size: 9px; opacity: 0.6; display: block; margin-top: 4px; text-align: right; }
    .online-indicator { width: 8px; height: 8px; background: #4df14d; border-radius: 50%; display: inline-block; }

    @media (max-width: 576px) {
        .modal-chat-custom { margin: 0; max-width: 100%; height: 100%; }
        .modal-chat-custom .modal-content { height: 100vh; border-radius: 0; }
        .chat-container { height: calc(100vh - 140px); }
    }
</style>

<script>
function openChatModal(userId, userName) {
    document.getElementById('chatTargetName').innerText = userName;
    document.getElementById('chat_receiver_id').value = userId;
    const chatBox = document.getElementById('chatBox');
    
    chatBox.innerHTML = `
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span class="ms-2 small text-muted">Memuat percakapan...</span>
        </div>`;

    const fetchUrl = "{{ url('/messages/history') }}/" + userId;

    fetch(fetchUrl)
        .then(res => {
            if (!res.ok) throw new Error('Respon jaringan tidak ok');
            return res.json();
        })
        .then(data => {
            chatBox.innerHTML = '';
            
            if (data.length === 0) {
                chatBox.innerHTML = '<div class="text-center my-auto text-muted small">Belum ada percakapan.<br>Mulai kirim pesan pertama.</div>';
                return;
            }

            data.forEach(msg => {
                const isMe = msg.sender_id == "{{ Auth::id() }}";
                const msgDiv = document.createElement('div');
                msgDiv.className = `msg-bubble ${isMe ? 'msg-me' : 'msg-other'}`;
                
                const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                msgDiv.innerHTML = `
                    <div class="msg-content">${msg.message}</div>
                    <span class="msg-time text-end">${time}</span>
                `;
                chatBox.appendChild(msgDiv);
            });
            
            setTimeout(() => {
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 100);
        })
        .catch(err => {
            console.error('Fetch Error:', err);
            chatBox.innerHTML = `
                <div class="text-center my-auto text-danger small">
                    <i class="bi bi-exclamation-triangle-fill mb-2 d-block fs-4"></i>
                    Gagal memuat pesan.<br>
                    <span style="font-size:10px; opacity:0.7;">Pastikan URL API benar atau periksa koneksi.</span>
                </div>`;
        });
}

function openArsipModal(nama, arsipData) {
    document.getElementById('arsipTargetName').innerText = nama;
    const container = document.getElementById('arsipBox');
    container.innerHTML = ''; 

    if (!arsipData || arsipData.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-folder-x display-4 d-block mb-3 opacity-50"></i>
                Belum ada data arsip dokumen untuk perawat ini.
            </div>`;
        new bootstrap.Modal(document.getElementById('modalArsip')).show();
        return;
    }

    const grouped = arsipData.reduce((acc, curr) => {
        if (!acc[curr.tahun]) acc[curr.tahun] = [];
        acc[curr.tahun].push(curr);
        return acc;
    }, {});

    const years = Object.keys(grouped).sort((a, b) => b - a);

    years.forEach(year => {
        let html = `<h5 class="fw-bold mt-2 mb-3 text-dark border-bottom pb-2"><i class="bi bi-calendar3 me-2 text-primary"></i>Tahun ${year}</h5>`;
        html += `<div class="row g-3 mb-4">`;
        
        grouped[year].forEach(item => {
            let isian = Array.isArray(item.isian_dokumen) ? item.isian_dokumen.join('<br> • ') : '-';
            
            html += `
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted" style="font-size: 12px;">Isian Dokumen:</h6>
                            <p class="card-text small mb-3 text-dark">
                                • ${isian}
                            </p>
                            <a href="${item.link_drive}" target="_blank" class="btn btn-sm btn-primary w-100" style="border-radius: 8px;">
                                <i class="bi bi-link-45deg me-1"></i> Buka Link Drive
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `</div>`;
        container.innerHTML += html;
    });

    new bootstrap.Modal(document.getElementById('modalArsip')).show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    async function exportToExcel() {
        const btn = document.querySelector('button[onclick="exportToExcel()"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyusun Struktur Excel...';

        try {
            const response = await fetch("{{ route('admin.perawat.export') }}");
            if (!response.ok) throw new Error('Gagal mengambil data dari server');
            
            const data = await response.json();
            const safe = (arr) => arr || [];
            
            const masterData = [];
            let no = 1;

            data.forEach(u => {
                const edus = safe(u.pendidikan);
                const works = safe(u.perawatPekerjaans);
                const trains = safe(u.pelatihan);
                const certsStr = safe(u.strs);
                const certsSip = safe(u.sips);
                const fams = safe(u.keluarga);
                
                const maxRows = Math.max(
                    1,
                    edus.length,
                    works.length,
                    trains.length,
                    certsStr.length,
                    certsSip.length,
                    fams.length
                );

                for (let idx = 0; idx < maxRows; idx++) {
                    masterData.push({
                        'NO': idx === 0 ? no : '',
                        'NAMA AKUN': idx === 0 ? (u.name ? u.name.toUpperCase() : '-') : '',
                        'NAMA LENGKAP': idx === 0 ? (u.profile?.nama_lengkap || '-') : '',
                        'NIK': idx === 0 ? (u.profile?.nik ? String(u.profile.nik) + '\t' : '-') : '',
                        'NO. WHATSAPP': idx === 0 ? (u.profile?.no_hp || '-') : '',
                        'ALAMAT': idx === 0 ? (u.profile?.alamat || '-') : '',
                        
                        'PENDIDIKAN JENJANG': edus[idx]?.jenjang || '-',
                        'PENDIDIKAN INSTITUSI': edus[idx]?.nama_institusi || '-',
                        'PENDIDIKAN TAHUN LULUS': edus[idx]?.tahun_lulus || '-',
                        
                        'JABATAN': works[idx]?.jabatan || '-',
                        'STATUS KEPEGAWAIAN': works[idx]?.status_kepegawaian || '-',
                        'UNIT KERJA / INSTANSI': works[idx]?.nama_instansi || works[idx]?.unit_kerja || '-',
                        'PERIODE KERJA': works[idx] ? `${works[idx].tahun_mulai || '-'} s/d ${works[idx].tahun_selesai || 'Sekarang'}` : '-',
                        
                        'NAMA PELATIHAN': trains[idx]?.nama_pelatihan || '-',
                        
                        'NOMOR STR': certsStr[idx]?.nomor || '-',
                        'MASA BERLAKU STR': certsStr[idx]?.tgl_expired || '-',
                        'NOMOR SIP': certsSip[idx]?.nomor || '-',
                        'MASA BERLAKU SIP': certsSip[idx]?.tgl_expired || '-',
                        
                        'NAMA KELUARGA': fams[idx]?.nama || '-',
                        'HUBUNGAN KELUARGA': fams[idx]?.hubungan || '-'
                    });
                }
                no++;
            });

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.json_to_sheet(masterData);

            const colWidths = Object.keys(masterData[0] || {}).map(key => {
                let maxLen = key.length;
                masterData.forEach(row => {
                    const valLen = String(row[key] || '').length;
                    if (valLen > maxLen) maxLen = valLen;
                });
                return { wch: Math.min(Math.max(maxLen + 2, 10), 40) };
            });
            ws['!cols'] = colWidths;

            const rowHeights = new Array(masterData.length + 1).fill({ hpt: 20 });
            rowHeights[0] = { hpt: 28 };
            ws['!rows'] = rowHeights;

            XLSX.utils.book_append_sheet(wb, ws, "Master Data Terstruktur");
            const fileName = `Data_Master_Perawat_Ketata_${new Date().toISOString().slice(0,10)}.xlsx`;
            XLSX.writeFile(wb, fileName);

        } catch (err) {
            console.error(err);
            alert('Terjadi kendala saat mengatur data Excel: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-file-earmark-excel me-1"></i> Semua';
        }
    }
</script>

<script>
    // Ekspor kustom: pilih banyak pelatihan (checkbox) sebagai kolom + filter dan/atau + preview hasil
    (function () {
        const form = document.getElementById('formEksporKustom');
        const input = document.getElementById('inputPelatihan');
        const wadah = document.getElementById('chipPelatihan');
        const daftar = document.getElementById('daftarPelatihanPilih');
        const wrapTerpilih = document.getElementById('wrapTerpilih');
        const badge = document.getElementById('jumlahDipilih');
        const ket = document.getElementById('ketFilter');
        if (!form || !input) return;

        const semuaPelatihan = @json(($daftarPelatihan ?? collect())->map(fn($p) => ['nama' => $p->nama, 'jumlah' => (int) $p->jumlah])->values());
        const MAKS = 20;
        const esc = v => String(v).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const terpilih = () => [...wadah.querySelectorAll('input[type=hidden]')].map(i => i.value);
        const sudahDipilih = v => terpilih().some(x => x.toLowerCase() === v.toLowerCase());

        function perbarui() {
            const n = terpilih().length;
            wrapTerpilih.style.display = n ? '' : 'none';
            badge.style.display = n ? '' : 'none';
            badge.textContent = n + ' dipilih';
            const mode = form.querySelector('input[name=filter_pelatihan]:checked').value;
            ket.textContent = !n ? '' : mode === 'semua'
                ? `Hanya perawat yang memiliki SEMUA ${n} pelatihan terpilih.`
                : mode === 'salah_satu' ? 'Hanya perawat yang memiliki minimal satu pelatihan terpilih.'
                : 'Semua perawat diekspor; kolom pelatihan berisi Ya / -.';
            tampilkanDaftar();
            sembunyikanPreview();
        }

        function tambah(v, kata = false) {
            v = v.trim();
            if (!v || sudahDipilih(v)) return;
            if (terpilih().length >= MAKS) return Swal.fire({ icon: 'info', text: `Maksimal ${MAKS} pelatihan.` });
            const chip = document.createElement('span');
            chip.className = 'ek-chip' + (kata ? ' kata' : '');
            chip.title = kata ? `Semua pelatihan yang mengandung "${v}"` : v;
            chip.innerHTML = `${kata ? '<i class="bi bi-search"></i>' : ''}<span class="t"></span><input type="hidden" name="pelatihan[]"><button type="button" title="Hapus">&times;</button>`;
            chip.querySelector('.t').textContent = kata ? `mengandung "${v}"` : v;
            chip.querySelector('input').value = v;
            chip.querySelector('button').addEventListener('click', () => { chip.remove(); perbarui(); });
            wadah.appendChild(chip);
            perbarui();
        }

        function hapus(v) {
            [...wadah.children].forEach(c => { if (c.querySelector('input').value.toLowerCase() === v.toLowerCase()) c.remove(); });
            perbarui();
        }

        // Daftar pelatihan bercentang, tersaring sesuai ketikan
        function tampilkanDaftar() {
            const q = input.value.trim().toLowerCase();
            const cocok = semuaPelatihan.filter(p => !q || p.nama.toLowerCase().includes(q));
            const tampil = cocok.slice(0, 150);
            const posisi = daftar.scrollTop; // pertahankan posisi scroll saat mencentang
            daftar.innerHTML = tampil.length
                ? tampil.map(p => {
                    const on = sudahDipilih(p.nama);
                    return `<label class="ek-item ${on ? 'dipilih' : ''}"><input type="checkbox" class="form-check-input" data-nama="${esc(p.nama)}" ${on ? 'checked' : ''}>
                        <span class="nm">${esc(p.nama)}</span><span class="jml">${p.jumlah} org</span></label>`;
                }).join('') + (cocok.length > tampil.length ? `<div class="small text-muted px-3 py-2">… ${cocok.length - tampil.length} lainnya, perjelas pencarian</div>` : '')
                : `<div class="small text-muted px-3 py-3">Tidak ada nama pelatihan yang cocok.${q ? ` Klik <b>Pakai kata ini</b> untuk memakai "${esc(input.value.trim())}".` : ''}</div>`;
            daftar.scrollTop = posisi;
        }

        daftar.addEventListener('change', e => {
            const cb = e.target.closest('input[data-nama]');
            if (!cb) return;
            cb.checked ? tambah(cb.dataset.nama) : hapus(cb.dataset.nama);
        });
        document.getElementById('btnTambahPelatihan').addEventListener('click', () => { tambah(input.value, true); input.value = ''; tampilkanDaftar(); });
        input.addEventListener('input', tampilkanDaftar);
        input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); tambah(input.value, true); input.value = ''; tampilkanDaftar(); } });
        document.getElementById('btnHapusSemua').addEventListener('click', () => { wadah.innerHTML = ''; perbarui(); });
        form.querySelectorAll('input[name=filter_pelatihan]').forEach(r => r.addEventListener('change', perbarui));
        tampilkanDaftar();

        // ---------- Preview hasil ----------
        const wrapPreview = document.getElementById('wrapPreview');
        const head = document.getElementById('previewHead');
        const body = document.getElementById('previewBody');
        const info = document.getElementById('previewInfo');
        const cari = document.getElementById('cariPreview');
        let dataPreview = null;

        function sembunyikanPreview() { dataPreview = null; wrapPreview.style.display = 'none'; }

        function renderPreview() {
            if (!dataPreview) return;
            const q = cari.value.trim().toLowerCase();
            const rows = dataPreview.baris.filter(r => !q || r.some(c => String(c).toLowerCase().includes(q)));
            const tampil = rows.slice(0, 300);
            head.innerHTML = '<tr>' + dataPreview.judul.map(j => `<th class="text-nowrap">${esc(j)}</th>`).join('') + '</tr>';
            body.innerHTML = tampil.length
                ? tampil.map(r => '<tr>' + r.map(c => `<td>${esc(c).replace(/\n/g, '<br>')}</td>`).join('') + '</tr>').join('')
                : `<tr><td colspan="${dataPreview.judul.length}" class="text-center text-muted py-3">Tidak ada data yang cocok.</td></tr>`;
            info.textContent = `· ${dataPreview.total} perawat akan diekspor` + (q ? ` · ${rows.length} cocok pencarian` : '')
                + (rows.length > tampil.length ? ` · menampilkan ${tampil.length} baris pertama` : '');
        }

        document.getElementById('btnPreviewEkspor').addEventListener('click', async function () {
            const btn = this, label = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memuat…';
            try {
                const params = new URLSearchParams(new FormData(form));
                params.set('preview', '1');
                const res = await fetch(form.action + '?' + params.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Gagal memuat preview');
                dataPreview = await res.json();
                wrapPreview.style.display = '';
                renderPreview();
                wrapPreview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
            } finally {
                btn.disabled = false;
                btn.innerHTML = label;
            }
        });
        cari.addEventListener('input', renderPreview);
        form.querySelectorAll('input[name="kolom[]"]').forEach(c => c.addEventListener('change', sembunyikanPreview));
    })();
</script>
@endsection