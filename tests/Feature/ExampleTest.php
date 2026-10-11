<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

    public function test_user_can_save_workout_with_sets_list(): void
    {
        $user = \App\Models\User::first();
        if (!$user) {
            $user = \App\Models\User::factory()->create();
        }

        $response = $this->actingAs($user)->post('/workouts', [
            'name' => 'Push Day Routine',
            'date' => '2026-10-11',
            'duration' => 45,
            'exercises' => [
                [
                    'exercise_id' => 'custom',
                    'custom_name' => 'Bench Press Test',
                    'sets_list' => [
                        ['weight' => 50, 'reps' => 12],
                        ['weight' => 55, 'reps' => 10],
                        ['weight' => 60, 'reps' => 8],
                    ],
                ]
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
    }

    public function test_user_can_update_profile(): void
    {
        $user = \App\Models\User::first();
        if (!$user) {
            $user = \App\Models\User::factory()->create();
        }

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Rizki Updated',
            'age' => 25,
            'height' => 172,
            'weight' => 60,
            'target_weight' => 65,
            'goal' => 'gain_muscle',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.index'));
        $this->assertEquals(60, $user->fresh()->profile->weight);
        $this->assertEquals('gain_muscle', $user->fresh()->profile->goal);
    }

    public function test_user_can_view_progress_page(): void
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        // Create a workout with workout exercise to trigger the exerciseProgress loop
        $workout = \App\Models\Workout::create([
            'user_id' => $user->id,
            'name' => 'Chest Day',
            'date' => '2026-10-11',
            'status' => 'completed',
        ]);

        $exercise = \App\Models\Exercise::firstOrCreate(
            ['name' => 'Bench Press'],
            ['category' => 'Chest', 'muscle' => 'Pectorals']
        );

        \App\Models\WorkoutExercise::create([
            'workout_id' => $workout->id,
            'exercise_id' => $exercise->id,
            'weight' => 70,
            'sets' => 3,
            'reps' => 10,
        ]);

        $response = $this->actingAs($user)->get('/progress');
        $response->assertStatus(200);
    }
}
