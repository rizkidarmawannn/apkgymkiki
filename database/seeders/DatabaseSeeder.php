<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ExerciseSeeder::class,
        ]);

        // Create default user for testing
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'rizki@example.com'],
            [
                'name' => 'Rizki',
                'password' => 'password',
            ]
        );

        // Create Profile
        \App\Models\Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'age' => 24,
                'height' => 170,
                'weight' => 58.5,
                'target_weight' => 55.0,
                'goal' => 'lose_weight',
            ]
        );

        // Sample Weight Log
        \App\Models\WeightLog::updateOrCreate(
            ['user_id' => $user->id, 'date' => \Carbon\Carbon::today()],
            ['weight' => 58.5]
        );
    }
}
