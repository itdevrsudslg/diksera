@extends('layouts.app')

@php
    $isEdit = $entry->exists;
    $pageTitle = $isEdit ? 'Ubah Logbook' : 'Tambah Logbook';
    $pageSubtitle = 'Satu pasien bisa banyak kegiatan, satu kegiatan bisa banyak tindakan';

    // Data awal kegiatan untuk form (old input > data tersimpan > 1 kegiatan kosong)
    $awal = old('kegiatan');
    if (!$awal) {
        $awal = $entry->kegiatans->map(fn($k) => [
            'nama' => $k->nama,
            'tindakan' => $k->tindakans->map(fn($t) => [
                'id' => $t->tindakan_id, 'jumlah' => $t->jumlah, 'label' => $t->kode . ' - ' . $t->nama,
            ])->values()->all(),
        ])->values()->all();
    }
    if (!$awal) $awal = [['nama' => '', 'tindakan' => [['id' => null, 'jumlah' => 1, 'label' => null]]]];

    // Label untuk tindakan dari old() yang belum punya label
    $labelIds = collect($awal)->flatMap(fn($k) => collect($k['tindakan'] ?? [])->pluck('id'))->filter()->unique();
    $labels = \App\Models\LogbookTindakan::whereIn('id', $labelIds)->get()->mapWithKeys(fn($t) => [$t->id => $t->kode . ' - ' . $t->nama]);
    foreach ($awal as &$k) {
        foreach ($k['tindakan'] as &$t) { $t['label'] = $t['label'] ?? ($labels[$t['id'] ?? 0] ?? null); }
    }
    unset($k, $t);
@endphp

@section('title', $pageTitle . ' – DIKSERA')

