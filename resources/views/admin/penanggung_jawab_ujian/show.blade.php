@extends('layouts.app')

@php
    $pageTitle = 'Dashboard Asesor';
    $pageSubtitle = 'Detail riwayat ujian oleh: ' . $penanggungJawab->nama;
@endphp

@section('title', 'Dashboard Asesor - ' . $penanggungJawab->nama)

@section('content')
<div class="container-fluid">
    
    <!-- Tombol Kembali -->
    <div class="mb-4">
        <a href="{{ route('admin.penanggung-jawab.index') }}" class="btn btn-sm btn-light border fw-bold shadow-sm rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- SUMMARY CARDS (4 Kolom) -->
    <div class="row mb-4">
        <!-- Card 1: Total Selesai -->
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <h6 class="text-white-50 fw-bold mb-1" style="font-size: 11px;">TOTAL SELESAI</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="fw-bold mb-0">{{ $totalSemuaSelesai }}</h2>
                        <div class="fs-1 opacity-25"><i class="bi bi-check2-all"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 2: Uji Kompetensi Selesai -->
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 bg-info text-white h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <h6 class="text-white-50 fw-bold mb-1" style="font-size: 11px;">UJI KOMP SELESAI</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="fw-bold mb-0">{{ $totalUjiKompSelesai }}</h2>
                        <div class="fs-1 opacity-25"><i class="bi bi-ui-checks"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 3: Kredensial Selesai -->
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 bg-secondary text-white h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <h6 class="text-white-50 fw-bold mb-1" style="font-size: 11px;">KREDENSIAL SELESAI</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="fw-bold mb-0">{{ $totalKredensialSelesai }}</h2>
                        <div class="fs-1 opacity-25"><i class="bi bi-card-checklist"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 4: On Going (Sedang Berjalan) -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <h6 class="text-dark-50 fw-bold mb-1 opacity-75" style="font-size: 11px;">ON GOING / TERJADWAL</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="fw-bold mb-0">{{ $totalOnGoing }}</h2>
                        <div class="fs-1 opacity-25"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER & TABEL RIWAYAT -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4 border-bottom pb-3">Daftar & Riwayat Ujian</h5>
            
            <!-- FORM FILTER -->
            <form action="{{ route('admin.penanggung-jawab.show', $penanggungJawab->id) }}" method="GET" class="row g-2 mb-4 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Tanggal Sampai</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Tipe Ujian</label>
                    <select name="metode" class="form-select form-select-sm">
                        <option value="">Semua Tipe</option>
                        <option value="pg_interview" {{ request('metode') == 'pg_interview' ? 'selected' : '' }}>Uji Kompetensi</option>
                        <option value="interview_only" {{ request('metode') == 'interview_only' ? 'selected' : '' }}>Kredensial</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select name="status_jadwal" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="completed" {{ request('status_jadwal') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="approved" {{ request('status_jadwal') == 'approved' ? 'selected' : '' }}>On Going</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Cari Nama Peserta</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Ketik nama..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <!-- TABEL DATA -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Nama Peserta</th>
                            <th>Tanggal Terjadwal</th>
                            <th>Tipe Ujian</th>
                            <th>Status Ujian</th>
                            <th class="text-center">Hasil/Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $item)
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration + $riwayat->firstItem() - 1 }}</td>
                            <td>
                                <!-- LINK MENUJU DETAIL PERAWAT -->
                                <a href="{{ route('admin.perawat.show', $item->pengajuan->user_id) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $item->pengajuan->user->name ?? 'User Hapus' }}
                                </a>
                                <br>
                                <small class="text-muted">
                                    {{ is_array($item->pengajuan->kfk) ? implode(', ', $item->pengajuan->kfk) : $item->pengajuan->kfk }}
                                </small>
                            </td>
                          <td>
                                {{ \Carbon\Carbon::parse($item->waktu_wawancara)->format('d M Y, H:i') }}
                                
                                {{-- Tombol Edit HANYA muncul jika statusnya 'approved' (On Going / Belum selesai) --}}
                                @if($item->status === 'approved')
                                    <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2" data-bs-toggle="modal" data-bs-target="#editTanggalModal-{{ $item->id }}" title="Edit Tanggal Ujian">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- MODAL EDIT TANGGAL -->
                                    <div class="modal fade" id="editTanggalModal-{{ $item->id }}" tabindex="-1" aria-labelledby="editTanggalModalLabel-{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <form action="{{ route('admin.penanggung-jawab.update_tanggal', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-light">
                                                        <h6 class="modal-title fw-bold" id="editTanggalModalLabel-{{ $item->id }}">
                                                            <i class="bi bi-calendar-event me-2"></i>Edit Tanggal Ujian
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                              <!-- ... kode modal di atasnya ... -->
<div class="modal-body text-start">
    <div class="alert alert-info py-2 small mb-3">
        <strong>Peserta:</strong> {{ $item->pengajuan->user->name ?? 'User' }} <br>
        <strong>Asesor Saat Ini:</strong> {{ $penanggungJawab->nama }}
    </div>
    
    <!-- Input Waktu Wawancara (Existing) -->
    <div class="mb-3">
        <label class="form-label fw-bold small text-muted">Tentukan Waktu Ujian Baru</label>
        <input type="datetime-local" class="form-control" name="waktu_wawancara" 
            value="{{ \Carbon\Carbon::parse($item->waktu_wawancara)->format('Y-m-d\TH:i') }}" required>
    </div>

    <!-- Tambahan Input Pindah Asesor -->
    <div class="mb-3">
        <label class="form-label fw-bold small text-muted">Pindah Asesor (Opsional)</label>
        <select name="penanggung_jawab_id" class="form-select">
            <option value="{{ $penanggungJawab->id }}" selected>Tetap di {{ $penanggungJawab->nama }}</option>
            @foreach($listAsesor as $asesor)
                <option value="{{ $asesor->id }}">{{ $asesor->nama }}</option>
            @endforeach
        </select>
    </div>
</div>
<!-- ... kode modal di bawahnya ... -->
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END MODAL -->
                                @endif
                            </td>
                            <td>
                                @if($item->pengajuan->metode === 'interview_only')
                                    <span class="badge bg-secondary">Kredensial</span>
                                @else
                                    <span class="badge bg-info">Uji Kompetensi</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status === 'completed')
                                    <span class="text-success fw-bold small"><i class="bi bi-check2-all"></i> Selesai</span>
                                @elseif($item->status === 'approved')
                                    <span class="text-warning fw-bold small"><i class="bi bi-hourglass-split"></i> On Going</span>
                                @else
                                    <span class="text-muted fw-bold small">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->status === 'completed' && isset($item->penilaian->keputusan))
                                    @if(in_array($item->penilaian->keputusan, ['lulus', 'valid']))
                                        <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check-circle"></i> {{ strtoupper($item->penilaian->keputusan) }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger"><i class="bi bi-x-circle"></i> {{ strtoupper($item->penilaian->keputusan) }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-muted border">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-folder2-open display-6 d-block mb-3 opacity-50"></i>
                                Tidak ada data yang ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $riwayat->links('vendor.pagination.diksera') }}
            </div>

        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif
        });
    </script>

@endsection