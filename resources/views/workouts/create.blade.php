@extends('layouts.app')

@section('title', isset($activeWorkout) ? 'Latihan Sedang Berjalan' : 'Latihan Baru')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div>
        <h1 class="mb-1">{{ isset($activeWorkout) ? 'Latihan Sedang Berjalan' : 'Latihan Baru' }}</h1>
        <p class="text-muted text-sm mb-0">Catat gerakan beban atau kardio. Waktu dan progres tersimpan otomatis.</p>
    </div>
    <div class="d-flex align-center flex-gap-sm flex-wrap">
        <span id="live-calories-counter" class="badge badge-warning" style="font-size: 0.85rem; font-weight: 700; padding: 6px 12px; background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
            🔥 <span id="live-cal-val">{{ $activeWorkout->estimated_calories ?? 0 }}</span> kkal
        </span>
        <span id="autosave-status" class="badge badge-secondary" style="font-size: 0.75rem; font-weight: 600; padding: 6px 10px;">
            ☁️ Tersimpan otomatis
        </span>
        <button type="button" id="audio-toggle-btn" class="audio-status-pill" title="Aktifkan/matikan suara timer">
            <span id="audio-icon">🔊</span> <span id="audio-label">Suara: ON</span>
        </button>
        <div class="workout-stopwatch badge badge-primary" id="workout-stopwatch" style="font-size: 0.95rem; font-weight: 700; padding: 6px 14px; border-radius: 9999px;">
            ⏱️ <span id="stopwatch-display" class="tabular-nums">00:00</span>
        </div>
    </div>
</div>

@if(isset($activeWorkout) && $activeWorkout)
    <div class="alert alert-warning mb-4 flex-between align-center" style="border: 1.5px solid var(--warning); background: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(245, 158, 11, 0.04));">
        <div>
            <strong style="color: var(--text-primary);">⚡ Melanjutkan Sesi: {{ $activeWorkout->name }}</strong>
            <div class="text-sm text-secondary">Progres dan waktu latihanmu otomatis disimpan. Klik "Selesai Latihan & Simpan" setelah seluruh latihan selesai.</div>
        </div>
        <a href="{{ route('workouts.create', ['new' => 1]) }}" class="btn btn-small btn-outline ml-3" style="white-space: nowrap;" onclick="return confirm('Mulai sesi latihan baru dari awal?');">Mulai Baru</a>
    </div>
@endif

<!-- Banner Istirahat -->
<div id="rest-timer-banner" class="card mb-4 p-3" style="display: none; border: 1px solid var(--warning); background: var(--warning-light);">
    <div class="flex-between align-center mb-2">
        <span class="badge badge-warning" style="font-weight: 700; font-size: 0.75rem; letter-spacing: 0.04em;">⏳ WAKTU ISTIRAHAT</span>
        <div class="rest-preset-btns d-flex flex-gap-sm">
            <button type="button" class="btn btn-small btn-outline set-rest-time" data-seconds="30">30d</button>
            <button type="button" class="btn btn-small btn-outline set-rest-time" data-seconds="60">60d</button>
            <button type="button" class="btn btn-small btn-outline set-rest-time" data-seconds="90">90d</button>
            <button type="button" class="btn btn-small btn-outline" id="cancel-rest-btn" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.3);">Lewati</button>
        </div>
    </div>
    <div class="rest-countdown-display text-center my-2">
        <span id="rest-seconds" class="tabular-nums" style="font-size: 2.25rem; font-weight: 800; color: var(--warning); line-height: 1;">60</span>
        <span class="text-sm text-secondary" style="margin-left: 6px; font-weight: 500;">detik lagi</span>
    </div>
    <div class="progress-bar mt-2" style="height: 6px; border-radius: 9999px;">
        <div id="rest-progress-fill" class="progress-fill" style="width: 100%; background: var(--warning); border-radius: 9999px;"></div>
    </div>
</div>

