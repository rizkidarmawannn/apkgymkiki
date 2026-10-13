@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div>
        <h1 class="mb-0">Halo, {{ $user->name ?? 'Pengguna' }} 👋</h1>
        <p class="text-sm text-muted mb-0">Yuk tetap konsisten hari ini.</p>
    </div>
    @if(isset($ongoingWorkout) && $ongoingWorkout)
        <a href="{{ route('workouts.resume', $ongoingWorkout) }}" class="btn btn-warning" style="font-weight: 700;">▶️ Lanjutkan Latihan</a>
    @else
        <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Latihan Baru</a>
    @endif
</div>

@if(isset($streakData))
<div id="streak-section" class="tiktok-streak-card mb-4 flame-theme-{{ $streakData['flame_level'] }}">
    <div class="streak-glow-bg"></div>
    <div class="streak-card-inner">
        <div class="streak-header-row">
            <div class="streak-badge-hero">
                <div class="flame-icon-wrapper {{ $streakData['status'] === 'lit' ? 'flame-animated' : ($streakData['status'] === 'warning' ? 'flame-flicker' : '') }}">
                    @if($streakData['status'] === 'extinguished')
                        <span class="flame-emoji-hero">🕯️</span>
                    @elseif($streakData['status'] === 'warning')
                        <span class="flame-emoji-hero">⏳</span>
                    @else
                        <span class="flame-emoji-hero">🔥</span>
                    @endif
                </div>
                <div class="streak-counter-info">
                    <div class="streak-number-row">
                        <span class="streak-number">{{ $streakData['current_streak'] }}</span>
                        <span class="streak-label">HARI STREAK</span>
                    </div>
                    <div class="streak-status-badge status-{{ $streakData['status'] }}">
                        @if($streakData['status'] === 'lit')
                            <span>⚡ {{ $streakData['flame_title'] }}</span>
                        @elseif($streakData['status'] === 'warning')
                            <span>⚠️ Apimu Hampir Padam Hari Ini!</span>
                        @else
                            <span>💤 Belum Ada Streak Aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="streak-best-badge">
                <span class="best-label">🏆 Rekor Terbaik</span>
                <span class="best-value">{{ $streakData['best_streak'] }} Hari</span>
            </div>
        </div>

        <div class="streak-motivation-msg">
            @if($streakData['status'] === 'lit')
                <p>🔥 <strong>Kerja keras terbayar!</strong> Latihan hari ini sudah dicatat. Jaga api streakmu tetap menyala besok!</p>
            @elseif($streakData['status'] === 'warning')
                <p class="warning-text">⏰ <strong>Peringatan Streak!</strong> Kamu latihan kemarin tapi belum ada sesi hari ini. Selesaikan 1 latihan sebelum 23:59 agar apimu tidak padam!</p>
            @else
                <p>💪 <strong>Mulai apimu hari ini!</strong> Selesaikan latihan pertamamu sekarang untuk memantik streak api seperti TikTok.</p>
            @endif
        </div>

        <!-- 7-Day Week Indicator Dots -->
        <div class="streak-week-track">
            <div class="streak-week-label">Minggu Ini:</div>
            <div class="streak-week-days">
                @foreach($streakData['week_days'] as $wDay)
                    <div class="streak-day-item {{ $wDay['is_today'] ? 'day-today' : '' }} {{ $wDay['has_workout'] ? 'day-done' : '' }}">
                        <span class="day-name">{{ $wDay['day_name'] }}</span>
                        <div class="day-dot">
                            @if($wDay['has_workout'])
                                <span>🔥</span>
                            @elseif($wDay['is_today'])
                                <span class="dot-pulse">●</span>
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

<div class="stats-grid">
    <div class="stat-card">
        <h3>Berat Sekarang</h3>
        <p class="stat-value">{{ isset($currentWeight) ? number_format($currentWeight, 1) : '—' }} <small class="text-muted fw-medium">kg</small></p>
    </div>
    <div class="stat-card">
        <h3>Kalori Hari Ini</h3>
        <p class="stat-value">{{ isset($todayCalories) ? number_format($todayCalories) : '—' }} <small class="text-muted fw-medium">kkal</small></p>
    </div>
    <div class="stat-card">
        <h3>Latihan</h3>
        <p class="stat-value">{{ $workoutsThisWeek ?? 0 }} <small class="text-muted fw-medium text-sm">minggu ini</small></p>
    </div>
    <div class="stat-card">
        <h3>Target Berat</h3>
        <p class="stat-value">{{ isset($targetWeight) ? number_format($targetWeight, 1) : '—' }} <small class="text-muted fw-medium">kg</small></p>
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
                <a href="{{ route('workouts.index') }}" class="btn btn-small btn-outline" style="padding: 3px 8px; font-size: 0.8rem;" title="Lihat Kalender Latihan">📅 Kalender</a>
            </div>
        </div>
        <div class="card-body">
            @if(isset($ongoingWorkout) && $ongoingWorkout)
                <div class="p-3 rounded mb-2" style="background: rgba(245, 158, 11, 0.08); border: 1.5px solid var(--warning);">
                    <div class="flex-between align-center mb-1">
                        <h3 class="mb-0 text-primary">{{ $ongoingWorkout->name }}</h3>
                        <span class="badge badge-warning">⚡ ON GOING</span>
                    </div>
                    <div class="workout-meta mb-3 mt-1">
                        <span class="badge badge-info">{{ $ongoingWorkout->workoutExercises->count() }} gerakan</span>
                        @if($ongoingWorkout->duration)
                            <span class="badge badge-secondary">{{ $ongoingWorkout->duration }} menit</span>
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
                        <span class="badge badge-{{ $todayWorkout->status === 'completed' ? 'success' : 'warning' }}">
                            {{ $todayWorkout->status === 'completed' ? 'Selesai' : 'Sedang Berjalan' }}
                        </span>
                    </div>
                    @if($todayWorkout->status === 'in_progress')
                        <a href="{{ route('workouts.resume', $todayWorkout) }}" class="btn btn-warning btn-small mr-2">▶️ Lanjutkan Latihan</a>
                    @endif
                    <a href="{{ route('workouts.show', $todayWorkout) }}" class="btn btn-outline btn-small">Lihat Detail</a>
                </div>
            @else
                <div class="text-center" style="padding: 24px 0;">
                    <p class="text-muted mb-3">Belum ada latihan hari ini.</p>
                    <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Mulai Latihan</a>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Makanan Hari Ini</h3>
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
@endsection
