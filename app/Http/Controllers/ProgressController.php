<?php

namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Models\WeightLog;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
        $period = $request->period ?? '30';

        // Weight logs for chart
        $weightLogs = WeightLog::where('user_id', $user->id)
            ->where('date', '>=', Carbon::now()->subDays((int)$period))
            ->orderBy('date')
            ->get();

        // Latest weight
        $latestWeight = WeightLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();

        // First weight (starting)
        $firstWeight = WeightLog::where('user_id', $user->id)
            ->orderBy('date', 'asc')
            ->first();

        // Weight change
        $weightChange = ($latestWeight && $firstWeight)
            ? round($latestWeight->weight - $firstWeight->weight, 1)
            : 0;

        // Workouts this week
        $workoutsThisWeek = Workout::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()])
            ->count();

        // Average daily calories (last 7 days)
        
        // Simpler avg calories calculation
        $last7DaysCals = FoodLog::where('user_id', $user->id)
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->sum('calories');
        $daysWithFood = FoodLog::where('user_id', $user->id)
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->distinct('date')
            ->count('date');
        $avgCalories = $daysWithFood > 0 ? round($last7DaysCals / $daysWithFood) : 0;

        // Exercise progress - get exercises user has done more than once
        $exerciseProgress = WorkoutExercise::whereHas('workout', fn($q) => $q->where('user_id', $user->id))
            ->with('exercise')
            ->selectRaw('exercise_id, MAX(weight) as max_weight, MAX(reps) as max_reps')
            ->groupBy('exercise_id')
            ->orderBy('max_weight', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) use ($user) {
                $records = WorkoutExercise::where('exercise_id', $item->exercise_id)
                    ->whereHas('workout', fn($q) => $q->where('user_id', $user->id))
                    ->with('workout')
                    ->orderBy('created_at', 'desc')
                    ->limit(2)
                    ->get();
                
                $current = $records->first();
                $previous = $records->count() > 1 ? $records->last() : null;
                
                return [
                    'exercise' => $item->exercise,
                    'current_weight' => $current?->weight ?? 0,
                    'current_reps' => $current?->reps ?? 0,
                    'previous_weight' => $previous?->weight ?? 0,
                    'previous_reps' => $previous?->reps ?? 0,
                    'progress' => $current && $previous ? round($current->weight - $previous->weight, 1) : 0,
                ];
            });

        $startingWeight = $firstWeight?->weight ?? $profile?->weight ?? 0;
        $currentWeight = $latestWeight?->weight ?? $profile?->weight ?? 0;
        $targetWeight = $profile?->target_weight ?? 0;

        return view('progress.index', compact(
            'user', 'profile', 'weightLogs', 'latestWeight', 'firstWeight',
            'startingWeight', 'currentWeight', 'targetWeight',
            'weightChange', 'workoutsThisWeek', 'avgCalories',
            'exerciseProgress', 'period'
        ));
    }

    public function storeWeight(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|min:20|max:300',
            'date' => 'required|date',
        ]);

        WeightLog::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'date' => $request->date,
            ],
            [
                'weight' => $request->weight,
            ]
        );

        // Also update profile weight
        $profile = Auth::user()->profile;
        if ($profile) {
            $profile->update(['weight' => $request->weight]);
        }

        return redirect()->route('progress.index')->with('success', 'Weight saved!');
    }

    public function weightData(Request $request)
    {
        $period = $request->period ?? 30;
        $weightLogs = WeightLog::where('user_id', Auth::id())
            ->where('date', '>=', Carbon::now()->subDays((int)$period))
            ->orderBy('date')
            ->get(['date', 'weight']);

        return response()->json($weightLogs);
    }
}
