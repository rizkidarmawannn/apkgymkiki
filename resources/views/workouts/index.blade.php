@extends('layouts.app')

@section('title', 'Latihan')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <h1 class="mb-0">Latihan</h1>
    <a href="{{ route('workouts.create') }}" class="btn btn-primary">+ Mulai Latihan</a>
</div>

<div class="workouts-list">
    @forelse($workouts ?? [] as $workout)
        <a href="{{ route('workouts.show', $workout) }}" class="workout-card-link">
            <div class="flex-between align-center mb-2">
                <h3>{{ $workout->name }}</h3>
                <span class="text-muted text-sm">{{ \Carbon\Carbon::parse($workout->date)->locale('id')->translatedFormat('d M Y') }}</span>
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
            <h3 class="mb-2">Belum ada latihan</h3>
            <p class="text-muted mb-4">Mulai latihan pertamamu untuk melacak progres!</p>
            <a href="{{ route('workouts.create') }}" class="btn btn-primary mx-auto" style="max-width: 220px;">Mulai Latihan Pertama</a>
        </div>
    @endforelse
</div>

@if(isset($workouts) && method_exists($workouts, 'links'))
    <div class="mt-4">
        {{ $workouts->links() }}
    </div>
@endif
@endsection
