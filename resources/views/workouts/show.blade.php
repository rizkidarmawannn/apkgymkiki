@extends('layouts.app')

@section('title', 'Workout Detail')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div class="d-flex align-center flex-gap">
        <a href="{{ route('workouts.index') }}" class="btn btn-outline btn-small">&larr; Back</a>
        <h1 class="mb-0">Workout Detail</h1>
    </div>
    <form action="{{ route('workouts.destroy', $workout->id ?? 0) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this workout?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-small">Delete</button>
    </form>
</div>

<div class="card mb-4 workout-hero-card">
    <div class="card-body">
        <h2>{{ $workout->name ?? 'Workout Name' }}</h2>
        <div class="workout-meta mt-2 flex-gap">
            <span class="badge badge-primary">📅 {{ isset($workout->date) ? \Carbon\Carbon::parse($workout->date)->format('F j, Y') : 'Date' }}</span>
            <span class="badge badge-secondary">⏱️ {{ $workout->duration ?? 0 }} mins</span>
            <span class="badge badge-{{ ($workout->status ?? '') === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($workout->status ?? 'Completed') }}</span>
        </div>
        @if(!empty($workout->notes))
            <div class="workout-notes mt-3">
                <strong>Notes:</strong>
                <p>{{ $workout->notes }}</p>
            </div>
        @endif
    </div>
</div>

@if(isset($prs) && count($prs) > 0)
    <div class="pr-alerts mb-4">
        @foreach($prs as $pr)
            <div class="alert alert-success pr-alert">
                🏆 <strong>New PR!</strong> {{ $pr['exercise'] }} {{ $pr['weight'] }}kg (Previous: {{ $pr['previous'] }}kg)
            </div>
        @endforeach
    </div>
@endif

<h3 class="mb-3">Exercises</h3>

<div class="exercise-list">
    @forelse($workout->workoutExercises ?? [] as $exercise)
        <div class="card exercise-detail-card mb-3">
            <div class="card-body">
                <div class="flex-between mb-2 align-center">
                    <h4 class="m-0 text-primary">{{ $exercise->exercise->name ?? 'Exercise Name' }}</h4>
                    <span class="badge badge-info">{{ $exercise->sets ?? 0 }} Sets Total</span>
                </div>

                <div class="exercise-stats mt-3">
                    @if(!empty($exercise->sets_data) && is_array($exercise->sets_data))
                        <small class="text-muted d-block mb-2">Set-by-Set Breakdown:</small>
                        <div class="d-flex flex-wrap flex-gap">
                            @foreach($exercise->sets_data as $s)
                                <div class="card p-2 bg-light text-center flex-1" style="min-width: 110px; border: 1px solid var(--border);">
                                    <small class="text-muted d-block">Set {{ $s['set'] }}</small>
                                    <strong class="text-primary text-md" style="display:block; margin:2px 0;">{{ $s['weight'] }} kg</strong>
                                    <span class="text-small text-muted">{{ $s['reps'] }} reps</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="stat-box">
                            <small class="text-muted d-block">Summary</small>
                            <strong>{{ $exercise->weight ?? 0 }} kg &times; {{ $exercise->sets }} sets &times; {{ $exercise->reps }} reps</strong>
                        </div>
                    @endif
                </div>

                @if(!empty($exercise->notes))
                    <div class="exercise-notes mt-3 text-small text-muted border-top pt-2">
                        <em>Notes: {{ $exercise->notes }}</em>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">No exercises recorded for this workout.</p>
    @endforelse
</div>
@endsection
