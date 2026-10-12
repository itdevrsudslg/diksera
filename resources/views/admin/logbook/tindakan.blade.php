@extends('layouts.app')

@php
    $pageTitle = 'Master Tindakan Logbook';
    $pageSubtitle = 'Daftar jenis tindakan dari SIMRS Khanza + tambahan manual';
@endphp

@section('title', 'Master Tindakan – Admin DIKSERA')

@push('styles')
    @include('logbook._styles')
@endpush

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="lb-card h-100">
                <div class="fw-bold mb-1"><i class="bi bi-database text-primary me-1"></i> Sinkron dari SIMRS Khanza</div>
                <div class="small text-muted mb-3">
                    @if ($khanza)
                        Total {{ $total }} tindakan. Sinkron terakhir: {{ $terakhirSinkron ? \Carbon\Carbon::parse($terakhirSinkron)->translatedFormat('d M Y H:i') : 'belum pernah' }}.
                    @else
                        Koneksi Khanza belum diatur. Isi <code>KHANZA_DB_HOST</code>, <code>KHANZA_DB_DATABASE</code>, <code>KHANZA_DB_USERNAME</code>, <code>KHANZA_DB_PASSWORD</code> di file <code>.env</code> server.
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.logbook.tindakan.sinkron') }}" class="js-confirm" data-confirm="Ambil daftar tindakan terbaru dari Khanza?">
                    @csrf
                    <button class="btn btn-primary btn-sm" {{ $khanza ? '' : 'disabled' }}><i class="bi bi-arrow-repeat"></i> Sinkron Sekarang</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lb-card h-100">
                <div class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary me-1"></i> Tambah Tindakan Manual</div>
                <form method="POST" action="{{ route('admin.logbook.tindakan.simpan') }}" class="d-flex flex-wrap gap-2">
                    @csrf
                    <input name="kode" class="form-control form-control-sm" style="max-width:120px" placeholder="Kode" required>
                    <input name="nama" class="form-control form-control-sm flex-grow-1" style="min-width:180px" placeholder="Nama tindakan" required>
                    <button class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Tambah</button>
                </form>
            </div>
        </div>
    </div>

    <div class="lb-card mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <div class="fw-bold"><i class="bi bi-file-earmark-excel text-success me-1"></i> Import / Export Excel</div>
                <div class="small text-muted">Kolom: <b>Kode</b>, <b>Nama Tindakan</b>, <b>Kategori</b> (ralan / ranap / manual), <b>Aktif</b> (Ya / Tidak).
                    Kode + kategori yang sudah ada akan diperbarui; tidak ada data yang dihapus.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-light border" id="btnTemplate"><i class="bi bi-file-earmark-arrow-down"></i> Template</button>
                <button type="button" class="btn btn-sm btn-outline-success" id="btnExportTdk"><i class="bi bi-download"></i> Export Excel</button>
                <label class="btn btn-sm btn-success mb-0">
                    <i class="bi bi-upload"></i> Import Excel
                    <input type="file" id="fileImport" accept=".xlsx,.xls,.csv" hidden>
                </label>
            </div>
        </div>
    </div>

    <div class="lb-card">
        <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" style="max-width:260px" placeholder="Cari kode / nama…">
            <select name="kategori" class="form-select form-select-sm" style="width:auto">
                <option value="">Semua kategori</option>
                @foreach (['ralan' => 'Rawat Jalan', 'ranap' => 'Rawat Inap', 'manual' => 'Manual'] as $v => $l)
                    <option value="{{ $v }}" {{ request('kategori') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
            <select name="aktif" class="form-select form-select-sm" style="width:auto">
                <option value="1">Aktif</option>
                <option value="0" {{ request('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button class="btn btn-sm btn-primary">Filter</button>
        </form>

        <div class="table-responsive">
            <table class="table lb-table align-middle">
                <thead><tr><th>Kode</th><th>Nama Tindakan</th><th>Kategori</th><th>Sumber</th><th class="text-end">Status</th></tr></thead>
                <tbody>
                    @forelse ($tindakans as $t)
                        <tr>
                            <td class="fw-semibold">{{ $t->kode }}</td>
                            <td>{{ $t->nama }}</td>
                            <td><span class="lb-pill info">{{ strtoupper($t->kategori) }}</span></td>
                            <td class="small text-muted">{{ ucfirst($t->sumber) }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.logbook.tindakan.toggle', $t->id) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm py-0 {{ $t->aktif ? 'btn-outline-success' : 'btn-outline-secondary' }}">
                                        {{ $t->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="lb-empty">Belum ada tindakan. Lakukan sinkron Khanza atau tambah manual.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $tindakans->links('vendor.pagination.diksera') }}
    </div>
@endsection

@push('scripts')
    @include('logbook._scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script>
        (function () {
            const urlExport = @json(route('admin.logbook.tindakan.export'));
            const urlImport = @json(route('admin.logbook.tindakan.import'));
            const csrf = @json(csrf_token());
            const esc = v => String(v).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            function simpanExcel(rows, nama) {
                const ws = XLSX.utils.json_to_sheet(rows);
                ws['!cols'] = [{ wch: 14 }, { wch: 60 }, { wch: 10 }, { wch: 8 }, { wch: 10 }];
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Master Tindakan');
                XLSX.writeFile(wb, nama);
            }

            document.getElementById('btnTemplate').addEventListener('click', () => simpanExcel([
                { 'Kode': 'T001', 'Nama Tindakan': 'Perawatan luka sedang', 'Kategori': 'manual', 'Aktif': 'Ya' },
                { 'Kode': 'T002', 'Nama Tindakan': 'Pemasangan infus', 'Kategori': 'manual', 'Aktif': 'Ya' },
            ], 'Template_Master_Tindakan.xlsx'));

            document.getElementById('btnExportTdk').addEventListener('click', async function () {
                const btn = this, label = btn.innerHTML;
                btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyusun…';
                try {
                    const rows = await (await fetch(urlExport)).json();
                    simpanExcel(rows, `Master_Tindakan_${new Date().toISOString().slice(0, 10)}.xlsx`);
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
                } finally { btn.disabled = false; btn.innerHTML = label; }
            });

            // Cocokkan judul kolom secara longgar (huruf besar/kecil & spasi diabaikan)
            const ambil = (row, ...nama) => {
                const keys = Object.keys(row);
                for (const n of nama) {
                    const k = keys.find(k => k.toLowerCase().replace(/[^a-z]/g, '') === n);
                    if (k !== undefined) return row[k];
                }
                return '';
            };

            document.getElementById('fileImport').addEventListener('change', async function () {
                const file = this.files[0];
                this.value = '';
                if (!file) return;
                try {
                    const wb = XLSX.read(await file.arrayBuffer());
                    const data = XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]], { defval: '' });
                    const rows = data.map(r => ({
                        kode: String(ambil(r, 'kode', 'kodetindakan', 'kdjenisprw')).trim(),
                        nama: String(ambil(r, 'namatindakan', 'nama', 'tindakan', 'nmperawatan')).trim(),
                        kategori: String(ambil(r, 'kategori')).trim(),
                        aktif: String(ambil(r, 'aktif', 'status')).trim(),
                    })).filter(r => r.kode || r.nama);

                    if (!rows.length) return Swal.fire({ icon: 'warning', text: 'Tidak ada baris yang terbaca. Pastikan ada kolom Kode dan Nama Tindakan.' });

                    const ok = await Swal.fire({
                        icon: 'question', title: `Import ${rows.length} tindakan?`,
                        html: `Contoh: <b>${esc(rows[0].kode)}</b> – ${esc(rows[0].nama)}`,
                        showCancelButton: true, confirmButtonText: 'Import', cancelButtonText: 'Batal',
                    });
                    if (!ok.isConfirmed) return;

                    Swal.fire({ title: 'Mengimpor…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    const res = await fetch(urlImport, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ rows }),
                    });
                    const h = await res.json();
                    if (!res.ok) throw new Error(h.message || 'Import gagal');

                    await Swal.fire({
                        icon: h.error.length ? 'warning' : 'success', title: 'Import selesai',
                        html: `Baru: <b>${h.baru}</b> · Diperbarui: <b>${h.diperbarui}</b> · Dilewati: <b>${h.dilewati}</b>` +
                            (h.error.length ? '<div class="text-start small mt-2 text-danger">' + h.error.map(esc).join('<br>') + '</div>' : ''),
                    });
                    location.reload();
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'Gagal membaca file', text: e.message });
                }
            });
        })();
    </script>
@endpush
