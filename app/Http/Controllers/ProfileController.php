<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile;
        return view('profile.index', compact('user', 'profile'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;
        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:10|max:100',
            'height' => 'nullable|numeric|min:50|max:300',
            'weight' => 'nullable|numeric|min:20|max:300',
            'target_weight' => 'nullable|numeric|min:20|max:300',
            'goal' => 'nullable|string|max:50',
            'fitness_goal' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $user->update(['name' => $request->name]);

        $rawGoal = $request->goal ?? $request->fitness_goal ?? 'maintain';
        $goalMap = [
            'lose' => 'lose_weight',
            'lose_weight' => 'lose_weight',
            'gain' => 'gain_muscle',
            'gain_muscle' => 'gain_muscle',
            'maintain' => 'maintain',
            'maintain_weight' => 'maintain',
        ];
        $goal = $goalMap[$rawGoal] ?? 'maintain';

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'age' => $request->age,
                'height' => $request->height,
                'weight' => $request->weight,
                'target_weight' => $request->target_weight,
                'goal' => $goal,
            ]
        );

        if ($request->filled('weight')) {
            \App\Models\WeightLog::updateOrCreate(
                ['user_id' => $user->id, 'date' => \Carbon\Carbon::today()],
                ['weight' => (float)$request->weight]
            );
        }

        return redirect()->route('profile.index')->with('success', 'Profile updated!');
    }
}
