<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    public function index()
    {
        $ongoingWorkout = Workout::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->with('workoutExercises.exercise')
            ->latest('id')
            ->first();

        $workouts = Workout::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with('workoutExercises')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $currentMonth = Carbon::today()->format('Y-m');
        $startOfMonth = Carbon::today()->startOfMonth();
        $endOfMonth = Carbon::today()->endOfMonth();

        $monthWorkouts = Workout::where('user_id', Auth::id())
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->with('workoutExercises.exercise')
            ->orderBy('date')
            ->get();

        $calendarData = $this->formatCalendarData($monthWorkouts, $currentMonth);

        return view('workouts.index', compact('workouts', 'ongoingWorkout', 'calendarData'));
    }

    public function calendarData(Request $request)
    {
        $month = $request->input('month', Carbon::today()->format('Y-m'));
        try {
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Exception $e) {
            $startDate = Carbon::today()->startOfMonth();
            $month = $startDate->format('Y-m');
        }
        $endDate = (clone $startDate)->endOfMonth();

        $workouts = Workout::where('user_id', Auth::id())
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with('workoutExercises.exercise')
            ->orderBy('date')
            ->get();

        return response()->json($this->formatCalendarData($workouts, $month));
    }

    private function formatCalendarData($workouts, string $month): array
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $grouped = [];

        foreach ($workouts as $w) {
            $dateKey = Carbon::parse($w->date)->format('Y-m-d');
            if (!isset($grouped[$dateKey])) {
                $grouped[$dateKey] = [];
            }

            $grouped[$dateKey][] = [
                'id' => $w->id,
                'name' => $w->name,
                'status' => $w->status,
                'duration' => $w->duration,
                'date_formatted' => Carbon::parse($w->date)->locale('id')->translatedFormat('l, d F Y'),
                'exercise_count' => $w->workoutExercises->count(),
                'exercises' => $w->workoutExercises->map(function ($we) {
                    return [
                        'name' => $we->exercise->name ?? 'Gerakan',
                        'sets' => $we->sets,
                        'weight' => (float)$we->weight,
                        'reps' => $we->reps,
                        'sets_data' => $we->sets_data,
                    ];
                }),
                'url' => route('workouts.show', $w),
                'resume_url' => route('workouts.resume', $w),
            ];
        }

        return [
            'month' => $month,
            'month_label' => $startDate->locale('id')->translatedFormat('F Y'),
            'total_completed' => $workouts->where('status', 'completed')->count(),
            'workouts_by_date' => $grouped,
        ];
    }

    public function create(Request $request)
    {
        $activeWorkout = null;
        if (!$request->has('new')) {
            $activeWorkout = Workout::where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->with('workoutExercises.exercise')
                ->latest('id')
                ->first();
        }

        $selectedDate = $request->input('date');
        $exercises = Exercise::orderBy('category')->orderBy('name')->get()->groupBy('category');
        return view('workouts.create', compact('exercises', 'activeWorkout', 'selectedDate'));
    }

    public function resume(Workout $workout)
    {
        $this->authorizeUser($workout);

        if ($workout->status === 'completed') {
            return redirect()->route('workouts.show', $workout);
        }

        $activeWorkout = $workout->load('workoutExercises.exercise');
        $exercises = Exercise::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('workouts.create', compact('exercises', 'activeWorkout'));
    }

    public function autosave(Request $request)
    {
        $request->validate([
            'workout_id' => 'nullable|integer',
            'name' => 'nullable|string|max:255',
            'exercises' => 'nullable|array',
            'duration_seconds' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $workoutId = $request->workout_id;
        $workout = null;

        if ($workoutId) {
            $workout = Workout::where('user_id', Auth::id())
                ->where('id', $workoutId)
                ->where('status', 'in_progress')
                ->first();
        }

        if (!$workout) {
            $workout = Workout::where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->latest('id')
                ->first();
        }

        $durationSecs = (int)($request->duration_seconds ?? 0);
        $durationMins = $durationSecs > 0 ? (int)ceil($durationSecs / 60) : ($request->duration ? (int)$request->duration : 0);

        $name = trim($request->name ?? '');
        if (empty($name)) {
            $name = 'Latihan ' . Carbon::today()->translatedFormat('d M Y');
        }

        if (!$workout) {
            $workout = Workout::create([
                'user_id' => Auth::id(),
                'name' => $name,
                'date' => $request->date ?? Carbon::today(),
                'duration' => $durationMins,
                'duration_seconds' => $durationSecs,
                'notes' => $request->notes,
                'status' => 'in_progress',
            ]);
        } else {
            $workout->update([
                'name' => $request->name ? $request->name : $workout->name,
                'date' => $request->date ?? $workout->date,
                'duration' => $durationMins > 0 ? $durationMins : $workout->duration,
                'duration_seconds' => $durationSecs > 0 ? $durationSecs : $workout->duration_seconds,
                'notes' => $request->notes ?? $workout->notes,
            ]);
        }

        // Sync exercises if provided
        if ($request->has('exercises') && is_array($request->exercises)) {
            $workout->workoutExercises()->delete();

            foreach ($request->exercises as $ex) {
                if (empty($ex['exercise_id'])) continue;
                $exerciseId = $ex['exercise_id'];
                if ($exerciseId === 'custom' || !is_numeric($exerciseId)) {
                    $customName = !empty($ex['custom_name']) ? trim($ex['custom_name']) : 'Custom Exercise';
                    $exercise = Exercise::firstOrCreate(
                        ['name' => $customName],
                        ['category' => 'Custom', 'muscle' => 'General']
                    );
                    $exerciseId = $exercise->id;
                }

                $setsData = [];
                if (!empty($ex['sets_list']) && is_array($ex['sets_list'])) {
                    foreach ($ex['sets_list'] as $idx => $setData) {
                        $setsData[] = [
                            'set' => $idx + 1,
                            'weight' => (float)($setData['weight'] ?? 0),
                            'reps' => (int)($setData['reps'] ?? 0),
                        ];
                    }
                }

                $maxWeight = !empty($setsData) ? max(array_column($setsData, 'weight')) : (float)($ex['weight'] ?? 0);
                $totalSets = !empty($setsData) ? count($setsData) : (int)($ex['sets'] ?? 1);
                $firstReps = !empty($setsData) ? ($setsData[0]['reps'] ?? 10) : (int)($ex['reps'] ?? 10);

                WorkoutExercise::create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exerciseId,
                    'weight' => $maxWeight,
                    'sets' => $totalSets,
                    'reps' => $firstReps,
                    'sets_data' => !empty($setsData) ? $setsData : null,
                    'notes' => $ex['notes'] ?? null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'workout_id' => $workout->id,
            'message' => 'Progres tersimpan otomatis',
            'saved_at' => Carbon::now()->format('H:i:s'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required',
            'exercises.*.custom_name' => 'nullable|string|max:255',
            'exercises.*.weight' => 'nullable|numeric|min:0',
            'exercises.*.sets' => 'nullable|integer|min:1',
            'exercises.*.reps' => 'nullable|integer|min:1',
            'exercises.*.sets_list' => 'nullable|array',
            'exercises.*.notes' => 'nullable|string|max:500',
        ]);

        $durationSecs = (int)($request->duration_seconds ?? 0);
        $durationMins = $durationSecs > 0 ? (int)ceil($durationSecs / 60) : ($request->duration ? (int)$request->duration : null);

        $workout = null;
        if (!empty($request->workout_id)) {
            $workout = Workout::where('user_id', Auth::id())
                ->where('id', $request->workout_id)
                ->first();
        }

        if (!$workout) {
            $workout = Workout::where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->latest('id')
                ->first();
        }

        if ($workout) {
            $workout->update([
                'name' => $request->name,
                'date' => $request->date ?? Carbon::today(),
                'duration' => $durationMins ?: ($workout->duration ?: 1),
                'duration_seconds' => $durationSecs ?: $workout->duration_seconds,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);
            $workout->workoutExercises()->delete();
        } else {
            $workout = Workout::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'date' => $request->date ?? Carbon::today(),
                'duration' => $durationMins ?: 1,
                'duration_seconds' => $durationSecs,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);
        }

        foreach ($request->exercises as $ex) {
            $exerciseId = $ex['exercise_id'];
            if ($exerciseId === 'custom' || !is_numeric($exerciseId)) {
                $customName = !empty($ex['custom_name']) ? trim($ex['custom_name']) : 'Custom Exercise';
                $exercise = Exercise::firstOrCreate(
                    ['name' => $customName],
                    ['category' => 'Custom', 'muscle' => 'General']
                );
                $exerciseId = $exercise->id;
            }

            // Individual Sets Data (e.g. Set 1: 40kg 12 reps, Set 2: 45kg 10 reps)
            $setsData = [];
            if (!empty($ex['sets_list']) && is_array($ex['sets_list'])) {
                foreach ($ex['sets_list'] as $idx => $setData) {
                    $setsData[] = [
                        'set' => $idx + 1,
                        'weight' => (float)($setData['weight'] ?? $ex['weight'] ?? 0),
                        'reps' => (int)($setData['reps'] ?? $ex['reps'] ?? 0),
                    ];
                }
            }

            $maxWeight = !empty($setsData) ? max(array_column($setsData, 'weight')) : ($ex['weight'] ?? 0);
            $totalSets = !empty($setsData) ? count($setsData) : ($ex['sets'] ?? 1);
            $firstReps = !empty($setsData) ? ($setsData[0]['reps'] ?? 10) : ($ex['reps'] ?? 10);

            WorkoutExercise::create([
                'workout_id' => $workout->id,
                'exercise_id' => $exerciseId,
                'weight' => $maxWeight,
                'sets' => $totalSets,
                'reps' => $firstReps,
                'sets_data' => !empty($setsData) ? $setsData : null,
                'notes' => $ex['notes'] ?? null,
            ]);
        }

        return redirect()->route('workouts.show', $workout)->with('success', 'Latihan selesai dan berhasil disimpan! 💪');
    }

    public function cancel(Workout $workout)
    {
        $this->authorizeUser($workout);
        $workout->delete();
        return redirect()->route('workouts.index')->with('success', 'Sesi latihan telah dibatalkan.');
    }

    public function show(Workout $workout)
    {
        $this->authorizeUser($workout);
        $workout->load('workoutExercises.exercise');

        // Check for personal records
        $prs = [];
        foreach ($workout->workoutExercises as $we) {
            $previousMax = WorkoutExercise::where('exercise_id', $we->exercise_id)
                ->whereHas('workout', fn($q) => $q->where('user_id', Auth::id())->where('id', '!=', $workout->id))
                ->max('weight');
            
            if ($previousMax !== null && $we->weight > $previousMax) {
                $prs[] = [
                    'exercise' => $we->exercise->name,
                    'weight' => $we->weight,
                    'previous' => $previousMax,
                ];
            }
        }

        return view('workouts.show', compact('workout', 'prs'));
    }

    public function destroy(Workout $workout)
    {
        $this->authorizeUser($workout);
        $workout->delete();
        return redirect()->route('workouts.index')->with('success', 'Workout deleted.');
    }

    private function authorizeUser(Workout $workout): void
    {
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
