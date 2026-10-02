@extends('layouts.app')

@php
    $pageTitle = 'Riwayat Kredensialing';
    $pageSubtitle = 'Timeline pengajuan UKOM / kredensialing ' . $user->name;
@endphp

@section('title', 'Riwayat Kredensialing – Admin DIKSERA')

@push('styles')
    <style>
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
        }

        .avatar-sm {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            overflow: hidden;
            background: var(--blue-soft);
            color: var(--blue-main);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .avatar-sm img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pengajuan-head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding-bottom: 16px;
            margin-bottom: 20px;
            border-bottom: 1px dashed var(--border-soft);
        }

        .pengajuan-head .meta {
            font-size: 12px;
            color: var(--text-muted);
        }

        .badge-soft {
            font-size: 11px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .badge-soft.success { background: #dcfce7; color: #166534; }
        .badge-soft.danger { background: #fee2e2; color: #991b1b; }
        .badge-soft.warning { background: #fef3c7; color: #92400e; }
        .badge-soft.info { background: #e0f2fe; color: #075985; }
        .badge-soft.secondary { background: #f1f5f9; color: #475569; }

        /* Timeline */
        .tl {
            list-style: none;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .tl-item {
            position: relative;
            padding: 0 0 22px 52px;
        }

        .tl-item:last-child {
            padding-bottom: 0;
        }

        .tl-item::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 36px;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .tl-item:last-child::before {
            display: none;
        }

        .tl-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            border: 2px solid transparent;
        }

        .tl-item.done .tl-dot { background: #dcfce7; color: #16a34a; }
        .tl-item.done::before { background: #86efac; }
        .tl-item.failed .tl-dot { background: #fee2e2; color: #dc2626; }
        .tl-item.current .tl-dot { background: #e0f2fe; color: #0284c7; border-color: #7dd3fc; animation: tl-pulse 1.8s infinite; }
        .tl-item.todo .tl-dot,
        .tl-item.skip .tl-dot { background: #f8fafc; color: #94a3b8; border: 2px dashed #cbd5e1; }
        .tl-item.todo .tl-title,
        .tl-item.skip .tl-title { color: #94a3b8; }
        .tl-item.skip .tl-title { text-decoration: line-through; }

        @keyframes tl-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(2, 132, 199, .35); }
            50% { box-shadow: 0 0 0 6px rgba(2, 132, 199, 0); }
        }

        .tl-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.3;
            padding-top: 2px;
        }

        .tl-time {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .tl-desc {
            font-size: 12.5px;
            color: #475569;
            margin-top: 4px;
            word-wrap: break-word;
        }

        .empty-state {
            text-align: center;
            padding: 48px 16px;
            color: var(--text-muted);
        }
    </style>
@endpush

@section('content')

    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        <a href="{{ route('admin.perawat.show', $user->id) }}" class="btn btn-sm btn-outline-secondary px-3"
            style="border-radius: 8px; font-size: 12px;">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Perawat
        </a>
    </div>

    {{-- HEADER PESERTA --}}
    <div class="content-card mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-sm">
                @if ($profile && $profile->foto_3x4)
                    <img src="{{ asset('storage/' . $profile->foto_3x4) }}" alt="Foto">
                @else
                    {{ substr($user->name, 0, 1) }}
                @endif
            </div>
            <div class="flex-grow-1" style="min-width: 0;">
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <div class="small text-muted">
                    {{ $profile->nik ?? '' }}{{ $profile && $profile->nik ? ' · ' : '' }}{{ $user->email }}
                </div>
            </div>
            <div class="text-end">
                <div class="fw-bold fs-4 lh-1">{{ $riwayat->count() }}</div>
                <div class="text-muted" style="font-size: 11px;">PENGAJUAN</div>
            </div>
        </div>
    </div>

    @forelse ($riwayat as $r)
        @php $p = $r['pengajuan']; @endphp
        <div class="content-card mb-4">
            <div class="pengajuan-head">
                <div>
                    <div class="fw-bold">
                        <i class="bi bi-journal-medical text-primary me-1"></i>
                        Pengajuan {{ ucfirst($p->jenis_pengajuan ?? '') }}
                        <span class="text-muted fw-normal">#{{ $p->id }}</span>
                    </div>
                    <div class="meta">
                        Diajukan {{ $p->created_at->translatedFormat('d M Y') }} · Metode: {{ $r['metode'] }}
                        @php $kfkText = collect((array) $p->kfk)->flatten()->filter(fn($v) => is_scalar($v) && $v !== '')->implode(', '); @endphp
                        @if ($kfkText)
                            · {{ $kfkText }}
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-soft {{ $r['status'][1] }}">{{ $r['status'][0] }}</span>
                    <a href="{{ route('admin.pengajuan.show', $p->id) }}" class="btn btn-sm btn-light border"
                        style="border-radius: 8px; font-size: 12px;" title="Buka detail pengajuan">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>

            <ul class="tl">
                @foreach ($r['steps'] as $s)
                    <li class="tl-item {{ $s['state'] }}">
                        <div class="tl-dot"><i class="bi {{ $s['icon'] }}"></i></div>
                        <div class="tl-title">{{ $s['title'] }}</div>
                        @if ($s['time'])
                            <div class="tl-time">
                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($s['time'])->translatedFormat('d M Y, H:i') }}
                            </div>
                        @elseif ($s['state'] === 'current')
                            <div class="tl-time text-primary fw-semibold">Tahap saat ini</div>
                        @endif
                        @if (!empty($s['desc']))
                            <div class="tl-desc">{{ $s['desc'] }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @empty
        <div class="content-card">
            <div class="empty-state">
                <i class="bi bi-hourglass display-5 d-block mb-2 opacity-50"></i>
                Peserta ini belum pernah mengajukan UKOM / kredensialing.
            </div>
        </div>
    @endforelse

@endsection
