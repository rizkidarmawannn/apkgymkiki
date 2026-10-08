<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // Chest
            ['name' => 'Bench Press', 'category' => 'chest', 'muscle' => 'pectoralis'],
            ['name' => 'Incline Bench Press', 'category' => 'chest', 'muscle' => 'upper pectoralis'],
            ['name' => 'Dumbbell Press', 'category' => 'chest', 'muscle' => 'pectoralis'],
            ['name' => 'Push Up', 'category' => 'chest', 'muscle' => 'pectoralis'],

            // Back
            ['name' => 'Pull Up', 'category' => 'back', 'muscle' => 'latissimus'],
            ['name' => 'Lat Pulldown', 'category' => 'back', 'muscle' => 'latissimus'],
            ['name' => 'Barbell Row', 'category' => 'back', 'muscle' => 'rhomboids'],
            ['name' => 'Dumbbell Row', 'category' => 'back', 'muscle' => 'latissimus'],

            // Shoulder
            ['name' => 'Shoulder Press', 'category' => 'shoulder', 'muscle' => 'deltoids'],
            ['name' => 'Lateral Raise', 'category' => 'shoulder', 'muscle' => 'lateral deltoid'],
            ['name' => 'Front Raise', 'category' => 'shoulder', 'muscle' => 'anterior deltoid'],

            // Biceps
            ['name' => 'Barbell Curl', 'category' => 'biceps', 'muscle' => 'biceps brachii'],
            ['name' => 'Dumbbell Curl', 'category' => 'biceps', 'muscle' => 'biceps brachii'],
            ['name' => 'Hammer Curl', 'category' => 'biceps', 'muscle' => 'brachialis'],

            // Triceps
            ['name' => 'Tricep Pushdown', 'category' => 'triceps', 'muscle' => 'triceps brachii'],
            ['name' => 'Dips', 'category' => 'triceps', 'muscle' => 'triceps brachii'],
            ['name' => 'Overhead Extension', 'category' => 'triceps', 'muscle' => 'triceps brachii'],

            // Legs
            ['name' => 'Squat', 'category' => 'legs', 'muscle' => 'quadriceps'],
            ['name' => 'Leg Press', 'category' => 'legs', 'muscle' => 'quadriceps'],
            ['name' => 'Lunges', 'category' => 'legs', 'muscle' => 'quadriceps'],
            ['name' => 'Leg Curl', 'category' => 'legs', 'muscle' => 'hamstrings'],
            ['name' => 'Calf Raise', 'category' => 'legs', 'muscle' => 'gastrocnemius'],

            // Core
            ['name' => 'Plank', 'category' => 'core', 'muscle' => 'rectus abdominis'],
            ['name' => 'Crunches', 'category' => 'core', 'muscle' => 'rectus abdominis'],
            ['name' => 'Russian Twist', 'category' => 'core', 'muscle' => 'obliques'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}
