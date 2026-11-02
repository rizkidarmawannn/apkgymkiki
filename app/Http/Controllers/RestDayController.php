<?php

namespace App\Http\Controllers;

use App\Models\RestDay;
use App\Services\StreakService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestDayController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $userId = Auth::id();
        $date = $request->filled('date')
            ? Carbon::parse($request->date)->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');

        $existing = RestDay::where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();

        if ($existing) {
            $existing->delete();
            $isRestDay = false;
            $message = 'Hari istirahat (Rest Day) dinonaktifkan.';
        } else {
            RestDay::create([
                'user_id' => $userId,
                'date' => $date,
                'notes' => $request->notes ?? 'Hari istirahat untuk pemulihan otot',
            ]);
            $isRestDay = true;
            $message = 'Hari istirahat (Rest Day) aktif. Api streakmu tetap aman!';
        }

        if ($request->wantsJson() || $request->ajax()) {
            $streakData = StreakService::getStreakData($userId);
            return response()->json([
                'success' => true,
                'is_rest_day' => $isRestDay,
                'date' => $date,
                'message' => $message,
                'streak' => $streakData,
            ]);
        }

        return back()->with('success', $message);
    }
}
