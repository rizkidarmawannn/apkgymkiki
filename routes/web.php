<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Workouts
    Route::get('/workouts', [WorkoutController::class, 'index'])->name('workouts.index');
    Route::get('/workouts/create', [WorkoutController::class, 'create'])->name('workouts.create');
    Route::post('/workouts', [WorkoutController::class, 'store'])->name('workouts.store');
    Route::get('/workouts/{workout}', [WorkoutController::class, 'show'])->name('workouts.show');
    Route::delete('/workouts/{workout}', [WorkoutController::class, 'destroy'])->name('workouts.destroy');

    // Food
    Route::get('/foods', [FoodLogController::class, 'index'])->name('foods.index');
    Route::post('/foods', [FoodLogController::class, 'store'])->name('foods.store');
    Route::get('/foods/{food}/edit', [FoodLogController::class, 'edit'])->name('foods.edit');
    Route::put('/foods/{food}', [FoodLogController::class, 'update'])->name('foods.update');
    Route::delete('/foods/{food}', [FoodLogController::class, 'destroy'])->name('foods.destroy');

    // Progress
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::post('/progress/weight', [ProgressController::class, 'storeWeight'])->name('progress.store');
    Route::get('/progress/weight-data', [ProgressController::class, 'weightData'])->name('progress.weightData');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
