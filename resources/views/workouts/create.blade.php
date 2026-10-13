@extends('layouts.app')

@section('title', isset($activeWorkout) ? 'Latihan Sedang Berjalan' : 'Latihan Baru')

@section('content')
<div class="page-header flex-between align-center mb-4">
    <div>
        <h1 class="mb-1">{{ isset($activeWorkout) ? 'Latihan Sedang Berjalan' : 'Latihan Baru' }}</h1>
        <p class="text-muted text-sm mb-0">Catat gerakan, set, berat dan waktu istirahatmu. Progres tersimpan otomatis.</p>
    </div>
    <div class="d-flex align-center flex-gap-sm">
        <span id="autosave-status" class="badge badge-secondary" style="font-size: 0.75rem; font-weight: 600; padding: 6px 10px;">
            ☁️ Tersimpan otomatis
        </span>
        <button type="button" id="audio-toggle-btn" class="audio-status-pill" title="Aktifkan/matikan suara">
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

<!-- Banner Istirahat (tersembunyi secara default) -->
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
    <input type="hidden" name="duration_seconds" id="duration-seconds" value="{{ $activeWorkout->duration_seconds ?? ($activeWorkout && $activeWorkout->duration ? $activeWorkout->duration * 60 : 0) }}">

    <div class="card mb-4">
        <div class="card-header">
            <h3>Detail Latihan</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>Tipe Latihan</label>
                <div class="workout-type-selector">
                    <button type="button" class="btn btn-outline type-btn" data-type="Push">Push</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Pull">Pull</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Legs">Legs</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Full Body">Full Body</button>
                    <button type="button" class="btn btn-outline type-btn active" data-type="Custom">Kustom</button>
                </div>
                <input type="hidden" name="type" id="workout-type" value="Custom">
            </div>

            <div class="form-group">
                <label for="workout-name">Nama Latihan</label>
                <input type="text" name="name" id="workout-name" class="form-control" required placeholder="cth. Rutinitas Barbell & Dumbbell" value="{{ $activeWorkout->name ?? '' }}">
            </div>

            <div class="form-row flex-gap">
                <div class="form-group col flex-1">
                    <label for="date">Tanggal</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ isset($activeWorkout->date) ? \Carbon\Carbon::parse($activeWorkout->date)->format('Y-m-d') : ($selectedDate ?? date('Y-m-d')) }}" required>
                </div>
                <div class="form-group col flex-1">
                    <label for="duration">Durasi (menit)</label>
                    <input type="number" name="duration" id="duration" class="form-control" placeholder="otomatis atau isi manual" value="{{ $activeWorkout->duration ?? '' }}">
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Catatan</label>
                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Gimana rasanya? (opsional)">{{ $activeWorkout->notes ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header flex-between align-center">
            <h3>Gerakan</h3>
            <button type="button" class="btn btn-small btn-primary" id="add-exercise-btn">+ Tambah Gerakan</button>
        </div>
        <div class="card-body">
            <div id="exercises-container">
                @php
                    $exercisesList = (isset($activeWorkout) && $activeWorkout->workoutExercises->count() > 0) 
                        ? $activeWorkout->workoutExercises 
                        : null;
                @endphp

                @if($exercisesList)
                    @foreach($exercisesList as $exIdx => $we)
                        <div class="exercise-row card mb-4 p-3 bg-light" data-exercise-index="{{ $exIdx }}">
                            <div class="flex-between align-center mb-3">
                                <h4 class="m-0 exercise-title text-primary">Gerakan {{ $exIdx + 1 }}</h4>
                                <button type="button" class="btn btn-small btn-danger remove-exercise-btn">&times; Hapus</button>
                            </div>

                            <input type="hidden" name="exercises[{{ $exIdx }}][sets]" class="exercise-total-sets" value="{{ $we->sets ?? 1 }}">
                            <input type="hidden" name="exercises[{{ $exIdx }}][reps]" class="exercise-first-reps" value="{{ $we->reps ?? 10 }}">

                            <div class="form-group mb-3">
                                <label>Pilih Gerakan</label>
                                <select name="exercises[{{ $exIdx }}][exercise_id]" class="form-control exercise-select" required>
                                    <option value="">-- Pilih Gerakan --</option>
                                    <option value="custom" {{ ($we->exercise && $we->exercise->category === 'Custom') ? 'selected' : '' }} style="font-weight: bold; color: var(--primary);">➕ Lainnya / Gerakan Kustom...</option>
                                    @if(isset($exercises))
                                        @foreach($exercises as $category => $exGrp)
                                            <optgroup label="── {{ strtoupper($category) }} ──">
                                                @foreach($exGrp as $ex)
                                                    <option value="{{ $ex->id }}" {{ $we->exercise_id == $ex->id ? 'selected' : '' }}>{{ $ex->name }} ({{ $ex->muscle }})</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="custom-name-wrapper mt-2" style="{{ ($we->exercise && $we->exercise->category === 'Custom') ? 'display: block;' : 'display: none;' }}">
                                    <input type="text" name="exercises[{{ $exIdx }}][custom_name]" class="form-control custom-name-input" placeholder="Nama gerakan kustom (cth. Barbell Row)..." value="{{ ($we->exercise && $we->exercise->category === 'Custom') ? $we->exercise->name : '' }}">
                                </div>
                            </div>

                            <div class="sets-wrapper mb-3">
                                <div class="flex-between align-center mb-2">
                                    <span class="text-xs text-muted" style="font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">SET & PROGRES</span>
                                    <button type="button" class="btn btn-small btn-outline add-set-btn">+ Tambah Set</button>
                                </div>

                                <div class="sets-table-header d-flex align-center flex-gap-sm mb-1 px-2 text-xs text-muted">
                                    <span class="set-col-label">Set</span>
                                    <span class="flex-1">Berat (kg)</span>
                                    <span class="flex-1">Reps</span>
                                    <span class="set-col-timer text-center">Timer</span>
                                    <span class="set-col-action"></span>
                                </div>

                                <div class="sets-list-container">
                                    @php
                                        $setsData = (!empty($we->sets_data) && is_array($we->sets_data)) ? $we->sets_data : [];
                                        if (empty($setsData)) {
                                            for ($s = 1; $s <= ($we->sets ?? 3); $s++) {
                                                $setsData[] = ['set' => $s, 'weight' => $we->weight ?? 0, 'reps' => $we->reps ?? 10];
                                            }
                                        }
                                    @endphp
                                    @foreach($setsData as $sIdx => $sd)
                                        <div class="set-item-row d-flex align-center flex-gap-sm mb-2 p-2 rounded" data-set-index="{{ $sIdx }}">
                                            <span class="badge badge-secondary set-badge">Set {{ $sIdx + 1 }}</span>
                                            <div class="flex-1">
                                                <input type="number" step="0.5" name="exercises[{{ $exIdx }}][sets_list][{{ $sIdx }}][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required value="{{ $sd['weight'] ?? 0 }}">
                                            </div>
                                            <div class="flex-1">
                                                <input type="number" name="exercises[{{ $exIdx }}][sets_list][{{ $sIdx }}][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="{{ $sd['reps'] ?? 10 }}">
                                            </div>
                                            <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">
                                                ▶️ Mulai
                                            </button>
                                            <button type="button" class="btn btn-small btn-outline remove-set-btn">&times;</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <input type="text" name="exercises[{{ $exIdx }}][notes]" class="form-control" placeholder="Catatan cth. Set 3 terasa berat (opsional)" value="{{ $we->notes ?? '' }}">
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Gerakan awal jika belum ada sesi ongoing -->
                    <div class="exercise-row card mb-4 p-3 bg-light" data-exercise-index="0">
                        <div class="flex-between align-center mb-3">
                            <h4 class="m-0 exercise-title text-primary">Gerakan 1</h4>
                            <button type="button" class="btn btn-small btn-danger remove-exercise-btn">&times; Hapus</button>
                        </div>

                        <input type="hidden" name="exercises[0][sets]" class="exercise-total-sets" value="3">
                        <input type="hidden" name="exercises[0][reps]" class="exercise-first-reps" value="10">

                        <div class="form-group mb-3">
                            <label>Pilih Gerakan</label>
                            <select name="exercises[0][exercise_id]" class="form-control exercise-select" required>
                                <option value="">-- Pilih Gerakan --</option>
                                <option value="custom" style="font-weight: bold; color: var(--primary);">➕ Lainnya / Gerakan Kustom...</option>
                                @if(isset($exercises))
                                    @foreach($exercises as $category => $exList)
                                        <optgroup label="── {{ strtoupper($category) }} ──">
                                            @foreach($exList as $ex)
                                                <option value="{{ $ex->id }}">{{ $ex->name }} ({{ $ex->muscle }})</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                @endif
                            </select>
                            <div class="custom-name-wrapper mt-2" style="display: none;">
                                <input type="text" name="exercises[0][custom_name]" class="form-control custom-name-input" placeholder="Nama gerakan kustom (cth. Barbell Row, Hip Thrust)...">
                            </div>
                        </div>

                        <div class="sets-wrapper mb-3">
                            <div class="flex-between align-center mb-2">
                                <span class="text-xs text-muted" style="font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">SET & PROGRES</span>
                                <button type="button" class="btn btn-small btn-outline add-set-btn">+ Tambah Set</button>
                            </div>

                            <div class="sets-table-header d-flex align-center flex-gap-sm mb-1 px-2 text-xs text-muted">
                                <span class="set-col-label">Set</span>
                                <span class="flex-1">Berat (kg)</span>
                                <span class="flex-1">Reps</span>
                                <span class="set-col-timer text-center">Timer</span>
                                <span class="set-col-action"></span>
                            </div>

                            <div class="sets-list-container">
                                <!-- Set 1 -->
                                <div class="set-item-row d-flex align-center flex-gap-sm mb-2 p-2 rounded" data-set-index="0">
                                    <span class="badge badge-secondary set-badge">Set 1</span>
                                    <div class="flex-1">
                                        <input type="number" step="0.5" name="exercises[0][sets_list][0][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                                    </div>
                                    <div class="flex-1">
                                        <input type="number" name="exercises[0][sets_list][0][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                                    </div>
                                    <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">
                                        ▶️ Mulai
                                    </button>
                                    <button type="button" class="btn btn-small btn-outline remove-set-btn">&times;</button>
                                </div>

                                <!-- Set 2 -->
                                <div class="set-item-row d-flex align-center flex-gap-sm mb-2 p-2 rounded" data-set-index="1">
                                    <span class="badge badge-secondary set-badge">Set 2</span>
                                    <div class="flex-1">
                                        <input type="number" step="0.5" name="exercises[0][sets_list][1][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                                    </div>
                                    <div class="flex-1">
                                        <input type="number" name="exercises[0][sets_list][1][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                                    </div>
                                    <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">
                                        ▶️ Mulai
                                    </button>
                                    <button type="button" class="btn btn-small btn-outline remove-set-btn">&times;</button>
                                </div>

                                <!-- Set 3 -->
                                <div class="set-item-row d-flex align-center flex-gap-sm mb-2 p-2 rounded" data-set-index="2">
                                    <span class="badge badge-secondary set-badge">Set 3</span>
                                    <div class="flex-1">
                                        <input type="number" step="0.5" name="exercises[0][sets_list][2][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                                    </div>
                                    <div class="flex-1">
                                        <input type="number" name="exercises[0][sets_list][2][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                                    </div>
                                    <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">
                                        ▶️ Mulai
                                    </button>
                                    <button type="button" class="btn btn-small btn-outline remove-set-btn">&times;</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <input type="text" name="exercises[0][notes]" class="form-control" placeholder="Catatan cth. Set 3 terasa berat (opsional)">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-actions mb-5">
        <button type="submit" class="btn btn-success btn-block btn-large" style="font-size: 1.15rem; padding: 15px; font-weight: 700;">
            ✓ Selesai Latihan & Simpan
        </button>
        <button type="button" id="discard-workout-btn" class="btn btn-outline btn-block mt-3" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.4); font-size: 0.95rem; padding: 10px;">
            ✕ Batalkan Sesi Latihan Ini
        </button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 0. AUDIO SYNTHESIS ENGINE (Web Audio API) ---
        const GymAudio = {
            ctx: null,
            muted: false,
            init() {
                if (!this.ctx) {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext) this.ctx = new AudioContext();
                }
                if (this.ctx && this.ctx.state === 'suspended') {
                    this.ctx.resume();
                }
            },
            playTone(freq, type, duration, delay = 0, volume = 0.3) {
                if (this.muted) return;
                try {
                    this.init();
                    if (!this.ctx) return;
                    const now = this.ctx.currentTime;
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    
                    osc.type = type;
                    osc.frequency.setValueAtTime(freq, now + delay);
                    
                    gain.gain.setValueAtTime(volume, now + delay);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + delay + duration);
                    
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    
                    osc.start(now + delay);
                    osc.stop(now + delay + duration);
                } catch(e) { console.warn('Audio play error:', e); }
            },
            playStartSound() {
                this.playTone(587.33, 'sine', 0.12, 0, 0.35);     // D5
                this.playTone(880.00, 'triangle', 0.22, 0.11, 0.4); // A5
            },
            playCountdownTick() {
                this.playTone(880, 'sine', 0.08, 0, 0.25);
            },
            playRestCompleteSound() {
                this.playTone(523.25, 'sine', 0.15, 0, 0.35);       // C5
                this.playTone(659.25, 'sine', 0.15, 0.14, 0.35);    // E5
                this.playTone(1046.50, 'triangle', 0.5, 0.28, 0.45); // C6
            }
        };

        // Audio toggle button listener
        const audioBtn = document.getElementById('audio-toggle-btn');
        const audioIcon = document.getElementById('audio-icon');
        const audioLabel = document.getElementById('audio-label');
        if (audioBtn) {
            audioBtn.addEventListener('click', function() {
                GymAudio.muted = !GymAudio.muted;
                if (GymAudio.muted) {
                    audioIcon.textContent = '🔇';
                    audioLabel.textContent = 'Suara: OFF';
                    audioBtn.style.background = '#F1F5F9';
                    audioBtn.style.color = '#64748B';
                    audioBtn.style.borderColor = '#CBD5E1';
                } else {
                    GymAudio.init();
                    GymAudio.playStartSound();
                    audioIcon.textContent = '🔊';
                    audioLabel.textContent = 'Suara: ON';
                    audioBtn.style.background = '#EFF6FF';
                    audioBtn.style.color = '#1D4ED8';
                    audioBtn.style.borderColor = '#BFDBFE';
                }
            });
        }

        // --- 1. OVERALL WORKOUT STOPWATCH ---
        const stopwatchDisplay = document.getElementById('stopwatch-display');
        const durationInput = document.getElementById('duration');
        const durationSecondsInput = document.getElementById('duration-seconds');
        
        let totalSeconds = parseInt(durationSecondsInput ? durationSecondsInput.value : 0) || 0;

        function formatStopwatch(sec) {
            const mins = String(Math.floor(sec / 60)).padStart(2, '0');
            const secs = String(sec % 60).padStart(2, '0');
            return `${mins}:${secs}`;
        }

        stopwatchDisplay.textContent = formatStopwatch(totalSeconds);
        if (durationInput && !durationInput.value && totalSeconds > 0) {
            durationInput.value = Math.max(1, Math.round(totalSeconds / 60));
        }

        setInterval(function() {
            totalSeconds++;
            stopwatchDisplay.textContent = formatStopwatch(totalSeconds);
            if (durationSecondsInput) {
                durationSecondsInput.value = totalSeconds;
            }
            if (durationInput) {
                durationInput.value = Math.max(1, Math.round(totalSeconds / 60));
            }
        }, 1000);

        // --- 2. REST TIMER & SET STOPWATCH ---
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

        // --- 3. WORKOUT TYPE SELECTOR ---
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
                } else if(!nameInput.value) {
                    nameInput.focus();
                }
                scheduleAutoSave();
            });
        });

        // --- 4. DYNAMIC EXERCISE & MULTI-SET MANAGEMENT ---
        const container = document.getElementById('exercises-container');
        const addBtn = document.getElementById('add-exercise-btn');

        function setupExerciseEvents(row) {
            const select = row.querySelector('.exercise-select');
            const customWrapper = row.querySelector('.custom-name-wrapper');
            const customInput = row.querySelector('.custom-name-input');
            const setsContainer = row.querySelector('.sets-list-container');
            const addSetBtn = row.querySelector('.add-set-btn');

            if (select) {
                select.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        customWrapper.style.display = 'block';
                        if (customInput) customInput.required = true;
                    } else {
                        customWrapper.style.display = 'none';
                        if (customInput) customInput.required = false;
                    }
                    scheduleAutoSave();
                });
            }

            // Remove Exercise Row
            const removeBtn = row.querySelector('.remove-exercise-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    if (container.querySelectorAll('.exercise-row').length > 1) {
                        row.remove();
                        updateExerciseIndices();
                        scheduleAutoSave();
                    } else {
                        alert('Harus ada minimal satu gerakan.');
                    }
                });
            }

            function updateSetBadges() {
                const setRows = setsContainer.querySelectorAll('.set-item-row');
                const exIdx = Array.from(container.querySelectorAll('.exercise-row')).indexOf(row);
                
                const totalSetsInput = row.querySelector('.exercise-total-sets');
                const firstRepsInput = row.querySelector('.exercise-first-reps');
                if (totalSetsInput) {
                    totalSetsInput.value = setRows.length;
                    totalSetsInput.setAttribute('name', `exercises[${exIdx}][sets]`);
                }
                if (firstRepsInput) {
                    firstRepsInput.value = setRows[0]?.querySelector('.set-reps-input')?.value || 10;
                    firstRepsInput.setAttribute('name', `exercises[${exIdx}][reps]`);
                }

                setRows.forEach((sRow, sIdx) => {
                    sRow.querySelector('.set-badge').textContent = `Set ${sIdx + 1}`;
                    sRow.setAttribute('data-set-index', sIdx);
                    
                    const weightInput = sRow.querySelector('.set-weight-input');
                    const repsInput = sRow.querySelector('.set-reps-input');
                    
                    if (weightInput) weightInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][weight]`);
                    if (repsInput) repsInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][reps]`);
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

            // Auto-fill weights on set 1 change
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
                });
            }

            // Add Set Button
            if (addSetBtn) {
                addSetBtn.addEventListener('click', function() {
                    const setRows = setsContainer.querySelectorAll('.set-item-row');
                    const nextSetIdx = setRows.length;
                    const lastRow = setRows[setRows.length - 1];
                    const lastWeight = lastRow ? lastRow.querySelector('.set-weight-input').value : '';
                    const lastReps = lastRow ? lastRow.querySelector('.set-reps-input').value : '10';

                    const newSetRow = document.createElement('div');
                    newSetRow.className = 'set-item-row d-flex align-center flex-gap-sm mb-2 p-2 rounded';
                    newSetRow.setAttribute('data-set-index', nextSetIdx);
                    
                    const exIdx = Array.from(container.querySelectorAll('.exercise-row')).indexOf(row);

                    newSetRow.innerHTML = `
                        <span class="badge badge-secondary set-badge">Set ${nextSetIdx + 1}</span>
                        <div class="flex-1">
                            <input type="number" step="0.5" name="exercises[${exIdx}][sets_list][${nextSetIdx}][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required value="${lastWeight}">
                        </div>
                        <div class="flex-1">
                            <input type="number" name="exercises[${exIdx}][sets_list][${nextSetIdx}][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="${lastReps}">
                        </div>
                        <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">
                            ▶️ Mulai
                        </button>
                        <button type="button" class="btn btn-small btn-outline remove-set-btn">&times;</button>
                    `;

                    setupSetRowEvents(newSetRow);
                    setsContainer.appendChild(newSetRow);
                    updateSetBadges();
                    scheduleAutoSave();
                });
            }
        }

        function updateExerciseIndices() {
            const rows = container.querySelectorAll('.exercise-row');
            rows.forEach((row, exIdx) => {
                row.querySelector('.exercise-title').textContent = 'Gerakan ' + (exIdx + 1);
                row.setAttribute('data-exercise-index', exIdx);

                const select = row.querySelector('.exercise-select');
                if (select) select.setAttribute('name', `exercises[${exIdx}][exercise_id]`);
                const customInput = row.querySelector('.custom-name-input');
                if (customInput) customInput.setAttribute('name', `exercises[${exIdx}][custom_name]`);
                const notesInput = row.querySelector('input[name*="[notes]"]');
                if (notesInput) notesInput.setAttribute('name', `exercises[${exIdx}][notes]`);

                const totalSetsInput = row.querySelector('.exercise-total-sets');
                const firstRepsInput = row.querySelector('.exercise-first-reps');
                if (totalSetsInput) totalSetsInput.setAttribute('name', `exercises[${exIdx}][sets]`);
                if (firstRepsInput) firstRepsInput.setAttribute('name', `exercises[${exIdx}][reps]`);

                const setRows = row.querySelectorAll('.set-item-row');
                setRows.forEach((sRow, sIdx) => {
                    const weightInput = sRow.querySelector('.set-weight-input');
                    const repsInput = sRow.querySelector('.set-reps-input');
                    if (weightInput) weightInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][weight]`);
                    if (repsInput) repsInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][reps]`);
                });
            });
        }

        // Attach events to all existing rows
        container.querySelectorAll('.exercise-row').forEach(row => setupExerciseEvents(row));

        // Add Exercise Button
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const exIdx = container.querySelectorAll('.exercise-row').length;
                const templateRow = container.querySelector('.exercise-row');
                const newExRow = templateRow.cloneNode(true);

                newExRow.querySelector('.exercise-title').textContent = 'Gerakan ' + (exIdx + 1);
                newExRow.setAttribute('data-exercise-index', exIdx);

                const select = newExRow.querySelector('.exercise-select');
                if (select) select.selectedIndex = 0;

                const customWrapper = newExRow.querySelector('.custom-name-wrapper');
                if (customWrapper) customWrapper.style.display = 'none';

                const customInput = newExRow.querySelector('.custom-name-input');
                if (customInput) customInput.value = '';

                const notesInput = newExRow.querySelector('input[name*="[notes]"]');
                if (notesInput) notesInput.value = '';

                const setsContainer = newExRow.querySelector('.sets-list-container');
                setsContainer.innerHTML = `
                    <div class="set-item-row d-flex align-center flex-gap-sm mb-2 p-2 rounded" data-set-index="0">
                        <span class="badge badge-secondary set-badge">Set 1</span>
                        <div class="flex-1">
                            <input type="number" step="0.5" name="exercises[${exIdx}][sets_list][0][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                        </div>
                        <div class="flex-1">
                            <input type="number" name="exercises[${exIdx}][sets_list][0][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                        </div>
                        <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">
                            ▶️ Mulai
                        </button>
                        <button type="button" class="btn btn-small btn-outline remove-set-btn">&times;</button>
                    </div>
                    <div class="set-item-row d-flex align-center flex-gap-sm mb-2 p-2 rounded" data-set-index="1">
                        <span class="badge badge-secondary set-badge">Set 2</span>
                        <div class="flex-1">
                            <input type="number" step="0.5" name="exercises[${exIdx}][sets_list][1][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                        </div>
                        <div class="flex-1">
                            <input type="number" name="exercises[${exIdx}][sets_list][1][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                        </div>
                        <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">
                            ▶️ Mulai
                        </button>
                        <button type="button" class="btn btn-small btn-outline remove-set-btn">&times;</button>
                    </div>
                    <div class="set-item-row d-flex align-center flex-gap-sm mb-2 p-2 rounded" data-set-index="2">
                        <span class="badge badge-secondary set-badge">Set 3</span>
                        <div class="flex-1">
                            <input type="number" step="0.5" name="exercises[${exIdx}][sets_list][2][weight]" class="form-control form-control-sm set-weight-input" placeholder="kg" required>
                        </div>
                        <div class="flex-1">
                            <input type="number" name="exercises[${exIdx}][sets_list][2][reps]" class="form-control form-control-sm set-reps-input" placeholder="reps" required value="10">
                        </div>
                        <button type="button" class="btn btn-small btn-outline set-row-timer-btn" data-status="idle">
                            ▶️ Mulai
                        </button>
                        <button type="button" class="btn btn-small btn-outline remove-set-btn">&times;</button>
                    </div>
                `;

                setupExerciseEvents(newExRow);
                container.appendChild(newExRow);
                updateExerciseIndices();
                scheduleAutoSave();
            });
        }

        // --- 5. AUTOMATIC PERSISTENCE & AUTOSAVE ENGINE ---
        const autosaveStatusEl = document.getElementById('autosave-status');
        const workoutForm = document.getElementById('workout-form');
        const workoutIdInput = document.getElementById('workout-id');
        let autosaveTimeout = null;
        let isSaving = false;

        function serializeWorkout() {
            const exercises = [];
            container.querySelectorAll('.exercise-row').forEach((row, exIdx) => {
                const select = row.querySelector('.exercise-select');
                const customInput = row.querySelector('.custom-name-input');
                const notesInput = row.querySelector('input[name*="[notes]"]');
                const exerciseId = select ? select.value : '';
                const customName = customInput ? customInput.value : '';
                const notes = notesInput ? notesInput.value : '';

                const setsList = [];
                row.querySelectorAll('.sets-list-container .set-item-row').forEach((sRow, sIdx) => {
                    const weightInput = sRow.querySelector('.set-weight-input');
                    const repsInput = sRow.querySelector('.set-reps-input');
                    setsList.push({
                        weight: weightInput ? (parseFloat(weightInput.value) || 0) : 0,
                        reps: repsInput ? (parseInt(repsInput.value) || 0) : 0,
                    });
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
                duration: Math.max(1, Math.round(totalSeconds / 60)),
                notes: document.getElementById('notes').value,
                exercises: exercises,
            };
        }

        function saveLocalBackup() {
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
            saveLocalBackup();

            const data = serializeWorkout();
            // Don't autosave to server if workout has no name and no exercises chosen
            if (!data.name && data.exercises.length === 0) return;

            if (isSaving) return;
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
                    if (json.workout_id && workoutIdInput) {
                        workoutIdInput.value = json.workout_id;
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
            saveLocalBackup();
            clearTimeout(autosaveTimeout);
            autosaveTimeout = setTimeout(triggerAutoSave, 1200);
        }

        // Listen on inputs
        if (workoutForm) {
            workoutForm.addEventListener('input', scheduleAutoSave);
            workoutForm.addEventListener('change', scheduleAutoSave);

            workoutForm.addEventListener('submit', function() {
                if (durationSecondsInput) {
                    durationSecondsInput.value = totalSeconds;
                }
                localStorage.removeItem('gymtracker_ongoing_workout');
            });
        }

        // Periodic background autosave every 20s
        setInterval(function() {
            if (totalSeconds > 0) {
                triggerAutoSave();
            }
        }, 20000);

        // Discard / Cancel workout button
        const discardBtn = document.getElementById('discard-workout-btn');
        if (discardBtn) {
            discardBtn.addEventListener('click', function() {
                if (!confirm('Yakin ingin membatalkan sesi latihan ini? Semua progres sesi ini akan dihapus.')) {
                    return;
                }
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

        // Save local backup when navigating away or minimizing app
        window.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                saveLocalBackup();
                triggerAutoSave();
            }
        });
        window.addEventListener('beforeunload', function() {
            saveLocalBackup();
        });
    });
</script>
@endpush
@endsection
