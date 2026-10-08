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
        $workouts = Workout::where('user_id', Auth::id())
            ->with('workoutExercises')
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('workouts.index', compact('workouts'));
    }

    public function create()
    {
        $exercises = Exercise::orderBy('category')->orderBy('name')->get()->groupBy('category');
        return view('workouts.create', compact('exercises'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required',
            'exercises.*.custom_name' => 'nullable|string|max:255',
            'exercises.*.weight' => 'nullable|numeric|min:0',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|integer|min:1',
            'exercises.*.notes' => 'nullable|string|max:500',
        ]);

        $workout = Workout::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'date' => $request->date ?? Carbon::today(),
            'duration' => $request->duration ?? null,
            'notes' => $request->notes,
            'status' => 'completed',
        ]);

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
            $firstReps = !empty($setsData) ? $setsData[0]['reps'] : ($ex['reps'] ?? 0);

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

        return redirect()->route('workouts.show', $workout)->with('success', 'Workout saved!');
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
