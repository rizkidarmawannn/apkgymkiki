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
            <span class="badge badge-primary" style="display: inline-flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <span>{{ isset($workout->date) ? \Carbon\Carbon::parse($workout->date)->format('d/m/Y') : 'Tanggal' }}</span>
            </span>
            <span class="badge badge-secondary" style="display: inline-flex; align-items: center; gap: 4px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ $workout->duration ?? 0 }} menit</span>
            </span>
            @if(!empty($workout->estimated_calories))
                <span class="badge badge-warning" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); display: inline-flex; align-items: center; gap: 4px;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 12px; height: 12px;"><path d="M12 2c-.5 2-3 5-3.5 8.5a5.5 5.5 0 0 0 10.5 3c.5-1.5.5-2.5.5-3.5-1.5 1.5-3 2-4 1.5-2-.8-2.5-3-1.5-5 1-1.5 1-3 0-4.5z"/></svg>
                    <span>{{ number_format($workout->estimated_calories) }} kkal terbakar</span>
                </span>
            @endif
            <span class="badge badge-{{ ($workout->status ?? '') === 'completed' ? 'success' : 'warning' }}">
                {{ ($workout->status ?? '') === 'completed' ? 'Selesai' : 'Sedang Berjalan' }}
            </span>
        </div>
        @if(($workout->status ?? '') === 'in_progress')
            <div class="alert alert-warning mt-3 flex-between align-center" style="border: 1px solid var(--warning); background: var(--warning-light);">
                <div>
                    <strong style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 14px; height: 14px; color: #f59e0b;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <span>Sesi Latihan Sedang Berjalan!</span>
                    </strong>
                    <div class="text-sm">Latihan ini belum diselesaikan. Lanjutkan sekarang untuk mencatat progres lainnya.</div>
                </div>
                <a href="{{ route('workouts.resume', $workout) }}" class="btn btn-warning btn-small ml-2" style="font-weight: 700; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 11px; height: 11px;"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                    <span>Lanjutkan Latihan</span>
                </a>
            </div>
        @elseif(($workout->status ?? '') === 'completed' && isset($streakData) && $streakData['current_streak'] > 0)
            <div class="streak-celebration-box mt-3" style="background: rgba(249, 115, 22, 0.1); border: 1px solid rgba(249, 115, 22, 0.25); border-radius: var(--r-md); padding: 12px 16px; display: flex; align-items: center; gap: 14px;">
                <div class="streak-celebration-icon" style="display: flex; align-items: center;">
                    <x-flame-shape :tier="$streakData['flame_tier'] ?? 1" size="32" />
                </div>
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
            <div class="alert alert-success" style="display: flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; color: #10b981; flex-shrink: 0;"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H7c-.55 0-1 .45-1 1v1h12v-1c0-.55-.45-1-1-1h-2c-.55 0-1-.45-1-1v-2.34"/><path d="M18 4H6v7a6 6 0 0 0 12 0V4z"/></svg>
                <span><strong>Rekor Baru!</strong> {{ $pr['exercise'] }} {{ $pr['weight'] }}kg (Sebelumnya: {{ $pr['previous'] }}kg)</span>
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
