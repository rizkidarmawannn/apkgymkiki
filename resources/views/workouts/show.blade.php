@extends('layouts.app')

@section('title', 'Detail Latihan')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div class="d-flex align-center flex-gap">
        <a href="{{ route('workouts.index') }}" class="btn btn-outline btn-small">← Kembali</a>
        <h1 class="mb-0">Detail Latihan</h1>
    </div>
    <form action="{{ route('workouts.destroy', $workout->id ?? 0) }}" method="POST" onsubmit="return confirm('Hapus latihan ini?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-small">Hapus</button>
    </form>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h2>{{ $workout->name ?? 'Nama Latihan' }}</h2>
        <div class="workout-meta mt-2 flex-gap">
            <span class="badge badge-primary">📅 {{ isset($workout->date) ? \Carbon\Carbon::parse($workout->date)->format('d/m/Y') : 'Tanggal' }}</span>
            <span class="badge badge-secondary">⏱️ {{ $workout->duration ?? 0 }} menit</span>
            <span class="badge badge-{{ ($workout->status ?? '') === 'completed' ? 'success' : 'warning' }}">
                {{ ($workout->status ?? '') === 'completed' ? 'Selesai' : 'Sedang Berjalan' }}
            </span>
        </div>
        @if(($workout->status ?? '') === 'in_progress')
            <div class="alert alert-warning mt-3 flex-between align-center" style="border: 1px solid var(--warning); background: var(--warning-light);">
                <div>
                    <strong>⚡ Sesi Latihan Sedang Berjalan!</strong>
                    <div class="text-sm">Latihan ini belum diselesaikan. Lanjutkan sekarang untuk mencatat progres lainnya.</div>
                </div>
                <a href="{{ route('workouts.resume', $workout) }}" class="btn btn-warning btn-small ml-2" style="font-weight: 700; white-space: nowrap;">
                    ▶️ Lanjutkan Latihan
                </a>
            </div>
        @elseif(($workout->status ?? '') === 'completed' && isset($streakData) && $streakData['current_streak'] > 0)
            <div class="streak-celebration-box mt-3">
                <div class="streak-celebration-icon">🔥</div>
                <div class="streak-celebration-text">
                    <strong>Streak Api Menyala: {{ $streakData['current_streak'] }} Hari Beruntun!</strong>
                    <p class="mb-0 text-sm">Konsistensimu luar biasa. {{ $streakData['flame_title'] }} terjaga hari ini!</p>
                </div>
            </div>
        @endif
        @if(!empty($workout->notes))
            <div class="mt-3" style="border-top: 1px solid var(--border); padding-top: 12px;">
                <small class="text-muted fw-semibold">CATATAN</small>
                <p class="mb-0 mt-1">{{ $workout->notes }}</p>
            </div>
        @endif
    </div>
</div>

@if(isset($prs) && count($prs) > 0)
    <div class="mb-4">
        @foreach($prs as $pr)
            <div class="alert alert-success">
                🏆 <strong>Rekor Baru!</strong> {{ $pr['exercise'] }} {{ $pr['weight'] }}kg (Sebelumnya: {{ $pr['previous'] }}kg)
            </div>
        @endforeach
    </div>
@endif

<h3 class="mb-3">Daftar Gerakan</h3>

<div class="exercise-list">
    @forelse($workout->workoutExercises ?? [] as $exercise)
        <div class="card mb-3">
            <div class="card-body">
                <div class="flex-between mb-3 align-center">
                    <h4 class="m-0 text-primary">{{ $exercise->exercise->name ?? 'Nama Gerakan' }}</h4>
                    <span class="badge badge-info">{{ $exercise->sets ?? 0 }} Set</span>
                </div>

                @if(!empty($exercise->sets_data) && is_array($exercise->sets_data))
                    <small class="text-muted fw-semibold d-block mb-2 text-uppercase letter-wide">Rincian Set</small>
                    <div class="d-flex flex-wrap flex-gap-sm">
                        @foreach($exercise->sets_data as $s)
                            <div style="flex: 1; min-width: 90px; background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-sm); padding: 8px 10px; text-align: center;">
                                <small class="text-muted d-block">Set {{ $s['set'] }}</small>
                                <strong class="text-primary" style="display: block; margin: 2px 0;">{{ $s['weight'] }} kg</strong>
                                <span class="text-sm text-muted">{{ $s['reps'] }} reps</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-sm); padding: 10px 14px;">
                        <small class="text-muted d-block">Ringkasan</small>
                        <strong>{{ $exercise->weight ?? 0 }} kg &times; {{ $exercise->sets }} set &times; {{ $exercise->reps }} reps</strong>
                    </div>
                @endif

                @if(!empty($exercise->notes))
                    <div class="mt-3" style="border-top: 1px solid var(--border); padding-top: 10px;">
                        <em class="text-sm text-muted">Catatan: {{ $exercise->notes }}</em>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">Tidak ada gerakan yang tercatat untuk latihan ini.</p>
    @endforelse
</div>
@endsection
