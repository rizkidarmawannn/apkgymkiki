<?php

namespace App\Providers;

use App\Models\Workout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || isset($_SERVER['LAMBDA_TASK_ROOT'])) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $userId = Auth::id();
                $activeWorkout = Workout::where('user_id', $userId)
                    ->where('status', 'in_progress')
                    ->with('workoutExercises.exercise')
                    ->latest('id')
                    ->first();
                $streakData = \App\Services\StreakService::getStreakData($userId);

                $view->with('activeWorkout', $activeWorkout)
                     ->with('streakData', $streakData);
            }
        });
    }
}
