<?php

namespace App\Http\Controllers;

use App\Models\FoodLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

        $foodLogs = FoodLog::where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->orderByRaw("CASE meal WHEN 'breakfast' THEN 1 WHEN 'lunch' THEN 2 WHEN 'dinner' THEN 3 WHEN 'snack' THEN 4 ELSE 5 END")
            ->get()
            ->groupBy('meal');

        $totalCalories = FoodLog::where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->sum('calories');

        $totalProtein = FoodLog::where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->sum('protein');

        // Calorie target
        $profile = Auth::user()->profile;
        $calorieTarget = 2200;
        if ($profile?->goal === 'lose_weight') $calorieTarget = 1800;
        if ($profile?->goal === 'gain_muscle') $calorieTarget = 2500;

        // History dates
        $dates = FoodLog::where('user_id', Auth::id())
            ->selectRaw('date, SUM(calories) as total_calories')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return view('foods.index', compact('foodLogs', 'totalCalories', 'totalProtein', 'calorieTarget', 'date', 'dates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'food_name' => 'required|string|max:255',
            'meal' => 'required|in:breakfast,lunch,dinner,snack',
            'portion' => 'required|string|max:255',
            'calories' => 'required|integer|min:0',
            'protein' => 'nullable|numeric|min:0',
        ]);

        FoodLog::create([
            'user_id' => Auth::id(),
            'food_name' => $request->food_name,
            'meal' => $request->meal,
            'portion' => $request->portion,
            'calories' => $request->calories,
            'protein' => $request->protein ?? 0,
            'date' => $request->date ?? Carbon::today(),
        ]);

        return redirect()->route('foods.index', ['date' => $request->date ?? today()->toDateString()])
            ->with('success', 'Food added!');
    }

    public function edit(FoodLog $food)
    {
        if ($food->user_id !== Auth::id()) abort(403);
        return view('foods.edit', compact('food'));
    }

    public function update(Request $request, FoodLog $food)
    {
        if ($food->user_id !== Auth::id()) abort(403);

        $request->validate([
            'food_name' => 'required|string|max:255',
            'meal' => 'required|in:breakfast,lunch,dinner,snack',
            'portion' => 'required|string|max:255',
            'calories' => 'required|integer|min:0',
            'protein' => 'nullable|numeric|min:0',
        ]);

        $food->update($request->only('food_name', 'meal', 'portion', 'calories', 'protein'));

        return redirect()->route('foods.index', ['date' => $food->date->toDateString()])
            ->with('success', 'Food updated!');
    }

    public function destroy(FoodLog $food)
    {
        if ($food->user_id !== Auth::id()) abort(403);
        $date = $food->date->toDateString();
        $food->delete();
        return redirect()->route('foods.index', ['date' => $date])
            ->with('success', 'Food deleted.');
    }
}
