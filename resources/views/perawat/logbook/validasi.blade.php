@extends('layouts.app')

@php
    $pageTitle = $isKaru ? 'Validasi Logbook' : 'Rekap Logbook (Mengetahui)';
    $pageSubtitle = $isKaru ? 'Kepala Ruangan · ' . ($unit ?? '-') : 'Kepala Seksi Keperawatan';
    $statusAktif = request('status', 'menunggu');
@endphp

@section('title', $pageTitle . ' – DIKSERA')

@push('styles')
    @include('logbook._styles')
@endpush

@section('content')
    <div class="lb-toolbar">
        <form method="GET" class="d-flex flex-wrap gap-2 m-0">
            <input type="month" name="bulan" value="{{ $bulan->format('Y-m') }}" class="form-control form-control-sm" style="width:auto" onchange="this.form.submit()">
            @if ($isKaru)
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto">
                    @foreach (['menunggu' => 'Menunggu validasi', 'ditolak' => 'Ditolak', 'valid' => 'Sudah valid', 'semua' => 'Semua'] as $v => $l)
                        <option value="{{ $v }}" {{ $statusAktif === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            @endif
            @if ($isKasi && $units->count())
                <select name="unit" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto">
                    <option value="">Semua unit</option>
                    @foreach ($units as $u)
                        <option value="{{ $u }}" {{ request('unit') === $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
            @endif
        </form>
    </div>

    {{-- REKAP BULANAN UNTUK DITANDATANGANI --}}
    <div class="lb-card mb-3">
        <div class="fw-bold mb-3"><i class="bi bi-pen text-primary me-1"></i> Rekap {{ $bulan->translatedFormat('F Y') }}</div>
        <div class="table-responsive">
            <table class="table lb-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Perawat</th>
                        @if ($isKasi)<th>Unit</th>@endif
                        <th>TTD Perawat</th><th>TTD Karu</th><th>TTD Kasi</th><th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekaps as $r)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ optional($r->user)->name }}</div>
                                <div class="text-muted small">NIP {{ optional(optional($r->user)->profile)->nip ?? '-' }}</div>
                            </td>
                            @if ($isKasi)<td class="small">{{ $r->unit_kerja }}</td>@endif
                            @foreach (['perawat_signed_at', 'karu_signed_at', 'kasi_signed_at'] as $col)
                                <td>
                                    @if ($r->$col)
                                        <span class="lb-pill success"><i class="bi bi-check2"></i> {{ $r->$col->translatedFormat('d M') }}</span>
                                    @else
                                        <span class="lb-pill muted">Belum</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-end text-nowrap">
                                <a href="{{ route('perawat.logbook.rekap', ['userId' => $r->user_id, 'jenis' => 'bulan', 'tahun' => $r->tahun, 'ke' => $r->bulan]) }}"
                                    class="btn btn-sm btn-light border" title="Lihat rekap"><i class="bi bi-eye"></i></a>
                                @if ($isKaru && $r->perawat_signed_at && !$r->karu_signed_at)
                                    <form method="POST" action="{{ route('perawat.logbook.validasi.kembalikan', $r->id) }}" class="d-inline js-confirm" data-confirm="Kembalikan rekap ke perawat?">
                                        @csrf <button class="btn btn-sm btn-light border text-warning" title="Kembalikan"><i class="bi bi-arrow-counterclockwise"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('perawat.logbook.validasi.ttd', $r->id) }}" class="d-inline js-confirm" data-confirm="Tandatangani rekap {{ optional($r->user)->name }} sebagai Kepala Ruangan?">
                                        @csrf <button class="btn btn-sm btn-success"><i class="bi bi-pen-fill"></i> TTD</button>
                                    </form>
                                @elseif ($isKasi && $r->karu_signed_at && !$r->kasi_signed_at)
                                    <form method="POST" action="{{ route('perawat.logbook.validasi.ttd', $r->id) }}" class="d-inline js-confirm" data-confirm="Tandatangani (mengetahui) rekap {{ optional($r->user)->name }}?">
                                        @csrf <button class="btn btn-sm btn-success"><i class="bi bi-pen-fill"></i> Mengetahui</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="lb-empty">
                            {{ $isKaru ? 'Belum ada perawat yang mengajukan rekap bulan ini.' : 'Belum ada rekap yang ditandatangani kepala ruangan bulan ini.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- VALIDASI ENTRI (KARU) --}}
    @if ($isKaru)
        <form method="POST" action="{{ route('perawat.logbook.validasi.entri') }}" id="formValidasi">
            @csrf
            <input type="hidden" name="aksi" id="aksiValidasi">
            <input type="hidden" name="catatan" id="catatanValidasi">

            <div class="lb-card">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <div class="fw-bold"><i class="bi bi-check2-square text-primary me-1"></i> Entri Logbook Unit ({{ $entries->count() }})</div>
                    @if ($entries->count())
                        <div class="d-flex gap-2 align-items-center">
                            <label class="small m-0"><input type="checkbox" class="form-check-input me-1" id="cekSemua"> Pilih semua</label>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-aksi="ditolak"><i class="bi bi-x-lg"></i> Tolak</button>
                            <button type="button" class="btn btn-sm btn-success fw-semibold" data-aksi="valid"><i class="bi bi-check2-all"></i> Validasi Terpilih</button>
                        </div>
                    @endif
                </div>

                @forelse ($entries->groupBy(fn($e) => $e->user->name) as $nama => $list)
                    <div class="lb-day">{{ $nama }} <span class="fw-normal">· {{ $list->count() }} entri</span></div>
                    @foreach ($list as $e)
                        <label class="lb-entry {{ $e->status }} d-flex gap-3 w-100" style="cursor:pointer">
                            <input type="checkbox" name="ids[]" value="{{ $e->id }}" class="form-check-input mt-1 cek-entri">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap justify-content-between gap-2">
                                    <div>
                                        <span class="text-muted small">{{ $e->tanggal->translatedFormat('d M') }}</span>
                                        <span class="lb-rm ms-1">RM {{ $e->no_rm }}</span>
                                        <span class="fw-semibold ms-1">{{ $e->nama_pasien }}</span>
                                    </div>
                                    <span class="lb-pill {{ $e->status }}">{{ ucfirst($e->status) }}</span>
                                </div>
                                @foreach ($e->kegiatans as $k)
                                    <div class="lb-keg"><span class="k">{{ $k->nama }}:</span>
                                        @foreach ($k->tindakans as $t)<span class="lb-tag"><b>{{ $t->jumlah }}×</b> {{ $t->nama }}</span>@endforeach
                                    </div>
                                @endforeach
                                @if ($e->catatan)<div class="small text-muted mt-1">{{ $e->catatan }}</div>@endif
                                @if ($e->status === 'ditolak' && $e->catatan_validator)<div class="small text-danger mt-1">Ditolak: {{ $e->catatan_validator }}</div>@endif
                            </div>
                        </label>
                    @endforeach
                @empty
                    <div class="lb-empty"><i class="bi bi-inbox display-6 d-block mb-2 opacity-50"></i>Tidak ada entri dengan status ini.</div>
                @endforelse
            </div>
        </form>
    @endif
@endsection

@push('scripts')
    @include('logbook._scripts')
    <script>
        (function () {
            const all = document.getElementById('cekSemua');
            if (all) all.addEventListener('change', () => document.querySelectorAll('.cek-entri').forEach(c => c.checked = all.checked));

            document.querySelectorAll('[data-aksi]').forEach(btn => btn.addEventListener('click', async () => {
                const n = document.querySelectorAll('.cek-entri:checked').length;
                if (!n) return Swal.fire({ icon: 'info', text: 'Pilih entri terlebih dahulu.' });
                const aksi = btn.dataset.aksi;
                let catatan = '';
                if (aksi === 'ditolak') {
                    const r = await Swal.fire({ title: `Tolak ${n} entri?`, input: 'textarea', inputPlaceholder: 'Alasan penolakan (wajib)',
                        showCancelButton: true, confirmButtonText: 'Tolak', cancelButtonText: 'Batal', confirmButtonColor: '#dc2626',
                        inputValidator: v => !v && 'Alasan wajib diisi' });
                    if (!r.isConfirmed) return;
                    catatan = r.value;
                } else {
                    const r = await Swal.fire({ text: `Validasi ${n} entri terpilih?`, icon: 'question', showCancelButton: true,
                        confirmButtonText: 'Validasi', cancelButtonText: 'Batal', confirmButtonColor: '#16a34a' });
                    if (!r.isConfirmed) return;
                }
                document.getElementById('aksiValidasi').value = aksi;
                document.getElementById('catatanValidasi').value = catatan;
                document.getElementById('formValidasi').submit();
            }));
        })();
    </script>
@endpush
