@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div>
        <h1 class="mb-0">Halo, {{ $user->name ?? 'Pengguna' }} 👋</h1>
        <p class="text-sm text-muted mb-0">Yuk tetap konsisten hari ini.</p>
    </div>
    <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Latihan Baru</a>
</div>

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
        <div class="card-header">
            <h3>Latihan Hari Ini</h3>
        </div>
        <div class="card-body">
            @if(isset($todayWorkout) && $todayWorkout)
                <div>
                    <h3 class="mb-2">{{ $todayWorkout->name }}</h3>
                    <div class="workout-meta mb-3">
                        <span class="badge badge-info">{{ $todayWorkout->workoutExercises->count() }} gerakan</span>
                        <span class="badge badge-secondary">{{ $todayWorkout->duration ?? 0 }} menit</span>
                        <span class="badge badge-{{ $todayWorkout->status === 'completed' ? 'success' : 'warning' }}">
                            {{ $todayWorkout->status === 'completed' ? 'Selesai' : 'Sedang Berjalan' }}
                        </span>
                    </div>
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
