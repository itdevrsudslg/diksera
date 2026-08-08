@extends('layouts.app')

@section('title', 'Penilaian Wawancara')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-body: #f8fafc;
            --border-color: #e2e8f0;
            /* Colors for Decision */
            --success-bg: #ecfdf5; --success-border: #10b981; --success-text: #047857;
            --danger-bg: #fef2f2; --danger-border: #ef4444; --danger-text: #b91c1c;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        /* --- Header --- */
        .page-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        /* --- Main Card --- */
        .form-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
        }

        /* --- Ticket Header Info --- */
        .ticket-header {
            background: #f1f5f9;
            padding: 24px 32px;
            border-bottom: 1px dashed #cbd5e1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        .info-block { display: flex; align-items: center; gap: 16px; }
        .info-icon {
            width: 48px; height: 48px;
            background: #fff; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .info-label { font-size: 0.75rem; text-transform: uppercase; color: var(--text-gray); font-weight: 600; letter-spacing: 0.5px; }
        .info-value { font-size: 1.1rem; font-weight: 700; color: var(--text-dark); }

        /* --- Form Sections --- */
        .form-body { padding: 32px; }
        .section-header {
            display: flex; align-items: center; gap: 10px;
            font-size: 1rem; font-weight: 700; color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f5f9;
        }
        .section-icon { color: var(--primary-color); }

        /* --- Score Inputs --- */
        .score-card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            transition: all 0.2s;
        }
        .score-card:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }
        .score-label { font-size: 0.9rem; font-weight: 600; color: var(--text-gray); margin-bottom: 8px; display: block; }
        .score-input-wrapper { position: relative; }
        .score-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            text-align: center;
        }
        .score-input:focus { outline: none; border-color: var(--primary-color); }
        .score-suffix {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            font-size: 0.85rem; color: #94a3b8; font-weight: 500;
        }

        /* --- Decision Cards (Radio) --- */
        .decision-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
        }
        .decision-item { position: relative; }
        .decision-item input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
        
        .decision-box {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 24px;
            background: #fff;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            height: 100%;
            color: var(--text-gray);
        }
        .decision-box i { font-size: 2rem; margin-bottom: 8px; transition: transform 0.3s; }
        .decision-title { font-size: 1rem; font-weight: 700; text-transform: uppercase; }
        .decision-desc { font-size: 0.8rem; font-weight: 400; opacity: 0.8; }

        /* Hover */
        .decision-box:hover { background: #f8fafc; border-color: #cbd5e1; }

        /* Checked Lulus */
        .decision-item input[value="lulus"]:checked + .decision-box {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        /* Checked Tidak Lulus */
        .decision-item input[value="tidak_lulus"]:checked + .decision-box {
            background: var(--danger-bg);
            border-color: var(--danger-border);
            color: var(--danger-text);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }
        
        .decision-item input:checked + .decision-box i { transform: scale(1.2); }

        /* --- Submit --- */
        .btn-save {
            background: var(--primary-color); color: #fff;
            padding: 12px 32px; border-radius: 50px;
            font-weight: 600; border: none; font-size: 1rem;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
            transition: all 0.2s;
        }
        .btn-save:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(37, 99, 235, 0.4); }

        /* --- Average Badge --- */
        .avg-badge {
            background: #334155; color: #fff;
            padding: 6px 12px; border-radius: 8px;
            font-size: 0.9rem; font-weight: 600;
        }
    </style>
@endpush

@section('content')
<div class=" py-5">
    
    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Penilaian Wawancara</h1>
            <p class="text-muted small mb-0 mt-1">Input skor kompetensi dan hasil akhir seleksi wawancara.</p>
        </div>
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-light border fw-bold shadow-sm" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.pengajuan_wawancara.store_penilaian', $jadwal->id) }}" method="POST">
        @csrf

        <div class="form-card">
            
            {{-- 1. INFO HEADER (TICKET STYLE) --}}
            <div class="ticket-header">
                <div class="info-block border-end border-light pe-4">
                    <div class="info-icon text-primary"><i class="bi bi-person-badge"></i></div>
                    <div>
                        <div class="info-label">Peserta</div>
                        <div class="info-value">{{ $jadwal->pengajuan->user->name }}</div>
                    </div>
                </div>
                <div class="info-block ps-4">
                    <div class="info-icon text-info"><i class="bi bi-person-video2"></i></div>
                    <div>
                        <div class="info-label">Pewawancara</div>
                        <div class="info-value">{{ $jadwal->pewawancara->nama }}</div>
                    </div>
                </div>
            </div>

            <div class="form-body">
                
                {{-- 2. INPUT NILAI --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-header mb-0 border-0 pb-0">
                        <i class="bi bi-calculator section-icon"></i> A. Poin Penilaian
                    </div>
                    <div class="avg-badge" id="avgDisplay" style="display: none;">
                        Rata-rata: <span id="avgScore">0</span>
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    {{-- Kompetensi --}}
                    <div class="col-md-4">
                        <div class="score-card">
                            <label class="score-label">Kompetensi Teknis</label>
                            <div class="score-input-wrapper">
                                <input type="number" name="skor_kompetensi" class="score-input calc-input" min="0" max="100" placeholder="0" required>
                                <span class="score-suffix">/100</span>
                            </div>
                        </div>
                    </div>
                    {{-- Sikap --}}
                    <div class="col-md-4">
                        <div class="score-card">
                            <label class="score-label">Sikap & Etika</label>
                            <div class="score-input-wrapper">
                                <input type="number" name="skor_sikap" class="score-input calc-input" min="0" max="100" placeholder="0" required>
                                <span class="score-suffix">/100</span>
                            </div>
                        </div>
                    </div>
                    {{-- Pengetahuan --}}
                    <div class="col-md-4">
                        <div class="score-card">
                            <label class="score-label">Pengetahuan Umum</label>
                            <div class="score-input-wrapper">
                                <input type="number" name="skor_pengetahuan" class="score-input calc-input" min="0" max="100" placeholder="0" required>
                                <span class="score-suffix">/100</span>
                            </div>
                        </div>
                    </div>
                    {{-- Catatan --}}
                    <div class="col-12">
                        <label class="score-label ms-1">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tuliskan catatan mengenai kelebihan atau kekurangan peserta..." style="background: #f8fafc; border-color: #cbd5e1;"></textarea>
                    </div>
                </div>

                {{-- 3. KEPUTUSAN --}}
                <div class="section-header">
                    <i class="bi bi-gavel section-icon"></i> B. Keputusan Akhir
                </div>

                <div class="decision-grid mb-5">
                    {{-- LULUS --}}
                    <div class="decision-item">
                        <input type="radio" name="keputusan" value="lulus" id="dec_lulus" required>
                        <label for="dec_lulus" class="decision-box">
                            <i class="bi bi-check-circle-fill"></i>
                            <div class="decision-title">LULUS</div>
                            <div class="decision-desc">Direkomendasikan Lulus</div>
                        </label>
                    </div>
                    {{-- TIDAK LULUS --}}
                    <div class="decision-item">
                        <input type="radio" name="keputusan" value="tidak_lulus" id="dec_fail" required>
                        <label for="dec_fail" class="decision-box">
                            <i class="bi bi-x-circle-fill"></i>
                            <div class="decision-title">TIDAK LULUS</div>
                            <div class="decision-desc">Belum Memenuhi Syarat</div>
                        </label>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="text-end">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-save2 me-2"></i> Simpan Penilaian
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Simple Script to Calculate Average Realtime (UX Enhancement)
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.calc-input');
        const avgDisplay = document.getElementById('avgDisplay');
        const avgScore = document.getElementById('avgScore');
        const radioLulus = document.getElementById('dec_lulus');
        const radioFail = document.getElementById('dec_fail');

        function calculateAvg() {
            let total = 0;
            let filled = 0;
            inputs.forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val)) {
                    total += val;
                    filled++;
                }
            });

            if (filled > 0) {
                const avg = (total / 3).toFixed(1); // Bagi 3 karena ada 3 komponen
                avgScore.innerText = avg;
                avgDisplay.style.display = 'inline-block';

                // Auto suggest decision (Optional UX)
                // if (avg >= 70) radioLulus.checked = true;
                // else radioFail.checked = true;
            } else {
                avgDisplay.style.display = 'none';
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculateAvg);
        });
    });
</script>
@endpush