@extends('layouts.app')

{{-- 1. STYLES --}}
@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

    <style>
        :root {
            /* Palette Minimalis */
            --primary-blue: #2563eb;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
            --card-bg: #ffffff;
        }

        body {
            font-size: 0.9rem;
            /* 14.4px */
            color: var(--text-dark);
            background-color: var(--bg-light);
        }

        /* --- Header --- */
        .page-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }

        .page-header p {
            font-size: 0.85rem;
            color: var(--text-gray);
            margin: 0;
        }

        /* --- Metric Cards (Minimalis) --- */
        .metric-card {
            background: var(--card-bg);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .metric-info h6 {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-gray);
            margin-bottom: 4px;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1;
        }

        .icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .bg-blue-soft {
            background: #eff6ff;
            color: #2563eb;
        }

        .bg-orange-soft {
            background: #fff7ed;
            color: #ea580c;
        }

        .bg-green-soft {
            background: #f0fdf4;
            color: #16a34a;
        }

        /* --- Buttons --- */
        .btn-action {
            background-color: var(--primary-blue);
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            text-decoration: none;
        }

        .btn-action:hover {
            background-color: #1d4ed8;
            color: white;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
        }

        /* --- Calendar Clean Style --- */
        .calendar-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .fc-toolbar-title {
            font-size: 1.1rem !important;
            font-weight: 700;
            color: var(--text-dark);
        }

        .fc-button {
            font-size: 0.8rem !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
            text-transform: capitalize;
        }

        .fc-button-primary {
            background: white !important;
            border: 1px solid #e2e8f0 !important;
            color: var(--text-gray) !important;
            box-shadow: none !important;
        }

        .fc-button-active,
        .fc-button-primary:hover {
            background: #f8fafc !important;
            color: var(--primary-blue) !important;
            border-color: var(--primary-blue) !important;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #f1f5f9 !important;
        }

        .fc-col-header-cell-cushion {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-gray);
            text-transform: uppercase;
            padding: 8px 0 !important;
        }

        .fc-daygrid-day-number {
            font-size: 0.8rem;
            color: var(--text-dark);
            padding: 4px 8px !important;
            text-decoration: none;
        }

        .fc-day-today {
            background-color: #f8fafc !important;
        }

        .fc-event {
            border: none !important;
            border-radius: 4px;
            font-size: 0.75rem;
            padding: 2px 6px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush

{{-- 2. CONTENT --}}
@section('content')
    <div class="container-fluid py-4 px-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 page-header">
            <div>
                <h2>Dashboard Asesor</h2>
                <p>Selamat datang, {{ $pewawancara->nama }}</p>
            </div>
            <div>
                <a href="{{ route('pewawancara.antrian') }}" class="btn-action">
                    <i class="bi bi-list-task"></i> Lihat Antrian
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="alert alert-success border-0 bg-success bg-opacity-10 text-success small py-2 px-3 rounded-3 mb-4 d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 0.7rem;"></button>
            </div>
        @endif

        {{-- Metric Cards --}}
        <div class="row g-3 mb-4">
            {{-- Card 1 --}}
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-info">
                        <h6>Total Menunggu</h6>
                        <div class="metric-value">{{ $totalAntrian }}</div>
                    </div>
                    <div class="icon-box bg-blue-soft">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-info">
                        <h6>Jadwal Hari Ini</h6>
                        <div class="metric-value">{{ $hariIni }}</div>
                    </div>
                    <div class="icon-box bg-orange-soft">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-info">
                        <h6>Selesai Dinilai</h6>
                        <div class="metric-value">{{ $selesai }}</div>
                    </div>
                    <div class="icon-box bg-green-soft">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="row">
            <div class="col-12">
                <div class="calendar-card">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- 3. SCRIPTS --}}
@push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                themeSystem: 'standard',
                height: 'auto',
                headerToolbar: {
                    left: 'title',
                    right: 'prev,next today'
                },
                buttonText: {
                    today: 'Hari Ini'
                },
                events: @json($jadwalKalender),

                // Styling Event
                eventDidMount: function(info) {
                    info.el.style.backgroundColor = '#eff6ff';
                    info.el.style.color = '#1e293b';
                    info.el.style.borderLeft = '3px solid #2563eb';
                    info.el.style.fontSize = '0.75rem';
                },

                // Handle Click
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                }
            });
            calendar.render();
        });
    </script>
@endpush
