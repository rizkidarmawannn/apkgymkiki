@extends('layouts.app')

@section('title', 'Workouts')

@section('content')
<div class="page-header flex-between">
    <h1>Workouts</h1>
    <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Start Workout</a>
</div>

<div class="workouts-list">
    @forelse($workouts ?? [] as $workout)
        <a href="{{ route('workouts.show', $workout) }}" class="card workout-card-link">
            <div class="workout-card-header flex-between">
                <h3>{{ $workout->name }}</h3>
                <span class="text-muted text-small">{{ \Carbon\Carbon::parse($workout->date)->format('M d, Y') }}</span>
            </div>
            <div class="workout-card-body mt-2">
                <div class="workout-meta">
                    <span class="badge badge-info">🏋️ {{ $workout->workoutExercises->count() }} exercises</span>
                    @if($workout->duration)
                        <span class="badge badge-secondary">⏱️ {{ $workout->duration }} min</span>
                    @endif
                    <span class="badge badge-{{ $workout->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($workout->status) }}</span>
                </div>
            </div>
        </a>
    @empty
        <div class="card empty-state text-center p-5">
            <div class="empty-icon mb-3">🏋️</div>
            <h3>No workouts yet</h3>
            <p class="text-muted mb-4">Start your first workout to track your progress!</p>
            <a href="{{ route('workouts.create') }}" class="btn btn-primary">Start Your First Workout</a>
        </div>
    @endforelse
</div>

@if(isset($workouts) && method_exists($workouts, 'links'))
    <div class="pagination-wrapper mt-4">
        {{ $workouts->links() }}
    </div>
@endif
@endsection
