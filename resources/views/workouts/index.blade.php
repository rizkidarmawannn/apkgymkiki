@extends('layouts.app')

@section('title', 'Latihan')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div>
        <h1 class="mb-1">Latihan</h1>
        <p class="text-muted text-sm mb-0">Pantau rutinitas, frekuensi kalender, dan progres latihanmu.</p>
    </div>
    @if(isset($ongoingWorkout) && $ongoingWorkout)
        <div class="d-flex flex-gap-sm">
            <a href="{{ route('workouts.create', ['new' => 1]) }}" class="btn btn-outline btn-small">+ Sesi Baru</a>
            <a href="{{ route('workouts.resume', $ongoingWorkout) }}" class="btn btn-warning btn-small" style="font-weight: 700;">▶️ Lanjutkan Latihan</a>
        </div>
    @else
        <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Mulai Latihan</a>
    @endif
</div>

@if(isset($ongoingWorkout) && $ongoingWorkout)
    <div class="card mb-4 p-3" style="border: 2px solid var(--warning); background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(245, 158, 11, 0.02));">
        <div class="flex-between align-center mb-2">
            <div class="d-flex align-center flex-gap-sm">
                <span class="badge badge-warning" style="font-weight: 700; font-size: 0.8rem; letter-spacing: 0.04em;">⚡ SEDANG BERJALAN</span>
                <span class="text-sm text-muted">{{ \Carbon\Carbon::parse($ongoingWorkout->date)->format('d/m/Y') }}</span>
            </div>
            <form action="{{ route('workouts.cancel', $ongoingWorkout) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan sesi latihan ini?');" class="m-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-small btn-outline text-danger" style="padding: 2px 8px; font-size: 0.75rem;" title="Batalkan sesi">&times; Batalkan</button>
            </form>
        </div>
        <h3 class="mb-2">{{ $ongoingWorkout->name }}</h3>
        <div class="workout-meta mb-3">
            <span class="badge badge-info">{{ $ongoingWorkout->workoutExercises->count() }} gerakan</span>
            @if($ongoingWorkout->duration)
                <span class="badge badge-secondary">{{ $ongoingWorkout->duration }} menit</span>
            @endif
            <span class="text-xs text-muted">Progres tersimpan &bull; Siap dilanjutkan</span>
        </div>
        <div>
            <a href="{{ route('workouts.resume', $ongoingWorkout) }}" class="btn btn-warning btn-block text-center" style="font-weight: 700; font-size: 1rem; padding: 10px;">
                ▶️ Lanjutkan Latihan Ini
            </a>
        </div>
    </div>
@endif

<!-- ================= KALENDER LATIHAN BULANAN ================= -->
<div class="workout-calendar-card mb-4">
    <div class="calendar-header">
        <div class="calendar-nav">
            <button type="button" class="calendar-nav-btn" id="prev-month-btn" title="Bulan Sebelumnya">◀</button>
            <h2 class="calendar-month-title" id="calendar-month-heading">
                {{ $calendarData['month_label'] ?? \Carbon\Carbon::today()->format('F Y') }}
            </h2>
            <button type="button" class="calendar-nav-btn" id="next-month-btn" title="Bulan Berikutnya">▶</button>
            <button type="button" class="calendar-nav-btn" id="today-month-btn" style="font-size: 0.8rem; font-weight: 600;">Hari Ini</button>
        </div>
        <div>
            <span class="badge badge-success" id="monthly-count-pill" style="font-size: 0.85rem; padding: 6px 14px; font-weight: 700;">
                🔥 <span id="monthly-count-number">{{ $calendarData['total_completed'] ?? 0 }}</span> Hari Latihan Bulan Ini
            </span>
        </div>
    </div>

    <!-- Hari dalam Seminggu (Sen - Min) -->
    <div class="calendar-grid-header">
        <div class="calendar-weekday-label">Sen</div>
        <div class="calendar-weekday-label">Sel</div>
        <div class="calendar-weekday-label">Rab</div>
        <div class="calendar-weekday-label">Kam</div>
        <div class="calendar-weekday-label">Jum</div>
        <div class="calendar-weekday-label">Sab</div>
        <div class="calendar-weekday-label">Min</div>
    </div>

    <!-- Grid Tanggal -->
    <div class="calendar-grid-days" id="calendar-days-grid">
        <!-- Dirender via Javascript -->
    </div>

    <!-- Keterangan / Legend -->
    <div class="calendar-legend">
        <div class="legend-item">
            <span class="legend-dot workout"></span>
            <span>Latihan Selesai</span>
        </div>
        <div class="legend-item">
            <span class="legend-dot ongoing"></span>
            <span>Sedang Berjalan</span>
        </div>
        <div class="legend-item">
            <span class="legend-dot today"></span>
            <span>Hari Ini</span>
        </div>
    </div>
