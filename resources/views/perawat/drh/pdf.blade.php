<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Riwayat Hidup BKN</title>
    <style>
        @page { margin: 1.5cm 1.5cm; }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
        }

        /* --- HALAMAN SAMPUL (COVER) --- */
        .cover-page {
            text-align: center;
            padding-top: 40px;
        }
        .cover-title {
            font-size: 22pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 80px;
        }
        .cover-bars-container {
            margin-bottom: 70px;
        }
        .cover-bar-wrapper {
            display: inline-block;
            border: 3px solid black;
            padding: 3px;
            margin: 0 15px;
            border-radius: 8px;
        }
        .cover-bar-inner {
            width: 32px;
            height: 280px;
            background-color: #c0c0c0;
            border: 2px solid black;
            border-radius: 5px;
        }
        .cover-photo-box {
            display: inline-block;
            width: 3cm;
            height: 4cm;
            border: 3px solid black;
            margin-bottom: 60px;
            overflow: hidden;
        }
        .cover-footer {
            font-size: 12pt;
            font-weight: bold;
            line-height: 1.5;
        }

        /* --- HALAMAN ISI --- */
        .header-text {
            text-align: left;
            font-size: 10pt;
            font-weight: bold;
            float: right;
            width: 320px;
            line-height: 1.2;
        }
        .content-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            clear: both;
            padding-top: 50px;
            margin-bottom: 20px;
        }

        /* --- TABEL --- */
        table.bkn-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.bkn-table th, table.bkn-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        table.bkn-table th { font-weight: bold; text-align: center; vertical-align: middle; }
        
        .numbering-row td {
            font-weight: bold;
            text-align: center;
            background-color: #fff;
        }

        table.no-border { width: 100%; border-collapse: collapse; border: none; }
        table.no-border td { border: none; vertical-align: top; padding: 2px; }

        .section-title {
            font-weight: bold;
            font-size: 10pt;
            margin-top: 20px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .text-center { text-align: center; }
        .page-break { page-break-after: always; }
        
        /* Lebar Kolom */
        .w-no { width: 4%; text-align: center; font-weight: bold; }
        .w-label { width: 33%; font-weight: bold; }

        /* Mencegah tabel terpotong di tengah baris */
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

    @php
        // Konfigurasi Lokasi & Waktu
        \Carbon\Carbon::setLocale('id');
        $waktuUnduh = \Carbon\Carbon::now('Asia/Jakarta');
        $tanggalCetak = $waktuUnduh->translatedFormat('d F Y');
        $waktuCetakFull = $waktuUnduh->translatedFormat('d-m-Y H:i:s');
        $namaPegawai = strtoupper($profile->nama_lengkap ?? $user->name);
    @endphp

    {{-- ============================================== --}}
    {{-- HALAMAN 1 : SAMPUL (COVER) --}}
    {{-- ============================================== --}}
    <div class="cover-page">
        <div class="cover-title">DAFTAR RIWAYAT HIDUP</div>
        
        <div class="cover-bars-container">
            <div class="cover-bar-wrapper"><div class="cover-bar-inner"></div></div>
            <div class="cover-bar-wrapper"><div class="cover-bar-inner"></div></div>
            <div class="cover-bar-wrapper"><div class="cover-bar-inner"></div></div>
        </div>

        <div class="cover-photo-box">
            @if(!empty($profile->foto_3x4) && file_exists(public_path('storage/' . $profile->foto_3x4)))
                <img src="{{ public_path('storage/' . $profile->foto_3x4) }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div style="margin-top: 1.5cm; font-size: 10pt; text-align: center;">Pas Foto<br>3 x 4</div>
            @endif
        </div>

        <div class="cover-footer">
            BERDASARKAN KEPUTUSAN KEPALA BKN<br>
            NOMOR : 11 TAHUN 2012<br>
            TANGGAL : 17 JUNI 2012
        </div>
    </div>

    <div class="page-break"></div>

    {{-- ============================================== --}}
    {{-- HALAMAN 2 : ISI DRH --}}
    {{-- ============================================== --}}
    
    <div class="header-text">
        KEPUTUSAN KEPALA BADAN KEPEGAWAIAN<br>
        NEGARA<br>
        NOMOR &nbsp;&nbsp;&nbsp;&nbsp;: 11 TAHUN 2012<br>
        TANGGAL : 17 JUNI 2012
    </div>

    <div class="content-title">DAFTAR RIWAYAT HIDUP</div>

    <!-- I. KETERANGAN PERORANGAN -->
    <div class="section-title">I. KETERANGAN PERORANGAN</div>
    <table class="bkn-table">
        <tr>
            <td class="w-no">1</td><td class="w-label">Nama Lengkap</td>
            <td>{{ $namaPegawai }}</td>
        </tr>
        <tr>
            <td class="w-no">2</td><td class="w-label">NIP / NIRP</td>
            <td>{{ $profile->nip ?? ($profile->nirp ?? '-') }}</td>
        </tr>
        <tr>
            <td class="w-no">3</td><td class="w-label">Pangkat dan Golongan Ruang</td>
            <td>{{ $profile->pangkat ?? '-' }} / {{ $profile->golongan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-no">4</td><td class="w-label">Tempat/ Tanggal Lahir</td>
            <td>{{ strtoupper($profile->tempat_lahir ?? '-') }} / {{ $profile->tanggal_lahir ? date('d-m-Y', strtotime($profile->tanggal_lahir)) : '-' }}</td>
        </tr>
        <tr>
            <td class="w-no">5</td><td class="w-label">Jenis Kelamin</td>
            <td>{{ ($profile->jenis_kelamin ?? '') == 'L' ? 'Pria' : (($profile->jenis_kelamin ?? '') == 'P' ? 'Wanita' : '-') }}</td>
        </tr>
        <tr>
            <td class="w-no">6</td><td class="w-label">Agama</td>
            <td>{{ $profile->agama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="w-no">7</td><td class="w-label">Status Perkawinan</td>
            <td>{{ $profile->status_perkawinan ?? '-' }}</td>
        </tr>

        {{-- Alamat Rumah Baru --}}
        <tr>
            <td class="w-no" rowspan="5">8</td>
            <td class="w-label" style="border-bottom: 0;">Alamat Rumah</td>
            <td style="border-bottom: 0;"></td>
        </tr>
        <tr>
            <td style="border-top: 0; border-bottom: 0;">&nbsp;&nbsp;&nbsp;&nbsp;a. Jalan / RT RW</td>
            <td style="border-top: 1px solid #000;">{{ $profile->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border-top: 0; border-bottom: 0;">&nbsp;&nbsp;&nbsp;&nbsp;b. Kelurahan / Desa</td>
            <td>{{ $profile->kelurahan_desa ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border-top: 0; border-bottom: 0;">&nbsp;&nbsp;&nbsp;&nbsp;c. Kecamatan</td>
            <td>{{ $profile->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border-top: 0;">&nbsp;&nbsp;&nbsp;&nbsp;d. Kabupaten / Kota</td>
            <td>{{ $profile->kota ?? '-' }}</td>
        </tr>

        {{-- Keterangan Badan --}}
        <tr>
            <td class="w-no" rowspan="8">9</td>
            <td class="w-label" style="border-bottom: 0;">Keterangan Badan</td>
            <td style="border-bottom: 0;"></td>
        </tr>
        <tr>
            <td style="border-top: 0; border-bottom: 0;">&nbsp;&nbsp;&nbsp;&nbsp;a. Tinggi (Cm)</td>
            <td style="border-top: 1px solid #000;">{{ $profile->tinggi_badan ?? '-' }} Cm</td>
        </tr>
        <tr>
            <td style="border-top: 0; border-bottom: 0;">&nbsp;&nbsp;&nbsp;&nbsp;b. Berat Badan (Kg)</td>
            <td>{{ $profile->berat_badan ?? '-' }} Kg</td>
        </tr>
        <tr>
            <td style="border-top: 0; border-bottom: 0;">&nbsp;&nbsp;&nbsp;&nbsp;c. Rambut</td>
            <td>{{ $profile->rambut ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border-top: 0; border-bottom: 0;">&nbsp;&nbsp;&nbsp;&nbsp;d. Bentuk Muka</td>
            <td>{{ $profile->bentuk_muka ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border-top: 0; border-bottom: 0;">&nbsp;&nbsp;&nbsp;&nbsp;e. Warna Kulit</td>
            <td>{{ $profile->warna_kulit ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border-top: 0; border-bottom: 0;">&nbsp;&nbsp;&nbsp;&nbsp;f. Ciri-Ciri Khas</td>
            <td>{{ $profile->ciri_khas ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border-top: 0;">&nbsp;&nbsp;&nbsp;&nbsp;g. Cacat Tubuh</td>
            <td>{{ $profile->cacat_tubuh ?? 'Tidak Ada' }}</td>
        </tr>

        <tr><td class="w-no">10</td><td class="w-label">Kegemaran (Hobby)</td><td>{{ strtoupper($profile->hobby ?? '-') }}</td></tr>
        <tr><td class="w-no">11</td><td class="w-label">No. HP</td><td>{{ $profile->no_hp ?? '-' }}</td></tr>
        <tr><td class="w-no">12</td><td class="w-label">NIK</td><td>{{ $profile->nik ?? '-' }}</td></tr>
        <tr><td class="w-no">13</td><td class="w-label">NPWP</td><td>{{ $profile->npwp ?? '-' }}</td></tr>
        <tr><td class="w-no">14</td><td class="w-label">Email</td><td>{{ $profile->email ?? $user->email }}</td></tr>
    </table>

    <div class="page-break"></div>

    <!-- II. KEPEGAWAIAN -->
    <div class="section-title">II. KEPEGAWAIAN</div>
    <table class="bkn-table">
        <tr><td class="w-no">1</td><td class="w-label">TMT CPNS</td><td>{{ $profile->tmt_cpns ? date('d-m-Y', strtotime($profile->tmt_cpns)) : '-' }}</td></tr>
        <tr><td class="w-no">2</td><td class="w-label">TMT PNS / PPPK</td><td>{{ $profile->tmt_pns ? date('d-m-Y', strtotime($profile->tmt_pns)) : '-' }}</td></tr>
        <tr><td class="w-no">3</td><td class="w-label">Pendidikan Terakhir</td><td>{{ $pendidikan->first()->jenjang ?? '-' }} {{ $pendidikan->first()->jurusan ?? '' }}</td></tr>
        <tr><td class="w-no">4</td><td class="w-label">Jabatan Saat Ini</td><td>{{ $profile->jabatan ?? '-' }}</td></tr>
        <tr><td class="w-no">5</td><td class="w-label">TMT Jabatan Saat Ini</td><td>{{ $profile->tmt_jabatan ? date('d-m-Y', strtotime($profile->tmt_jabatan)) : '-' }}</td></tr>
        <tr><td class="w-no">6</td><td class="w-label">Masa Kerja Golongan</td><td>{{ $profile->masa_kerja_tahun ?? '0' }} Tahun, {{ $profile->masa_kerja_bulan ?? '0' }} Bulan</td></tr>
        
        {{-- Tampilkan Semua STR --}}
        <tr>
            <td class="w-no">7</td><td class="w-label">Nomor STR</td>
            <td>{!! $str->isEmpty() ? '-' : implode('<br>', $str->pluck('nomor')->toArray()) !!}</td>
        </tr>
        <tr>
            <td class="w-no">8</td><td class="w-label">Tanggal STR</td>
            <td>{!! $str->isEmpty() ? '-' : implode('<br>', $str->map(fn($s) => date('d-m-Y', strtotime($s->tgl_terbit)))->toArray()) !!}</td>
        </tr>
        <tr>
            <td class="w-no">9</td><td class="w-label">Masa Berlaku STR</td>
            <td>{!! $str->isEmpty() ? '-' : implode('<br>', $str->map(fn($s) => $s->tgl_expired ? date('d-m-Y', strtotime($s->tgl_expired)) : 'Seumur Hidup')->toArray()) !!}</td>
        </tr>

        {{-- Tampilkan Semua SIP --}}
        <tr>
            <td class="w-no">10</td><td class="w-label">Nomor SIP</td>
            <td>{!! $sip->isEmpty() ? '-' : implode('<br>', $sip->pluck('nomor')->toArray()) !!}</td>
        </tr>
        <tr>
            <td class="w-no">11</td><td class="w-label">Tanggal SIP</td>
            <td>{!! $sip->isEmpty() ? '-' : implode('<br>', $sip->map(fn($s) => date('d-m-Y', strtotime($s->tgl_terbit)))->toArray()) !!}</td>
        </tr>
        <tr>
            <td class="w-no">12</td><td class="w-label">Masa Berlaku SIP</td>
            <td>{!! $sip->isEmpty() ? '-' : implode('<br>', $sip->map(fn($s) => $s->tgl_expired ? date('d-m-Y', strtotime($s->tgl_expired)) : 'Seumur Hidup')->toArray()) !!}</td>
        </tr>
    </table>

    <!-- III. PENDIDIKAN -->
    <div class="section-title">III. PENDIDIKAN</div>
    <strong>1. Pendidikan di Dalam dan di Luar Negeri</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th>Tingkat</th>
                <th>Nama Pendidikan</th>
                <th>Jurusan</th>
                <th>STTB/ Tanda<br>Lulus /<br>Ijasah Tahun</th>
                <th>Tempat</th>
                <th>Nama Kepala<br>Sekolah/Direktur/<br>Dekan/Promotor</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td>
            </tr>
        </thead>
        <tbody>
            @php
                // AUTO MAPPING PENDIDIKAN
                $mappedEdu = [];
                foreach($pendidikan as $ed) {
                    $j = strtoupper(trim($ed->jenjang));
                    $bknLvl = $j;
                    
                    if (in_array($j, ['SD', 'SEKOLAH DASAR', 'MI'])) $bknLvl = 'SD';
                    elseif (in_array($j, ['SMP', 'SLTP', 'MTS'])) $bknLvl = 'SLTP';
                    elseif (in_array($j, ['SMA', 'SLTA', 'SMK', 'MA', 'STM', 'SMEA'])) $bknLvl = 'SLTA';
                    elseif (in_array($j, ['D1', 'D 1', 'DI', 'D I'])) $bknLvl = 'DI';
                    elseif (in_array($j, ['D2', 'D 2', 'DII', 'D II'])) $bknLvl = 'DII';
                    elseif (in_array($j, ['D3', 'D 3', 'DIII', 'D III', 'DIPLOMA 3', 'DIPLOMA III'])) $bknLvl = 'DIII';
                    elseif (in_array($j, ['D4', 'D 4', 'DIV', 'D IV', 'DIPLOMA 4', 'DIPLOMA IV'])) $bknLvl = 'DIV';
                    elseif (in_array($j, ['S1', 'S 1', 'STRATA 1', 'SARJANA'])) $bknLvl = 'S1';
                    elseif (in_array($j, ['S2', 'S 2', 'STRATA 2', 'MAGISTER'])) $bknLvl = 'S2';
                    elseif (in_array($j, ['S3', 'S 3', 'STRATA 3', 'DOKTOR'])) $bknLvl = 'S3';
                    elseif (in_array($j, ['PROFESI', 'NERS', 'NS', 'BDN', 'PROFESI S1'])) $bknLvl = 'Profesi S1';
                    elseif (in_array($j, ['SPESIALIS 1', 'SPESIALIS I', 'SP 1'])) $bknLvl = 'Spesialis I';
                    elseif (in_array($j, ['SPESIALIS 2', 'SPESIALIS II', 'SP 2'])) $bknLvl = 'Spesialis II';
                    
                    $mappedEdu[$bknLvl] = $ed;
                }

                $tingkatBkn = ['SD', 'SLTP', 'SLTA', 'DI', 'DII', 'DIII', 'DIV', 'S1', 'S2', 'S3', 'Spesialis I', 'Spesialis II', 'Profesi S1'];
            @endphp

            @foreach($tingkatBkn as $index => $tingkat)
                @php $edu = $mappedEdu[$tingkat] ?? null; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $tingkat }}</td>
                    <td>{{ $edu->nama_institusi ?? '' }}</td>
                    <td>{{ $edu->jurusan ?? '' }}</td>
                    <td class="text-center">{{ $edu->tahun_lulus ?? '' }}</td>
                    <td>{{ $edu->tempat ?? '' }}</td>
                    <td>{{ $edu->nama_kepala_institusi ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <strong>2. Kursus / Latihan di Dalam dan di Luar Negeri</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th>Nama/Kursus/Latihan</th>
                <th>Lamanya<br>Tgl/Bln/Thn s/d<br>Tgl/Bln/Thn</th>
                <th>Ijasah /<br>Tanda Lulus /<br>Surat<br>Keterangan<br>Tahun</th>
                <th>Tempat</th>
                <th>Ket.</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
            </tr>
        </thead>
        <tbody>
            @foreach($pelatihan as $index => $train)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $train->nama_pelatihan }}</td>
                    <td class="text-center">
                        {{ $train->tanggal_mulai ? date('d/m/Y', strtotime($train->tanggal_mulai)) : '-' }} s/d<br>
                        {{ $train->tanggal_selesai ? date('d/m/Y', strtotime($train->tanggal_selesai)) : '-' }}
                    </td>
                    <td>{{ $train->durasi ?? '-' }}</td>
                    <td>{{ $train->tempat ?? '-' }}</td>
                    <td>{{ $train->penyelenggara ?? '-' }}</td>
                </tr>
            @endforeach
            @for($i = count($pelatihan); $i < 5; $i++)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- IV. RIWAYAT PEKERJAAN -->
    <div class="section-title">IV. RIWAYAT PEKERJAAN</div>
    
    <strong>1. Riwayat Kepangkatan Golongan Ruang Penggajian</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th rowspan="2" style="width:3%;">No</th>
                <th rowspan="2">Pangkat</th>
                <th rowspan="2">Gol Ruang<br>Penggajian</th>
                <th rowspan="2">Berlaku<br>Terhitung<br>mulai tgl</th>
                <th rowspan="2">Gaji<br>Pokok</th>
                <th colspan="3">Surat Keputusan</th>
                <th rowspan="2">Peraturan<br>yang<br>dijadikan<br>Dasar</th>
            </tr>
            <tr>
                <th>Pejabat</th>
                <th>Nomor</th>
                <th>Tanggal</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td>
            </tr>
        </thead>
        <tbody>
            @foreach($pekerjaan as $index => $job)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $job->pangkat ?? '-' }}</td>
                <td class="text-center">{{ $job->gol_ruang ?? '-' }}</td>
                <td class="text-center">{{ $job->tgl_mulai ? date('d-m-Y', strtotime($job->tgl_mulai)) : ($job->tahun_mulai ?? '-') }}</td>
                <td>{{ $job->gaji_pokok ?? '-' }}</td>
                <td>{{ $job->pejabat_sk ?? '-' }}</td>
                <td>{{ $job->nomor_sk ?? '-' }}</td>
                <td class="text-center">{{ $job->tanggal_sk ? date('d-m-Y', strtotime($job->tanggal_sk)) : '-' }}</td>
                <td>{{ $job->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
            @for($i = count($pekerjaan); $i < 3; $i++)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <strong>2. Pengalaman Jabatan / Pekerjaan</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th rowspan="2" style="width:3%;">No</th>
                <th rowspan="2">Jabatan /<br>Pekerjaan</th>
                <th rowspan="2">Mulai dan<br>Sampai</th>
                <th rowspan="2">Gol Ruang<br>Penggajian</th>
                <th rowspan="2">Gaji<br>Pokok</th>
                <th colspan="3">Surat Keputusan</th>
            </tr>
            <tr>
                <th>Pejabat</th>
                <th>Nomor</th>
                <th>Tanggal</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td>
            </tr>
        </thead>
        <tbody>
            @foreach($pekerjaan as $index => $job)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $job->jabatan }} di {{ $job->nama_instansi }}</td>
                <td class="text-center">{{ $job->tahun_mulai ?? '-' }} s/d {{ $job->tahun_selesai ?? 'Sek.' }}</td>
                <td class="text-center">{{ $job->gol_ruang ?? '-' }}</td>
                <td>{{ $job->gaji_pokok ?? '-' }}</td>
                <td>{{ $job->pejabat_sk ?? '-' }}</td>
                <td>{{ $job->nomor_sk ?? '-' }}</td>
                <td class="text-center">{{ $job->tanggal_sk ? date('d-m-Y', strtotime($job->tanggal_sk)) : '-' }}</td>
            </tr>
            @endforeach
            @for($i = count($pekerjaan); $i < 3; $i++)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <!-- V. TANDA JASA/PENGHARGAAN -->
    <div class="section-title">V. TANDA JASA / PENGHARGAAN</div>
    <table class="bkn-table">
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th>Nama Bintang / Satya Lencana /<br>Penghargaan</th>
                <th>Tahun Perolehan</th>
                <th>Nama Negara / Instansi<br>yang member</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td>
            </tr>
        </thead>
        <tbody>
            @foreach($tandajasa as $index => $tj)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $tj->nama_penghargaan }}<br><small>No SK: {{ $tj->nomor_sk ?? '-' }}</small></td>
                <td class="text-center">{{ $tj->tahun }}</td>
                <td>{{ $tj->instansi_pemberi }}</td>
            </tr>
            @endforeach
            @for($i = count($tandajasa); $i < 3; $i++)
                <tr><td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <!-- VI. PENGALAMAN (Kunjungan ke Luar Negeri) -->
    <div class="section-title">VI. PENGALAMAN</div>
    <strong>1. Kunjungan ke Luar Negeri</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th>Negara</th>
                <th>Tujuan<br>Kunjungan</th>
                <th>Lamanya</th>
                <th>Yang Membiayai</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td>
            </tr>
        </thead>
        <tbody>
            @forelse($kunjungan as $index => $k)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $k->negara }}</td>
                    <td>{{ $k->tujuan_kunjungan }}</td>
                    <td>{{ $k->lamanya }}</td>
                    <td>{{ $k->yang_membiayai }}</td>
                </tr>
            @empty
                <tr><td class="text-center">1</td><td></td><td></td><td></td><td></td></tr>
                <tr><td class="text-center">2</td><td></td><td></td><td></td><td></td></tr>
            @endforelse
            
            @for($i = count($kunjungan); $i < 2; $i++)
                <tr><td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- VII. KETERANGAN KELUARGA -->
    @php
        $istriSuami = $keluarga->filter(fn($k) => in_array(strtolower($k->hubungan), ['suami', 'istri']))->values();
        $anak = $keluarga->filter(fn($k) => strtolower($k->hubungan) == 'anak')->values();
        $ortu = $keluarga->filter(fn($k) => in_array(strtolower($k->hubungan), ['ayah', 'ibu', 'bapak']))->values();
        $mertua = $keluarga->filter(fn($k) => in_array(strtolower($k->hubungan), ['mertua', 'ayah mertua', 'ibu mertua']))->values();
        $saudara = $keluarga->filter(fn($k) => in_array(strtolower($k->hubungan), ['saudara', 'kakak', 'adik']))->values();
    @endphp

    <div class="section-title">VII. KETERANGAN KELUARGA</div>
    
    <strong>1. Istri / Suami</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th class="w-no">No</th><th>Nama</th><th>Tempat<br>Lahir</th><th>Tanggal<br>Lahir</th><th>Tanggal<br>Nikah</th><th>Pekerjaan</th><th>Ket.</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td>
            </tr>
        </thead>
        <tbody>
            @foreach($istriSuami as $index => $is)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $is->nama }}</td>
                    <td>{{ $is->tempat_lahir ?? '-' }}</td>
                    <td class="text-center">{{ $is->tanggal_lahir ? date('d-m-Y', strtotime($is->tanggal_lahir)) : '' }}</td>
                    <td class="text-center">{{ $is->tanggal_nikah ? date('d-m-Y', strtotime($is->tanggal_nikah)) : '-' }}</td>
                    <td>{{ $is->pekerjaan }}</td>
                    <td>{{ $is->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
            @for($i = count($istriSuami); $i < 2; $i++)
                <tr><td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <strong>2. Anak</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th class="w-no">No</th><th>Nama</th><th>Jenis<br>Kelamin</th><th>Tempat<br>Lahir</th><th>Tanggal<br>Lahir</th><th>Pekerjaan</th><th>Ket.</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td>
            </tr>
        </thead>
        <tbody>
            @foreach($anak as $index => $ch)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $ch->nama }}</td>
                    <td class="text-center">{{ $ch->jenis_kelamin == 'L' ? 'Laki-laki' : ($ch->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                    <td>{{ $ch->tempat_lahir ?? '-' }}</td>
                    <td class="text-center">{{ $ch->tanggal_lahir ? date('d-m-Y', strtotime($ch->tanggal_lahir)) : '' }}</td>
                    <td>{{ $ch->pekerjaan }}</td>
                    <td>{{ $ch->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
            @for($i = count($anak); $i < 3; $i++)
                <tr><td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <strong>3. Bapak dan Ibu Kandung</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th class="w-no">No</th><th>Nama</th><th>Tanggal Lahir / Umur</th><th>Pekerjaan</th><th>Ket.</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td>
            </tr>
        </thead>
        <tbody>
            @foreach($ortu as $index => $pr)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $pr->nama }}</td>
                    <td class="text-center">{{ $pr->tanggal_lahir ? date('d-m-Y', strtotime($pr->tanggal_lahir)) : '' }}</td>
                    <td>{{ $pr->pekerjaan }}</td>
                    <td>{{ $pr->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
            @for($i = count($ortu); $i < 2; $i++)
                <tr><td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <strong>4. Bapak dan Ibu Mertua</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th class="w-no">No</th><th>Nama</th><th>Tanggal Lahir / Umur</th><th>Pekerjaan</th><th>Ket.</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td>
            </tr>
        </thead>
        <tbody>
            @foreach($mertua as $index => $mr)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $mr->nama }}</td>
                    <td class="text-center">{{ $mr->tanggal_lahir ? date('d-m-Y', strtotime($mr->tanggal_lahir)) : '' }}</td>
                    <td>{{ $mr->pekerjaan }}</td>
                    <td>{{ $mr->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
            @for($i = count($mertua); $i < 2; $i++)
                <tr><td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <strong>5. Saudara Kandung</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th class="w-no">No</th><th>Nama</th><th>Jenis Kelamin</th><th>Tanggal<br>Lahir /<br>Umur</th><th>Pekerjaan</th><th>Ket.</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
            </tr>
        </thead>
        <tbody>
            @foreach($saudara as $index => $sd)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sd->nama }}</td>
                    <td class="text-center">{{ $sd->jenis_kelamin == 'L' ? 'Laki-laki' : ($sd->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                    <td class="text-center">{{ $sd->tanggal_lahir ? date('d-m-Y', strtotime($sd->tanggal_lahir)) : '' }}</td>
                    <td>{{ $sd->pekerjaan }}</td>
                    <td>{{ $sd->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
            @for($i = count($saudara); $i < 3; $i++)
                <tr><td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- VIII. KETERANGAN ORGANISASI -->
    @php
        $orgSLTA = $organisasi->filter(fn($o) => strtolower($o->masa_organisasi) == 'slta')->values();
        $orgPT = $organisasi->filter(fn($o) => strtolower($o->masa_organisasi) == 'pt')->values();
        $orgBekerja = $organisasi->filter(fn($o) => strtolower($o->masa_organisasi) == 'bekerja')->values();
    @endphp

    <div class="section-title">VIII. KETERANGAN ORGANISASI</div>
    
    <strong>1. Semasa mengikuti pendidikan SLTA ke bawah</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th class="w-no">No</th><th>Nama Organisasi</th><th>Kedudukan<br>Dalam<br>Organisasi</th><th>Dalam<br>Th. s/d<br>Th.</th><th>Tempat</th><th>Nama<br>Pimpinan<br>Organisasi</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
            </tr>
        </thead>
        <tbody>
            @forelse($orgSLTA as $index => $org)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $org->nama_organisasi }}</td>
                <td>{{ $org->jabatan }}</td>
                <td class="text-center">{{ $org->tahun_mulai ? date('Y', strtotime($org->tahun_mulai)) : '-' }} s/d {{ $org->tahun_selesai ? date('Y', strtotime($org->tahun_selesai)) : 'Sek.' }}</td>
                <td>{{ $org->tempat ?? '-' }}</td>
                <td>{{ $org->pemimpin ?? '-' }}</td>
            </tr>
            @empty
            <tr><td class="text-center">1</td><td></td><td></td><td></td><td></td><td></td></tr>
            @endforelse
        </tbody>
    </table>

    <strong>2. Semasa mengikuti pendidikan di Perguruan Tinggi</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th class="w-no">No</th><th>Nama Organisasi</th><th>Kedudukan<br>Dalam<br>Organisasi</th><th>Dalam<br>Th. s/d<br>Th.</th><th>Tempat</th><th>Nama<br>Pimpinan<br>Organisasi</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
            </tr>
        </thead>
        <tbody>
            @forelse($orgPT as $index => $org)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $org->nama_organisasi }}</td>
                <td>{{ $org->jabatan }}</td>
                <td class="text-center">{{ $org->tahun_mulai ? date('Y', strtotime($org->tahun_mulai)) : '-' }} s/d {{ $org->tahun_selesai ? date('Y', strtotime($org->tahun_selesai)) : 'Sek.' }}</td>
                <td>{{ $org->tempat ?? '-' }}</td>
                <td>{{ $org->pemimpin ?? '-' }}</td>
            </tr>
            @empty
            <tr><td class="text-center">1</td><td></td><td></td><td></td><td></td><td></td></tr>
            @endforelse
        </tbody>
    </table>

    <strong>3. Sesudah selesai pendidikan dan atau selama menjadi pegawai</strong>
    <table class="bkn-table" style="margin-top: 5px;">
        <thead>
            <tr>
                <th class="w-no">No</th><th>Nama<br>Organisasi</th><th>Kedudukan<br>Dalam<br>Organisasi</th><th>Dalam Th. s/d<br>Th.</th><th>Tempat</th><th>Nama<br>Pimpinan<br>Organisasi</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
            </tr>
        </thead>
        <tbody>
            @forelse($orgBekerja as $index => $org)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $org->nama_organisasi }}</td>
                <td>{{ $org->jabatan }}</td>
                <td class="text-center">{{ $org->tahun_mulai ? date('Y', strtotime($org->tahun_mulai)) : '-' }} s/d {{ $org->tahun_selesai ? date('Y', strtotime($org->tahun_selesai)) : 'Sek.' }}</td>
                <td>{{ $org->tempat ?? '-' }}</td>
                <td>{{ $org->pemimpin ?? '-' }}</td>
            </tr>
            @empty
            <tr><td class="text-center">1</td><td></td><td></td><td></td><td></td><td></td></tr>
            @endforelse
            
            @for($i = count($orgBekerja); $i < 3; $i++)
                <tr><td class="text-center">{{ $i + 1 }}</td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <!-- IX. KETERANGAN LAIN-LAIN -->
    <div class="section-title">IX. KETERANGAN LAIN-LAIN</div>
    <table class="bkn-table">
        <thead>
            <tr>
                <th class="w-no" rowspan="2" style="vertical-align: middle;">No</th>
                <th rowspan="2" style="vertical-align: middle;">Nama Keterangan</th>
                <th colspan="3">Surat Keterangan</th>
            </tr>
            <tr>
                <th>Pejabat</th>
                <th>Nomor</th>
                <th>Tanggal</th>
            </tr>
            <tr class="numbering-row">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Surat Keterangan Berkelakuan Baik (SKCK)</td>
                <td>{{ $profile->skck_pejabat ?? '-' }}</td>
                <td>{{ $profile->skck_nomor ?? '-' }}</td>
                <td class="text-center">{{ $profile->skck_tanggal ? date('d-m-Y', strtotime($profile->skck_tanggal)) : '-' }}</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Surat Keterangan Berbadan Sehat</td>
                <td>{{ $profile->sehat_pejabat ?? '-' }}</td>
                <td>{{ $profile->sehat_nomor ?? '-' }}</td>
                <td class="text-center">{{ $profile->sehat_tanggal ? date('d-m-Y', strtotime($profile->sehat_tanggal)) : '-' }}</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Keterangan Lain Yang Dianggap Perlu</td>
                <td>{{ $profile->ket_lain_pejabat ?? '-' }}</td>
                <td>{{ $profile->ket_lain_nomor ?? '-' }}</td>
                <td class="text-center">{{ $profile->ket_lain_tanggal ? date('d-m-Y', strtotime($profile->ket_lain_tanggal)) : '-' }}</td>
            </tr>
            <tr>
                <td colspan="5" style="height: 50px;"></td>
            </tr>
        </tbody>
    </table>

    <br>
    
    <!-- TTD Section dengan QR CODE & Tanggal Dinamis -->
    <table class="no-border" style="margin-top: 10px;">
        <tr>
            <td style="width: 60%; padding-right: 20px; text-align: justify; line-height: 1.4;">
                <p style="text-indent: 30px; margin-top:0;">
                    Demikian Daftar Riwayat Hidup ini saya buat dengan sesungguhnya, 
                    dan apabila dikemudian hari terdapat keterangan yang tidak benar, 
                    saya bersedia dituntut di muka pengadilan serta bersedia menerima 
                    segala tindakan yang diambil oleh Pemerintah.
                </p>
                <br>
                <strong>PERHATIAN :</strong>
                <ol style="margin-top: 5px; padding-left: 15px; font-size: 10pt;">
                    <li>Harus ditulis dengan tangan sendiri, dengan huruf kapital/balok dan dengan tinta hitam.</li>
                    <li>Jika ada yang salah harus dicoret, yang dicoret tersebut tetap terbaca, kemudian yang benar dituliskan diatas atau dibawahnya dan diparaf.</li>
                    <li>Kolom yang kosong diberi tanda ( - ).</li>
                </ol>
            </td>
            <td style="width: 40%; text-align: center;">
                @php
                    // Generate data QR Code untuk verifikasi TTD digital
                    $qrData = urlencode("Ditandatangani oleh: {$namaPegawai}\nTanggal Cetak: {$waktuCetakFull} WIB");
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={$qrData}";
                @endphp
                
                KEDIRI, {{ $tanggalCetak }}<br>
                Yang membuat,
                
                <div style="margin: 15px 0;">
                    <img src="{{ $qrUrl }}" style="width: 80px; height: 80px;" alt="QR TTD">
                </div>
                
                <strong>( {{ $namaPegawai }} )</strong>
            </td>
        </tr>
    </table>

</body>
</html>