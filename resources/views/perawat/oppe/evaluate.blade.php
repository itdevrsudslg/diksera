@extends('layouts.app')
@section('title', 'Penilaian OPPE')

@section('content')
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
                <div class="d-flex justify-content-end mb-2">
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
                                                    <option value="" disabled selected>-</option>
                                                    <option value="1">1</option><option value="2">2</option><option value="3">3</option>
                                                </select>
                                            @else
                                                <span class="fw-bold">{{ $item['skor_self'] ?? '-' }}</span>
                                            @endif
                                        </td>

                                        <!-- KOLOM PEER -->
                                        <td class="text-center {{ $roleEvaluasi == 'peer' ? 'bg-primary bg-opacity-10' : '' }}">
                                            @if($roleEvaluasi == 'peer')
                                                <select name="skor[{{ $item['item_id'] }}]" class="form-select form-select-sm border-primary dropdown-skor" required>
                                                    <option value="" disabled selected>-</option>
                                                    <option value="1">1</option><option value="2">2</option><option value="3">3</option>
                                                </select>
                                            @else
                                                <span class="fw-bold">{{ $item['skor_peer'] ?? '-' }}</span>
                                            @endif
                                        </td>

                                        <!-- KOLOM KARU -->
                                        <td class="text-center {{ $roleEvaluasi == 'karu' ? 'bg-primary bg-opacity-10' : '' }}">
                                            @if($roleEvaluasi == 'karu')
                                                <select name="skor[{{ $item['item_id'] }}]" class="form-select form-select-sm border-primary dropdown-skor" required>
                                                    <option value="" disabled selected>-</option>
                                                    <option value="1">1</option><option value="2">2</option><option value="3">3</option>
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
                    <div class="mb-4 mt-3">
                        <label class="form-label fw-bold">Catatan Evaluator / Kepala Ruangan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Berikan evaluasi akhir atau rekomendasi untuk pegawai..." required></textarea>
                    </div>
                @endif

                @if($canEdit)
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary fw-bold px-5 py-2 rounded-pill shadow-sm">
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
    // Fungsi untuk menghitung nilai secara realtime
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

        // Update ke tampilan HTML
        let elTotal = document.getElementById('rt-total');
        let elMax = document.getElementById('rt-max');
        let elPersen = document.getElementById('rt-persentase');

        if(elTotal) elTotal.innerText = totalScore;
        if(elMax) elMax.innerText = maxScore;
        if(elPersen) elPersen.innerText = percentage + '%';

        return { totalScore, maxScore, percentage };
    }

    // Pasang Event Listener 'change' ke setiap dropdown
    document.addEventListener('DOMContentLoaded', function() {
        let selects = document.querySelectorAll('.dropdown-skor');
        selects.forEach(function(select) {
            select.addEventListener('change', hitungRealtime);
        });
        
        // Panggil sekali saat halaman dimuat
        hitungRealtime();
    });

    // Fungsi untuk mengubah semua dropdown skor menjadi bernilai 3
    function isiSemuaNilai3() {
        if(confirm('Isi otomatis semua kompetensi dengan nilai 3 (Sesuai dengan standar)?')) {
            let dropdowns = document.querySelectorAll('.dropdown-skor');
            dropdowns.forEach(function(select) {
                select.value = "3";
            });
            // Update skor realtime setelah diisi otomatis
            hitungRealtime();
        }
    }

    // Fungsi SweetAlert sebelum Submit
    document.getElementById('formEvaluasi').addEventListener('submit', function(e) {
        e.preventDefault(); // Hentikan proses submit sementara

        // Ambil hasil perhitungan terbaru
        let hasil = hitungRealtime();

        // Munculkan SweetAlert konfirmasi
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
                this.submit(); 
            }
        });
    });
</script>
@endpush