</div>

<!-- ================= RIWAYAT LATIHAN ================= -->
<div class="flex-between align-center mb-3">
    <h3 class="m-0 text-secondary text-sm font-semibold uppercase tracking-wider">Riwayat Semua Latihan</h3>
    <span class="text-muted text-xs">{{ $workouts->total() ?? count($workouts) }} total sesi selesai</span>
</div>

<div class="workouts-list">
    @forelse($workouts ?? [] as $workout)
        <a href="{{ route('workouts.show', $workout) }}" class="workout-card-link">
            <div class="flex-between align-center mb-2">
                <h3>{{ $workout->name }}</h3>
                <span class="text-muted text-sm">{{ \Carbon\Carbon::parse($workout->date)->format('d/m/Y') }}</span>
            </div>
            <div class="workout-meta">
                <span class="badge badge-info">{{ $workout->workoutExercises->count() }} gerakan</span>
                @if($workout->duration)
                    <span class="badge badge-secondary">{{ $workout->duration }} menit</span>
                @endif
                <span class="badge badge-{{ $workout->status === 'completed' ? 'success' : 'warning' }}">
                    {{ $workout->status === 'completed' ? 'Selesai' : 'Sedang Berjalan' }}
                </span>
            </div>
        </a>
    @empty
        <div class="card text-center" style="padding: 40px 24px;">
            <div style="font-size: 2.5rem; margin-bottom: 12px;">🏋️</div>
            <h3 class="mb-2">Belum ada latihan selesai</h3>
            <p class="text-muted mb-4">Selesaikan latihanmu untuk melacak riwayat dan progres!</p>
            @if(!isset($ongoingWorkout) || !$ongoingWorkout)
                <a href="{{ route('workouts.create') }}" class="btn btn-primary mx-auto" style="max-width: 220px;">Mulai Latihan Sekarang</a>
            @endif
        </div>
    @endforelse
</div>

@if(isset($workouts) && method_exists($workouts, 'links'))
    <div class="mt-4">
        {{ $workouts->links() }}
    </div>
@endif

<!-- ================= MODAL DETAIL LATIHAN KALENDER ================= -->
<div id="calendar-day-modal" class="modal-overlay">
    <div class="modal-card" style="max-width: 520px;">
        <div class="modal-header flex-between align-center">
            <h3 class="m-0" id="modal-date-title">📅 Detail Latihan</h3>
            <button type="button" class="modal-close-btn" id="close-calendar-modal">&times;</button>
        </div>
        <div class="modal-body" id="modal-workouts-container" style="max-height: 70vh; overflow-y: auto;">
            <!-- Konten diisi lewat Javascript saat tanggal diklik -->
        </div>
    </div>
</div>

@php
    $safeCalendarData = $calendarData ?? ['month' => date('Y-m'), 'month_label' => '', 'total_completed' => 0, 'workouts_by_date' => []];
