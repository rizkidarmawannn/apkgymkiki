@extends('layouts.app')

@section('title', 'New Workout')

@section('content')
<div class="page-header flex-between align-center mb-3">
    <h1>New Workout</h1>
    <!-- Live Workout Timer Bar -->
    <div class="workout-stopwatch badge badge-info p-2 text-md" id="workout-stopwatch" style="font-size:1.1rem; padding: 8px 14px;">
        ⏱️ <span id="stopwatch-display">00:00</span>
    </div>
</div>

<!-- Rest Timer Floating Alert Banner (hidden by default) -->
<div id="rest-timer-banner" class="card mb-4 p-3 bg-primary text-white" style="display: none; border-left: 6px solid var(--warning);">
    <div class="flex-between align-center mb-2">
        <strong style="font-size: 1.1rem;">⏳ REST TIMER</strong>
        <div class="rest-preset-btns btn-group">
            <button type="button" class="btn btn-small btn-outline text-white set-rest-time" data-seconds="30">30s</button>
            <button type="button" class="btn btn-small btn-outline text-white set-rest-time" data-seconds="60">60s</button>
            <button type="button" class="btn btn-small btn-outline text-white set-rest-time" data-seconds="90">90s</button>
            <button type="button" class="btn btn-small btn-danger btn-small" id="cancel-rest-btn">Skip</button>
        </div>
    </div>
    <div class="rest-countdown-display text-center my-2">
        <span id="rest-seconds" style="font-size: 2.5rem; font-weight: 700; color: #F59E0B;">60</span>
        <span style="font-size: 1.2rem;">seconds remaining</span>
    </div>
    <div class="progress-bar mt-2" style="background: rgba(255,255,255,0.2); height: 8px;">
        <div id="rest-progress-fill" class="progress-fill" style="width: 100%; background: #F59E0B;"></div>
    </div>
</div>