@push('styles')
    @include('logbook._styles')
    <style>
        .keg-card { border: 1px solid var(--border-soft); border-radius: 14px; padding: 16px; margin-bottom: 14px; background: #fbfcff; }
        .keg-head { display: flex; gap: 10px; align-items: center; margin-bottom: 12px; }
        .keg-no { width: 28px; height: 28px; border-radius: 8px; background: var(--blue-soft); color: var(--blue-main); font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .tdk-row { display: grid; grid-template-columns: 1fr 90px 38px; gap: 8px; align-items: start; margin-bottom: 8px; }
        .tdk-row .choices { margin-bottom: 0; }
        .tdk-row .choices__inner { min-height: 38px; padding: 4px 8px; border-radius: 8px; background: #fff; font-size: 13.5px; }
        .tdk-row .choices__list--dropdown .choices__item { font-size: 13px; }
        .btn-icon-sm { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; }
        .rm-status { font-size: 12px; min-height: 18px; }
        @media (max-width: 576px) { .tdk-row { grid-template-columns: 1fr 70px 38px; } }
    </style>
@endpush

@section('content')
    <form method="POST" action="{{ $isEdit ? route('perawat.logbook.update', $entry->id) : route('perawat.logbook.store') }}" id="lbForm">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        @if ($errors->any())
            <div class="alert alert-danger small">
                <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle"></i> Periksa kembali isian:</div>
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- IDENTITAS --}}
            <div class="col-lg-4">
                <div class="lb-card">
                    <div class="fw-bold mb-3"><i class="bi bi-person-badge text-primary me-1"></i> Identitas</div>

                    <div class="small text-muted">Pengisi</div>
                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                    <div class="small text-muted mb-3">
                        NIP {{ optional(Auth::user()->profile)->nip ?? '-' }} · {{ $unit ?? 'Unit belum diisi' }}
                    </div>

                    <label class="form-label small fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control mb-3 @error('tanggal') is-invalid @enderror"
                        value="{{ old('tanggal', optional($entry->tanggal)->toDateString() ?? $entry->tanggal) }}" max="{{ now()->toDateString() }}" required>

                    <label class="form-label small fw-semibold">Nomor RM Pasien</label>
                    <div class="input-group">
                        <input type="text" name="no_rm" id="noRm" class="form-control fw-semibold @error('no_rm') is-invalid @enderror"
                            value="{{ old('no_rm', $entry->no_rm) }}" placeholder="contoh: 123456" autocomplete="off" required>
                        <button type="button" class="btn btn-outline-secondary" id="btnCariRm" title="Cari nama pasien"><i class="bi bi-search"></i></button>
                    </div>
                    <div class="rm-status mt-1 mb-2" id="rmStatus">
                        @if (!$khanza)<span class="text-muted">Nama pasien diisi manual (Khanza belum terhubung).</span>@endif
                    </div>

                    <label class="form-label small fw-semibold">Nama Pasien</label>
                    <input type="text" name="nama_pasien" id="namaPasien" class="form-control mb-3"
                        value="{{ old('nama_pasien', $entry->nama_pasien) }}" placeholder="otomatis dari No RM">

                    <label class="form-label small fw-semibold">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                    <textarea name="catatan" rows="2" class="form-control">{{ old('catatan', $entry->catatan) }}</textarea>

                    @if ($isEdit && $entry->status === 'ditolak' && $entry->catatan_validator)
                        <div class="alert alert-danger small mt-3 mb-0">
                            <b>Ditolak karu:</b> {{ $entry->catatan_validator }}<br>
                            Setelah disimpan, entri kembali menunggu validasi.
                        </div>
                    @endif
                </div>
            </div>

            {{-- KEGIATAN & TINDAKAN --}}
            <div class="col-lg-8">
                <div class="lb-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="fw-bold"><i class="bi bi-list-check text-primary me-1"></i> Kegiatan & Tindakan</div>
                        <span class="small text-muted" id="ringkasTindakan"></span>
                    </div>

                    <div id="kegiatanList"></div>

                    <button type="button" class="btn btn-outline-primary w-100 border-2 border-dashed" id="btnTambahKegiatan" style="border-style: dashed;">
                        <i class="bi bi-plus-circle"></i> Tambah Kegiatan
                    </button>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-end mt-3">
                    <a href="{{ route('perawat.logbook.index') }}" class="btn btn-light border">Batal</a>
                    @unless ($isEdit)
                        <button type="submit" name="aksi" value="simpan_lagi" class="btn btn-outline-primary fw-semibold">
                            <i class="bi bi-plus-lg"></i> Simpan & Tambah Pasien Lain
                        </button>
                    @endunless
                    <button type="submit" name="aksi" value="simpan" class="btn btn-primary fw-semibold px-4">
                        <i class="bi bi-check-lg"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- TEMPLATE --}}
    <template id="tplKegiatan">
        <div class="keg-card" data-keg>
            <div class="keg-head">
                <div class="keg-no" data-no>1</div>
                <input type="text" class="form-control fw-semibold" data-nama placeholder="Nama kegiatan, contoh: Perawatan luka / Pemasangan infus" required>
                <button type="button" class="btn btn-light border btn-icon-sm text-danger" data-hapus-keg title="Hapus kegiatan"><i class="bi bi-trash"></i></button>
            </div>
            <div class="small text-muted mb-1 d-flex justify-content-between">
                <span>Jenis tindakan</span><span style="width:136px">Jumlah</span>
            </div>
            <div data-tindakan-list></div>
            <button type="button" class="btn btn-sm btn-link px-0 text-decoration-none" data-tambah-tdk><i class="bi bi-plus"></i> Tambah tindakan</button>
        </div>
    </template>
    <template id="tplTindakan">
        <div class="tdk-row" data-tdk>
            <select data-tindakan></select>
            <input type="number" class="form-control text-center" data-jumlah min="1" max="999" value="1" required>
            <button type="button" class="btn btn-light border btn-icon-sm text-danger" data-hapus-tdk title="Hapus tindakan"><i class="bi bi-x-lg"></i></button>
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        (function () {
            const awal = @json($awal);
            const urlTindakan = @json(route('perawat.logbook.cari_tindakan'));
            const urlPasien = @json(route('perawat.logbook.cari_pasien'));
            const list = document.getElementById('kegiatanList');
            const tplK = document.getElementById('tplKegiatan');
            const tplT = document.getElementById('tplTindakan');

            function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }

            function initChoices(select, preset) {
                const c = new Choices(select, {
                    searchPlaceholderValue: 'Ketik kode / nama tindakan…',
                    placeholder: true, placeholderValue: 'Pilih tindakan',
                    shouldSort: false, itemSelectText: '', noResultsText: 'Tidak ditemukan',
                    noChoicesText: 'Ketik untuk mencari tindakan', searchResultLimit: 30, removeItemButton: false,
                });
                if (preset && preset.id) c.setChoices([{ value: String(preset.id), label: preset.label || ('#' + preset.id), selected: true }], 'value', 'label', true);

                const load = debounce(function (q) {
                    fetch(urlTindakan + '?q=' + encodeURIComponent(q)).then(r => r.json()).then(rows => {
                        c.setChoices(rows.map(r => ({ value: String(r.value), label: r.label })), 'value', 'label', true);
                    });
                }, 250);
                select.addEventListener('search', e => load(e.detail.value));
                select.addEventListener('showDropdown', () => { if (!select.dataset.loaded) { select.dataset.loaded = 1; load(''); } });
                select.addEventListener('change', hitung);
            }

            function tambahTindakan(kegEl, preset) {
                const row = tplT.content.firstElementChild.cloneNode(true);
                kegEl.querySelector('[data-tindakan-list]').appendChild(row);
                if (preset && preset.jumlah) row.querySelector('[data-jumlah]').value = preset.jumlah;
                row.querySelector('[data-jumlah]').addEventListener('input', hitung);
                row.querySelector('[data-hapus-tdk]').addEventListener('click', () => {
                    if (kegEl.querySelectorAll('[data-tdk]').length > 1) { row.remove(); penomoran(); }
                });
                initChoices(row.querySelector('[data-tindakan]'), preset);
                penomoran();
            }

            function tambahKegiatan(preset) {
                const keg = tplK.content.firstElementChild.cloneNode(true);
                list.appendChild(keg);
                keg.querySelector('[data-nama]').value = (preset && preset.nama) || '';
                keg.querySelector('[data-tambah-tdk]').addEventListener('click', () => tambahTindakan(keg));
                keg.querySelector('[data-hapus-keg]').addEventListener('click', () => {
                    if (list.querySelectorAll('[data-keg]').length > 1) { keg.remove(); penomoran(); }
                });
                const tdk = (preset && preset.tindakan && preset.tindakan.length) ? preset.tindakan : [{ jumlah: 1 }];
                tdk.forEach(t => tambahTindakan(keg, t));
                if (!preset) keg.querySelector('[data-nama]').focus();
            }

            // Beri atribut name sesuai urutan agar terkirim sebagai kegiatan[i][tindakan][j][...]
            function penomoran() {
                list.querySelectorAll('[data-keg]').forEach((keg, i) => {
                    keg.querySelector('[data-no]').textContent = i + 1;
                    keg.querySelector('[data-nama]').name = `kegiatan[${i}][nama]`;
                    keg.querySelectorAll('[data-tdk]').forEach((row, j) => {
                        row.querySelector('select[data-tindakan]').name = `kegiatan[${i}][tindakan][${j}][id]`;
                        row.querySelector('[data-jumlah]').name = `kegiatan[${i}][tindakan][${j}][jumlah]`;
                    });
                });
                hitung();
            }

            function hitung() {
                const keg = list.querySelectorAll('[data-keg]').length;
                let total = 0;
                list.querySelectorAll('[data-jumlah]').forEach(i => total += parseInt(i.value || 0));
                document.getElementById('ringkasTindakan').textContent = `${keg} kegiatan · ${total} tindakan`;
            }

            document.getElementById('btnTambahKegiatan').addEventListener('click', () => tambahKegiatan());
            awal.forEach(k => tambahKegiatan(k));

            // Cari nama pasien dari No RM
            const noRm = document.getElementById('noRm');
            const nama = document.getElementById('namaPasien');
            const status = document.getElementById('rmStatus');
            function cariPasien() {
                const rm = noRm.value.trim();
                if (!rm) return;
                status.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm"></span> Mencari pasien…</span>';
                fetch(urlPasien + '?no_rm=' + encodeURIComponent(rm)).then(r => r.json()).then(d => {
                    if (d.found) {
                        nama.value = d.nama;
                        status.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> ' +
                            (d.sumber === 'riwayat' ? 'Nama dari logbook sebelumnya' : 'Pasien ditemukan di SIMRS') + '</span>';
                    } else {
                        status.innerHTML = '<span class="text-warning"><i class="bi bi-info-circle"></i> Tidak ditemukan, isi nama manual.</span>';
                    }
                }).catch(() => status.innerHTML = '<span class="text-muted">Pencarian gagal, isi nama manual.</span>');
            }
            document.getElementById('btnCariRm').addEventListener('click', cariPasien);
            noRm.addEventListener('change', cariPasien);
            noRm.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); cariPasien(); } });

            // Cegah kirim jika ada tindakan belum dipilih
            document.getElementById('lbForm').addEventListener('submit', function (e) {
                const kosong = [...list.querySelectorAll('select[data-tindakan]')].some(s => !s.value);
                if (kosong) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', text: 'Ada tindakan yang belum dipilih.' });
                }
            });
        })();
    </script>
@endpush
