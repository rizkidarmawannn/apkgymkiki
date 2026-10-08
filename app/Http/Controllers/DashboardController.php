<?php

namespace App\Http\Controllers;

use App\Models\FoodLog;
use App\Models\WeightLog;
use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $profile = $user->profile;

        // Current weight from latest weight log or profile
        $latestWeight = WeightLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();
        $currentWeight = $latestWeight?->weight ?? $profile?->weight ?? 0;

        // Today's calories
        $todayCalories = FoodLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->sum('calories');

        // Workouts this week
        $weekStart = Carbon::now()->startOfWeek();
        $workoutsThisWeek = Workout::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('date', [$weekStart, $today])
            ->count();

        // Target weight
        $targetWeight = $profile?->target_weight ?? 0;

        // Today's workout
        $todayWorkout = Workout::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->with('workoutExercises.exercise')
            ->first();

        // Today's food logs grouped by meal
        $todayFoods = FoodLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('meal')
            ->get()
            ->groupBy('meal');

        // Calorie target (simple: 2200 default or based on goal)
        $calorieTarget = 2200;
        if ($profile?->goal === 'lose_weight') $calorieTarget = 1800;
        if ($profile?->goal === 'gain_muscle') $calorieTarget = 2500;

        return view('dashboard.index', compact(
            'user', 'currentWeight', 'todayCalories', 'workoutsThisWeek',
            'targetWeight', 'todayWorkout', 'todayFoods', 'calorieTarget'
        ));
    }
}
