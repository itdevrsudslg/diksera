@extends('layouts.app')
@section('title', 'Penilaian OPPE')

@section('content')
@php
    // Deteksi apakah item MPP/PPI sudah ada di dalam array instrumen
    $instrumenFlat = collect($oppe->instrumen_penilaian);
    $hasMpp = $instrumenFlat->contains(function ($item) { return str_starts_with($item['item_id'], 'MPP'); });
    $hasPpi = $instrumenFlat->contains(function ($item) { return str_starts_with($item['item_id'], 'PPI'); });

    // Hitung statis total skor Diri Sendiri dan Sejawat untuk ditampilkan ke Karu
    $sumSelf = $instrumenFlat->sum(function($item) { return (int) ($item['skor_self'] ?? 0); });
    $sumPeer = $instrumenFlat->sum(function($item) { return (int) ($item['skor_peer'] ?? 0); });
@endphp

<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white p-4 border-bottom">
            <h5 class="fw-bold mb-0">PENILAIAN KINERJA PERAWAT (OPPE)</h5>
            <p class="text-muted small mb-0">PERIODE {{ $oppe->periode }}</p>
        </div>

        <div class="card-body p-4 bg-light">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless small mb-0">
                        <tr>
                            <td width="30%" class="text-muted">Nama Pegawai</td>
                            <td class="fw-bold">: {{ $oppe->user->profile->nama_lengkap ?? $oppe->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Unit / Ruang</td>
                            <td class="fw-bold">: 
                                @php
                                    $unitKerja = optional($oppe->user->pekerjaans->sortByDesc('tahun_mulai')->first())->unit_kerja ?? '-';
                                @endphp
                                {{ is_array($unitKerja) ? implode(', ', $unitKerja) : $unitKerja }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kualifikasi (PK)</td>
                            <td class="fw-bold">: 
                                @php
                                    $bidang = $oppe->lisensi->bidang ?? '';
                                    $kfk = $oppe->lisensi->kfk ?? '';
                                    
                                    $bidangText = is_array($bidang) ? implode(', ', $bidang) : $bidang;
                                    $kfkText = is_array($kfk) ? implode(', ', $kfk) : $kfk;
                                @endphp
                                {{ trim($bidangText . ' ' . $kfkText) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nilai OPPE Terakhir</td>
                            <td class="fw-bold text-success">: 
                                @if(isset($lastOppe) && $lastOppe)
                                    {{ $lastOppe->nilai_akhir }}% <span class="text-muted fw-normal">(Periode: {{ $lastOppe->periode }})</span>
                                @else
                                    <span class="text-muted fw-normal">Belum Ada Data</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info border-0 py-2 small mb-0">
                        <strong>Petunjuk Pengisian:</strong><br>
                        1 = Kinerja belum ditunjukkan<br>
                        2 = Kinerja perlu diperbaiki<br>
                        3 = Kinerja sesuai dengan standar
                    </div>
                </div>
            </div>

            <form id="formEvaluasi" action="{{ route('perawat.oppe.store_evaluation', $oppe->id) }}" method="POST">
                @csrf
                
                @if($canEdit)
                <!-- ============================================== -->
                <!-- KOTAK SWITCH TAMBAH INSTRUMEN -->
                <!-- ============================================== -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear-fill me-2"></i>Tugas Tambahan (Otomatis Menambah Soal)</h6>
                    <div class="d-flex gap-3 flex-wrap">
                        
                        <!-- Switch MPP -->
                        <div class="p-3 bg-white border rounded shadow-sm flex-fill custom-switch-card d-flex align-items-center">
                            <!-- CSS Manual pengganti Bootstrap form-switch jika error -->
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_mpp" value="1" class="auto-reload-switch" {{ $hasMpp ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                            <div class="ms-3">
                                <label class="fw-bold mb-0" style="cursor: pointer;">
                                    <i class="bi bi-person-workspace text-primary"></i> Manajer Pelayanan Pasien (MPP)
                                </label>
                                <small class="text-muted d-block mt-1">Hidupkan untuk memunculkan item kompetensi MPP.</small>
                            </div>
                        </div>

                        <!-- Switch PPI -->
                        <div class="p-3 bg-white border rounded shadow-sm flex-fill custom-switch-card d-flex align-items-center">
                            <!-- CSS Manual pengganti Bootstrap form-switch jika error -->
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_ppi" value="1" class="auto-reload-switch" {{ $hasPpi ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                            <div class="ms-3">
                                <label class="fw-bold mb-0" style="cursor: pointer;">
                                    <i class="bi bi-shield-plus text-danger"></i> Tim Pengendalian Infeksi (PPI)
                                </label>
                                <small class="text-muted d-block mt-1">Hidupkan untuk memunculkan item kompetensi PPI.</small>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- STYLE MANUAL UNTUK MEMASTIKAN ON/OFF BUTTON PASTI MUNCUL -->
                <style>
                    .custom-switch-card { transition: all 0.3s ease; }
                    .custom-switch-card:hover { border-color: #0d6efd !important; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }
                    
                    /* Tampilan Switch Manual (Anti Gagal) */
                    .toggle-switch {
                        position: relative;
                        display: inline-block;
                        width: 50px;
                        height: 26px;
                        flex-shrink: 0;
                    }
                    .toggle-switch input { 
                        opacity: 0;
                        width: 0;
                        height: 0;
                    }
                    .slider {
                        position: absolute;
                        cursor: pointer;
                        top: 0; left: 0; right: 0; bottom: 0;
                        background-color: #cbd5e1;
                        transition: .4s;
                    }
                    .slider:before {
                        position: absolute;
                        content: "";
                        height: 18px;
                        width: 18px;
                        left: 4px;
                        bottom: 4px;
                        background-color: white;
                        transition: .4s;
                    }
                    input:checked + .slider {
                        background-color: #0d6efd;
                    }
                    input:checked + .slider:before {
                        transform: translateX(24px);
                    }
                    .slider.round {
                        border-radius: 34px;
                    }
                    .slider.round:before {
                        border-radius: 50%;
                    }
                </style>
                <!-- ============================================== -->

                <div class="d-flex justify-content-end mb-2 mt-4">
                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="isiSemuaNilai3()">
                        <i class="bi bi-check2-all me-1"></i> Isi Semua Nilai 3 (Sesuai Standar)
                    </button>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered align-middle bg-white table-hover small">
                        <thead class="table-light text-center align-middle">
                            <tr>
                                <th width="5%" rowspan="2">NO</th>
                                <th width="50%" rowspan="2">KOMPETENSI KLINIK</th>
                                <th colspan="3">SKOR PENILAIAN (1-3)</th>
                            </tr>
                            <tr>
                                <th width="15%" class="{{ $roleEvaluasi == 'self' ? 'bg-primary text-white' : '' }}">DIRI SENDIRI</th>
                                <th width="15%" class="{{ $roleEvaluasi == 'peer' ? 'bg-primary text-white' : '' }}">SEJAWAT</th>
                                <th width="15%" class="{{ $roleEvaluasi == 'karu' ? 'bg-primary text-white' : '' }}">ATASAN/KARU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($instrumenGrouped as $kategori => $items)
                                <tr class="table-secondary">
                                    <td colspan="5" class="fw-bold">{{ $kategori }}</td>
                                </tr>
                                @foreach($items as $item)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $item['deskripsi'] }}</td>
                                        
                                        <!-- KOLOM SELF -->
                                        <td class="text-center {{ $roleEvaluasi == 'self' ? 'bg-primary bg-opacity-10' : '' }}">
                                            @if($roleEvaluasi == 'self')
                                                <select name="skor[{{ $item['item_id'] }}]" class="form-select form-select-sm border-primary dropdown-skor" required>
                                                    <option value="" disabled {{ !isset($item['skor_self']) ? 'selected' : '' }}>-</option>
                                                    <option value="1" {{ (isset($item['skor_self']) && $item['skor_self'] == 1) ? 'selected' : '' }}>1</option>
                                                    <option value="2" {{ (isset($item['skor_self']) && $item['skor_self'] == 2) ? 'selected' : '' }}>2</option>
                                                    <option value="3" {{ (isset($item['skor_self']) && $item['skor_self'] == 3) ? 'selected' : '' }}>3</option>
                                                </select>
                                            @else
                                                <span class="fw-bold">{{ $item['skor_self'] ?? '-' }}</span>
                                            @endif
                                        </td>

                                        <!-- KOLOM PEER -->
                                        <td class="text-center {{ $roleEvaluasi == 'peer' ? 'bg-primary bg-opacity-10' : '' }}">
                                            @if($roleEvaluasi == 'peer')
                                                <select name="skor[{{ $item['item_id'] }}]" class="form-select form-select-sm border-primary dropdown-skor" required>
                                                    <option value="" disabled {{ !isset($item['skor_peer']) ? 'selected' : '' }}>-</option>
                                                    <option value="1" {{ (isset($item['skor_peer']) && $item['skor_peer'] == 1) ? 'selected' : '' }}>1</option>
                                                    <option value="2" {{ (isset($item['skor_peer']) && $item['skor_peer'] == 2) ? 'selected' : '' }}>2</option>
                                                    <option value="3" {{ (isset($item['skor_peer']) && $item['skor_peer'] == 3) ? 'selected' : '' }}>3</option>
                                                </select>
                                            @else
                                                <span class="fw-bold">{{ $item['skor_peer'] ?? '-' }}</span>
                                            @endif
                                        </td>

                                        <!-- KOLOM KARU -->
                                        <td class="text-center {{ $roleEvaluasi == 'karu' ? 'bg-primary bg-opacity-10' : '' }}">
                                            @if($roleEvaluasi == 'karu')
                                                <select name="skor[{{ $item['item_id'] }}]" class="form-select form-select-sm border-primary dropdown-skor" required>
                                                    <option value="" disabled {{ !isset($item['skor_karu']) ? 'selected' : '' }}>-</option>
                                                    <option value="1" {{ (isset($item['skor_karu']) && $item['skor_karu'] == 1) ? 'selected' : '' }}>1</option>
                                                    <option value="2" {{ (isset($item['skor_karu']) && $item['skor_karu'] == 2) ? 'selected' : '' }}>2</option>
                                                    <option value="3" {{ (isset($item['skor_karu']) && $item['skor_karu'] == 3) ? 'selected' : '' }}>3</option>
                                                </select>
                                            @else
                                                <span class="fw-bold">{{ $item['skor_karu'] ?? '-' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- KOTAK PERHITUNGAN REALTIME --}}
                @if($canEdit)
                <div class="card border-primary bg-primary bg-opacity-10 mb-4 mt-2">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center py-2 px-4">
                        <div class="mb-2 mb-md-0 text-primary">
                            <span class="fw-bold me-2">Total Skor Sementara:</span> 
                            <span id="rt-total" class="fs-5 fw-bold">0</span> 
                            <span class="text-muted">/ <span id="rt-max">0</span></span>
                        </div>
                        <div class="text-success">
                            <span class="fw-bold me-2">Persentase:</span> 
                            <span id="rt-persentase" class="fs-4 fw-bold">0.00%</span>
                        </div>
                    </div>
                </div>
                @endif
@if($roleEvaluasi == 'karu')
                <div class="card border-info bg-info bg-opacity-10 mb-4 mt-3 shadow-sm">
                    <div class="card-header bg-info text-white fw-bold">
                        <i class="bi bi-bar-chart-fill me-2"></i> Rekapitulasi Nilai Kumulatif (3 Penilai)
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 border-end border-info border-opacity-25">
                                <span class="text-muted d-block small fw-bold">Total Diri Sendiri</span>
                                <span class="fs-4 fw-bold text-primary">{{ $sumSelf }}</span>
                            </div>
                            <div class="col-md-3 border-end border-info border-opacity-25">
                                <span class="text-muted d-block small fw-bold">Total Sejawat</span>
                                <span class="fs-4 fw-bold text-success">{{ $sumPeer }}</span>
                            </div>
                            <div class="col-md-3 border-end border-info border-opacity-25">
                                <span class="text-muted d-block small fw-bold">Total Atasan (Live)</span>
                                <span class="fs-4 fw-bold text-warning" id="rekap-karu">0</span>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted d-block small fw-bold">Rata-Rata Akhir</span>
                                <span class="fs-4 fw-bold text-danger" id="rekap-rata">0.00%</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($roleEvaluasi == 'karu')
                    <div class="mb-4 mt-3">
                        <label class="form-label fw-bold">Catatan Evaluator / Kepala Ruangan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Berikan evaluasi akhir atau rekomendasi untuk pegawai..." required></textarea>
                    </div>
                @endif

                @if($canEdit)
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" name="action" value="final" class="btn btn-primary fw-bold px-5 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-save me-2"></i> Kunci & Simpan Penilaian
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // =========================================================================
    // FUNGSI SUPER AUTO-RELOAD (ANTI GAGAL)
    // =========================================================================
    document.querySelectorAll('.auto-reload-switch').forEach(function(switchElement) {
        switchElement.addEventListener('change', function() {
            let status = this.checked ? 'menambahkan' : 'menghapus';
            if(confirm(`Halaman akan dimuat ulang untuk ${status} kompetensi tambahan. Lanjutkan?`)) {
                let form = document.getElementById('formEvaluasi');
                
                // Tambahkan elemen hidden penanda action
                let actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'update_instrumen';
                form.appendChild(actionInput);
                
                // [PENTING] Bunuh (Disable) semua atribut required agar HTML5 tidak menghalangi form tersubmit
                document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));
                
                // Eksekusi Submit
                form.submit();
            } else {
                // Kembalikan tombol ke arah semula jika user klik 'Cancel'
                this.checked = !this.checked; 
            }
        });
    });

    // =========================================================================
    // KALKULASI SKOR
    // =========================================================================
// =========================================================================
    // KALKULASI SKOR
    // =========================================================================
    function hitungRealtime() {
        let selects = document.querySelectorAll('.dropdown-skor');
        let totalScore = 0;
        let maxScore = selects.length * 3;

        selects.forEach(function(select) {
            if (select.value) {
                totalScore += parseInt(select.value);
            }
        });

        let percentage = 0;
        if(maxScore > 0) {
            percentage = ((totalScore / maxScore) * 100).toFixed(2);
        }

        // Update Kotak Realtime Biasa
        let elTotal = document.getElementById('rt-total');
        let elMax = document.getElementById('rt-max');
        let elPersen = document.getElementById('rt-persentase');

        if(elTotal) elTotal.innerText = totalScore;
        if(elMax) elMax.innerText = maxScore;
        if(elPersen) elPersen.innerText = percentage + '%';

        // Update Kotak Rekapitulasi Kumulatif Khusus Karu
        let roleEvaluasi = "{{ $roleEvaluasi }}";
        if (roleEvaluasi === 'karu') {
            let sumSelf = {{ $sumSelf ?? 0 }};
            let sumPeer = {{ $sumPeer ?? 0 }};
            
            let rekapKaru = document.getElementById('rekap-karu');
            let rekapRata = document.getElementById('rekap-rata');

            if (rekapKaru) rekapKaru.innerText = totalScore;

            if (rekapRata && maxScore > 0) {
                // Hitung persentase masing-masing
                let persenSelf = (sumSelf / maxScore) * 100;
                let persenPeer = (sumPeer / maxScore) * 100;
                let persenKaru = (totalScore / maxScore) * 100;

                // Rata-rata dari 3 penilai
                let rataRata = (persenSelf + persenPeer + persenKaru) / 3;
                rekapRata.innerText = rataRata.toFixed(2) + '%';
            }
        }

        return { totalScore, maxScore, percentage };
    }

    document.addEventListener('DOMContentLoaded', function() {
        let selects = document.querySelectorAll('.dropdown-skor');
        selects.forEach(function(select) {
            select.addEventListener('change', hitungRealtime);
        });
        hitungRealtime();
    });

    function isiSemuaNilai3() {
        if(confirm('Isi otomatis semua kompetensi dengan nilai 3 (Sesuai dengan standar)?')) {
            let dropdowns = document.querySelectorAll('.dropdown-skor');
            dropdowns.forEach(function(select) {
                select.value = "3";
            });
            hitungRealtime();
        }
    }

    document.getElementById('formEvaluasi').addEventListener('submit', function(e) {
        let actionVal = document.querySelector('input[name="action"][value="update_instrumen"]');
        if(actionVal) return true; // Jangan cegat form jika sedang auto reload dari switch

        e.preventDefault(); 
        let hasil = hitungRealtime();

        Swal.fire({
            title: 'Kunci & Simpan Penilaian?',
            html: `Total Skor Anda: <b>${hasil.totalScore}</b> dari Maksimal <b>${hasil.maxScore}</b><br>
                   Persentase Nilai: <b>${hasil.percentage}%</b><br><br>
                   <span class='text-danger small'><b>Perhatian:</b> Data yang sudah dikunci dan disimpan tidak dapat diubah lagi!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-check-circle"></i> Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let finalAction = document.createElement('input');
                finalAction.type = 'hidden';
                finalAction.name = 'action';
                finalAction.value = 'final';
                this.appendChild(finalAction);
                this.submit(); 
            }
        });
    });
</script>
@endpush