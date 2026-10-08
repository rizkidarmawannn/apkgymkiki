@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-header">
    <h1>Hello, {{ $user->name ?? 'User' }}</h1>
    <p class="subtitle">Let's stay consistent!</p>
</div>

<div class="stats-grid">
    <div class="card stat-card stat-weight">
        <h3>Current Weight</h3>
        <p class="stat-value">{{ isset($currentWeight) ? number_format($currentWeight, 1) : '0.0' }} kg</p>
    </div>
    <div class="card stat-card stat-calories">
        <h3>Today's Calories</h3>
        <p class="stat-value">{{ isset($todayCalories) ? number_format($todayCalories) : '0' }} kcal</p>
    </div>
    <div class="card stat-card stat-workouts">
        <h3>Workouts</h3>
        <p class="stat-value">{{ $workoutsThisWeek ?? 0 }}x this week</p>
    </div>
    <div class="card stat-card stat-target">
        <h3>Target</h3>
        <p class="stat-value">{{ isset($targetWeight) ? number_format($targetWeight, 1) : '0.0' }} kg</p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card dashboard-card">
        <div class="card-header">
            <h2>Today's Workout</h2>
        </div>
        <div class="card-body">
            @if(isset($todayWorkout) && $todayWorkout)
                <div class="workout-summary">
                    <h3>{{ $todayWorkout->name }}</h3>
                    <div class="workout-meta">
                        <span class="badge badge-info">{{ $todayWorkout->workoutExercises->count() }} exercises</span>
                        <span class="badge badge-secondary">{{ $todayWorkout->duration ?? 0 }} min</span>
                        <span class="badge badge-{{ $todayWorkout->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($todayWorkout->status) }}</span>
                    </div>
                    <a href="{{ route('workouts.show', $todayWorkout) }}" class="btn btn-secondary mt-3">View Details</a>
                </div>
            @else
                <div class="empty-state">
                    <p>No workout today</p>
                    <a href="{{ route('workouts.create') }}" class="btn btn-primary mt-2">+ Start Workout</a>
                </div>
            @endif
        </div>
    </div>

    <div class="card dashboard-card">
        <div class="card-header">
            <h2>Today's Food</h2>
            <a href="{{ route('foods.index') }}" class="btn btn-small btn-outline">+ Add Food</a>
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
                    <span>Calories</span>
                    <span>{{ number_format($current) }} / {{ number_format($target) }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill {{ $progressClass }}" style="width: {{ $percentage }}%"></div>
                </div>
            </div>

            @if(isset($todayFoods) && count($todayFoods) > 0)
                <ul class="food-summary-list">
                    @foreach(['breakfast', 'lunch', 'dinner', 'snack'] as $meal)
                        @if(isset($todayFoods[$meal]) && count($todayFoods[$meal]) > 0)
                            <li class="meal-group-summary">
                                <strong>{{ ucfirst($meal) }}</strong>
                                <ul>
                                    @foreach($todayFoods[$meal] as $food)
                                        <li>{{ $food->food_name }} <span class="text-muted">{{ $food->calories }} kcal</span></li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <div class="empty-state">
                    <p>No food logged yet today</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
