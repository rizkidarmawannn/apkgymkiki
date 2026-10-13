<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $selectedYear = (int)$request->input('year', Carbon::today()->year);

        // Find min and max year from user workouts
        $minYear = Workout::where('user_id', $userId)->min('date');
        $startYear = $minYear ? Carbon::parse($minYear)->year : Carbon::today()->year;
        $currentYear = Carbon::today()->year;

        $availableYears = range(max($currentYear, $startYear), min($startYear, $currentYear - 2));
        if (!in_array($selectedYear, $availableYears)) {
            $availableYears[] = $selectedYear;
            rsort($availableYears);
        }

        $startDate = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear();
        $endDate = Carbon::createFromDate($selectedYear, 12, 31)->endOfYear();

        $workouts = Workout::where('user_id', $userId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with('workoutExercises.exercise')
            ->orderBy('date')
            ->get();

        $formattedData = $this->formatYearWorkouts($workouts, $selectedYear);

        // Stats calculation
        $totalCompletedYear = $workouts->where('status', 'completed')->count();
        $activeDaysYear = $workouts->where('status', 'completed')->pluck('date')->unique()->count();

        // Calculate current streak
        $currentStreak = $this->calculateStreak($userId);

        // Ongoing workout if any
        $ongoingWorkout = Workout::where('user_id', $userId)
            ->where('status', 'in_progress')
            ->with('workoutExercises.exercise')
            ->latest('id')
            ->first();

        return view('calendar.index', [
            'year' => $selectedYear,
            'availableYears' => $availableYears,
            'calendarData' => $formattedData,
            'totalCompletedYear' => $totalCompletedYear,
            'activeDaysYear' => $activeDaysYear,
            'currentStreak' => $currentStreak,
            'ongoingWorkout' => $ongoingWorkout,
        ]);
    }

    public function calendarData(Request $request)
    {
        $userId = Auth::id();
        $year = (int)$request->input('year', Carbon::today()->year);

        $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();

        $workouts = Workout::where('user_id', $userId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with('workoutExercises.exercise')
            ->orderBy('date')
            ->get();

        return response()->json($this->formatYearWorkouts($workouts, $year));
    }

    private function formatYearWorkouts($workouts, int $year): array
    {
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
                'date' => $dateKey,
                'date_formatted' => Carbon::parse($w->date)->format('d/m/Y'),
                'date_full' => (function($date) {
                    $days = [0=>'Minggu',1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu'];
                    $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    return $days[(int)$date->format('w')] . ', ' . $date->format('d') . ' ' . $months[(int)$date->format('n')] . ' ' . $date->format('Y');
                })(Carbon::parse($w->date)),
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
            'year' => $year,
            'total_completed' => $workouts->where('status', 'completed')->count(),
            'workouts_by_date' => $grouped,
        ];
    }

    private function calculateStreak(int $userId): int
    {
        $workoutDates = Workout::where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->unique()
            ->values();

        if ($workoutDates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $checkDate = Carbon::today();

        // If no workout today, check if there was one yesterday to continue streak
        if (!$workoutDates->contains($checkDate->format('Y-m-d'))) {
            $checkDate = Carbon::yesterday();
            if (!$workoutDates->contains($checkDate->format('Y-m-d'))) {
                return 0;
            }
        }

        while ($workoutDates->contains($checkDate->format('Y-m-d'))) {
            $streak++;
            $checkDate->subDay();
        }

        return $streak;
    }
}
