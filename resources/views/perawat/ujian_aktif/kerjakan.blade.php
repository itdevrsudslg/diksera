@extends('layouts.app')

@section('title', 'Ujian: ' . $form->judul)

@push('styles')
    <style>
        /* --- Layout & Spacing --- */
        .exam-container {
            padding-bottom: 100px;
        }
        
        body {
            -webkit-user-select: none; /* Safari */
            -ms-user-select: none;     /* IE 10 dan 11 */
            user-select: none;         /* Standar (Chrome, Firefox, Opera) */
        }

        /* --- Question Card (Pagination Logic) --- */
        .question-card {
            display: none; /* Sembunyikan semua secara default */
            animation: fadeIn 0.3s ease-in-out;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.2s;
            scroll-margin-top: 100px;
        }

        .question-card.active {
            display: block; /* Hanya tampilkan yang aktif */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .question-badge {
            width: 32px;
            height: 32px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* --- Custom Radio Options --- */
        .option-label {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            background: #fff;
            position: relative;
        }

        .option-label:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        /* State: Checked */
        .btn-check:checked+.option-label {
            background-color: #eff6ff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
            color: #1e40af;
        }

        /* Circle Indicator di dalam opsi */
        .option-circle {
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .option-circle::after {
            content: '';
            width: 10px;
            height: 10px;
            background: #3b82f6;
            border-radius: 50%;
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s;
        }

        .btn-check:checked+.option-label .option-circle {
            border-color: #3b82f6;
        }

        .btn-check:checked+.option-label .option-circle::after {
            opacity: 1;
            transform: scale(1);
        }

        /* --- Sidebar Navigation (Desktop) --- */
        .sidebar-sticky {
            position: sticky;
            top: 90px;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }

        .nav-item-box {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #f1f5f9;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid transparent;
            cursor: pointer;
            padding: 0;
        }

        .nav-item-box:hover {
            background: #e2e8f0;
            color: #334155;
        }

        .nav-item-box.answered {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        /* Warna box navigasi saat ragu-ragu */
        .nav-item-box.flagged {
            background: #f59e0b !important;
            color: #fff !important;
            border-color: #f59e0b !important;
        }

        /* --- Timer Box --- */
        .timer-box {
            background: #fff;
            border: 1px solid #fee2e2;
            color: #dc2626;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .timer-time {
            font-size: 24px;
            font-weight: 800;
            font-family: 'Courier New', monospace;
            line-height: 1;
        }

        /* --- Mobile Sticky Footer --- */
        .mobile-footer {
            display: none;
        }

        @media (max-width: 991.98px) {
            .sidebar-sticky {
                display: none;
            }

            .mobile-footer {
                display: block;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #fff;
                padding: 15px;
                border-top: 1px solid #e2e8f0;
                z-index: 1030;
                box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
            }

            .exam-container {
                padding-bottom: 120px;
            }
        }
    </style>
@endpush

@section('content')

    {{-- Progress Bar Top --}}
    <div class="progress" style="height: 4px; border-radius: 0; position: sticky; top: 0; z-index: 1040;">
        <div class="progress-bar bg-primary" role="progressbar" id="progressBar" style="width: 0%"></div>
    </div>

    <div class="container-fluid mt-4 exam-container">
        <form action="{{ route('perawat.ujian.submit', $form->slug) }}" method="POST" id="examForm">
            @csrf

            <div class="row g-4">

                {{-- KOLOM KIRI: SOAL --}}
                <div class="col-lg-8">

                    {{-- Header Mobile (Timer Only) --}}
                    <div class="d-lg-none mb-3">
                        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm border">
                            <span class="fw-bold text-truncate" style="max-width: 200px;">{{ $form->judul }}</span>
                            <span class="badge bg-danger bg-opacity-10 text-danger fs-6" id="mobile-timer">--:--:--</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Lembar Soal</h5>
                        <span class="text-muted small">Total: {{ $questions->count() }} Soal</span>
                    </div>

                    @forelse($questions as $index => $soal)
                        {{-- Class 'active' hanya untuk soal pertama (index 0) --}}
                        <div class="card question-card bg-white p-4 mb-4 {{ $index == 0 ? 'active' : '' }}" id="soal-{{ $index + 1 }}">
                            
                            {{-- Header Soal --}}
                            <div class="d-flex gap-3 mb-3">
                                <div class="question-badge">{{ $index + 1 }}</div>
                                <div class="flex-grow-1">
                                    <div class="flex-grow-1">
                                        @if ($soal->gambar)
                                            <div class="mb-3">
                                                <img src="{{ asset('storage/' . $soal->gambar) }}" alt="Gambar Soal"
                                                    class="img-fluid rounded border"
                                                    style="max-height: 300px; width: auto;">
                                            </div>
                                        @endif

                                        <h6 class="text-dark lh-base mb-0" style="font-size: 16px; font-weight: 500;">
                                            {!! nl2br(e($soal->pertanyaan)) !!}
                                        </h6>
                                    </div>
                                </div>
                            </div>

                            {{-- Opsi Jawaban --}}
                            <div class="d-flex flex-column gap-2 ms-md-5">
                                @foreach (['a', 'b', 'c', 'd', 'e'] as $optKey)
                                    @if (isset($soal->opsi_jawaban[$optKey]) && !empty($soal->opsi_jawaban[$optKey]))
                                        <div>
                                            <input type="radio" class="btn-check answer-radio"
                                                name="answers[{{ $soal->id }}]"
                                                id="q_{{ $soal->id }}_{{ $optKey }}" value="{{ $optKey }}"
                                                data-index="{{ $index + 1 }}" autocomplete="off">

                                            <label class="option-label" for="q_{{ $soal->id }}_{{ $optKey }}">
                                                <div class="option-circle"></div>
                                                <div class="flex-grow-1 text-dark">{{ $soal->opsi_jawaban[$optKey] }}</div>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Navigasi per Soal & Tombol Ragu-ragu --}}
                            <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQuestion(-1)" {{ $index == 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-arrow-left"></i> Sebelumnya
                                </button>

                                <div class="form-check form-switch mx-2">
                                    <input class="form-check-input" type="checkbox" id="flag-{{ $index + 1 }}" onchange="toggleFlag({{ $index + 1 }})" style="cursor: pointer;">
                                    <label class="form-check-label text-warning fw-bold" for="flag-{{ $index + 1 }}" style="cursor: pointer;">Ragu-ragu</label>
                                </div>

                                @if($index == count($questions) - 1)
                                    <button type="button" class="btn btn-success fw-bold" onclick="confirmSubmit()">
                                        Selesai <i class="bi bi-check2-circle"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary" onclick="changeQuestion(1)">
                                        Selanjutnya <i class="bi bi-arrow-right"></i>
                                    </button>
                                @endif
                            </div>

                        </div>
                    @empty
                        <div class="alert alert-info text-center py-5">
                            <i class="bi bi-info-circle display-4 mb-3 d-block"></i>
                            Tidak ada soal yang tersedia untuk ujian ini.
                        </div>
                    @endforelse
                </div>

                {{-- KOLOM KANAN: NAVIGASI & TIMER (Sticky Desktop) --}}
                <div class="col-lg-4">
                    <div class="sidebar-sticky">

                        {{-- Timer Card --}}
                        <div class="timer-box">
                            <div class="small text-uppercase fw-bold mb-1 opacity-75">Sisa Waktu</div>
                            <div id="desktop-timer" class="timer-time">--:--:--</div>
                        </div>

                        {{-- Navigasi Soal Card --}}
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white py-3 border-bottom-0">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-grid-3x3-gap me-2"></i> Navigasi Soal</h6>
                            </div>
                            <div class="card-body pt-0">
                                <div class="nav-grid">
                                    @foreach ($questions as $index => $soal)
                                        <button type="button" id="nav-item-{{ $index + 1 }}" onclick="goToQuestion({{ $index + 1 }})" class="nav-item-box">
                                            {{ $index + 1 }}
                                        </button>
                                    @endforeach
                                </div>

                                <div class="d-flex flex-wrap gap-3 mt-3 justify-content-center">
                                    <div class="d-flex align-items-center gap-1 small text-muted">
                                        <span style="width:10px; height:10px; background:#f1f5f9; display:inline-block; border-radius:2px; border:1px solid #cbd5e1;"></span> Kosong
                                    </div>
                                    <div class="d-flex align-items-center gap-1 small text-muted">
                                        <span style="width:10px; height:10px; background:#2563eb; display:inline-block; border-radius:2px;"></span> Terjawab
                                    </div>
                                    <div class="d-flex align-items-center gap-1 small text-muted">
                                        <span style="width:10px; height:10px; background:#f59e0b; display:inline-block; border-radius:2px;"></span> Ragu
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Submit Desktop --}}
                        <button type="button" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" onclick="confirmSubmit()">
                            <i class="bi bi-send-fill me-2"></i> Kumpulkan Jawaban
                        </button>
                    </div>
                </div>
            </div>

            {{-- FOOTER MOBILE --}}
            <div class="mobile-footer">
                <div class="row align-items-center">
                    <div class="col-6">
                        <small class="text-muted d-block">Terjawab</small>
                        <span class="fw-bold text-primary fs-5"><span id="answered-count">0</span> /
                            {{ $questions->count() }}</span>
                    </div>
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-primary w-100 fw-bold" onclick="confirmSubmit()">
                            Selesai <i class="bi bi-check-lg ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>

    {{-- Script JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variabel Global Ujian
        const totalQuestions = {{ $questions->count() }};
        const formId = {{ $form->id }};
        const userId = {{ Auth::id() }};
        const flagStorageKey = `exam_flags_${formId}_${userId}`;
        let currentQuestion = 1;
        const examForm = document.getElementById("examForm");
        let violationCount = 0;

        document.addEventListener("DOMContentLoaded", function() {
            
            /* =========================================
               1. TIMER & ALERT LOGIC
               ========================================= */
            var countDownDate = new Date("{{ \Carbon\Carbon::parse($timerEnd)->format('Y-m-d H:i:s') }}").getTime();
            
            var warned10m = false;
            var warned5m = false;
            var warned15s = false;

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });

            var timerInterval = setInterval(function() {
                var now = new Date().getTime();
                var distance = countDownDate - now;

                if (distance <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById("desktop-timer").innerHTML = "00:00:00";
                    if(document.getElementById("mobile-timer")) document.getElementById("mobile-timer").innerHTML = "00:00:00";
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'WAKTU HABIS!',
                        text: 'Jawaban Anda akan dikirim otomatis.',
                        showConfirmButton: false,
                        timer: 2000,
                        allowOutsideClick: false
                    }).then(() => {
                        localStorage.removeItem(flagStorageKey);
                        examForm.submit();
                    });
                    return;
                }

                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                var textTime = (hours < 10 ? "0" + hours : hours) + ":" + 
                               (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                               (seconds < 10 ? "0" + seconds : seconds);

                document.getElementById("desktop-timer").innerHTML = textTime;
                if (document.getElementById("mobile-timer")) {
                    document.getElementById("mobile-timer").innerHTML = textTime;
                }

                // Peringatan 10 Menit
                if (distance <= 10 * 60 * 1000 && !warned10m) {
                    warned10m = true;
                    Toast.fire({ icon: 'info', title: 'Waktu sisa 10 menit lagi!' });
                }

                // Peringatan 5 Menit
                if (distance <= 5 * 60 * 1000) {
                    document.getElementById("desktop-timer").style.color = "#dc2626";
                    document.querySelector('.timer-box').classList.add('animate__animated', 'animate__pulse', 'animate__infinite');
                    if (!warned5m) {
                        warned5m = true;
                        Toast.fire({ icon: 'warning', title: 'Awas! Sisa waktu kurang dari 5 menit!' });
                    }
                }

                // Peringatan 15 Detik
                if (distance <= 15 * 1000 && !warned15s) {
                    warned15s = true;
                    playWarningSound();
                    Swal.fire({
                        title: '15 DETIK LAGI!',
                        text: 'Segera selesaikan jawaban Anda!',
                        icon: 'error',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: false,
                        position: 'center'
                    });
                }

            }, 1000);


            /* =========================================
               2. PAGINATION & FLAG LOGIC
               ========================================= */
            const radios = document.querySelectorAll('.answer-radio');
            
            window.showQuestion = function(index) {
                document.querySelectorAll('.question-card').forEach(card => card.classList.remove('active'));
                
                const targetCard = document.getElementById('soal-' + index);
                if (targetCard) targetCard.classList.add('active');
                
                currentQuestion = index;

                // Sync status checkbox ragu-ragu
                const flags = JSON.parse(localStorage.getItem(flagStorageKey)) || {};
                const flagInput = document.getElementById('flag-' + index);
                if(flagInput) {
                    flagInput.checked = !!flags[index];
                }
            }

            window.changeQuestion = function(step) {
                let newIndex = currentQuestion + step;
                if (newIndex >= 1 && newIndex <= totalQuestions) {
                    showQuestion(newIndex);
                }
            }

            window.goToQuestion = function(index) {
                showQuestion(index);
            }

            window.toggleFlag = function(index) {
                let flags = JSON.parse(localStorage.getItem(flagStorageKey)) || {};
                const isChecked = document.getElementById('flag-' + index).checked;
                
                if (isChecked) {
                    flags[index] = true;
                } else {
                    delete flags[index];
                }
                
                localStorage.setItem(flagStorageKey, JSON.stringify(flags));
                updateProgress(); 
            }

            window.updateProgress = function() {
                const answered = document.querySelectorAll('.answer-radio:checked').length;
                const flags = JSON.parse(localStorage.getItem(flagStorageKey)) || {};

                // Reset sidebar nav box
                for(let i = 1; i <= totalQuestions; i++) {
                    const navBox = document.getElementById('nav-item-' + i);
                    if(navBox) navBox.classList.remove('answered', 'flagged');
                }

                // Terjawab = Biru
                document.querySelectorAll('.answer-radio:checked').forEach(radio => {
                    const idx = radio.getAttribute('data-index');
                    const navBox = document.getElementById('nav-item-' + idx);
                    if (navBox) navBox.classList.add('answered');
                });

                // Ragu-ragu = Kuning (menimpa Biru)
                Object.keys(flags).forEach(idx => {
                    const navBox = document.getElementById('nav-item-' + idx);
                    if (navBox && flags[idx]) {
                        navBox.classList.remove('answered'); 
                        navBox.classList.add('flagged');
                    }
                });

                // Update Progress Bar & Counter
                if (document.getElementById('answered-count')) {
                    document.getElementById('answered-count').innerText = answered;
                }
                const percent = (answered / totalQuestions) * 100;
                document.getElementById('progressBar').style.width = percent + "%";
            }

            radios.forEach(radio => {
                radio.addEventListener('change', updateProgress);
            });

            // Init Load
            showQuestion(1);
            updateProgress();

            window.confirmSubmit = function() {
                const answered = document.querySelectorAll('.answer-radio:checked').length;
                const unanswered = totalQuestions - answered;
                
                const flags = JSON.parse(localStorage.getItem(flagStorageKey)) || {};
                const flagCount = Object.keys(flags).length;

                let warningText = "Pastikan Anda sudah memeriksa kembali semua jawaban.";
                if (unanswered > 0) {
                    warningText = "Masih ada " + unanswered + " soal yang BELUM dijawab! Yakin ingin mengumpulkan?";
                } else if (flagCount > 0) {
                    warningText = "Ada " + flagCount + " soal yang masih ditandai RAGU-RAGU! Yakin ingin mengumpulkan?";
                }

                Swal.fire({
                    title: 'Kumpulkan Jawaban?',
                    text: warningText,
                    icon: (unanswered > 0 || flagCount > 0) ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Kumpulkan!',
                    cancelButtonText: 'Periksa Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        localStorage.removeItem(flagStorageKey);
                        examForm.submit();
                    }
                });
            }


            /* =========================================
               3. KEAMANAN & ANTI-CHEAT
               ========================================= */
            function toggleFullscreen() {
                let elem = document.documentElement;
                if (!document.fullscreenElement) {
                    if (elem.requestFullscreen) { elem.requestFullscreen(); }
                    else if (elem.webkitRequestFullscreen) { elem.webkitRequestFullscreen(); }
                    else if (elem.msRequestFullscreen) { elem.msRequestFullscreen(); }
                }
            }

            function playWarningSound() {
                try {
                    let audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    let oscillator = audioCtx.createOscillator();
                    oscillator.type = 'sine'; 
                    oscillator.frequency.setValueAtTime(440, audioCtx.currentTime);
                    oscillator.connect(audioCtx.destination);
                    oscillator.start();
                    oscillator.stop(audioCtx.currentTime + 0.5);
                } catch (e) {
                    console.log("Audio blocked");
                }
            }

            function handleViolation(message) {
                violationCount++;
                playWarningSound();

                if (violationCount === 1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'PERINGATAN 1!',
                        text: message + '. Sekali lagi Anda melanggar, ujian akan otomatis TER-SUBMIT.',
                        confirmButtonText: 'Saya Mengerti & Kembali Ujian',
                        allowOutsideClick: false
                    }).then(() => {
                        toggleFullscreen();
                    });
                } else if (violationCount >= 2) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'UJIAN DIHENTIKAN!',
                        text: 'Anda melanggar aturan 2x. Jawaban akan dikirim otomatis sekarang.',
                        showConfirmButton: false,
                        timer: 2000,
                        allowOutsideClick: false
                    }).then(() => {
                        localStorage.removeItem(flagStorageKey);
                        examForm.submit();
                    });
                }
            }

            window.addEventListener('blur', function() {
                handleViolation("Dilarang meninggalkan halaman ujian (Minimize/Pindah Tab)");
            });

            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement) {
                    handleViolation("Ujian harus tetap dalam mode Fullscreen");
                }
            });

            document.addEventListener('click', function() {
                toggleFullscreen();
            }, { once: true });

            function blockEvent(e) { e.preventDefault(); return false; }
            document.addEventListener('contextmenu', blockEvent, true);
            document.addEventListener('copy', blockEvent, true);
            document.addEventListener('paste', blockEvent, true);
            document.addEventListener('cut', blockEvent, true);
            document.addEventListener('dragstart', blockEvent, true);
            document.addEventListener('selectstart', blockEvent, true);

            document.addEventListener('keydown', function(e) {
                const forbiddenKeys = [123, 44]; // F12 & PrintScreen
                if (
                    forbiddenKeys.includes(e.keyCode) || 
                    (e.ctrlKey && e.shiftKey && e.keyCode == 73) || 
                    (e.ctrlKey && e.keyCode == 85) ||
                    e.altKey || 
                    e.metaKey
                ) {
                    e.preventDefault();
                    return false;
                }
            });

        });
    </script>
@endsection