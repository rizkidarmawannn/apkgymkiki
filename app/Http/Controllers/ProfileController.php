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
            'goal' => 'nullable|in:lose_weight,gain_muscle,maintain',
        ]);

        $user = Auth::user();
        $user->update(['name' => $request->name]);

        $user->profile->update([
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'target_weight' => $request->target_weight,
            'goal' => $request->goal,
        ]);

        return redirect()->route('profile.index')->with('success', 'Profile updated!');
    }
}