@endphp
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data kalender awal dari server
        let calendarState = {!! json_encode($safeCalendarData) !!};
        let currentYearMonth = calendarState.month || '{{ date("Y-m") }}';

        const monthHeading = document.getElementById('calendar-month-heading');
        const countNumber = document.getElementById('monthly-count-number');
        const daysGrid = document.getElementById('calendar-days-grid');
        const prevBtn = document.getElementById('prev-month-btn');
        const nextBtn = document.getElementById('next-month-btn');
        const todayBtn = document.getElementById('today-month-btn');

        const modal = document.getElementById('calendar-day-modal');
        const closeModalBtn = document.getElementById('close-calendar-modal');
        const modalDateTitle = document.getElementById('modal-date-title');
        const modalContainer = document.getElementById('modal-workouts-container');

        // Hari ini format YYYY-MM-DD
        const now = new Date();
        const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

        function renderCalendar() {
            if (!daysGrid) return;
            daysGrid.innerHTML = '';

            const [yearStr, monthStr] = currentYearMonth.split('-');
            const year = parseInt(yearStr);
            const monthIdx = parseInt(monthStr) - 1; // 0-based

            if (monthHeading && calendarState.month_label) {
                monthHeading.textContent = calendarState.month_label;
            }
            if (countNumber) {
                countNumber.textContent = calendarState.total_completed ?? 0;
            }

            // Hitung padding awal (Senin = 1, Minggu = 7)
            const firstDay = new Date(year, monthIdx, 1);
            let dayOfWeek = firstDay.getDay(); // 0 = Minggu, 1 = Senin
            const startPadding = (dayOfWeek === 0) ? 6 : (dayOfWeek - 1);

            // Jumlah hari di bulan ini
            const totalDays = new Date(year, monthIdx + 1, 0).getDate();

            // Jumlah hari di bulan sebelumnya untuk padding
            const prevMonthLastDay = new Date(year, monthIdx, 0).getDate();

            // 1. Render Padding Bulan Lalu
            for (let i = startPadding - 1; i >= 0; i--) {
                const dayNum = prevMonthLastDay - i;
                const cell = document.createElement('div');
                cell.className = 'calendar-day-cell other-month';
                cell.innerHTML = `<span class="day-number">${dayNum}</span>`;
                daysGrid.appendChild(cell);
            }

            // 2. Render Hari-hari Bulan Ini
            const workoutsMap = calendarState.workouts_by_date || {};

            for (let d = 1; d <= totalDays; d++) {
                const dateKey = `${year}-${String(monthIdx + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const cell = document.createElement('div');
                cell.className = 'calendar-day-cell';

                if (dateKey === todayStr) {
                    cell.classList.add('today');
                }

                const dayWorkouts = workoutsMap[dateKey] || [];
                const hasCompleted = dayWorkouts.some(w => w.status === 'completed');
                const hasOngoing = dayWorkouts.some(w => w.status === 'in_progress');

                if (hasCompleted) {
                    cell.classList.add('has-workout');
                } else if (hasOngoing) {
                    cell.classList.add('has-ongoing');
                }

                // Konten isi cell
                let indicatorHtml = '';
                if (hasCompleted) {
                    indicatorHtml = `<span class="day-badge-icon" title="${dayWorkouts.length} latihan">🏋️</span>`;
                } else if (hasOngoing) {
                    indicatorHtml = `<span class="day-badge-icon" title="Sedang berjalan">⚡</span>`;
                }

                cell.innerHTML = `
                    <span class="day-number">${d}</span>
                    <div class="day-workout-indicator">
                        ${indicatorHtml}
                    </div>
                `;

                // Event klik tanggal
                cell.addEventListener('click', function() {
                    openDayModal(dateKey, dayWorkouts);
                });

                daysGrid.appendChild(cell);
            }

            // 3. Render Padding Bulan Berikutnya (agar grid rapi kelipatan 7)
            const totalCells = startPadding + totalDays;
            const endPadding = (7 - (totalCells % 7)) % 7;
            for (let p = 1; p <= endPadding; p++) {
                const cell = document.createElement('div');
                cell.className = 'calendar-day-cell other-month';
                cell.innerHTML = `<span class="day-number">${p}</span>`;
                daysGrid.appendChild(cell);
            }
        }

        async function fetchMonthData(targetMonth) {
            currentYearMonth = targetMonth;
            if (monthHeading) {
                monthHeading.textContent = 'Memuat...';
            }

            try {
                const res = await fetch(`{{ route('workouts.calendarData') }}?month=${targetMonth}`);
                if (res.ok) {
                    calendarState = await res.json();
                    renderCalendar();
                }
            } catch (err) {
                console.warn('Gagal memuat kalender:', err);
            }
        }

        // Navigasi Bulan
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                const [y, m] = currentYearMonth.split('-').map(Number);
                let newDate = new Date(y, m - 2, 1);
                let newMonthStr = `${newDate.getFullYear()}-${String(newDate.getMonth() + 1).padStart(2, '0')}`;
                fetchMonthData(newMonthStr);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                const [y, m] = currentYearMonth.split('-').map(Number);
                let newDate = new Date(y, m, 1);
                let newMonthStr = `${newDate.getFullYear()}-${String(newDate.getMonth() + 1).padStart(2, '0')}`;
                fetchMonthData(newMonthStr);
            });
        }

        if (todayBtn) {
            todayBtn.addEventListener('click', function() {
                const thisMonthStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
                fetchMonthData(thisMonthStr);
            });
        }

        // --- MODAL HANDLER ---
        function openDayModal(dateStr, workouts) {
            // Format judul tanggal
            const parts = dateStr.split('-');
            const dObj = new Date(parts[0], parts[1] - 1, parts[2]);
            const formattedDate = dObj.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            if (modalDateTitle) {
                modalDateTitle.textContent = `📅 ${formattedDate}`;
            }

            if (!modalContainer) return;
            modalContainer.innerHTML = '';

            if (workouts && workouts.length > 0) {
                let html = `<p class="text-sm text-muted mb-3">Ditemukan <strong>${workouts.length} latihan</strong> pada tanggal ini:</p>`;

                workouts.forEach(w => {
                    const isDone = w.status === 'completed';
                    html += `
                        <div class="calendar-workout-item">
                            <div class="flex-between align-center mb-2">
                                <h4 class="m-0 text-primary" style="font-size: 1.05rem;">${w.name}</h4>
                                <span class="badge badge-${isDone ? 'success' : 'warning'}">
                                    ${isDone ? '✓ Selesai' : '⚡ Sedang Berjalan'}
                                </span>
                            </div>

                            <div class="workout-meta mb-2">
                                <span class="badge badge-info">${w.exercise_count} gerakan</span>
                                ${w.duration ? `<span class="badge badge-secondary">⏱️ ${w.duration} menit</span>` : ''}
                            </div>

                            ${w.exercises && w.exercises.length > 0 ? `
                                <div class="mt-2 mb-3 p-2 rounded" style="background: var(--card-bg); border: 1px solid var(--border-light); font-size: 0.85rem;">
                                    <strong class="d-block text-xs text-muted mb-1 text-uppercase">Rincian Gerakan:</strong>
                                    <ul style="margin: 0; padding-left: 18px;">
                                        ${w.exercises.map(ex => `
                                            <li class="mb-1">
                                                <strong>${ex.name}</strong> — ${ex.sets} set &bull; maks ${ex.weight} kg (${ex.reps} reps)
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            ` : ''}

                            <div class="d-flex flex-gap-sm mt-3">
                                ${isDone ? `
                                    <a href="${w.url}" class="btn btn-small btn-primary flex-1 text-center">Lihat Detail Lengkap →</a>
                                ` : `
                                    <a href="${w.resume_url}" class="btn btn-small btn-warning flex-1 text-center" style="font-weight: 700;">▶️ Lanjutkan Latihan Ini</a>
                                `}
                            </div>
                        </div>
                    `;
                });

                html += `
                    <div class="mt-3 text-center">
                        <a href="{{ route('workouts.create') }}?date=${dateStr}&new=1" class="btn btn-outline btn-small">+ Catat Latihan Lain untuk Tanggal Ini</a>
                    </div>
                `;

                modalContainer.innerHTML = html;
            } else {
                modalContainer.innerHTML = `
                    <div class="text-center py-4">
                        <div style="font-size: 2.8rem; margin-bottom: 12px;">🛋️</div>
                        <h4 class="mb-1">Belum Ada Latihan</h4>
                        <p class="text-muted text-sm mb-4">Tidak ada sesi latihan yang dicatat pada ${formattedDate}.</p>
                        <a href="{{ route('workouts.create') }}?date=${dateStr}&new=1" class="btn btn-primary btn-small">
                            + Mulai Latihan pada Tanggal Ini
                        </a>
                    </div>
                `;
            }

            modal.classList.add('active');
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                modal.classList.remove('active');
            });
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                modal.classList.remove('active');
            }
        });

        // Render pertama kali saat load
        renderCalendar();
    });
</script>
@endpush
@endsection
