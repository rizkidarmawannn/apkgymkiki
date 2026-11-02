<?php

namespace App\Services;

use App\Models\RestDay;
use App\Models\Workout;
use Carbon\Carbon;

class StreakService
{
    public static function getStreakData(int $userId): array
    {
        // 1. Get completed workout dates (Y-m-d)
        try {
            $workoutDates = Workout::where('user_id', $userId)
                ->where('status', 'completed')
                ->pluck('date')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->unique()
                ->values();
        } catch (\Throwable $e) {
            $workoutDates = collect();
        }

        // 2. Get rest day dates (Y-m-d)
        try {
            $restDates = RestDay::where('user_id', $userId)
                ->pluck('date')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->unique()
                ->values();
        } catch (\Throwable $e) {
            $restDates = collect();
        }

        // 3. Union of all active dates (workout OR rest day maintains the chain)
        $activeDates = $workoutDates->concat($restDates)->unique()->sortDesc()->values();

        $today = Carbon::today()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $hasWorkoutToday = $workoutDates->contains($today);
        $hasRestToday = $restDates->contains($today);
        $isActiveToday = $hasWorkoutToday || $hasRestToday;

        $hasWorkoutYesterday = $workoutDates->contains($yesterday);
        $hasRestYesterday = $restDates->contains($yesterday);
        $isActiveYesterday = $hasWorkoutYesterday || $hasRestYesterday;

        // 4. Calculate current streak
        $currentStreak = 0;
        $status = 'extinguished'; // 'lit', 'rest', 'warning', 'extinguished'

        if ($isActiveToday) {
            $status = $hasRestToday && !$hasWorkoutToday ? 'rest' : 'lit';
            $checkDate = Carbon::today();
            while ($activeDates->contains($checkDate->format('Y-m-d'))) {
                $currentStreak++;
                $checkDate->subDay();
            }
        } elseif ($isActiveYesterday) {
            $status = 'warning'; // Streak alive from yesterday! Can log workout or rest today.
            $checkDate = Carbon::yesterday();
            while ($activeDates->contains($checkDate->format('Y-m-d'))) {
                $currentStreak++;
                $checkDate->subDay();
            }
        } else {
            $status = 'extinguished';
            $currentStreak = 0;
        }

        // 5. Calculate all-time best streak
        $bestStreak = 0;
        if ($activeDates->isNotEmpty()) {
            $sortedDates = $activeDates->sort()->values();
            $tempStreak = 1;
            for ($i = 1; $i < $sortedDates->count(); $i++) {
                $prev = Carbon::parse($sortedDates[$i - 1]);
                $curr = Carbon::parse($sortedDates[$i]);
                if ($prev->copy()->addDay()->format('Y-m-d') === $curr->format('Y-m-d')) {
                    $tempStreak++;
                } else {
                    $bestStreak = max($bestStreak, $tempStreak);
                    $tempStreak = 1;
                }
            }
            $bestStreak = max($bestStreak, $tempStreak, $currentStreak);
        } else {
            $bestStreak = $currentStreak;
        }

        // 6. Milestone Flame Tiers: 1-9 (orange), 10-49 (blue), 50-99 (plasma purple), 100+ (golden apex)
        $flameTier = 0;
        $flameLevel = 'cold';
        $flameTitle = 'Api Belum Menyala';
        $flameEmoji = '🕯️';
        $flameColor = '#64748b';
        $nextMilestone = 10;
        $prevMilestone = 0;

        if ($currentStreak === 0) {
            $flameTier = 0;
            $flameLevel = 'cold';
            $flameTitle = 'Api Belum Menyala';
            $flameEmoji = '🕯️';
            $flameColor = '#64748b';
            $nextMilestone = 10;
            $prevMilestone = 0;
        } elseif ($currentStreak < 10) {
            $flameTier = 1;
            $flameLevel = 'warm';
            $flameTitle = 'Api Membara';
            $flameEmoji = '🔥';
            $flameColor = '#f97316';
            $nextMilestone = 10;
            $prevMilestone = 1;
        } elseif ($currentStreak < 50) {
            $flameTier = 2;
            $flameLevel = 'blue';
            $flameTitle = 'Api Biru 10+ Hari';
            $flameEmoji = '⚡🔥';
            $flameColor = '#06b6d4';
            $nextMilestone = 50;
            $prevMilestone = 10;
        } elseif ($currentStreak < 100) {
            $flameTier = 3;
            $flameLevel = 'plasma';
            $flameTitle = 'Api Plasma 50+ Hari';
            $flameEmoji = '🟣🔥';
            $flameColor = '#a855f7';
            $nextMilestone = 100;
            $prevMilestone = 50;
        } else {
            $flameTier = 4;
            $flameLevel = 'godlike';
            $flameTitle = 'Api Emas Mahkota 100+ Hari';
            $flameEmoji = '👑🔥';
            $flameColor = '#eab308';
            $nextMilestone = 200;
            $prevMilestone = 100;
        }

        $milestoneDiff = max(1, $nextMilestone - $prevMilestone);
        $currentProgress = min(100, max(0, round((($currentStreak - $prevMilestone) / $milestoneDiff) * 100)));
        $daysToMilestone = max(0, $nextMilestone - $currentStreak);

        // 7. Weekly schedule dots (Sen - Min of current week)
        $startOfWeek = Carbon::now()->startOfWeek();
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $dayStr = $day->format('Y-m-d');
            $hasWk = $workoutDates->contains($dayStr);
            $hasRst = $restDates->contains($dayStr);

            $weekDays[] = [
                'day_name' => ['Min','Sen','Sel','Rab','Kam','Jum','Sab'][(int)$day->format('w')],
                'date' => $day->format('d/m'),
                'date_full' => $dayStr,
                'is_today' => ($dayStr === $today),
                'is_past' => $day->isPast() && ($dayStr !== $today),
                'has_workout' => $hasWk,
                'is_rest' => $hasRst,
            ];
        }

        return [
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak,
            'status' => $status,
            'is_active_today' => $isActiveToday,
            'has_workout_today' => $hasWorkoutToday,
            'is_rest_today' => $hasRestToday,
            'flame_tier' => $flameTier,
            'flame_level' => $flameLevel,
            'flame_title' => $flameTitle,
            'flame_emoji' => $flameEmoji,
            'flame_color' => $flameColor,
            'next_milestone' => $nextMilestone,
            'prev_milestone' => $prevMilestone,
            'days_to_milestone' => $daysToMilestone,
            'milestone_progress' => $currentProgress,
            'week_days' => $weekDays,
        ];
    }
}
