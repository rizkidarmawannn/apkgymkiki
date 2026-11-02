@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div>
        <h1 class="mb-0">Halo, {{ $user->name ?? 'Pengguna' }}</h1>
        <p class="text-sm text-muted mb-0">Yuk tetap konsisten bangun kebiasaan sehatmu.</p>
    </div>
    @if(isset($ongoingWorkout) && $ongoingWorkout)
        <a href="{{ route('workouts.resume', $ongoingWorkout) }}" class="btn btn-warning" style="font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 13px; height: 13px;"><polygon points="6 3 20 12 6 21 6 3"/></svg>
            <span>Lanjutkan Latihan</span>
        </a>
    @else
        <a href="{{ route('workouts.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="width: 15px; height: 15px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Latihan Baru</span>
        </a>
    @endif
</div>

@if(isset($streakData))
<div id="streak-section" class="streak-pro-card mb-4 flame-tier-{{ $streakData['flame_tier'] }} flame-theme-{{ $streakData['flame_level'] }}">
    <div class="streak-glow-ambient"></div>
    <div class="streak-card-body">
        <div class="streak-top-bar flex-between align-center">
            <!-- Left Hero: Bespoke Vector Flame Shape + Counter -->
            <div class="streak-hero-group">
                <div class="streak-flame-circle tier-{{ $streakData['flame_tier'] }} status-{{ $streakData['status'] }}">
                    <x-flame-shape :tier="$streakData['flame_tier']" :status="$streakData['status']" size="38" />
                </div>
                <div class="streak-text-group">
                    <div class="streak-count-row">
                        <span class="streak-huge-number">{{ $streakData['current_streak'] }}</span>
                        <span class="streak-unit-label">HARI STREAK</span>
                    </div>
                    <div class="streak-tier-badge tier-{{ $streakData['flame_tier'] }}">
                        @if($streakData['status'] === 'rest')
                            <span class="badge-dot dot-yellow"></span>
                            <span>Hari Istirahat &bull; Streak Aman</span>
                        @elseif($streakData['status'] === 'lit')
                            <span class="badge-dot dot-flame"></span>
                            <span>{{ $streakData['flame_title'] }}</span>
                        @elseif($streakData['status'] === 'warning')
                            <span class="badge-dot dot-warning"></span>
                            <span>Selesaikan 1 Sesi atau Ambil Rest Day</span>
                        @else
                            <span class="badge-dot dot-muted"></span>
                            <span>{{ $streakData['flame_title'] }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Best Record & Quick Rest Day Toggle -->
            <div class="streak-actions-group">
                <div class="streak-record-pill">
                    <span class="record-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px; vertical-align: -1px; margin-right: 2px;"><path d="M6 9H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2"/><path d="M18 9h2a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-2"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H7v4h10v-4h-2c-.55 0-1-.45-1-1v-2.34"/><path d="M6 3h12a2 2 0 0 1 2 2v6a8 8 0 0 1-16 0V5a2 2 0 0 1 2-2z"/></svg>
                        Rekor Terbaik
                    </span>
                    <strong class="record-days">{{ $streakData['best_streak'] }} Hari</strong>
                </div>

                <button type="button" 
                        id="btn-rest-day-toggle" 
                        class="btn-rest-toggle {{ ($isRestDayToday ?? false) ? 'active' : '' }}" 
                        data-active="{{ ($isRestDayToday ?? false) ? '1' : '0' }}"
                        title="Tandai hari ini sebagai Rest Day agar streakmu tidak padam">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px;"><path d="M12 2a10 10 0 1 0 10 10 10 10 0 0 1-10-10z"/></svg>
                    <span id="rest-btn-label">{{ ($isRestDayToday ?? false) ? 'Rest Day Aktif' : 'Ambil Rest Day' }}</span>
                </button>
            </div>
        </div>

        <!-- Milestone Progress (Tier 10, 50, 100+) -->
        <div class="streak-milestone-bar-wrap mt-3">
            <div class="flex-between align-center text-xs mb-1">
                <span class="text-secondary fw-medium">
                    @if($streakData['current_streak'] >= 100)
                        Tier Mahkota Emas Maksimal (100+ Hari)
                    @else
                        Target Tier Berikutnya: <strong>{{ $streakData['next_milestone'] }} Hari</strong>
                        <span class="text-muted">({{ $streakData['days_to_milestone'] }} hari lagi)</span>
                    @endif
                </span>
                <span class="fw-bold tier-color-text">{{ $streakData['milestone_progress'] }}%</span>
            </div>
            <div class="milestone-track">
                <div class="milestone-fill tier-{{ $streakData['flame_tier'] }}" style="width: {{ $streakData['milestone_progress'] }}%;"></div>
            </div>
        </div>

        <!-- 7-Day Week Indicator Strip -->
        <div class="streak-week-track mt-3 pt-3">
            <div class="streak-week-label">Minggu Ini:</div>
            <div class="streak-week-days">
                @foreach($streakData['week_days'] as $wDay)
                    <div class="streak-day-item {{ $wDay['is_today'] ? 'day-today' : '' }} {{ $wDay['has_workout'] ? 'day-done' : '' }} {{ $wDay['is_rest'] ? 'day-rest' : '' }}">
                        <span class="day-name">{{ $wDay['day_name'] }}</span>
                        <div class="day-dot">
                            @if($wDay['has_workout'])
                                <svg viewBox="0 0 24 24" fill="currentColor" class="dot-shape shape-workout" style="width: 13px; height: 13px; color: #f97316;"><path d="M12 2c-.3 2-2.5 4.5-2.5 7.5a6 6 0 0 0 11.5 2.5c.3-.8.5-1.7.5-2.5-1 1-2.2 1.5-3.5 1.5-2.5 0-4.5-2-4.5-4.5 0-1.8.8-3.4 2-4.5C13.5 2 12.5 2 12 2z"/></svg>
                            @elseif($wDay['is_rest'])
                                <svg viewBox="0 0 24 24" fill="currentColor" class="dot-shape shape-rest" style="width: 12px; height: 12px; color: #f59e0b;"><circle cx="12" cy="12" r="7"/></svg>
                            @elseif($wDay['is_today'])
                                <span class="dot-pulse" title="Hari Ini"></span>
                            @else
                                <span class="dot-empty"></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- Stats Ringkasan Grid -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <h3 style="display: flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px; color: var(--text-muted);"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            <span>Berat Sekarang</span>
        </h3>
        <p class="stat-value">{{ isset($currentWeight) ? number_format($currentWeight, 1) : '—' }} <small class="text-muted fw-medium">kg</small></p>
    </div>
    <div class="stat-card">
        <h3 style="display: flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 15px; height: 15px; color: #f59e0b;"><path d="M12 2c-.3 2-2.5 4.5-2.5 7.5a6 6 0 0 0 11.5 2.5c.3-.8.5-1.7.5-2.5-1 1-2.2 1.5-3.5 1.5-2.5 0-4.5-2-4.5-4.5 0-1.8.8-3.4 2-4.5C13.5 2 12.5 2 12 2z"/></svg>
            <span>Kalori Terbakar</span>
        </h3>
        <p class="stat-value text-warning">{{ isset($todayCaloriesBurned) && $todayCaloriesBurned > 0 ? number_format($todayCaloriesBurned) : '0' }} <small class="text-muted fw-medium">kkal</small></p>
    </div>
    <div class="stat-card">
        <h3 style="display: flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px; color: var(--text-muted);"><path d="M18 2v20"/><path d="M18 7c1.66 0 3-1.34 3-3V2h-3v5Z"/><path d="M6 2v6a3 3 0 0 0 6 0V2"/><line x1="9" x2="9" y1="8" y2="22"/></svg>
            <span>Asupan Makanan</span>
        </h3>
        <p class="stat-value">{{ isset($todayCalories) ? number_format($todayCalories) : '0' }} <small class="text-muted fw-medium">kkal</small></p>
    </div>
    <div class="stat-card">
        <h3 style="display: flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px; color: var(--text-muted);"><path d="M6 5v14M18 5v14M2 9v6M22 9v6M6 12h12M4 7v10M20 7v10"/></svg>
            <span>Sesi Latihan</span>
        </h3>
        <p class="stat-value">{{ $workoutsThisWeek ?? 0 }} <small class="text-muted fw-medium text-sm">minggu ini</small></p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header flex-between align-center">
            <h3 class="m-0">Latihan Hari Ini</h3>
            <div class="d-flex align-center flex-gap-sm">
                @if(isset($ongoingWorkout) && $ongoingWorkout)
                    <span class="badge badge-warning">Sedang Berjalan</span>
                @endif
                <a href="{{ route('calendar.index') }}" class="btn btn-small btn-outline" style="padding: 3px 8px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;" title="Lihat Kalender Latihan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    <span>Kalender</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(isset($ongoingWorkout) && $ongoingWorkout)
                <div class="p-3 rounded mb-2 ongoing-workout-glass-card">
                    <div class="flex-between align-center mb-1">
                        <h3 class="mb-0 text-primary">{{ $ongoingWorkout->name }}</h3>
                        <span class="badge badge-warning">SESI BERJALAN</span>
                    </div>
                    <div class="workout-meta mb-3 mt-1">
                        <span class="badge badge-info">{{ $ongoingWorkout->workoutExercises->count() }} gerakan</span>
                        @if($ongoingWorkout->duration)
                            <span class="badge badge-secondary">{{ $ongoingWorkout->duration }} menit</span>
                        @endif
                        @if($ongoingWorkout->estimated_calories)
                            <span class="badge badge-warning">~{{ $ongoingWorkout->estimated_calories }} kkal</span>
                        @endif
                    </div>
                    <p class="text-sm text-muted mb-3">Sesi latihanmu belum selesai. Klik untuk melanjutkan dan catat set berikutnya!</p>
                    <div class="d-flex flex-gap-sm">
                        <a href="{{ route('workouts.resume', $ongoingWorkout) }}" class="btn btn-warning btn-small flex-1 text-center" style="font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px;"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                            <span>Lanjutkan Latihan</span>
                        </a>
                        <button type="button" class="btn btn-outline btn-small trigger-cancel-workout" data-workout-id="{{ $ongoingWorkout->id }}" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.3);">
                            Batalkan
                        </button>
                    </div>
                </div>
            @elseif(isset($todayWorkout) && $todayWorkout)
                <div>
                    <h3 class="mb-2">{{ $todayWorkout->name }}</h3>
                    <div class="workout-meta mb-3">
                        <span class="badge badge-info">{{ $todayWorkout->workoutExercises->count() }} gerakan</span>
                        <span class="badge badge-secondary">{{ $todayWorkout->duration ?? 0 }} menit</span>
                        @if($todayWorkout->estimated_calories)
                            <span class="badge badge-warning">{{ $todayWorkout->estimated_calories }} kkal</span>
                        @endif
                        <span class="badge badge-{{ $todayWorkout->status === 'completed' ? 'success' : 'warning' }}">
                            {{ $todayWorkout->status === 'completed' ? 'Selesai' : 'Sedang Berjalan' }}
                        </span>
                    </div>
                    @if($todayWorkout->status === 'in_progress')
                        <a href="{{ route('workouts.resume', $todayWorkout) }}" class="btn btn-warning btn-small mr-2" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px;"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                            <span>Lanjutkan Latihan</span>
                        </a>
                    @endif
                    <a href="{{ route('workouts.show', $todayWorkout) }}" class="btn btn-outline btn-small">Lihat Detail</a>
                </div>
            @elseif(isset($isRestDayToday) && $isRestDayToday)
                <div class="text-center py-4">
                    <div class="mb-2" style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background: rgba(245, 158, 11, 0.12);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="width: 28px; height: 28px;"><path d="M12 2a10 10 0 1 0 10 10 10 10 0 0 1-10-10z"/></svg>
                    </div>
                    <h4 class="mb-1 text-warning">Hari Ini: Rest Day</h4>
                    <p class="text-muted text-sm mb-0">Pemulihan otot aktif. Streak konsistensimu tetap aman terjaga.</p>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted mb-3">Belum ada latihan yang dicatat hari ini.</p>
                    <div class="d-flex justify-content-center flex-gap-sm">
                        <a href="{{ route('workouts.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="width: 14px; height: 14px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span>Mulai Latihan</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header flex-between align-center">
            <h3 class="m-0">Makanan Hari Ini</h3>
            <a href="{{ route('foods.index') }}" class="btn btn-small btn-outline">+ Tambah</a>
        </div>
        <div class="card-body">
            @php
                $target = $calorieTarget ?? 2000;
                $current = $todayCalories ?? 0;
                $percentage = $target > 0 ? min(100, ($current / $target) * 100) : 0;
                $progressClass = $current > $target ? 'progress-danger' : 'progress-success';
            @endphp
            <div class="progress-container mb-4">
                <div class="progress-labels">
                    <span class="text-sm">Kalori</span>
                    <span class="text-sm fw-semibold">{{ number_format($current) }} / {{ number_format($target) }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill {{ $progressClass }}" style="width: {{ $percentage }}%"></div>
                </div>
            </div>

            @if(isset($todayFoods) && count($todayFoods) > 0)
                @foreach(['breakfast' => 'Sarapan', 'lunch' => 'Makan Siang', 'dinner' => 'Makan Malam', 'snack' => 'Camilan'] as $meal => $label)
                    @if(isset($todayFoods[$meal]) && count($todayFoods[$meal]) > 0)
                        <div class="mb-2">
                            <small class="text-muted fw-semibold text-uppercase letter-wide">{{ $label }}</small>
                            @foreach($todayFoods[$meal] as $food)
                                <div class="flex-between align-center" style="padding: 3px 0; border-bottom: 1px solid var(--border-light);">
                                    <span class="text-sm">{{ $food->food_name }}</span>
                                    <span class="text-sm text-muted">{{ $food->calories }} kkal</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            @else
                <p class="text-muted text-sm mb-0">Belum ada makanan yang dicatat hari ini.</p>
            @endif
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal (Bukan browser alert pop-up) -->
<div id="cancel-confirm-modal" class="modal-overlay">
    <div class="modal-card" style="max-width: 400px; text-align: center; padding: 24px;">
        <div class="mb-3" style="width: 52px; height: 52px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); display: inline-flex; align-items: center; justify-content: center; color: var(--danger);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 26px; height: 26px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <h3 class="mb-1">Batalkan Sesi Latihan?</h3>
        <p class="text-muted text-sm mb-4">Semua progres dan set pada sesi latihan ini akan dihapus permanen.</p>
        <div class="d-flex flex-gap-sm">
            <button type="button" class="btn btn-outline flex-1" id="btn-modal-dismiss">Batal</button>
            <form id="cancel-workout-form" method="POST" action="" class="flex-1 m-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">Ya, Batalkan</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const restBtn = document.getElementById('btn-rest-day-toggle');
    if (restBtn) {
        restBtn.addEventListener('click', async function() {
            restBtn.disabled = true;
            try {
                const res = await fetch("{{ route('rest-days.toggle') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ date: "{{ date('Y-m-d') }}" })
                });

                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal mengubah status Rest Day.');
                    restBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
                restBtn.disabled = false;
            }
        });
    }

    // Modal Confirmation for cancel workout
    const cancelModal = document.getElementById('cancel-confirm-modal');
    const cancelForm = document.getElementById('cancel-workout-form');
    const dismissBtn = document.getElementById('btn-modal-dismiss');

    document.querySelectorAll('.trigger-cancel-workout').forEach(btn => {
        btn.addEventListener('click', function() {
            const wId = this.getAttribute('data-workout-id');
            if (cancelForm) {
                cancelForm.action = `/workouts/${wId}/cancel`;
            }
            if (cancelModal) {
                cancelModal.classList.add('active');
            }
        });
    });

    if (dismissBtn && cancelModal) {
        dismissBtn.addEventListener('click', () => cancelModal.classList.remove('active'));
        cancelModal.addEventListener('click', (e) => {
            if (e.target === cancelModal) cancelModal.classList.remove('active');
        });
    }
});
</script>
@endpush
@endsection
