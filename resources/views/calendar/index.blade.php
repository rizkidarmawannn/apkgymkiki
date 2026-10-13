@extends('layouts.app')

@section('title', 'Kalender Latihan')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div>
        <h1 class="mb-1">📅 Kalender & Konsistensi</h1>
        <p class="text-muted text-sm mb-0">Tren latihan tahunan seperti Hevy app dan rekap kehadiran latihanmu.</p>
    </div>
    <div class="d-flex align-center flex-gap-sm">
        <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Mulai Latihan</a>
    </div>
</div>

<!-- Stats Ringkasan Konsistensi -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <h3>Latihan Tahun {{ $year }}</h3>
        <p class="stat-value text-primary">{{ $totalCompletedYear }} <small class="text-muted text-sm fw-medium">sesi</small></p>
    </div>
    <div class="stat-card">
        <h3>Streak Konsistensi</h3>
        <p class="stat-value text-warning">{{ $currentStreak }} <small class="text-muted text-sm fw-medium">hari beruntun</small></p>
    </div>
    <div class="stat-card">
        <h3>Hari Aktif</h3>
        <p class="stat-value text-accent">{{ $activeDaysYear }} <small class="text-muted text-sm fw-medium">hari</small></p>
    </div>
    <div class="stat-card">
        <h3>Tahun</h3>
        <div class="d-flex flex-gap-sm mt-1">
            @foreach($availableYears as $y)
                <a href="{{ route('calendar.index', ['year' => $y]) }}" class="btn btn-small {{ $y == $year ? 'btn-primary' : 'btn-outline' }}" style="padding: 4px 10px; font-size: 0.85rem;">
                    {{ $y }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- ================= HEVY APP ANNUAL HEATMAP GRID ================= -->
<div class="hevy-year-card mb-4">
    <div class="hevy-year-header">
        <div class="d-flex align-center flex-gap-sm">
            <h2 class="hevy-year-title">{{ $year }}</h2>
            <span class="badge badge-info" style="font-size: 0.8rem; background: rgba(37, 99, 235, 0.2); color: #60A5FA; border: 1px solid rgba(96, 165, 250, 0.4);">
                🔥 Tren Konsistensi Hevy
            </span>
        </div>
        <div class="text-sm" style="color: #94A3B8;">
            Klik pada kotak tanggal untuk melihat latihan yang dilakukan
        </div>
    </div>

    <div class="hevy-grid-scroll-wrapper">
        <div class="hevy-heatmap-container" id="hevy-heatmap-box">
            <!-- Bulan Header Jan - Des -->
            <div class="hevy-months-row">
                <div class="hevy-month-label">Jan</div>
                <div class="hevy-month-label">Feb</div>
                <div class="hevy-month-label">Mar</div>
                <div class="hevy-month-label">Apr</div>
                <div class="hevy-month-label">Mei</div>
                <div class="hevy-month-label">Jun</div>
                <div class="hevy-month-label">Jul</div>
                <div class="hevy-month-label">Agu</div>
                <div class="hevy-month-label">Sep</div>
                <div class="hevy-month-label">Okt</div>
                <div class="hevy-month-label">Nov</div>
                <div class="hevy-month-label">Des</div>
            </div>

            <div class="hevy-grid-body">
                <!-- Label Hari: Sen, Rab, Jum -->
                <div class="hevy-weekday-labels">
                    <span>Sen</span>
                    <span>Rab</span>
                    <span>Jum</span>
                    <span>Min</span>
                </div>

                <!-- 52-53 Kolom Minggu (Rendered via JS) -->
                <div class="hevy-weeks-columns" id="hevy-weeks-container">
                    <!-- Javascript generates weekly box columns -->
                </div>
            </div>
        </div>
    </div>

    <!-- Keterangan Grid -->
    <div class="hevy-legend">
        <div class="hevy-legend-item">
            <span class="hevy-legend-box" style="background: #1E293B;"></span>
            <span>Istirahat</span>
        </div>
        <div class="hevy-legend-item">
            <span class="hevy-legend-box" style="background: #2563EB; box-shadow: 0 0 4px #2563EB;"></span>
            <span>Latihan Selesai</span>
        </div>
        <div class="hevy-legend-item">
            <span class="hevy-legend-box" style="background: #F59E0B; box-shadow: 0 0 4px #F59E0B;"></span>
            <span>Sedang Berjalan</span>
        </div>
        <div class="hevy-legend-item">
            <span class="hevy-legend-box" style="outline: 2px solid #38BDF8;"></span>
            <span>Hari Ini</span>
        </div>
    </div>
</div>

<!-- ================= KALENDER BULANAN DETAIL ================= -->
<div class="workout-calendar-card mb-4">
    <div class="calendar-header">
        <div class="calendar-nav">
            <button type="button" class="calendar-nav-btn" id="prev-month-btn" title="Bulan Sebelumnya">◀</button>
            <h2 class="calendar-month-title" id="calendar-month-heading">
                {{ \Carbon\Carbon::today()->locale('id')->translatedFormat('F Y') }}
            </h2>
            <button type="button" class="calendar-nav-btn" id="next-month-btn" title="Bulan Berikutnya">▶</button>
            <button type="button" class="calendar-nav-btn" id="today-month-btn" style="font-size: 0.8rem; font-weight: 600;">Hari Ini</button>
        </div>
        <div>
            <span class="badge badge-success" id="monthly-count-pill" style="font-size: 0.85rem; padding: 6px 14px; font-weight: 700;">
                🔥 <span id="monthly-count-number">0</span> Hari Latihan Bulan Ini
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
</div>

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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const yearData = {!! json_encode($calendarData ?? ['year' => $year, 'workouts_by_date' => []]) !!};
        const selectedYear = {{ $year }};
        const workoutsMap = yearData.workouts_by_date || {};

        const now = new Date();
        const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

        // Modal Elements
        const modal = document.getElementById('calendar-day-modal');
        const closeModalBtn = document.getElementById('close-calendar-modal');
        const modalDateTitle = document.getElementById('modal-date-title');
        const modalContainer = document.getElementById('modal-workouts-container');

        // --- 1. RENDER HEVY STYLE ANNUAL HEATMAP GRID ---
        const weeksContainer = document.getElementById('hevy-weeks-container');

        function renderHevyHeatmap() {
            if (!weeksContainer) return;
            weeksContainer.innerHTML = '';

            // Start from Jan 1st of selectedYear
            const jan1 = new Date(selectedYear, 0, 1);
            // Day of week: 0=Sun, 1=Mon, ..., 6=Sat
            // We want Monday=0, Tuesday=1, ..., Sunday=6
            const jan1Day = (jan1.getDay() === 0) ? 6 : (jan1.getDay() - 1);

            // Compute starting date so the first column starts on Monday
            const gridStart = new Date(selectedYear, 0, 1 - jan1Day);

            // End at Dec 31st of selectedYear
            const dec31 = new Date(selectedYear, 11, 31);
            const dec31Day = (dec31.getDay() === 0) ? 6 : (dec31.getDay() - 1);
            const gridEnd = new Date(selectedYear, 11, 31 + (6 - dec31Day));

            let curDate = new Date(gridStart);
            let curWeekCol = document.createElement('div');
            curWeekCol.className = 'hevy-week-col';

            let dayCounter = 0;

            while (curDate <= gridEnd) {
                const y = curDate.getFullYear();
                const m = String(curDate.getMonth() + 1).padStart(2, '0');
                const d = String(curDate.getDate()).padStart(2, '0');
                const dateKey = `${y}-${m}-${d}`;
                const isCurrentYear = (y === selectedYear);

                const box = document.createElement('div');
                box.className = 'hevy-day-box';

                if (!isCurrentYear) {
                    box.style.opacity = '0.08';
                    box.style.cursor = 'default';
                } else {
                    const dayWorkouts = workoutsMap[dateKey] || [];
                    const hasCompleted = dayWorkouts.some(w => w.status === 'completed');
                    const hasOngoing = dayWorkouts.some(w => w.status === 'in_progress');

                    if (hasCompleted) {
                        box.classList.add('active-workout');
                    } else if (hasOngoing) {
                        box.classList.add('active-ongoing');
                    }

                    if (dateKey === todayStr) {
                        box.classList.add('is-today');
                    } else if (curDate > now) {
                        box.classList.add('is-future');
                    }

                    // Tooltip text format: dd/mm/yyyy
                    const ddmmyyyy = `${d}/${m}/${y}`;
                    const workoutCount = dayWorkouts.length;
                    box.title = `${ddmmyyyy} ${workoutCount > 0 ? `(${workoutCount} latihan)` : ''}`;

                    box.addEventListener('click', function() {
                        openDayModal(dateKey, dayWorkouts);
                    });
                }

                curWeekCol.appendChild(box);
                dayCounter++;

                if (dayCounter % 7 === 0) {
                    weeksContainer.appendChild(curWeekCol);
                    curWeekCol = document.createElement('div');
                    curWeekCol.className = 'hevy-week-col';
                }

                // Next day
                curDate.setDate(curDate.getDate() + 1);
            }

            if (curWeekCol.children.length > 0) {
                weeksContainer.appendChild(curWeekCol);
            }
        }

        renderHevyHeatmap();

        // --- 2. RENDER MONTHLY CALENDAR ---
        let currentYearMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        const monthHeading = document.getElementById('calendar-month-heading');
        const countNumber = document.getElementById('monthly-count-number');
        const daysGrid = document.getElementById('calendar-days-grid');
        const prevBtn = document.getElementById('prev-month-btn');
        const nextBtn = document.getElementById('next-month-btn');
        const todayBtn = document.getElementById('today-month-btn');

        async function renderMonthlyCalendar() {
            if (!daysGrid) return;
            daysGrid.innerHTML = '';

            const [yearStr, monthStr] = currentYearMonth.split('-');
            const yearNum = parseInt(yearStr);
            const monthIdx = parseInt(monthStr) - 1;

            const firstDay = new Date(yearNum, monthIdx, 1);
            let dayOfWeek = firstDay.getDay();
            const startPadding = (dayOfWeek === 0) ? 6 : (dayOfWeek - 1);

            const totalDays = new Date(yearNum, monthIdx + 1, 0).getDate();
            const prevMonthLastDay = new Date(yearNum, monthIdx, 0).getDate();

            if (monthHeading) {
                monthHeading.textContent = firstDay.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
            }

            // Hitung total latihan di bulan ini
            let monthCompletedCount = 0;

            // Padding bulan lalu
            for (let i = startPadding - 1; i >= 0; i--) {
                const dayNum = prevMonthLastDay - i;
                const cell = document.createElement('div');
                cell.className = 'calendar-day-cell other-month';
                cell.innerHTML = `<span class="day-number">${dayNum}</span>`;
                daysGrid.appendChild(cell);
            }

            // Hari-hari bulan ini
            for (let d = 1; d <= totalDays; d++) {
                const dateKey = `${yearNum}-${String(monthIdx + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
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
                    monthCompletedCount++;
                } else if (hasOngoing) {
                    cell.classList.add('has-ongoing');
                }

                let indicatorHtml = '';
                if (hasCompleted) {
                    indicatorHtml = `<span class="day-badge-icon">🏋️</span>`;
                } else if (hasOngoing) {
                    indicatorHtml = `<span class="day-badge-icon">⚡</span>`;
                }

                cell.innerHTML = `
                    <span class="day-number">${d}</span>
                    <div class="day-workout-indicator">
                        ${indicatorHtml}
                    </div>
                `;

                cell.addEventListener('click', function() {
                    openDayModal(dateKey, dayWorkouts);
                });

                daysGrid.appendChild(cell);
            }

            if (countNumber) {
                countNumber.textContent = monthCompletedCount;
            }

            // Padding bulan berikutnya
            const totalCells = startPadding + totalDays;
            const endPadding = (7 - (totalCells % 7)) % 7;
            for (let p = 1; p <= endPadding; p++) {
                const cell = document.createElement('div');
                cell.className = 'calendar-day-cell other-month';
                cell.innerHTML = `<span class="day-number">${p}</span>`;
                daysGrid.appendChild(cell);
            }
        }

        renderMonthlyCalendar();

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                const [y, m] = currentYearMonth.split('-').map(Number);
                let newDate = new Date(y, m - 2, 1);
                currentYearMonth = `${newDate.getFullYear()}-${String(newDate.getMonth() + 1).padStart(2, '0')}`;
                renderMonthlyCalendar();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                const [y, m] = currentYearMonth.split('-').map(Number);
                let newDate = new Date(y, m, 1);
                currentYearMonth = `${newDate.getFullYear()}-${String(newDate.getMonth() + 1).padStart(2, '0')}`;
                renderMonthlyCalendar();
            });
        }

        if (todayBtn) {
            todayBtn.addEventListener('click', function() {
                currentYearMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
                renderMonthlyCalendar();
            });
        }

        // --- 3. MODAL DETAIL LATIHAN ---
        function openDayModal(dateKey, workouts) {
            const parts = dateKey.split('-');
            const ddmmyyyy = `${parts[2]}/${parts[1]}/${parts[0]}`;
            const dObj = new Date(parts[0], parts[1] - 1, parts[2]);
            const dayName = dObj.toLocaleDateString('id-ID', { weekday: 'long' });

            if (modalDateTitle) {
                modalDateTitle.textContent = `📅 ${dayName}, ${ddmmyyyy}`;
            }

            if (!modalContainer) return;
            modalContainer.innerHTML = '';

            if (workouts && workouts.length > 0) {
                let html = `<p class="text-sm text-muted mb-3">Ditemukan <strong>${workouts.length} sesi latihan</strong> pada tanggal ${ddmmyyyy}:</p>`;

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
                        <a href="{{ route('workouts.create') }}?date=${dateKey}&new=1" class="btn btn-outline btn-small">+ Catat Latihan Lain (${ddmmyyyy})</a>
                    </div>
                `;

                modalContainer.innerHTML = html;
            } else {
                modalContainer.innerHTML = `
                    <div class="text-center py-4">
                        <div style="font-size: 2.8rem; margin-bottom: 12px;">🛋️</div>
                        <h4 class="mb-1">Belum Ada Latihan</h4>
                        <p class="text-muted text-sm mb-4">Tidak ada sesi latihan yang dicatat pada ${dayName}, ${ddmmyyyy}.</p>
                        <a href="{{ route('workouts.create') }}?date=${dateKey}&new=1" class="btn btn-primary btn-small">
                            + Mulai Latihan pada Tanggal Ini (${ddmmyyyy})
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
    });
</script>
@endpush
@endsection