<form action="{{ route('workouts.store') }}" method="POST" id="workout-form">
    @csrf
    <input type="hidden" name="workout_id" id="workout-id" value="{{ $activeWorkout->id ?? '' }}">
    <input type="hidden" name="duration" id="duration" value="{{ $activeWorkout->duration ?? 1 }}">
    <input type="hidden" name="duration_seconds" id="duration-seconds" value="{{ $activeWorkout->duration_seconds ?? ($activeWorkout && $activeWorkout->duration ? $activeWorkout->duration * 60 : 0) }}">

    <!-- Detail Sesi Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Detail Latihan</h3>
        </div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label>Tipe Latihan</label>
                <div class="workout-type-selector">
                    <button type="button" class="btn btn-outline type-btn" data-type="Push">Push</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Pull">Pull</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Legs">Legs</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Full Body">Full Body</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Cardio">Kardio</button>
                    <button type="button" class="btn btn-outline type-btn active" data-type="Custom">Kustom</button>
                </div>
                <input type="hidden" name="type" id="workout-type" value="Custom">
            </div>

            <div class="form-group mb-3">
                <label for="workout-name">Nama Latihan</label>
                <input type="text" name="name" id="workout-name" class="form-control" required placeholder="cth. Rutinitas Barbell & Kardio" value="{{ $activeWorkout->name ?? old('name', 'Latihan Kustom') }}">
            </div>

            <div class="form-row flex-gap mb-3">
                <div class="form-group col flex-1">
                    <div class="flex-between align-center mb-1">
                        <label for="date" class="m-0">Tanggal Latihan</label>
                        <button type="button" class="btn btn-small btn-outline py-0 px-2" id="set-today-date-btn" style="font-size: 0.75rem; border-radius: 9999px;" title="Ubah tanggal latihan ke hari ini">
                            Set Hari Ini
                        </button>
                    </div>
                    <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate ?? (isset($activeWorkout->date) ? \Carbon\Carbon::parse($activeWorkout->date)->format('Y-m-d') : date('Y-m-d')) }}" required>
                </div>

                <!-- Durasi Otomatis (Tanpa input manual angka yang membingungkan) -->
                <div class="form-group col flex-1">
                    <label class="mb-1 d-block">Durasi Latihan (Otomatis)</label>
                    <div class="auto-duration-display-box">
                        <span class="auto-duration-icon">⏱️</span>
                        <span id="auto-duration-live" class="tabular-nums fw-bold">00:00</span>
                        <span class="auto-duration-tag">Tercatat Otomatis</span>
                    </div>
                </div>
            </div>

            <div class="form-group mb-0">
                <label for="notes">Catatan Sesi (Opsional)</label>
                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Gimana rasanya latihan hari ini?">{{ $activeWorkout->notes ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <!-- Gerakan & Kardio Section (Hevy App Style - Clean Unified Cards) -->
    <div class="workout-exercises-section mb-4">
        <div class="flex-between align-center mb-3">
            <div>
                <h3 class="m-0">Daftar Gerakan & Kardio</h3>
                <span class="text-xs text-muted">Bisa mencatat angkat beban (kg & reps) maupun kardio (durasi menit & jarak km).</span>
            </div>
            <button type="button" class="btn btn-primary btn-small" id="add-exercise-btn">
                + Tambah Gerakan
            </button>
        </div>

        <div id="exercises-container">
            @php
                $exercisesList = (isset($activeWorkout) && $activeWorkout->workoutExercises->count() > 0) 
                    ? $activeWorkout->workoutExercises 
                    : null;
            @endphp

            @if($exercisesList)
                @foreach($exercisesList as $exIdx => $we)
                    @php
                        $isCardio = strtolower($we->exercise->category ?? '') === 'cardio';
                    @endphp
                    <div class="exercise-block mb-3 {{ $isCardio ? 'exercise-cardio-mode' : '' }}" data-exercise-index="{{ $exIdx }}" data-is-cardio="{{ $isCardio ? '1' : '0' }}">
                        <div class="exercise-block-header flex-between align-center">
                            <div class="d-flex align-center flex-gap-sm flex-1">
                                <span class="exercise-number-pill">#{{ $exIdx + 1 }}</span>
                                <div class="flex-1">
                                    <select name="exercises[{{ $exIdx }}][exercise_id]" class="form-control exercise-select" required>
                                        <option value="">-- Pilih Gerakan / Kardio --</option>
                                        <option value="custom" {{ ($we->exercise && $we->exercise->category === 'Custom') ? 'selected' : '' }} style="font-weight: bold; color: var(--primary);">➕ Lainnya / Gerakan Kustom...</option>
                                        @if(isset($exercises))
                                            @foreach($exercises as $category => $exGrp)
                                                <optgroup label="── {{ strtoupper($category) }} ──">
                                                    @foreach($exGrp as $ex)
                                                        <option value="{{ $ex->id }}" data-category="{{ strtolower($ex->category) }}" {{ $we->exercise_id == $ex->id ? 'selected' : '' }}>
                                                            {{ $ex->name }} ({{ $ex->muscle }})
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-center flex-gap-sm ml-2">
                                <span class="badge exercise-category-badge {{ $isCardio ? 'badge-warning' : 'badge-info' }}">
                                    {{ $isCardio ? 'KARDIO' : strtoupper($we->exercise->category ?? 'BEBAN') }}
                                </span>
                                <button type="button" class="btn-remove-exercise remove-exercise-btn" title="Hapus gerakan ini">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="custom-name-wrapper mt-2 px-3" style="{{ ($we->exercise && $we->exercise->category === 'Custom') ? 'display: block;' : 'display: none;' }}">
                            <input type="text" name="exercises[{{ $exIdx }}][custom_name]" class="form-control custom-name-input" placeholder="Nama gerakan kustom (cth. Barbell Row atau Treadmill)..." value="{{ ($we->exercise && $we->exercise->category === 'Custom') ? $we->exercise->name : '' }}">
                        </div>

                        <input type="hidden" name="exercises[{{ $exIdx }}][sets]" class="exercise-total-sets" value="{{ $we->sets ?? 1 }}">
                        <input type="hidden" name="exercises[{{ $exIdx }}][reps]" class="exercise-first-reps" value="{{ $we->reps ?? 10 }}">

                        <!-- Sets Table Container -->
                        <div class="exercise-sets-area px-3 pt-2 pb-3">
                            <div class="sets-table-header d-flex align-center flex-gap-sm mb-1 px-1 text-xs text-muted">
                                <span class="set-col-label">{{ $isCardio ? 'Sesi' : 'Set' }}</span>
                                <span class="flex-1 col-field-1-label">{{ $isCardio ? 'Durasi (menit)' : 'Berat (kg)' }}</span>
                                <span class="flex-1 col-field-2-label">{{ $isCardio ? 'Jarak km (opsional)' : 'Reps' }}</span>
                                <span class="set-col-timer text-center">Timer</span>
                                <span class="set-col-action"></span>
                            </div>

                            <div class="sets-list-container">
                                @php
                                    $setsData = (!empty($we->sets_data) && is_array($we->sets_data)) ? $we->sets_data : [];
                                    if (empty($setsData)) {
                                        for ($s = 1; $s <= ($we->sets ?? 3); $s++) {
                                            $setsData[] = ['set' => $s, 'weight' => $we->weight ?? 0, 'reps' => $we->reps ?? 10, 'duration' => $we->reps ?? 15, 'distance' => 0];
                                        }
                                    }
                                @endphp
                                @foreach($setsData as $sIdx => $sd)
                                    <div class="set-item-row d-flex align-center flex-gap-sm mb-2" data-set-index="{{ $sIdx }}">
                                        <span class="badge badge-secondary set-badge">{{ $isCardio ? 'Sesi ' . ($sIdx + 1) : 'Set ' . ($sIdx + 1) }}</span>
                                        <div class="flex-1 field-1-wrap">
                                            @if($isCardio)
                                                <input type="number" step="1" min="1" name="exercises[{{ $exIdx }}][sets_list][{{ $sIdx }}][duration]" class="form-control form-control-sm set-duration-input" placeholder="menit" required value="{{ $sd['duration'] ?? 15 }}">
                                            @else
                                                <input type="number" step="0.5" min="0" name="exercises[{{ $exIdx }}][sets_list][{{ $sIdx }}][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required value="{{ $sd['weight'] ?? 0 }}">
                                            @endif
                                        </div>
                                        <div class="flex-1 field-2-wrap">
                                            @if($isCardio)
                                                <input type="number" step="0.1" min="0" name="exercises[{{ $exIdx }}][sets_list][{{ $sIdx }}][distance]" class="form-control form-control-sm set-distance-input" placeholder="km" value="{{ $sd['distance'] ?? '' }}">
                                            @else
                                                <input type="number" step="1" min="0" name="exercises[{{ $exIdx }}][sets_list][{{ $sIdx }}][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="{{ $sd['reps'] ?? 10 }}">
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">
                                            ▶️ Mulai
                                        </button>
                                        <button type="button" class="btn btn-small btn-outline remove-set-btn" title="Hapus set">&times;</button>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex-between align-center mt-2 pt-2 border-top-subtle">
                                <button type="button" class="btn btn-small btn-outline add-set-btn">
                                    + Tambah {{ $isCardio ? 'Sesi' : 'Set' }}
                                </button>
                                <div class="flex-1 ml-3">
                                    <input type="text" name="exercises[{{ $exIdx }}][notes]" class="form-control form-control-sm" placeholder="Catatan gerakan (opsional, cth. beban dinaikkan)..." value="{{ $we->notes ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Initial Clean Exercise Block 1 -->
                <div class="exercise-block mb-3" data-exercise-index="0" data-is-cardio="0">
                    <div class="exercise-block-header flex-between align-center">
                        <div class="d-flex align-center flex-gap-sm flex-1">
                            <span class="exercise-number-pill">#1</span>
                            <div class="flex-1">
                                <select name="exercises[0][exercise_id]" class="form-control exercise-select" required>
                                    <option value="">-- Pilih Gerakan / Kardio --</option>
                                    <option value="custom" style="font-weight: bold; color: var(--primary);">➕ Lainnya / Gerakan Kustom...</option>
                                    @if(isset($exercises))
                                        @foreach($exercises as $category => $exGrp)
                                            <optgroup label="── {{ strtoupper($category) }} ──">
                                                @foreach($exGrp as $ex)
                                                    <option value="{{ $ex->id }}" data-category="{{ strtolower($ex->category) }}">
                                                        {{ $ex->name }} ({{ $ex->muscle }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="d-flex align-center flex-gap-sm ml-2">
                            <span class="badge exercise-category-badge badge-info">BEBAN</span>
                            <button type="button" class="btn-remove-exercise remove-exercise-btn" title="Hapus gerakan ini">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="custom-name-wrapper mt-2 px-3" style="display: none;">
                        <input type="text" name="exercises[0][custom_name]" class="form-control custom-name-input" placeholder="Nama gerakan kustom (cth. Barbell Row atau Treadmill)...">
                    </div>

                    <input type="hidden" name="exercises[0][sets]" class="exercise-total-sets" value="3">
                    <input type="hidden" name="exercises[0][reps]" class="exercise-first-reps" value="10">

                    <div class="exercise-sets-area px-3 pt-2 pb-3">
                        <div class="sets-table-header d-flex align-center flex-gap-sm mb-1 px-1 text-xs text-muted">
                            <span class="set-col-label">Set</span>
                            <span class="flex-1 col-field-1-label">Berat (kg)</span>
                            <span class="flex-1 col-field-2-label">Reps</span>
                            <span class="set-col-timer text-center">Timer</span>
                            <span class="set-col-action"></span>
                        </div>

                        <div class="sets-list-container">
                            <div class="set-item-row d-flex align-center flex-gap-sm mb-2" data-set-index="0">
                                <span class="badge badge-secondary set-badge">Set 1</span>
                                <div class="flex-1 field-1-wrap">
                                    <input type="number" step="0.5" min="0" name="exercises[0][sets_list][0][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                                </div>
                                <div class="flex-1 field-2-wrap">
                                    <input type="number" step="1" min="0" name="exercises[0][sets_list][0][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                                </div>
                                <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">▶️ Mulai</button>
                                <button type="button" class="btn btn-small btn-outline remove-set-btn" title="Hapus set">&times;</button>
                            </div>
                            <div class="set-item-row d-flex align-center flex-gap-sm mb-2" data-set-index="1">
                                <span class="badge badge-secondary set-badge">Set 2</span>
                                <div class="flex-1 field-1-wrap">
                                    <input type="number" step="0.5" min="0" name="exercises[0][sets_list][1][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                                </div>
                                <div class="flex-1 field-2-wrap">
                                    <input type="number" step="1" min="0" name="exercises[0][sets_list][1][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                                </div>
                                <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">▶️ Mulai</button>
                                <button type="button" class="btn btn-small btn-outline remove-set-btn" title="Hapus set">&times;</button>
                            </div>
                            <div class="set-item-row d-flex align-center flex-gap-sm mb-2" data-set-index="2">
                                <span class="badge badge-secondary set-badge">Set 3</span>
                                <div class="flex-1 field-1-wrap">
                                    <input type="number" step="0.5" min="0" name="exercises[0][sets_list][2][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                                </div>
                                <div class="flex-1 field-2-wrap">
                                    <input type="number" step="1" min="0" name="exercises[0][sets_list][2][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                                </div>
                                <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">▶️ Mulai</button>
                                <button type="button" class="btn btn-small btn-outline remove-set-btn" title="Hapus set">&times;</button>
                            </div>
                        </div>

                        <div class="flex-between align-center mt-2 pt-2 border-top-subtle">
                            <button type="button" class="btn btn-small btn-outline add-set-btn">+ Tambah Set</button>
                            <div class="flex-1 ml-3">
                                <input type="text" name="exercises[0][notes]" class="form-control form-control-sm" placeholder="Catatan gerakan (opsional)...">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <button type="button" class="btn-add-exercise-dashed w-100" id="add-exercise-bottom-btn">
            + Tambah Gerakan Baru / Kardio
        </button>
    </div>

    <!-- Action Bar -->
    <div class="card p-3 workout-bottom-actions">
        <div class="flex-between align-center flex-wrap flex-gap">
            <button type="button" id="discard-workout-btn" class="btn btn-outline" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.4);">
                Batalkan Sesi Latihan
            </button>
            <div class="d-flex align-center flex-gap-sm">
                <button type="submit" class="btn btn-primary" id="save-workout-btn" style="padding: 10px 24px; font-weight: 700; font-size: 1rem;">
                    ✓ Selesai Latihan & Simpan
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cardioIds = {!! json_encode($cardioExerciseIds ?? []) !!};

        // --- 1. WEB AUDIO API SYNTHESIZER ---
        const GymAudio = (function() {
            let ctx = null;
            let enabled = localStorage.getItem('gymtracker_sound_enabled') !== 'false';

            function getContext() {
                if (!ctx) {
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (AudioCtx) ctx = new AudioCtx();
                }
                if (ctx && ctx.state === 'suspended') {
                    ctx.resume();
                }
                return ctx;
            }

            function playTone(freq, duration, type = 'sine', gainVal = 0.25) {
                if (!enabled) return;
                try {
                    const c = getContext();
                    if (!c) return;
                    const osc = c.createOscillator();
                    const gain = c.createGain();
                    osc.type = type;
                    osc.frequency.setValueAtTime(freq, c.currentTime);
                    gain.gain.setValueAtTime(gainVal, c.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, c.currentTime + duration);
                    osc.connect(gain);
                    gain.connect(c.destination);
                    osc.start();
                    osc.stop(c.currentTime + duration);
                } catch (e) {
                    console.warn('Audio tone error:', e);
                }
            }

            return {
                isEnabled: () => enabled,
                toggle: function() {
                    enabled = !enabled;
                    localStorage.setItem('gymtracker_sound_enabled', enabled ? 'true' : 'false');
                    return enabled;
                },
                playStartSound: function() {
                    playTone(523.25, 0.15, 'sine', 0.2);
                    setTimeout(() => playTone(659.25, 0.2, 'sine', 0.25), 120);
                },
                playCountdownTick: function() {
                    playTone(880, 0.08, 'sine', 0.15);
                },
                playRestCompleteSound: function() {
                    playTone(587.33, 0.12, 'sine', 0.3);
                    setTimeout(() => playTone(783.99, 0.15, 'sine', 0.3), 120);
                    setTimeout(() => playTone(1046.50, 0.4, 'triangle', 0.35), 260);
                }
            };
        })();

        // Sound Toggle Pill
        const audioBtn = document.getElementById('audio-toggle-btn');
        const audioIcon = document.getElementById('audio-icon');
        const audioLabel = document.getElementById('audio-label');
        function updateAudioUI(isOn) {
            if (audioIcon) audioIcon.textContent = isOn ? '🔊' : '🔇';
            if (audioLabel) audioLabel.textContent = isOn ? 'Suara: ON' : 'Suara: OFF';
            if (audioBtn) audioBtn.classList.toggle('muted', !isOn);
        }
        updateAudioUI(GymAudio.isEnabled());
        if (audioBtn) {
            audioBtn.addEventListener('click', function() {
                const nowOn = GymAudio.toggle();
                updateAudioUI(nowOn);
                if (nowOn) GymAudio.playStartSound();
            });
        }

        // --- 2. LIVE CONTINUOUS STOPWATCH ENGINE ---
        const durationSecondsInput = document.getElementById('duration-seconds');
        const durationInput = document.getElementById('duration');
        const stopwatchDisplay = document.getElementById('stopwatch-display');
        const autoDurationLive = document.getElementById('auto-duration-live');

        let totalSeconds = parseInt(durationSecondsInput?.value) || 0;

        function formatStopwatch(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }

        stopwatchDisplay.textContent = formatStopwatch(totalSeconds);
        if (autoDurationLive) autoDurationLive.textContent = formatStopwatch(totalSeconds);

        setInterval(function() {
            totalSeconds++;
            const fmt = formatStopwatch(totalSeconds);
            stopwatchDisplay.textContent = fmt;
            if (autoDurationLive) autoDurationLive.textContent = fmt;
            if (durationSecondsInput) durationSecondsInput.value = totalSeconds;
            if (durationInput) durationInput.value = Math.max(1, Math.ceil(totalSeconds / 60));
            updateLiveCalories();
        }, 1000);

        // --- 3. LIVE CALORIES CALCULATION ---
        const liveCalVal = document.getElementById('live-cal-val');
        function updateLiveCalories() {
            if (!liveCalVal) return;
            const currentMins = Math.max(1, Math.ceil(totalSeconds / 60));
            let totalVolume = 0;
            let cardioMins = 0;
            let strengthSets = 0;

            container.querySelectorAll('.exercise-block').forEach(block => {
                const isCardio = block.getAttribute('data-is-cardio') === '1';
                block.querySelectorAll('.set-item-row').forEach(sRow => {
                    if (isCardio) {
                        const durInput = sRow.querySelector('.set-duration-input');
                        cardioMins += (parseFloat(durInput?.value) || 0);
                    } else {
                        const w = parseFloat(sRow.querySelector('.set-weight-input')?.value) || 0;
                        const r = parseInt(sRow.querySelector('.set-reps-input')?.value) || 0;
                        totalVolume += (w * r);
                        strengthSets++;
                    }
                });
            });

            const userWeight = 70; // standard approx weight
            const effectiveMins = Math.max(currentMins, cardioMins + (strengthSets * 2));
            const strengthMins = Math.max(0, effectiveMins - cardioMins);

            const strengthCal = (strengthMins * 5.5 * 3.5 * userWeight / 200) + (totalVolume * 0.015);
            const cardioCal = (cardioMins * 8.0 * 3.5 * userWeight / 200);

            let est = Math.round(strengthCal + cardioCal);
            if (est <= 0 && (strengthSets > 0 || cardioMins > 0)) {
                est = (strengthSets * 18) + Math.round(cardioMins * 8);
            }
            liveCalVal.textContent = Math.max(0, est);
        }

        // --- 4. REST TIMER & SET STOPWATCH ---
        let restInterval = null;
        let restTotal = 60;
        let restRemaining = 60;

        const restBanner = document.getElementById('rest-timer-banner');
        const restDisplay = document.getElementById('rest-seconds');
        const restProgress = document.getElementById('rest-progress-fill');
        const cancelRestBtn = document.getElementById('cancel-rest-btn');

        function startRestTimer(seconds = 60) {
            clearInterval(restInterval);
            restTotal = seconds;
            restRemaining = seconds;
            restBanner.style.display = 'block';
            restDisplay.textContent = restRemaining;
            restProgress.style.width = '100%';

            restBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            restInterval = setInterval(function() {
                restRemaining--;
                restDisplay.textContent = restRemaining;
                const pct = (restRemaining / restTotal) * 100;
                restProgress.style.width = `${pct}%`;

                if (restRemaining > 0 && restRemaining <= 3) {
                    GymAudio.playCountdownTick();
                }

                if (restRemaining <= 0) {
                    clearInterval(restInterval);
                    restDisplay.textContent = 'ISTIRAHAT SELESAI! 🔔';
                    GymAudio.playRestCompleteSound();
                    setTimeout(() => {
                        restBanner.style.display = 'none';
                    }, 3000);
                }
            }, 1000);
        }

        if (cancelRestBtn) {
            cancelRestBtn.addEventListener('click', function() {
                clearInterval(restInterval);
                restBanner.style.display = 'none';
            });
        }

        document.querySelectorAll('.set-rest-time').forEach(btn => {
            btn.addEventListener('click', function() {
                const secs = parseInt(this.dataset.seconds) || 60;
                startRestTimer(secs);
            });
        });

        // --- 5. WORKOUT TYPE SELECTOR ---
        const typeBtns = document.querySelectorAll('.type-btn');
        const typeInput = document.getElementById('workout-type');
        const nameInput = document.getElementById('workout-name');

        typeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                typeBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const type = this.dataset.type;
                typeInput.value = type;
                if(type !== 'Custom') {
                    nameInput.value = type + ' Workout';
                } else if(!nameInput.value || nameInput.value.includes('Workout')) {
                    nameInput.value = 'Latihan Kustom';
                }
                scheduleAutoSave();
            });
        });

        // --- 6. EXERCISE & CARDIO MULTI-SET MANAGEMENT ---
        const container = document.getElementById('exercises-container');
        const addBtn = document.getElementById('add-exercise-btn');
        const addBottomBtn = document.getElementById('add-exercise-bottom-btn');

        function setupExerciseEvents(block) {
            const select = block.querySelector('.exercise-select');
            const customWrapper = block.querySelector('.custom-name-wrapper');
            const customInput = block.querySelector('.custom-name-input');
            const setsContainer = block.querySelector('.sets-list-container');
            const addSetBtn = block.querySelector('.add-set-btn');
            const categoryBadge = block.querySelector('.exercise-category-badge');
            const headerCol1 = block.querySelector('.col-field-1-label');
            const headerCol2 = block.querySelector('.col-field-2-label');
            const headerSetCol = block.querySelector('.set-col-label');

            function applyCardioOrStrengthMode(isCardio) {
                block.setAttribute('data-is-cardio', isCardio ? '1' : '0');
                block.classList.toggle('exercise-cardio-mode', isCardio);

                if (categoryBadge) {
                    categoryBadge.textContent = isCardio ? 'KARDIO' : 'BEBAN';
                    categoryBadge.className = `badge exercise-category-badge ${isCardio ? 'badge-warning' : 'badge-info'}`;
                }

                if (headerSetCol) headerSetCol.textContent = isCardio ? 'Sesi' : 'Set';
                if (headerCol1) headerCol1.textContent = isCardio ? 'Durasi (menit)' : 'Berat (kg)';
                if (headerCol2) headerCol2.textContent = isCardio ? 'Jarak km (opsional)' : 'Reps';
                if (addSetBtn) addSetBtn.textContent = isCardio ? '+ Tambah Sesi Kardio' : '+ Tambah Set';

                // Re-render rows to switch inputs between kg/reps and menit/km
                const rows = setsContainer.querySelectorAll('.set-item-row');
                const exIdx = Array.from(container.querySelectorAll('.exercise-block')).indexOf(block);

                rows.forEach((sRow, sIdx) => {
                    const badge = sRow.querySelector('.set-badge');
                    if (badge) badge.textContent = isCardio ? `Sesi ${sIdx + 1}` : `Set ${sIdx + 1}`;

                    const f1Wrap = sRow.querySelector('.field-1-wrap');
                    const f2Wrap = sRow.querySelector('.field-2-wrap');

                    if (isCardio) {
                        if (!sRow.querySelector('.set-duration-input')) {
                            const oldReps = sRow.querySelector('.set-reps-input')?.value || 15;
                            f1Wrap.innerHTML = `<input type="number" step="1" min="1" name="exercises[${exIdx}][sets_list][${sIdx}][duration]" class="form-control form-control-sm set-duration-input" placeholder="menit" required value="${oldReps}">`;
                            f2Wrap.innerHTML = `<input type="number" step="0.1" min="0" name="exercises[${exIdx}][sets_list][${sIdx}][distance]" class="form-control form-control-sm set-distance-input" placeholder="km" value="">`;
                        }
                    } else {
                        if (!sRow.querySelector('.set-weight-input')) {
                            const oldDur = sRow.querySelector('.set-duration-input')?.value || 0;
                            f1Wrap.innerHTML = `<input type="number" step="0.5" min="0" name="exercises[${exIdx}][sets_list][${sIdx}][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required value="0">`;
                            f2Wrap.innerHTML = `<input type="number" step="1" min="0" name="exercises[${exIdx}][sets_list][${sIdx}][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">`;
                        }
                    }
                });

                updateLiveCalories();
            }

            if (select) {
                select.addEventListener('change', function() {
                    const val = this.value;
                    const selectedOpt = this.options[this.selectedIndex];
                    const cat = selectedOpt ? selectedOpt.getAttribute('data-category') : '';

                    const isCardio = cat === 'cardio' || cardioIds.includes(parseInt(val));

                    if (val === 'custom') {
                        if (customWrapper) customWrapper.style.display = 'block';
                        if (customInput) customInput.required = true;
                    } else {
                        if (customWrapper) customWrapper.style.display = 'none';
                        if (customInput) customInput.required = false;
                    }

                    applyCardioOrStrengthMode(isCardio);
                    scheduleAutoSave();
                });
            }

            // Remove Exercise Block
            const removeBtn = block.querySelector('.remove-exercise-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    if (container.querySelectorAll('.exercise-block').length > 1) {
                        block.remove();
                        updateExerciseIndices();
                        scheduleAutoSave();
                        updateLiveCalories();
                    } else {
                        alert('Harus ada minimal satu gerakan.');
                    }
                });
            }

            function updateSetBadges() {
                const setRows = setsContainer.querySelectorAll('.set-item-row');
                const exIdx = Array.from(container.querySelectorAll('.exercise-block')).indexOf(block);
                const isCardio = block.getAttribute('data-is-cardio') === '1';

                const totalSetsInput = block.querySelector('.exercise-total-sets');
                const firstRepsInput = block.querySelector('.exercise-first-reps');
                if (totalSetsInput) {
                    totalSetsInput.value = setRows.length;
                    totalSetsInput.setAttribute('name', `exercises[${exIdx}][sets]`);
                }
                if (firstRepsInput) {
                    const firstVal = isCardio
                        ? (setRows[0]?.querySelector('.set-duration-input')?.value || 15)
                        : (setRows[0]?.querySelector('.set-reps-input')?.value || 10);
                    firstRepsInput.value = firstVal;
                    firstRepsInput.setAttribute('name', `exercises[${exIdx}][reps]`);
                }

                setRows.forEach((sRow, sIdx) => {
                    const badge = sRow.querySelector('.set-badge');
                    if (badge) badge.textContent = isCardio ? `Sesi ${sIdx + 1}` : `Set ${sIdx + 1}`;
                    sRow.setAttribute('data-set-index', sIdx);

                    const weightInput = sRow.querySelector('.set-weight-input');
                    const repsInput = sRow.querySelector('.set-reps-input');
                    const durInput = sRow.querySelector('.set-duration-input');
                    const distInput = sRow.querySelector('.set-distance-input');

                    if (weightInput) weightInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][weight]`);
                    if (repsInput) repsInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][reps]`);
                    if (durInput) durInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][duration]`);
                    if (distInput) distInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][distance]`);
                });
            }

            function setupSetRowEvents(sRow) {
                const removeSetBtn = sRow.querySelector('.remove-set-btn');
                if (removeSetBtn) {
                    removeSetBtn.addEventListener('click', function() {
                        if (setsContainer.querySelectorAll('.set-item-row').length > 1) {
                            sRow.remove();
                            updateSetBadges();
                            scheduleAutoSave();
                            updateLiveCalories();
                        } else {
                            alert('Setiap gerakan harus memiliki minimal 1 set.');
                        }
                    });
                }

                const setTimerBtn = sRow.querySelector('.set-row-timer-btn');
                if (setTimerBtn) {
                    let setRowInterval = null;
                    let setRowSecs = 0;

                    setTimerBtn.addEventListener('click', function() {
                        const status = this.dataset.status || 'idle';
                        if (status === 'idle') {
                            GymAudio.playStartSound();
                            this.dataset.status = 'running';
                            this.classList.remove('btn-outline');
                            this.classList.add('btn-warning');
                            setRowSecs = 0;
                            this.textContent = '⏱️ 0s';

                            setRowInterval = setInterval(() => {
                                setRowSecs++;
                                this.textContent = `⏱️ ${setRowSecs}s`;
                            }, 1000);
                        } else if (status === 'running') {
                            clearInterval(setRowInterval);
                            this.dataset.status = 'done';
                            this.classList.remove('btn-warning');
                            this.classList.add('btn-success');
                            this.textContent = `✓ ${setRowSecs}s`;

                            startRestTimer(60);
                            scheduleAutoSave();
                        } else if (status === 'done') {
                            this.dataset.status = 'idle';
                            this.classList.remove('btn-success');
                            this.classList.add('btn-outline');
                            this.textContent = '▶️ Mulai';
                        }
                    });
                }
            }

            setsContainer.querySelectorAll('.set-item-row').forEach(sRow => setupSetRowEvents(sRow));

            // Auto-fill weights on set 1 change for strength
            const firstWeight = setsContainer.querySelector('.set-item-row:first-child .set-weight-input');
            if (firstWeight) {
                firstWeight.addEventListener('input', function() {
                    const val = this.value;
                    setsContainer.querySelectorAll('.set-item-row').forEach((sRow, i) => {
                        if (i > 0) {
                            const input = sRow.querySelector('.set-weight-input');
                            if (input && !input.value) {
                                input.value = val;
                            }
                        }
                    });
                    updateLiveCalories();
                });
            }

            // Add Set Button
            if (addSetBtn) {
                addSetBtn.addEventListener('click', function() {
                    const setRows = setsContainer.querySelectorAll('.set-item-row');
                    const nextSetIdx = setRows.length;
                    const exIdx = Array.from(container.querySelectorAll('.exercise-block')).indexOf(block);
                    const isCardio = block.getAttribute('data-is-cardio') === '1';

                    const lastRow = setRows[setRows.length - 1];
                    let field1Html = '';
                    let field2Html = '';

                    if (isCardio) {
                        const lastDur = lastRow?.querySelector('.set-duration-input')?.value || '15';
                        field1Html = `<input type="number" step="1" min="1" name="exercises[${exIdx}][sets_list][${nextSetIdx}][duration]" class="form-control form-control-sm set-duration-input" placeholder="menit" required value="${lastDur}">`;
                        field2Html = `<input type="number" step="0.1" min="0" name="exercises[${exIdx}][sets_list][${nextSetIdx}][distance]" class="form-control form-control-sm set-distance-input" placeholder="km" value="">`;
                    } else {
                        const lastWeight = lastRow?.querySelector('.set-weight-input')?.value || '';
                        const lastReps = lastRow?.querySelector('.set-reps-input')?.value || '10';
                        field1Html = `<input type="number" step="0.5" min="0" name="exercises[${exIdx}][sets_list][${nextSetIdx}][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required value="${lastWeight}">`;
                        field2Html = `<input type="number" step="1" min="0" name="exercises[${exIdx}][sets_list][${nextSetIdx}][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="${lastReps}">`;
                    }

                    const newSetRow = document.createElement('div');
                    newSetRow.className = 'set-item-row d-flex align-center flex-gap-sm mb-2';
                    newSetRow.setAttribute('data-set-index', nextSetIdx);

                    newSetRow.innerHTML = `
                        <span class="badge badge-secondary set-badge">${isCardio ? 'Sesi ' + (nextSetIdx + 1) : 'Set ' + (nextSetIdx + 1)}</span>
                        <div class="flex-1 field-1-wrap">${field1Html}</div>
                        <div class="flex-1 field-2-wrap">${field2Html}</div>
                        <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">▶️ Mulai</button>
                        <button type="button" class="btn btn-small btn-outline remove-set-btn" title="Hapus set">&times;</button>
                    `;

                    setupSetRowEvents(newSetRow);
                    setsContainer.appendChild(newSetRow);
                    updateSetBadges();
                    scheduleAutoSave();
                    updateLiveCalories();
                });
            }
        }

        function updateExerciseIndices() {
            const blocks = container.querySelectorAll('.exercise-block');
            blocks.forEach((block, exIdx) => {
                const numPill = block.querySelector('.exercise-number-pill');
                if (numPill) numPill.textContent = '#' + (exIdx + 1);
                block.setAttribute('data-exercise-index', exIdx);

                const select = block.querySelector('.exercise-select');
                if (select) select.setAttribute('name', `exercises[${exIdx}][exercise_id]`);
                const customInput = block.querySelector('.custom-name-input');
                if (customInput) customInput.setAttribute('name', `exercises[${exIdx}][custom_name]`);
                const notesInput = block.querySelector('input[name*="[notes]"]');
                if (notesInput) notesInput.setAttribute('name', `exercises[${exIdx}][notes]`);

                const totalSetsInput = block.querySelector('.exercise-total-sets');
                const firstRepsInput = block.querySelector('.exercise-first-reps');
                if (totalSetsInput) totalSetsInput.setAttribute('name', `exercises[${exIdx}][sets]`);
                if (firstRepsInput) firstRepsInput.setAttribute('name', `exercises[${exIdx}][reps]`);

                const setRows = block.querySelectorAll('.set-item-row');
                setRows.forEach((sRow, sIdx) => {
                    const weightInput = sRow.querySelector('.set-weight-input');
                    const repsInput = sRow.querySelector('.set-reps-input');
                    const durInput = sRow.querySelector('.set-duration-input');
                    const distInput = sRow.querySelector('.set-distance-input');

                    if (weightInput) weightInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][weight]`);
                    if (repsInput) repsInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][reps]`);
                    if (durInput) durInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][duration]`);
                    if (distInput) distInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][distance]`);
                });
            });
        }

        // Attach events to all existing blocks
        container.querySelectorAll('.exercise-block').forEach(block => setupExerciseEvents(block));

        // Add Exercise Button handler
        function createNewExerciseBlock() {
            const exIdx = container.querySelectorAll('.exercise-block').length;
            const templateBlock = container.querySelector('.exercise-block');
            if (!templateBlock) return;

            const newBlock = templateBlock.cloneNode(true);
            newBlock.setAttribute('data-exercise-index', exIdx);
            newBlock.setAttribute('data-is-cardio', '0');
            newBlock.classList.remove('exercise-cardio-mode');

            const numPill = newBlock.querySelector('.exercise-number-pill');
            if (numPill) numPill.textContent = '#' + (exIdx + 1);

            const select = newBlock.querySelector('.exercise-select');
            if (select) select.selectedIndex = 0;

            const catBadge = newBlock.querySelector('.exercise-category-badge');
            if (catBadge) {
                catBadge.textContent = 'BEBAN';
                catBadge.className = 'badge exercise-category-badge badge-info';
            }

            const customWrapper = newBlock.querySelector('.custom-name-wrapper');
            if (customWrapper) customWrapper.style.display = 'none';

            const customInput = newBlock.querySelector('.custom-name-input');
            if (customInput) customInput.value = '';

            const notesInput = newBlock.querySelector('input[name*="[notes]"]');
            if (notesInput) notesInput.value = '';

            const headerSetCol = newBlock.querySelector('.set-col-label');
            if (headerSetCol) headerSetCol.textContent = 'Set';
            const headerCol1 = newBlock.querySelector('.col-field-1-label');
            if (headerCol1) headerCol1.textContent = 'Berat (kg)';
            const headerCol2 = newBlock.querySelector('.col-field-2-label');
            if (headerCol2) headerCol2.textContent = 'Reps';
            const addSetBtn = newBlock.querySelector('.add-set-btn');
            if (addSetBtn) addSetBtn.textContent = '+ Tambah Set';

            const setsContainer = newBlock.querySelector('.sets-list-container');
            setsContainer.innerHTML = `
                <div class="set-item-row d-flex align-center flex-gap-sm mb-2" data-set-index="0">
                    <span class="badge badge-secondary set-badge">Set 1</span>
                    <div class="flex-1 field-1-wrap">
                        <input type="number" step="0.5" min="0" name="exercises[${exIdx}][sets_list][0][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                    </div>
                    <div class="flex-1 field-2-wrap">
                        <input type="number" step="1" min="0" name="exercises[${exIdx}][sets_list][0][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                    </div>
                    <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">▶️ Mulai</button>
                    <button type="button" class="btn btn-small btn-outline remove-set-btn" title="Hapus set">&times;</button>
                </div>
                <div class="set-item-row d-flex align-center flex-gap-sm mb-2" data-set-index="1">
                    <span class="badge badge-secondary set-badge">Set 2</span>
                    <div class="flex-1 field-1-wrap">
                        <input type="number" step="0.5" min="0" name="exercises[${exIdx}][sets_list][1][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                    </div>
                    <div class="flex-1 field-2-wrap">
                        <input type="number" step="1" min="0" name="exercises[${exIdx}][sets_list][1][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                    </div>
                    <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">▶️ Mulai</button>
                    <button type="button" class="btn btn-small btn-outline remove-set-btn" title="Hapus set">&times;</button>
                </div>
                <div class="set-item-row d-flex align-center flex-gap-sm mb-2" data-set-index="2">
                    <span class="badge badge-secondary set-badge">Set 3</span>
                    <div class="flex-1 field-1-wrap">
                        <input type="number" step="0.5" min="0" name="exercises[${exIdx}][sets_list][2][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                    </div>
                    <div class="flex-1 field-2-wrap">
                        <input type="number" step="1" min="0" name="exercises[${exIdx}][sets_list][2][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                    </div>
                    <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">▶️ Mulai</button>
                    <button type="button" class="btn btn-small btn-outline remove-set-btn" title="Hapus set">&times;</button>
                </div>
            `;

            setupExerciseEvents(newBlock);
            container.appendChild(newBlock);
            updateExerciseIndices();
            scheduleAutoSave();
            updateLiveCalories();
        }

        if (addBtn) addBtn.addEventListener('click', createNewExerciseBlock);
        if (addBottomBtn) addBottomBtn.addEventListener('click', createNewExerciseBlock);

        // --- 7. AUTOMATIC AUTOSAVE & PERSISTENCE ---
        const autosaveStatusEl = document.getElementById('autosave-status');
        const workoutForm = document.getElementById('workout-form');
        const workoutIdInput = document.getElementById('workout-id');
        let autosaveTimeout = null;
        let isSaving = false;
        let isSubmitting = false;

        const setTodayBtn = document.getElementById('set-today-date-btn');
        if (setTodayBtn) {
            setTodayBtn.addEventListener('click', function() {
                const today = new Date();
                const y = today.getFullYear();
                const m = String(today.getMonth() + 1).padStart(2, '0');
                const d = String(today.getDate()).padStart(2, '0');
                const dateInput = document.getElementById('date');
                if (dateInput) {
                    dateInput.value = `${y}-${m}-${d}`;
                    scheduleAutoSave();
                }
            });
        }

        function serializeWorkout() {
            const exercises = [];
            container.querySelectorAll('.exercise-block').forEach((block, exIdx) => {
                const select = block.querySelector('.exercise-select');
                const customInput = block.querySelector('.custom-name-input');
                const notesInput = block.querySelector('input[name*="[notes]"]');
                const exerciseId = select ? select.value : '';
                const customName = customInput ? customInput.value : '';
                const notes = notesInput ? notesInput.value : '';
                const isCardio = block.getAttribute('data-is-cardio') === '1';

                const setsList = [];
                block.querySelectorAll('.sets-list-container .set-item-row').forEach((sRow, sIdx) => {
                    if (isCardio) {
                        const durInput = sRow.querySelector('.set-duration-input');
                        const distInput = sRow.querySelector('.set-distance-input');
                        setsList.push({
                            duration: durInput ? (parseFloat(durInput.value) || 0) : 0,
                            distance: distInput ? (parseFloat(distInput.value) || 0) : 0,
                            weight: 0,
                            reps: durInput ? (parseInt(durInput.value) || 0) : 0,
                        });
                    } else {
                        const weightInput = sRow.querySelector('.set-weight-input');
                        const repsInput = sRow.querySelector('.set-reps-input');
                        setsList.push({
                            weight: weightInput ? (parseFloat(weightInput.value) || 0) : 0,
                            reps: repsInput ? (parseInt(repsInput.value) || 0) : 0,
                            duration: 0,
                            distance: 0,
                        });
                    }
                });

                if (exerciseId) {
                    exercises.push({
                        exercise_id: exerciseId,
                        custom_name: customName,
                        notes: notes,
                        sets_list: setsList,
                    });
                }
            });

            return {
                workout_id: workoutIdInput ? workoutIdInput.value : '',
                name: nameInput ? (nameInput.value || 'Latihan Baru') : 'Latihan Baru',
                date: document.getElementById('date').value,
                duration_seconds: totalSeconds,
                duration: Math.max(1, Math.ceil(totalSeconds / 60)),
                notes: document.getElementById('notes').value,
                exercises: exercises,
            };
        }

        function saveLocalBackup() {
            if (isSubmitting) return;
            try {
                const data = serializeWorkout();
                localStorage.setItem('gymtracker_ongoing_workout', JSON.stringify({
                    data: data,
                    timestamp: Date.now()
                }));
            } catch (e) {
                console.warn('LocalStorage backup error:', e);
            }
        }

        async function triggerAutoSave() {
            if (isSubmitting || isSaving) return;
            saveLocalBackup();

            const data = serializeWorkout();
            if (!data.name && data.exercises.length === 0) return;

            isSaving = true;

            if (autosaveStatusEl) {
                autosaveStatusEl.textContent = '🔄 Menyimpan...';
                autosaveStatusEl.className = 'badge badge-warning';
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('{{ route('workouts.autosave') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (res.ok) {
                    const json = await res.json();
                    if (json.workout_id && workoutIdInput && !isSubmitting) {
                        workoutIdInput.value = json.workout_id;
                    }
                    if (json.estimated_calories !== undefined && liveCalVal) {
                        liveCalVal.textContent = json.estimated_calories;
                    }
                    if (autosaveStatusEl) {
                        autosaveStatusEl.textContent = `☁️ Tersimpan (${json.saved_at || 'otomatis'})`;
                        autosaveStatusEl.className = 'badge badge-secondary';
                    }
                }
            } catch (err) {
                console.warn('AutoSave background sync:', err);
                if (autosaveStatusEl) {
                    autosaveStatusEl.textContent = '💾 Tersimpan lokal';
                    autosaveStatusEl.className = 'badge badge-secondary';
                }
            } finally {
                isSaving = false;
            }
        }

        function scheduleAutoSave() {
            if (isSubmitting) return;
            saveLocalBackup();
            clearTimeout(autosaveTimeout);
            autosaveTimeout = setTimeout(triggerAutoSave, 1200);
        }

        // Listen on inputs
        if (workoutForm) {
            workoutForm.addEventListener('input', function() {
                scheduleAutoSave();
                updateLiveCalories();
            });
            workoutForm.addEventListener('change', function() {
                scheduleAutoSave();
                updateLiveCalories();
            });

            workoutForm.addEventListener('submit', function() {
                isSubmitting = true;
                clearTimeout(autosaveTimeout);
                if (periodicAutosaveInterval) clearInterval(periodicAutosaveInterval);
                if (durationSecondsInput) {
                    durationSecondsInput.value = totalSeconds;
                }
                if (durationInput) {
                    durationInput.value = Math.max(1, Math.ceil(totalSeconds / 60));
                }
                localStorage.removeItem('gymtracker_ongoing_workout');
            });
        }

        const periodicAutosaveInterval = setInterval(function() {
            if (totalSeconds > 0 && !isSubmitting) {
                triggerAutoSave();
            }
        }, 20000);

        const discardBtn = document.getElementById('discard-workout-btn');
        if (discardBtn) {
            discardBtn.addEventListener('click', function() {
                if (!confirm('Yakin ingin membatalkan sesi latihan ini? Semua progres sesi ini akan dihapus.')) {
                    return;
                }
                isSubmitting = true;
                clearTimeout(autosaveTimeout);
                if (periodicAutosaveInterval) clearInterval(periodicAutosaveInterval);
                localStorage.removeItem('gymtracker_ongoing_workout');
                const wId = workoutIdInput ? workoutIdInput.value : '';
                if (wId) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/workouts/${wId}/cancel`;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    window.location.href = '{{ route('workouts.index') }}';
                }
            });
        }

        window.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden' && !isSubmitting) {
                saveLocalBackup();
                triggerAutoSave();
            }
        });
        window.addEventListener('beforeunload', function() {
            if (!isSubmitting) {
                saveLocalBackup();
            }
        });

        updateLiveCalories();
    });
</script>
@endpush
@endsection
