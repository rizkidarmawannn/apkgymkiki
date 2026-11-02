@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div>
        <h1 class="mb-0">Halo, {{ $user->name ?? 'Pengguna' }} 👋</h1>
        <p class="text-sm text-muted mb-0">Yuk tetap konsisten bangun kebiasaan sehatmu.</p>
    </div>
    @if(isset($ongoingWorkout) && $ongoingWorkout)
        <a href="{{ route('workouts.resume', $ongoingWorkout) }}" class="btn btn-warning" style="font-weight: 700;">▶️ Lanjutkan Latihan</a>
    @else
        <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Latihan Baru</a>
    @endif
</div>

@if(isset($streakData))
<div id="streak-section" class="streak-pro-card mb-4 flame-tier-{{ $streakData['flame_tier'] }} flame-theme-{{ $streakData['flame_level'] }}">
    <div class="streak-glow-ambient"></div>
    <div class="streak-card-body">
        <div class="streak-top-bar flex-between align-center">
            <!-- Left Hero: Animated Flame + Counter -->
            <div class="streak-hero-group">
                <div class="streak-flame-circle tier-{{ $streakData['flame_tier'] }} status-{{ $streakData['status'] }}">
                    <span class="streak-flame-emoji">{{ $streakData['flame_emoji'] }}</span>
                </div>
                <div class="streak-text-group">
                    <div class="streak-count-row">
                        <span class="streak-huge-number">{{ $streakData['current_streak'] }}</span>
                        <span class="streak-unit-label">HARI STREAK</span>
                    </div>
                    <div class="streak-tier-badge tier-{{ $streakData['flame_tier'] }}">
                        @if($streakData['status'] === 'rest')
                            <span class="badge-dot dot-yellow"></span>
                            <span>🛌 Hari Istirahat &bull; Streak Terjaga</span>
                        @elseif($streakData['status'] === 'lit')
                            <span class="badge-dot dot-flame"></span>
                            <span>{{ $streakData['flame_title'] }}</span>
                        @elseif($streakData['status'] === 'warning')
                            <span class="badge-dot dot-warning"></span>
                            <span>⚠️ Selesaikan 1 Latihan atau Ambil Rest Day!</span>
                        @else
                            <span class="badge-dot dot-muted"></span>
                            <span>Api Belum Aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Best Record & Quick Rest Day Toggle -->
            <div class="streak-actions-group">
                <div class="streak-record-pill">
                    <span class="record-title">🏆 Rekor Terbaik</span>
                    <strong class="record-days">{{ $streakData['best_streak'] }} Hari</strong>
                </div>

                <button type="button" 
                        id="btn-rest-day-toggle" 
                        class="btn-rest-toggle {{ ($isRestDayToday ?? false) ? 'active' : '' }}" 
                        data-active="{{ ($isRestDayToday ?? false) ? '1' : '0' }}"
                        title="Tandai hari ini sebagai Rest Day agar streakmu tidak padam">
                    <span class="rest-icon">🛌</span>
                    <span id="rest-btn-label">{{ ($isRestDayToday ?? false) ? 'Rest Day Aktif (Kuning)' : 'Ambil Rest Day' }}</span>
                </button>
            </div>
        </div>

        <!-- Milestone Progress (Tier 10, 50, 100+) -->
        <div class="streak-milestone-bar-wrap mt-3">
            <div class="flex-between align-center text-xs mb-1">
                <span class="text-secondary fw-medium">
                    @if($streakData['current_streak'] >= 100)
                        👑 Tier Mahkota Emas Maksimal (100+ Hari)
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

        <!-- 7-Day Week Indicator Dots -->
        <div class="streak-week-track mt-3 pt-3">
            <div class="streak-week-label">Minggu Ini:</div>
            <div class="streak-week-days">
                @foreach($streakData['week_days'] as $wDay)
                    <div class="streak-day-item {{ $wDay['is_today'] ? 'day-today' : '' }} {{ $wDay['has_workout'] ? 'day-done' : '' }} {{ $wDay['is_rest'] ? 'day-rest' : '' }}">
                        <span class="day-name">{{ $wDay['day_name'] }}</span>
                        <div class="day-dot">
                            @if($wDay['has_workout'])
                                <span class="dot-icon" title="Latihan selesai">🔥</span>
                            @elseif($wDay['is_rest'])
                                <span class="dot-icon dot-rest" title="Hari Istirahat (Rest Day)">🟡</span>
                            @elseif($wDay['is_today'])
                                <span class="dot-pulse" title="Hari Ini">●</span>
                            @else
                                <span class="dot-empty">•</span>
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
        <h3>Berat Sekarang</h3>
        <p class="stat-value">{{ isset($currentWeight) ? number_format($currentWeight, 1) : '—' }} <small class="text-muted fw-medium">kg</small></p>
    </div>
    <div class="stat-card">
        <h3>🔥 Terbakar Hari Ini</h3>
        <p class="stat-value text-warning">{{ isset($todayCaloriesBurned) && $todayCaloriesBurned > 0 ? number_format($todayCaloriesBurned) : '0' }} <small class="text-muted fw-medium">kkal</small></p>
    </div>
    <div class="stat-card">
        <h3>🍽️ Asupan Kalori</h3>
        <p class="stat-value">{{ isset($todayCalories) ? number_format($todayCalories) : '0' }} <small class="text-muted fw-medium">kkal</small></p>
    </div>
    <div class="stat-card">
        <h3>Latihan</h3>
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
                        <span class="badge badge-warning">⚡ ON GOING</span>
                    </div>
                    <div class="workout-meta mb-3 mt-1">
                        <span class="badge badge-info">{{ $ongoingWorkout->workoutExercises->count() }} gerakan</span>
                        @if($ongoingWorkout->duration)
                            <span class="badge badge-secondary">{{ $ongoingWorkout->duration }} menit</span>
                        @endif
                        @if($ongoingWorkout->estimated_calories)
                            <span class="badge badge-warning">🔥 ~{{ $ongoingWorkout->estimated_calories }} kkal</span>
                        @endif
                    </div>
                    <p class="text-sm text-muted mb-3">Sesi latihanmu belum selesai. Klik untuk melanjutkan dan catat set berikutnya!</p>
                    <div class="d-flex flex-gap-sm">
                        <a href="{{ route('workouts.resume', $ongoingWorkout) }}" class="btn btn-warning btn-small flex-1 text-center" style="font-weight: 700;">▶️ Lanjutkan Latihan</a>
                        <form action="{{ route('workouts.cancel', $ongoingWorkout) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan sesi latihan ini?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-small" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.3);">Batalkan</button>
                        </form>
                    </div>
                </div>
            @elseif(isset($todayWorkout) && $todayWorkout)
                <div>
                    <h3 class="mb-2">{{ $todayWorkout->name }}</h3>
                    <div class="workout-meta mb-3">
                        <span class="badge badge-info">{{ $todayWorkout->workoutExercises->count() }} gerakan</span>
                        <span class="badge badge-secondary">{{ $todayWorkout->duration ?? 0 }} menit</span>
                        @if($todayWorkout->estimated_calories)
                            <span class="badge badge-warning">🔥 {{ $todayWorkout->estimated_calories }} kkal</span>
                        @endif
                        <span class="badge badge-{{ $todayWorkout->status === 'completed' ? 'success' : 'warning' }}">
                            {{ $todayWorkout->status === 'completed' ? 'Selesai' : 'Sedang Berjalan' }}
                        </span>
                    </div>
                    @if($todayWorkout->status === 'in_progress')
                        <a href="{{ route('workouts.resume', $todayWorkout) }}" class="btn btn-warning btn-small mr-2">▶️ Lanjutkan Latihan</a>
                    @endif
                    <a href="{{ route('workouts.show', $todayWorkout) }}" class="btn btn-outline btn-small">Lihat Detail</a>
                </div>
            @elseif(isset($isRestDayToday) && $isRestDayToday)
                <div class="text-center py-4">
                    <div style="font-size: 2.4rem; margin-bottom: 8px;">🛋️</div>
                    <h4 class="mb-1 text-warning">Hari Ini: Rest Day</h4>
                    <p class="text-muted text-sm mb-0">Tubuhmu butuh pemulihan untuk pertumbuhan otot yang maksimal. Streak apimu tetap terjaga!</p>
                </div>
            @else
                <div class="text-center" style="padding: 24px 0;">
                    <p class="text-muted mb-3">Belum ada latihan hari ini.</p>
                    <div class="d-flex justify-content-center flex-gap-sm">
                        <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Mulai Latihan</a>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const restBtn = document.getElementById('btn-rest-day-toggle');
    if (!restBtn) return;

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
                // Reload to reflect all updated stats & streak instantly
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
});
</script>
@endpush
@endsection