<form action="{{ route('workouts.store') }}" method="POST" id="workout-form">
    @csrf
    
    <div class="card mb-4">
        <div class="card-header">
            <h3>Workout Details</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>Workout Type</label>
                <div class="workout-type-selector">
                    <button type="button" class="btn btn-outline type-btn" data-type="Push">Push</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Pull">Pull</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Legs">Legs</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Full Body">Full Body</button>
                    <button type="button" class="btn btn-outline type-btn" data-type="Custom">Custom</button>
                </div>
                <input type="hidden" name="type" id="workout-type" value="Custom">
            </div>

            <div class="form-group">
                <label for="name">Workout Name</label>
                <input type="text" name="name" id="workout-name" class="form-control" required placeholder="e.g. Barbell & Dumbbell Routine">
            </div>

            <div class="form-row flex-gap">
                <div class="form-group col flex-1">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group col flex-1">
                    <label for="duration">Duration (mins)</label>
                    <input type="number" name="duration" id="duration" class="form-control" placeholder="auto-calculated or 45">
                </div>
            </div>
            
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="How did it feel? (optional)"></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header flex-between align-center">
            <h3>Exercises</h3>
            <button type="button" class="btn btn-small btn-primary" id="add-exercise-btn">+ Add Exercise</button>
        </div>
        <div class="card-body">
            <div id="exercises-container">
                <!-- Initial exercise row -->
                <div class="exercise-row card mb-4 p-3 bg-light" data-exercise-index="0">
                    <div class="flex-between align-center mb-3">
                        <h4 class="m-0 exercise-title text-primary">Exercise 1</h4>
                        <div class="d-flex align-center flex-gap">
                            <!-- Live Set & Rest Timer Button -->
                            <button type="button" class="btn btn-small btn-outline set-timer-btn" data-status="idle">
                                ▶️ Start Set
                            </button>
                            <button type="button" class="btn btn-small btn-danger remove-exercise-btn">&times;</button>
                        </div>
                    </div>

                    <!-- Exercise Selection Dropdown & Custom Input -->
                    <div class="form-group mb-3">
                        <label>Select Exercise</label>
                        <select name="exercises[0][exercise_id]" class="form-control exercise-select" required>
                            <option value="">-- Choose Exercise --</option>
                            <option value="custom" style="font-weight: bold; color: var(--primary);">➕ Other / Custom Exercise...</option>
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
                        <!-- Custom Exercise Name Input (Hidden by default unless custom chosen) -->
                        <div class="custom-name-wrapper mt-2" style="display: none;">
                            <input type="text" name="exercises[0][custom_name]" class="form-control custom-name-input" placeholder="Enter custom exercise name (e.g. Barbell Row, Hip Thrust)...">
                        </div>
                    </div>

                    <!-- Multi-Set Table/List -->
                    <div class="sets-wrapper card p-3 mb-3 bg-white">
                        <div class="flex-between align-center mb-2">
                            <strong class="text-sm text-muted">SETS & REPS DETAILS (Naik / Turun per Set)</strong>
                            <button type="button" class="btn btn-small btn-outline add-set-btn">+ Add Set</button>
                        </div>

                        <div class="sets-list-container">
                            <!-- Set 1 -->
                            <div class="set-item-row d-flex align-center flex-gap mb-2 p-2 bg-light rounded" data-set-index="0">
                                <span class="badge badge-secondary set-badge" style="min-width: 50px;">Set 1</span>
                                <div class="flex-1">
                                    <input type="number" step="0.5" name="exercises[0][sets_list][0][weight]" class="form-control form-control-sm set-weight-input" placeholder="Weight (kg)" required>
                                </div>
                                <div class="flex-1">
                                    <input type="number" name="exercises[0][sets_list][0][reps]" class="form-control form-control-sm set-reps-input" placeholder="Reps" required value="10">
                                </div>
                                <button type="button" class="btn btn-small btn-outline remove-set-btn" style="padding: 2px 8px;">&times;</button>
                            </div>

                            <!-- Set 2 -->
                            <div class="set-item-row d-flex align-center flex-gap mb-2 p-2 bg-light rounded" data-set-index="1">
                                <span class="badge badge-secondary set-badge" style="min-width: 50px;">Set 2</span>
                                <div class="flex-1">
                                    <input type="number" step="0.5" name="exercises[0][sets_list][1][weight]" class="form-control form-control-sm set-weight-input" placeholder="Weight (kg)" required>
                                </div>
                                <div class="flex-1">
                                    <input type="number" name="exercises[0][sets_list][1][reps]" class="form-control form-control-sm set-reps-input" placeholder="Reps" required value="10">
                                </div>
                                <button type="button" class="btn btn-small btn-outline remove-set-btn" style="padding: 2px 8px;">&times;</button>
                            </div>

                            <!-- Set 3 -->
                            <div class="set-item-row d-flex align-center flex-gap mb-2 p-2 bg-light rounded" data-set-index="2">
                                <span class="badge badge-secondary set-badge" style="min-width: 50px;">Set 3</span>
                                <div class="flex-1">
                                    <input type="number" step="0.5" name="exercises[0][sets_list][2][weight]" class="form-control form-control-sm set-weight-input" placeholder="Weight (kg)" required>
                                </div>
                                <div class="flex-1">
                                    <input type="number" name="exercises[0][sets_list][2][reps]" class="form-control form-control-sm set-reps-input" placeholder="Reps" required value="10">
                                </div>
                                <button type="button" class="btn btn-small btn-outline remove-set-btn" style="padding: 2px 8px;">&times;</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <input type="text" name="exercises[0][notes]" class="form-control" placeholder="Notes e.g. Felt heavy on set 3 (optional)">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions mb-5">
        <button type="submit" class="btn btn-success btn-block btn-large" style="font-size: 1.1rem; padding: 14px;">✓ Finish Workout</button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. OVERALL WORKOUT STOPWATCH ---
        let totalSeconds = 0;
        const stopwatchDisplay = document.getElementById('stopwatch-display');
        const durationInput = document.getElementById('duration');

        setInterval(function() {
            totalSeconds++;
            const mins = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
            const secs = String(totalSeconds % 60).padStart(2, '0');
            stopwatchDisplay.textContent = `${mins}:${secs}`;
            if (durationInput && !durationInput.value) {
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

                if (restRemaining <= 0) {
                    clearInterval(restInterval);
                    restDisplay.textContent = 'REST DONE! 🔔';
                    playBeep();
                    setTimeout(() => {
                        restBanner.style.display = 'none';
                    }, 2500);
                }
            }, 1000);
        }

        function playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                osc.type = 'sine';
                osc.frequency.value = 880;
                osc.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            } catch(e) {}
        }

        cancelRestBtn.addEventListener('click', function() {
            clearInterval(restInterval);
            restBanner.style.display = 'none';
        });

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
                } else {
                    nameInput.value = '';
                    nameInput.focus();
                }
            });
        });

        // --- 4. DYNAMIC EXERCISE & MULTI-SET MANAGEMENT ---
        const container = document.getElementById('exercises-container');
        const addBtn = document.getElementById('add-exercise-btn');

        function setupExerciseEvents(row) {
            const select = row.querySelector('.exercise-select');
            const customWrapper = row.querySelector('.custom-name-wrapper');
            const customInput = row.querySelector('.custom-name-input');
            const timerBtn = row.querySelector('.set-timer-btn');
            const setsContainer = row.querySelector('.sets-list-container');
            const addSetBtn = row.querySelector('.add-set-btn');

            // Toggle custom input display
            select.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customWrapper.style.display = 'block';
                    if (customInput) customInput.required = true;
                } else {
                    customWrapper.style.display = 'none';
                    if (customInput) customInput.required = false;
                }
            });

            // Set Timer & Rest Trigger
            let setTimer = null;
            let setSecs = 0;

            if (timerBtn) {
                timerBtn.addEventListener('click', function() {
                    const status = this.dataset.status;
                    if (status === 'idle') {
                        this.dataset.status = 'running';
                        this.className = 'btn btn-small btn-warning set-timer-btn';
                        setSecs = 0;
                        const btnText = this;
                        setTimer = setInterval(() => {
                            setSecs++;
                            btnText.textContent = `⏱️ ${setSecs}s (Stop & Rest)`;
                        }, 1000);
                    } else if (status === 'running') {
                        clearInterval(setTimer);
                        this.dataset.status = 'idle';
                        this.className = 'btn btn-small btn-outline set-timer-btn';
                        this.textContent = `▶️ Start Set (${setSecs}s recorded)`;
                        startRestTimer(60);
                    }
                });
            }

            // Remove Exercise Row
            const removeBtn = row.querySelector('.remove-exercise-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    if (container.querySelectorAll('.exercise-row').length > 1) {
                        row.remove();
                        updateExerciseIndices();
                    } else {
                        alert('You must have at least one exercise.');
                    }
                });
            }

            // Multi-Set Handler
            function updateSetBadges() {
                const setRows = setsContainer.querySelectorAll('.set-item-row');
                const exIdx = Array.from(container.querySelectorAll('.exercise-row')).indexOf(row);
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
                        } else {
                            alert('Each exercise must have at least one set.');
                        }
                    });
                }
            }

            // Attach to initial set rows
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
                    newSetRow.className = 'set-item-row d-flex align-center flex-gap mb-2 p-2 bg-light rounded';
                    newSetRow.setAttribute('data-set-index', nextSetIdx);
                    
                    const exIdx = Array.from(container.querySelectorAll('.exercise-row')).indexOf(row);

                    newSetRow.innerHTML = `
                        <span class="badge badge-secondary set-badge" style="min-width: 50px;">Set ${nextSetIdx + 1}</span>
                        <div class="flex-1">
                            <input type="number" step="0.5" name="exercises[${exIdx}][sets_list][${nextSetIdx}][weight]" class="form-control form-control-sm set-weight-input" placeholder="Weight (kg)" required value="${lastWeight}">
                        </div>
                        <div class="flex-1">
                            <input type="number" name="exercises[${exIdx}][sets_list][${nextSetIdx}][reps]" class="form-control form-control-sm set-reps-input" placeholder="Reps" required value="${lastReps}">
                        </div>
                        <button type="button" class="btn btn-small btn-outline remove-set-btn" style="padding: 2px 8px;">&times;</button>
                    `;

                    setupSetRowEvents(newSetRow);
                    setsContainer.appendChild(newSetRow);
                });
            }
        }

        function updateExerciseIndices() {
            const rows = container.querySelectorAll('.exercise-row');
            rows.forEach((row, exIdx) => {
                row.querySelector('.exercise-title').textContent = 'Exercise ' + (exIdx + 1);
                row.setAttribute('data-exercise-index', exIdx);

                // Update select & custom input names
                const select = row.querySelector('.exercise-select');
                if (select) select.setAttribute('name', `exercises[${exIdx}][exercise_id]`);
                const customInput = row.querySelector('.custom-name-input');
                if (customInput) customInput.setAttribute('name', `exercises[${exIdx}][custom_name]`);
                const notesInput = row.querySelector('input[name*="[notes]"]');
                if (notesInput) notesInput.setAttribute('name', `exercises[${exIdx}][notes]`);

                // Update set inputs
                const setRows = row.querySelectorAll('.set-item-row');
                setRows.forEach((sRow, sIdx) => {
                    const weightInput = sRow.querySelector('.set-weight-input');
                    const repsInput = sRow.querySelector('.set-reps-input');
                    if (weightInput) weightInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][weight]`);
                    if (repsInput) repsInput.setAttribute('name', `exercises[${exIdx}][sets_list][${sIdx}][reps]`);
                });
            });
        }

        // Setup first exercise
        setupExerciseEvents(container.firstElementChild);

        // Add Exercise Button
        addBtn.addEventListener('click', function() {
            const exIdx = container.querySelectorAll('.exercise-row').length;
            const newExRow = container.firstElementChild.cloneNode(true);

            newExRow.querySelector('.exercise-title').textContent = 'Exercise ' + (exIdx + 1);
            newExRow.setAttribute('data-exercise-index', exIdx);

            // Reset inputs
            const select = newExRow.querySelector('.exercise-select');
            if (select) select.selectedIndex = 0;

            const customWrapper = newExRow.querySelector('.custom-name-wrapper');
            if (customWrapper) customWrapper.style.display = 'none';

            const customInput = newExRow.querySelector('.custom-name-input');
            if (customInput) customInput.value = '';

            const notesInput = newExRow.querySelector('input[name*="[notes]"]');
            if (notesInput) notesInput.value = '';

            const timerBtn = newExRow.querySelector('.set-timer-btn');
            if (timerBtn) {
                timerBtn.dataset.status = 'idle';
                timerBtn.className = 'btn btn-small btn-outline set-timer-btn';
                timerBtn.textContent = '▶️ Start Set';
            }

            // Reset sets to 3 blank rows
            const setsContainer = newExRow.querySelector('.sets-list-container');
            setsContainer.innerHTML = `
                <div class="set-item-row d-flex align-center flex-gap mb-2 p-2 bg-light rounded" data-set-index="0">
                    <span class="badge badge-secondary set-badge" style="min-width: 50px;">Set 1</span>
                    <div class="flex-1">
                        <input type="number" step="0.5" name="exercises[${exIdx}][sets_list][0][weight]" class="form-control form-control-sm set-weight-input" placeholder="Weight (kg)" required>
                    </div>
                    <div class="flex-1">
                        <input type="number" name="exercises[${exIdx}][sets_list][0][reps]" class="form-control form-control-sm set-reps-input" placeholder="Reps" required value="10">
                    </div>
                    <button type="button" class="btn btn-small btn-outline remove-set-btn" style="padding: 2px 8px;">&times;</button>
                </div>
                <div class="set-item-row d-flex align-center flex-gap mb-2 p-2 bg-light rounded" data-set-index="1">
                    <span class="badge badge-secondary set-badge" style="min-width: 50px;">Set 2</span>
                    <div class="flex-1">
                        <input type="number" step="0.5" name="exercises[${exIdx}][sets_list][1][weight]" class="form-control form-control-sm set-weight-input" placeholder="Weight (kg)" required>
                    </div>
                    <div class="flex-1">
                        <input type="number" name="exercises[${exIdx}][sets_list][1][reps]" class="form-control form-control-sm set-reps-input" placeholder="Reps" required value="10">
                    </div>
                    <button type="button" class="btn btn-small btn-outline remove-set-btn" style="padding: 2px 8px;">&times;</button>
                </div>
                <div class="set-item-row d-flex align-center flex-gap mb-2 p-2 bg-light rounded" data-set-index="2">
                    <span class="badge badge-secondary set-badge" style="min-width: 50px;">Set 3</span>
                    <div class="flex-1">
                        <input type="number" step="0.5" name="exercises[${exIdx}][sets_list][2][weight]" class="form-control form-control-sm set-weight-input" placeholder="Weight (kg)" required>
                    </div>
                    <div class="flex-1">
                        <input type="number" name="exercises[${exIdx}][sets_list][2][reps]" class="form-control form-control-sm set-reps-input" placeholder="Reps" required value="10">
                    </div>
                    <button type="button" class="btn btn-small btn-outline remove-set-btn" style="padding: 2px 8px;">&times;</button>
                </div>
            `;

            setupExerciseEvents(newExRow);
            container.appendChild(newExRow);
            updateExerciseIndices();
        });
    });
</script>
@endpush
@endsection
