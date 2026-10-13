<?php

namespace App\Services;

use App\Models\Workout;
use Carbon\Carbon;

class StreakService
{
    public static function getStreakData(int $userId): array
    {
        // Get all completed workout dates sorted descending (Y-m-d)
        $dates = Workout::where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->unique()
            ->values();

        $today = Carbon::today()->format('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $hasWorkoutToday = $dates->contains($today);
        $hasWorkoutYesterday = $dates->contains($yesterday);

        // Calculate current streak
        $currentStreak = 0;
        $status = 'extinguished'; // 'lit', 'warning', 'extinguished'

        if ($hasWorkoutToday) {
            $status = 'lit';
            $checkDate = Carbon::today();
            while ($dates->contains($checkDate->format('Y-m-d'))) {
                $currentStreak++;
                $checkDate->subDay();
            }
        } elseif ($hasWorkoutYesterday) {
            $status = 'warning'; // Streak alive from yesterday, needs workout today!
            $checkDate = Carbon::yesterday();
            while ($dates->contains($checkDate->format('Y-m-d'))) {
                $currentStreak++;
                $checkDate->subDay();
            }
        } else {
            $status = 'extinguished';
            $currentStreak = 0;
        }

        // Calculate all-time best streak
        $bestStreak = 0;
        if ($dates->isNotEmpty()) {
            $sortedDates = $dates->sort()->values();
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

        // Determine flame level & title (TikTok style)
        $flameLevel = 'starter';
        $flameTitle = 'Percikan Api';
        $flameEmoji = '🔥';

        if ($currentStreak === 0) {
            $flameLevel = 'cold';
            $flameTitle = 'Api Belum Menyala';
            $flameEmoji = '🕯️';
        } elseif ($currentStreak >= 1 && $currentStreak <= 3) {
            $flameLevel = 'warm';
            $flameTitle = 'Api Menyala';
            $flameEmoji = '🔥';
        } elseif ($currentStreak >= 4 && $currentStreak <= 7) {
            $flameLevel = 'blazing';
            $flameTitle = 'Api Membara';
            $flameEmoji = '🔥✨';
        } elseif ($currentStreak >= 8 && $currentStreak <= 14) {
            $flameLevel = 'intense';
            $flameTitle = 'Badai Api';
            $flameEmoji = '⚡🔥';
        } elseif ($currentStreak >= 15 && $currentStreak <= 29) {
            $flameLevel = 'inferno';
            $flameTitle = 'Inferno Menggila';
            $flameEmoji = '💥🔥';
        } else {
            $flameLevel = 'godlike';
            $flameTitle = 'Dewa Konsistensi';
            $flameEmoji = '👑🔥';
        }

        // Weekly schedule dots (Sen - Min of current week)
        $startOfWeek = Carbon::now()->startOfWeek();
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $dayStr = $day->format('Y-m-d');
            $weekDays[] = [
                'day_name' => $day->locale('id')->translatedFormat('D'),
                'date' => $day->format('d/m'),
                'is_today' => ($dayStr === $today),
                'is_past' => $day->isPast() && ($dayStr !== $today),
                'has_workout' => $dates->contains($dayStr),
            ];
        }

        return [
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak,
            'status' => $status,
            'is_active_today' => $hasWorkoutToday,
            'flame_level' => $flameLevel,
            'flame_title' => $flameTitle,
            'flame_emoji' => $flameEmoji,
            'week_days' => $weekDays,
        ];
    }
}
