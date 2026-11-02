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
            @if(!empty($workout->estimated_calories))
                <span class="badge badge-warning" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                    🔥 {{ number_format($workout->estimated_calories) }} kkal terbakar
                </span>
            @endif
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
            <div class="streak-celebration-box mt-3" style="background: rgba(249, 115, 22, 0.1); border: 1px solid rgba(249, 115, 22, 0.25); border-radius: var(--r-md); padding: 12px 16px; display: flex; align-items: center; gap: 14px;">
                <div class="streak-celebration-icon" style="font-size: 1.8rem;">{{ $streakData['flame_emoji'] }}</div>
                <div class="streak-celebration-text">
                    <strong>Streak Api Menyala: {{ $streakData['current_streak'] }} Hari Beruntun!</strong>
                    <p class="mb-0 text-sm text-muted">Konsistensimu terjaga dengan baik. {{ $streakData['flame_title'] }} aktif!</p>
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
        @php
            $isCardio = strtolower($exercise->exercise->category ?? '') === 'cardio';
        @endphp
        <div class="card mb-3">
            <div class="card-body">
                <div class="flex-between mb-3 align-center">
                    <h4 class="m-0 text-primary">
                        {{ $exercise->exercise->name ?? 'Nama Gerakan' }}
                        @if($isCardio)
                            <span class="badge badge-info ml-2" style="font-size: 0.72rem; vertical-align: middle;">Kardio</span>
                        @endif
                    </h4>
                    <span class="badge badge-secondary">{{ $exercise->sets ?? 0 }} {{ $isCardio ? 'Sesi' : 'Set' }}</span>
                </div>

                @if(!empty($exercise->sets_data) && is_array($exercise->sets_data))
                    <small class="text-muted fw-semibold d-block mb-2 text-uppercase letter-wide">Rincian {{ $isCardio ? 'Sesi Kardio' : 'Set' }}</small>
                    <div class="d-flex flex-wrap flex-gap-sm">
                        @foreach($exercise->sets_data as $s)
                            <div style="flex: 1; min-width: 100px; background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-sm); padding: 8px 12px; text-align: center;">
                                <small class="text-muted d-block">{{ $isCardio ? 'Sesi' : 'Set' }} {{ $s['set'] }}</small>
                                @if($isCardio || isset($s['duration']) && $s['duration'] > 0)
                                    <strong class="text-primary" style="display: block; margin: 2px 0;">{{ $s['duration'] ?? 0 }} mnt</strong>
                                    <span class="text-sm text-muted">{{ !empty($s['distance']) ? $s['distance'] . ' km' : 'Kardio' }}</span>
                                @else
                                    <strong class="text-primary" style="display: block; margin: 2px 0;">{{ $s['weight'] ?? 0 }} kg</strong>
                                    <span class="text-sm text-muted">{{ $s['reps'] ?? 0 }} reps</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-sm); padding: 10px 14px;">
                        <small class="text-muted d-block">Ringkasan</small>
                        @if($isCardio)
                            <strong>{{ $exercise->reps }} menit &times; {{ $exercise->sets }} sesi</strong>
                        @else
                            <strong>{{ $exercise->weight ?? 0 }} kg &times; {{ $exercise->sets }} set &times; {{ $exercise->reps }} reps</strong>
                        @endif
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
        <div class="card p-4 text-center text-muted">
            Belum ada gerakan dicatat pada latihan ini.
        </div>
    @endforelse
</div>
@endsection
