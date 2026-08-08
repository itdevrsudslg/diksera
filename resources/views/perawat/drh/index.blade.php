@extends('layouts.app')

@section('title', 'DRH Perawat – DIKSERA')

@section('content')

    @php
        // Jaga-jaga jika variabel ini belum di-passing dari Controller index()
        $kunjungan = $kunjungan ?? \App\Models\PerawatKunjungan::where('user_id', $user->id)->get();
        $pelatihan = $pelatihan ?? [];
        $pekerjaan = $pekerjaan ?? [];
        $keluarga = $keluarga ?? [];
        $organisasi = $organisasi ?? [];
        $tandajasa = $tandajasa ?? [];
    @endphp

    <div class="container-fluid py-3">

        {{-- SECTION 1: HEADER UTAMA & FOTO --}}
        <div class="dash-card p-4 mb-3">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                {{-- Foto Profil --}}
                <div class="flex-shrink-0">
                    <div class="hero-icon-circle" style="width:100px;height:100px;">
                        @if (!empty($profile->foto_3x4))
                            <img src="{{ asset('storage/' . $profile->foto_3x4) }}" class="hero-icon-img"
                                style="object-fit: cover" alt="Foto Profil">
                        @else
                            <img src="{{ asset('icon.png') }}" class="hero-icon-img" alt="Default Avatar">
                        @endif
                    </div>
                </div>
                {{-- Nama & Info Utama --}}
                <div class="flex-grow-1 text-center text-md-start">
                    <h4 class="fw-bold mb-1">{{ $profile->nama_lengkap ?? $user->name }}</h4>
                    
                    {{-- Jabatan & NIP/NIRP --}}
                    <div class="text-muted mb-2 small">
                        {{ $profile->jabatan ?? 'Jabatan belum diisi' }}
                        <span class="mx-1 text-secondary opacity-50">|</span>
                        @if (!empty($profile->nip))
                            NIP. {{ $profile->nip }}
                        @elseif(!empty($profile->nirp))
                            NIRP. {{ $profile->nirp }}
                        @else
                            -
                        @endif
                    </div>

                    {{-- BADGE INFO (Pangkat & Status Kepegawaian) --}}
                    <div class="d-flex gap-2 justify-content-center justify-content-md-start align-items-center">
                        {{-- Badge Pangkat --}}
                        <div class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10">
                            {{ $profile->pangkat ?? 'Pangkat (-)' }} / {{ $profile->golongan ?? 'Gol (-)' }}
                        </div>

                        {{-- Badge Status Kepegawaian --}}
                        @if(!empty($profile->status_kepegawaian))
                            <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10">
                                {{ $profile->status_kepegawaian }}
                            </div>
                        @else
                            <div class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10">
                                Status (-)
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Tombol Action --}}
                <div class="flex-shrink-0 d-flex gap-2">
                    <a href="{{ route('perawat.data.lengkap') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-text me-1"></i> Data Lengkap
                    </a>
                    <a href="{{ route('perawat.identitas.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <a href="{{ route('perawat.drh.download_pdf') }}" class="btn btn-outline-danger" target="_blank">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh DRH 
                    </a>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SECTION REKOMENDASI PK (UI MODERN)             --}}
        {{-- ============================================== --}}
        @if(isset($rekomendasiPk))
            @php
                $rek = $rekomendasiPk['rekomendasi'];
                
                // Konfigurasi Warna berdasarkan status
                if ($rek['status'] === 'bisa_upgrade') {
                    $bgClass = 'bg-success bg-opacity-10 border-success';
                    $iconClass = 'bi-rocket-takeoff text-success';
                    $titleColor = 'text-success';
                } elseif ($rek['status'] === 'maksimal') {
                    $bgClass = 'bg-primary bg-opacity-10 border-primary';
                    $iconClass = 'bi-award-fill text-primary';
                    $titleColor = 'text-primary';
                } else {
                    $bgClass = 'bg-warning bg-opacity-10 border-warning';
                    $iconClass = 'bi-hourglass-split text-warning';
                    $titleColor = 'text-warning-emphasis';
                }
            @endphp
            
            <div class="card border {{ $bgClass }} shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        
                        {{-- Kiri: Ikon & Pesan Utama --}}
                        <div class="col-md-8">
                            <div class="d-flex align-items-start gap-3 mb-3 mb-md-0">
                                <div class="bg-white p-3 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                                    <i class="bi {{ $iconClass }} fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 {{ $titleColor }} text-uppercase" style="letter-spacing: 0.5px;">
                                        Rekomendasi Kenaikan Klinis
                                    </h6>
                                    <p class="mb-0 text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                                        {!! $rek['pesan'] !!}
                                    </p>
                                    
                                    @if($rek['status'] === 'bisa_upgrade')
                                        <a href="{{ route('perawat.lisensi.create') }}" class="btn btn-success fw-bold px-4 py-2 mt-3 rounded-pill shadow-sm" style="transition: transform 0.2s;">
                                            <i class="bi bi-file-earmark-check me-2"></i> Ajukan {{ $rek['target_pk'] }} Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Kanan: Statistik Ringkas --}}
                        <div class="col-md-4">
                            <div class="bg-white p-3 rounded-4 shadow-sm border border-light">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-muted small fw-medium">PK Terakhir</span>
                                    <span class="badge bg-primary px-2 py-1">{{ $rekomendasiPk['current_pk'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-muted small fw-medium">Pend. Terakhir</span>
                                    <span class="fw-bold text-dark small">{{ $rekomendasiPk['pendidikan_level'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-muted small fw-medium">Masa Kerja ({{ $rekomendasiPk['current_pk'] }})</span>
                                    <span class="fw-bold text-dark small">{{ $rekomendasiPk['tahun_sebagai_pk_current'] }} Tahun</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small fw-medium">SKP Aktif (5 Thn)</span>
                                    <span class="fw-bold text-dark small">
                                        {{ $rekomendasiPk['total_skp_aktif'] }} <span class="text-muted" style="font-size: 0.75rem;">/ {{ $rekomendasiPk['req_skp'] ?: '-' }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================================== --}}
        <div class="row g-3">
            {{-- SECTION 2: DATA PRIBADI & KEPEGAWAIAN --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">
                        <i class="bi bi-person-lines-fill me-2"></i>Data Pribadi & Kepegawaian
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-borderless table-sm small align-middle mb-0">
                            <tr>
                                <td class="text-muted" width="35%">NIK</td>
                                <td class="fw-bold font-monospace">: {{ $profile->nik ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NPWP</td>
                                <td class="fw-bold font-monospace">: {{ $profile->npwp ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Profesi</td>
                                <td class="fw-bold">: {{ $profile->profesi ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td class="fw-bold">: {{ $profile->status_kepegawaian ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    {{ !empty($profile->nip) ? 'NIP' : (!empty($profile->nirp) ? 'NIRP' : 'NIP/NIRP') }}
                                </td>
                                <td class="fw-bold font-monospace">: 
                                    {{ !empty($profile->nip) ? $profile->nip : ($profile->nirp ?? '—') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted align-top pt-2">KFK / Kompetensi</td>
                                <td class="pt-2">
                                    <div class="d-flex flex-wrap gap-1">
                                        @php
                                            $tp = $profile->type_perawat ?? [];
                                            if (is_string($tp)) {
                                                $decoded = json_decode($tp, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $tp = $decoded;
                                                } else {
                                                    $tp = array_map('trim', explode(',', $tp));
                                                }
                                            }
                                        @endphp
                                        @if (!empty($tp) && is_array($tp))
                                            @foreach($tp as $kfk)
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle fw-normal">
                                                    {{ $kfk }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted fw-bold">: —</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            
                            {{-- TAMBAHAN: DATA TMT & MASA KERJA --}}
                            <tr><td colspan="2"><hr class="my-2 text-muted"></td></tr>
                            <tr>
                                <td class="text-muted">TMT CPNS / PNS</td>
                                <td class="fw-bold">: 
                                    {{ optional($profile)->tmt_cpns ? date('d M Y', strtotime($profile->tmt_cpns)) : '-' }} / 
                                    {{ optional($profile)->tmt_pns ? date('d M Y', strtotime($profile->tmt_pns)) : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">TMT Jabatan Saat Ini</td>
                                <td class="fw-bold">: {{ optional($profile)->tmt_jabatan ? date('d M Y', strtotime($profile->tmt_jabatan)) : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Masa Kerja Golongan</td>
                                <td class="fw-bold">: {{ $profile->masa_kerja_tahun ?? '0' }} Tahun, {{ $profile->masa_kerja_bulan ?? '0' }} Bulan</td>
                            </tr>
                            <tr><td colspan="2"><hr class="my-2 text-muted"></td></tr>

                            <tr>
                                <td class="text-muted">Tempat, Tgl Lahir</td>
                                <td class="fw-bold">
                                    : {{ optional($profile)->tempat_lahir ?? '—' }},
                                    {{ optional($profile)->tanggal_lahir ? date('d-m-Y', strtotime($profile->tanggal_lahir)) : '—' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Kelamin</td>
                                <td class="fw-bold">: {{ optional($profile)->jenis_kelamin == 'L' ? 'Laki-laki' : (optional($profile)->jenis_kelamin == 'P' ? 'Perempuan' : '—') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Agama</td>
                                <td class="fw-bold">: {{ $profile->agama ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status Nikah</td>
                                <td class="fw-bold">: {{ $profile->status_perkawinan ?? '—' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: FISIK, KONTAK & SURAT KETERANGAN --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    
                    {{-- Kontak & Alamat --}}
                    <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">
                        <i class="bi bi-geo-alt-fill me-2"></i>Kontak & Alamat
                    </h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-borderless table-sm small align-middle mb-0">
                            <tr>
                                <td class="text-muted" width="35%">No. HP (WA)</td>
                                <td class="fw-bold text-success">: 
                                    @if(optional($profile)->no_hp)
                                        @php
                                            $nomor_bersih = preg_replace('/[^0-9]/', '', $profile->no_hp);
                                            if (substr($nomor_bersih, 0, 1) == '0') $nomor_bersih = '62' . substr($nomor_bersih, 1);
                                        @endphp
                                        <a href="https://wa.me/{{ $nomor_bersih }}" target="_blank" class="text-success text-decoration-none">
                                            <i class="bi bi-whatsapp me-1"></i> {{ $profile->no_hp }}
                                        </a>
                                    @else — @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted align-top">Alamat Lengkap</td>
                                <td class="fw-bold" style="line-height: 1.4;">
                                    : {{ $profile->alamat ?? '—' }} <br>
                                    <span class="text-muted fw-normal ms-2">Kel/Desa:</span> {{ $profile->kelurahan_desa ?? '-' }} <br>
                                    <span class="text-muted fw-normal ms-2">Kecamatan:</span> {{ $profile->kecamatan ?? '-' }} <br>
                                    <span class="text-muted fw-normal ms-2">Kota/Kab:</span> {{ $profile->kota ?? '—' }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Keterangan Badan --}}
                    <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">
                        <i class="bi bi-activity me-2"></i>Keterangan Fisik & Ciri
                    </h6>
                    <div class="row g-2 small mb-4">
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Gol. Darah</span>
                            <strong>{{ $profile->golongan_darah ?? '-' }}</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Tinggi (cm)</span>
                            <strong>{{ $profile->tinggi_badan ?? '-' }} cm</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Berat (kg)</span>
                            <strong>{{ $profile->berat_badan ?? '-' }} kg</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="text-muted d-block">Warna Kulit</span>
                            <strong>{{ $profile->warna_kulit ?? '-' }}</strong>
                        </div>
                        <div class="col-6 col-md-4 mt-2">
                            <span class="text-muted d-block">Rambut / Muka</span>
                            <strong>{{ $profile->rambut ?? '-' }} / {{ $profile->bentuk_muka ?? '-' }}</strong>
                        </div>
                        <div class="col-6 col-md-8 mt-2">
                            <span class="text-muted d-block">Ciri Khas / Cacat</span>
                            <strong>{{ $profile->ciri_khas ?? '-' }} / {{ $profile->cacat_tubuh ?? '-' }}</strong>
                        </div>
                    </div>

                    {{-- Surat Keterangan --}}
                    <h6 class="border-bottom pb-2 mb-3 text-primary fw-bold">
                        <i class="bi bi-file-earmark-check-fill me-2"></i>Surat Keterangan
                    </h6>
                    <div class="row g-2 small mb-2">
                        <div class="col-6">
                            <span class="text-muted d-block">SKCK</span>
                            <strong>{{ $profile->skck_nomor ?? '-' }}</strong> <br>
                            <span class="text-muted">{{ $profile->skck_pejabat ?? '-' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Ket. Sehat Jasmani</span>
                            <strong>{{ $profile->sehat_nomor ?? '-' }}</strong> <br>
                            <span class="text-muted">{{ $profile->sehat_pejabat ?? '-' }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SECTION 4: KUMPULAN TABEL RIWAYAT BKN          --}}
        {{-- ============================================== --}}
        
        <div class="row g-3 mt-1">
            
            {{-- PENDIDIKAN --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Riwayat Pendidikan</h6>
                        <a href="{{ route('perawat.pendidikan.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:12px;">Kelola</a>
                    </div>
                    @if ($pendidikan->isEmpty())
                        <div class="text-center small text-muted py-3">Belum ada riwayat pendidikan.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle small mb-0">
                                <thead class="table-light text-muted">
                                    <tr><th>Jenjang & Institusi</th><th>Lulus</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendidikan->take(3) as $row)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $row->jenjang }} - {{ $row->nama_institusi }}</span><br>
                                                <span class="text-muted" style="font-size:11px;">Jurusan: {{ $row->jurusan ?? '-' }}</span>
                                            </td>
                                            <td>{{ $row->tahun_lulus ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- PELATIHAN --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>Kursus / Pelatihan</h6>
                        <a href="{{ route('perawat.pelatihan.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:12px;">Kelola</a>
                    </div>
                    @if (empty($pelatihan) || count($pelatihan) == 0)
                        <div class="text-center small text-muted py-3">Belum ada riwayat pelatihan.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle small mb-0">
                                <thead class="table-light text-muted">
                                    <tr><th>Nama Pelatihan</th><th>Tahun</th></tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($pelatihan)->take(3) as $row)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $row->nama_pelatihan }}</span><br>
                                                <span class="text-muted" style="font-size:11px;">Oleh: {{ $row->penyelenggara ?? '-' }}</span>
                                            </td>
                                            <td>{{ $row->tanggal_mulai ? date('Y', strtotime($row->tanggal_mulai)) : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- PEKERJAAN --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-briefcase-fill me-2 text-primary"></i>Riwayat Pekerjaan & SK</h6>
                        <a href="{{ route('perawat.pekerjaan.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:12px;">Kelola</a>
                    </div>
                    @if (empty($pekerjaan) || count($pekerjaan) == 0)
                        <div class="text-center small text-muted py-3">Belum ada riwayat pekerjaan.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle small mb-0">
                                <thead class="table-light text-muted">
                                    <tr><th>Instansi & Jabatan</th><th>Tahun</th></tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($pekerjaan)->take(3) as $row)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $row->jabatan }}</span><br>
                                                <span class="text-muted" style="font-size:11px;">{{ $row->nama_instansi }} | Gol: {{ $row->gol_ruang ?? '-' }}</span>
                                            </td>
                                            <td>{{ $row->tahun_mulai ?? '-' }} - {{ $row->tahun_selesai ?? 'Sek.' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- KUNJUNGAN LUAR NEGERI --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-airplane-fill me-2 text-primary"></i>Kunjungan Luar Negeri</h6>
                        <a href="{{ route('perawat.kunjungan.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:12px;">Kelola</a>
                    </div>
                    @if (empty($kunjungan) || count($kunjungan) == 0)
                        <div class="text-center small text-muted py-3">Belum ada riwayat kunjungan ke luar negeri.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle small mb-0">
                                <thead class="table-light text-muted">
                                    <tr><th>Negara & Tujuan</th><th>Lama</th></tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($kunjungan)->take(3) as $row)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $row->negara }}</span><br>
                                                <span class="text-muted" style="font-size:11px;">{{ $row->tujuan_kunjungan }}</span>
                                            </td>
                                            <td>{{ $row->lamanya ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- KELUARGA --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-people-fill me-2 text-primary"></i>Data Keluarga</h6>
                        <a href="{{ route('perawat.keluarga.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:12px;">Kelola</a>
                    </div>
                    @if (empty($keluarga) || count($keluarga) == 0)
                        <div class="text-center small text-muted py-3">Belum ada riwayat keluarga.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle small mb-0">
                                <thead class="table-light text-muted">
                                    <tr><th>Hubungan & Nama</th><th>L/P</th></tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($keluarga)->take(3) as $row)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $row->hubungan }}</span> 
                                                <span class="fw-bold">{{ $row->nama }}</span>
                                            </td>
                                            <td>{{ $row->jenis_kelamin ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ORGANISASI --}}
            <div class="col-lg-6">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Riwayat Organisasi</h6>
                        <a href="{{ route('perawat.organisasi.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:12px;">Kelola</a>
                    </div>
                    @if (empty($organisasi) || count($organisasi) == 0)
                        <div class="text-center small text-muted py-3">Belum ada riwayat organisasi.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle small mb-0">
                                <thead class="table-light text-muted">
                                    <tr><th>Nama Organisasi & Jabatan</th><th>Masa</th></tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($organisasi)->take(3) as $row)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $row->nama_organisasi }}</span><br>
                                                <span class="text-muted" style="font-size:11px;">Jabatan: {{ $row->jabatan }}</span>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $row->masa_organisasi ?? '-' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- TANDA JASA --}}
            <div class="col-lg-12">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-trophy-fill me-2 text-primary"></i>Tanda Jasa / Penghargaan</h6>
                        <a href="{{ route('perawat.tandajasa.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:12px;">Kelola</a>
                    </div>
                    @if (empty($tandajasa) || count($tandajasa) == 0)
                        <div class="text-center small text-muted py-3">Belum ada riwayat penghargaan.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle small mb-0">
                                <thead class="table-light text-muted">
                                    <tr><th>Nama Bintang / Penghargaan</th><th>Instansi Pemberi</th><th>Tahun</th></tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($tandajasa)->take(3) as $row)
                                        <tr>
                                            <td class="fw-bold">{{ $row->nama_penghargaan }}</td>
                                            <td>{{ $row->instansi_pemberi ?? '-' }}</td>
                                            <td>{{ $row->tahun ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection

@push('styles')
    <style>
        .dash-card {
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(209, 213, 219, 0.8);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            backdrop-filter: blur(10px);
            transition: transform 0.2s;
        }

        .hero-icon-circle {
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #f8fafc, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 3px solid #fff;
        }

        .hero-icon-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .table-borderless td {
            padding-top: 4px;
            padding-bottom: 4px;
        }
    </style>
@endpush