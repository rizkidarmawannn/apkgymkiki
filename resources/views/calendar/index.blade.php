@extends('layouts.app')

@section('title', 'Kalender Latihan')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div>
        <h1 class="mb-1" style="display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            <span>Kalender & Konsistensi</span>
        </h1>
        <p class="text-muted text-sm mb-0">Tren latihan tahunan, hari istirahat pemulihan, dan rekap konsistensi.</p>
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
        <p class="stat-value" style="color: {{ $streakData['flame_color'] ?? '#f59e0b' }}; display: flex; align-items: center; gap: 8px;">
            <x-flame-shape :tier="$streakData['flame_tier'] ?? 0" :status="$streakData['status'] ?? 'extinguished'" size="22" />
            <span>{{ $streakData['current_streak'] }}</span>
            <small class="text-muted text-sm fw-medium">({{ $streakData['flame_title'] }})</small>
        </p>
    </div>
    <div class="stat-card">
        <h3>Hari Aktif (Latihan + Rest)</h3>
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
            <span class="badge badge-info" style="font-size: 0.8rem; background: rgba(37, 99, 235, 0.2); color: #60A5FA; border: 1px solid rgba(96, 165, 250, 0.4); display: inline-flex; align-items: center; gap: 5px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 13px; height: 13px;"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                <span>Tren Konsistensi</span>
            </span>
        </div>
        <div class="text-sm" style="color: #94A3B8;">
            Klik pada kotak tanggal untuk melihat latihan atau mengubah Rest Day
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
            <span>Kosong</span>
        </div>
        <div class="hevy-legend-item">
            <span class="hevy-legend-box" style="background: #2563EB; box-shadow: 0 0 4px #2563EB;"></span>
            <span>Latihan Selesai</span>
        </div>
        <div class="hevy-legend-item">
            <span class="hevy-legend-box" style="background: #F59E0B; box-shadow: 0 0 4px #F59E0B;"></span>
            <span>Hari Istirahat (Rest Day)</span>
        </div>
        <div class="hevy-legend-item">
            <span class="hevy-legend-box" style="background: #F97316; box-shadow: 0 0 4px #F97316;"></span>
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
                {{ \Carbon\Carbon::today()->format('F Y') }}
            </h2>
            <button type="button" class="calendar-nav-btn" id="next-month-btn" title="Bulan Berikutnya">▶</button>
            <button type="button" class="calendar-nav-btn" id="today-month-btn" style="font-size: 0.8rem; font-weight: 600;">Hari Ini</button>
        </div>
        <div class="d-flex align-center flex-gap-sm">
            <span class="badge badge-success" id="monthly-count-pill" style="font-size: 0.85rem; padding: 6px 14px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width: 13px; height: 13px;"><path d="M12 2c-.5 2-3 5-3.5 8.5a5.5 5.5 0 0 0 10.5 3c.5-1.5.5-2.5.5-3.5-1.5 1.5-3 2-4 1.5-2-.8-2.5-3-1.5-5 1-1.5 1-3 0-4.5z"/></svg>
                <span><span id="monthly-count-number">0</span> Hari Latihan</span>
            </span>
            <span class="badge badge-warning" id="monthly-rest-pill" style="font-size: 0.85rem; padding: 6px 14px; font-weight: 700; background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); display: inline-flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                <span><span id="monthly-rest-number">0</span> Hari Rest</span>
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
            <h3 class="m-0" id="modal-date-title" style="display: flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <span>Detail Tanggal</span>
            </h3>
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
        const yearData = {!! json_encode($calendarData ?? ['year' => $year, 'workouts_by_date' => [], 'rest_days_by_date' => []]) !!};
        const selectedYear = {{ $year }};
        const workoutsMap = yearData.workouts_by_date || {};
        let restDaysMap = yearData.rest_days_by_date || {};

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

            const jan1 = new Date(selectedYear, 0, 1);
            const jan1Day = (jan1.getDay() === 0) ? 6 : (jan1.getDay() - 1);
            const gridStart = new Date(selectedYear, 0, 1 - jan1Day);
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
                    const isRestDay = !!restDaysMap[dateKey];

                    if (hasCompleted) {
                        box.classList.add('active-workout');
                    } else if (hasOngoing) {
                        box.classList.add('active-ongoing');
                    } else if (isRestDay) {
                        box.classList.add('active-restday');
                    }

                    if (dateKey === todayStr) {
                        box.classList.add('is-today');
                    } else if (curDate > now) {
                        box.classList.add('is-future');
                    }

                    const ddmmyyyy = `${d}/${m}/${y}`;
                    let titleStr = ddmmyyyy;
                    if (hasCompleted) titleStr += ` (${dayWorkouts.length} latihan)`;
                    else if (isRestDay) titleStr += ` (Hari Istirahat / Rest Day)`;
                    box.title = titleStr;

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
        const restNumber = document.getElementById('monthly-rest-number');
        const daysGrid = document.getElementById('calendar-days-grid');
        const prevBtn = document.getElementById('prev-month-btn');
        const nextBtn = document.getElementById('next-month-btn');
        const todayBtn = document.getElementById('today-month-btn');

        function renderMonthlyCalendar() {
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

            let monthCompletedCount = 0;
            let monthRestCount = 0;

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
                const isRestDay = !!restDaysMap[dateKey];

                if (hasCompleted) {
                    cell.classList.add('has-workout');
                    monthCompletedCount++;
                } else if (hasOngoing) {
                    cell.classList.add('has-ongoing');
                } else if (isRestDay) {
                    cell.classList.add('has-restday');
                    monthRestCount++;
                }

                let indicatorHtml = '';
                if (hasCompleted) {
                    indicatorHtml = `<span class="day-badge-icon" title="Latihan selesai"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 15px; height: 15px; color: #38bdf8;"><line x1="2" y1="12" x2="22" y2="12"/><line x1="6" y1="7" x2="6" y2="17"/><line x1="4" y1="9" x2="4" y2="15"/><line x1="18" y1="7" x2="18" y2="17"/><line x1="20" y1="9" x2="20" y2="15"/></svg></span>`;
                } else if (hasOngoing) {
                    indicatorHtml = `<span class="day-badge-icon" title="Sedang berjalan"><svg viewBox="0 0 24 24" fill="currentColor" style="width: 13px; height: 13px; color: #f59e0b;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span>`;
                } else if (isRestDay) {
                    indicatorHtml = `<span class="day-badge-icon dot-rest-indicator" title="Hari Istirahat" style="display: inline-flex; align-items: center; justify-content: center;"><span style="display: inline-block; width: 9px; height: 9px; border-radius: 50%; background: #f59e0b; box-shadow: 0 0 5px rgba(245, 158, 11, 0.8);"></span></span>`;
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

            if (countNumber) countNumber.textContent = monthCompletedCount;
            if (restNumber) restNumber.textContent = monthRestCount;

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

        // --- 3. MODAL DETAIL LATIHAN & REST DAY ---
        function openDayModal(dateKey, workouts) {
            const parts = dateKey.split('-');
            const ddmmyyyy = `${parts[2]}/${parts[1]}/${parts[0]}`;
            const dObj = new Date(parts[0], parts[1] - 1, parts[2]);
            const dayName = dObj.toLocaleDateString('id-ID', { weekday: 'long' });
            const isRestDay = !!restDaysMap[dateKey];

            if (modalDateTitle) {
                modalDateTitle.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; margin-right: 6px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg><span>${dayName}, ${ddmmyyyy}</span>`;
            }

            if (!modalContainer) return;
            modalContainer.innerHTML = '';

            let html = '';

            // Rest day alert / banner
            if (isRestDay) {
                html += `
                    <div class="calendar-rest-banner mb-3 p-3 rounded" style="background: rgba(245, 158, 11, 0.12); border: 1.5px solid #f59e0b;">
                        <div class="flex-between align-center">
                            <div class="d-flex align-center flex-gap-sm">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center; color: #f59e0b; flex-shrink: 0;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                </div>
                                <div>
                                    <strong style="color: #f59e0b; display: block;">Hari Istirahat (Rest Day)</strong>
                                    <span class="text-xs text-muted">Pemulihan otot aktif & streak konsistensimu tetap aman!</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-small btn-outline action-toggle-rest" data-date="${dateKey}" style="border-color: rgba(245, 158, 11, 0.5); color: #f59e0b; font-size: 0.75rem;">
                                Batalkan
                            </button>
                        </div>
                    </div>
                `;
            }

            if (workouts && workouts.length > 0) {
                html += `<p class="text-sm text-muted mb-3">Ditemukan <strong>${workouts.length} sesi latihan</strong> pada tanggal ${ddmmyyyy}:</p>`;

                workouts.forEach(w => {
                    const isDone = w.status === 'completed';
                    html += `
                        <div class="calendar-workout-item mb-3">
                            <div class="flex-between align-center mb-2">
                                <h4 class="m-0 text-primary" style="font-size: 1.05rem;">${w.name}</h4>
                                <span class="badge badge-${isDone ? 'success' : 'warning'}" style="display: inline-flex; align-items: center; gap: 4px;">
                                    ${isDone ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 12px; height: 12px;"><polyline points="20 6 9 17 4 12"/></svg><span>Selesai</span>' : '<svg viewBox="0 0 24 24" fill="currentColor" style="width: 11px; height: 11px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg><span>Sedang Berjalan</span>'}
                                </span>
                            </div>

                            <div class="workout-meta mb-2 flex-gap-sm">
                                <span class="badge badge-info">${w.exercise_count} gerakan</span>
                                ${w.duration ? `<span class="badge badge-secondary" style="display: inline-flex; align-items: center; gap: 4px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 11px; height: 11px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>${w.duration} menit</span></span>` : ''}
                                ${w.estimated_calories ? `<span class="badge badge-warning" style="display: inline-flex; align-items: center; gap: 4px;"><svg viewBox="0 0 24 24" fill="currentColor" style="width: 11px; height: 11px;"><path d="M12 2c-.5 2-3 5-3.5 8.5a5.5 5.5 0 0 0 10.5 3c.5-1.5.5-2.5.5-3.5-1.5 1.5-3 2-4 1.5-2-.8-2.5-3-1.5-5 1-1.5 1-3 0-4.5z"/></svg><span>${w.estimated_calories} kkal</span></span>` : ''}
                            </div>

                            ${w.exercises && w.exercises.length > 0 ? `
                                <div class="mt-2 mb-3 p-2 rounded" style="background: var(--card-bg); border: 1px solid var(--border-light); font-size: 0.85rem;">
                                    <strong class="d-block text-xs text-muted mb-1 text-uppercase">Rincian Gerakan:</strong>
                                    <ul style="margin: 0; padding-left: 18px;">
                                        ${w.exercises.map(ex => {
                                            const isCardio = (ex.category || '').toLowerCase() === 'cardio';
                                            if (isCardio) {
                                                return `<li class="mb-1"><strong>${ex.name}</strong> (Kardio) — ${ex.reps} menit</li>`;
                                            }
                                            return `<li class="mb-1"><strong>${ex.name}</strong> — ${ex.sets} set &bull; maks ${ex.weight} kg (${ex.reps} reps)</li>`;
                                        }).join('')}
                                    </ul>
                                </div>
                            ` : ''}

                            <div class="d-flex flex-gap-sm mt-3">
                                ${isDone ? `
                                    <a href="${w.url}" class="btn btn-small btn-primary flex-1 text-center">Lihat Detail Lengkap →</a>
                                ` : `
                                    <a href="${w.resume_url}" class="btn btn-small btn-warning flex-1 text-center" style="font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 11px; height: 11px;"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                                        <span>Lanjutkan Latihan Ini</span>
                                    </a>
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
            } else if (!isRestDay) {
                html += `
                    <div class="text-center py-4">
                        <div class="mb-3" style="width: 54px; height: 54px; border-radius: 50%; background: var(--bg-hover); display: inline-flex; align-items: center; justify-content: center; color: var(--text-muted); margin: 0 auto;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width: 26px; height: 26px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        </div>
                        <h4 class="mb-1">Belum Ada Sesi Latihan</h4>
                        <p class="text-muted text-sm mb-4">Tidak ada sesi latihan yang dicatat pada ${dayName}, ${ddmmyyyy}.</p>
                        <div class="d-flex flex-column align-center flex-gap-sm" style="max-width: 320px; margin: 0 auto;">
                            <a href="{{ route('workouts.create') }}?date=${dateKey}&new=1" class="btn btn-primary btn-small w-100">
                                + Catat Latihan (${ddmmyyyy})
                            </a>
                            <button type="button" class="btn btn-outline btn-small w-100 action-toggle-rest" data-date="${dateKey}" style="border-color: rgba(245, 158, 11, 0.4); color: #f59e0b; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                                <span>Jadikan Hari Ini Rest Day (Kuning)</span>
                            </button>
                        </div>
                    </div>
                `;
            }

            modalContainer.innerHTML = html;

            // Bind toggle rest day inside modal
            modalContainer.querySelectorAll('.action-toggle-rest').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const btnDate = this.getAttribute('data-date');
                    this.disabled = true;
                    try {
                        const res = await fetch("{{ route('rest-days.toggle') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ date: btnDate })
                        });
                        const data = await res.json();
                        if (data.success) {
                            if (data.is_rest_day) {
                                restDaysMap[btnDate] = { date: btnDate, notes: 'Rest Day' };
                            } else {
                                delete restDaysMap[btnDate];
                            }
                            renderHevyHeatmap();
                            renderMonthlyCalendar();
                            openDayModal(btnDate, workouts);
                        } else {
                            alert(data.message || 'Gagal mengubah Rest Day');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Terjadi kesalahan jaringan.');
                    }
                });
            });

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
