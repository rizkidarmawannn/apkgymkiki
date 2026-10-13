<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinishWorkoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeExercise(array $attrs = []): Exercise
    {
        return Exercise::create(array_merge([
            'name' => 'Bench Press',
            'category' => 'Chest',
            'muscle' => 'Pectoral',
        ], $attrs));
    }

    public function test_store_workout_creates_completed_workout(): void
    {
        $user = User::factory()->create();
        $exercise = $this->makeExercise();

        $response = $this->actingAs($user)->post(route('workouts.store'), [
            'name' => 'Latihan Test',
            'date' => now()->format('Y-m-d'),
            'duration' => 45,
            'duration_seconds' => 2700,
            'notes' => 'Test notes',
            'exercises' => [
                [
                    'exercise_id' => $exercise->id,
                    'weight' => 60,
                    'sets' => 3,
                    'reps' => 10,
                    'sets_list' => [
                        ['weight' => 60, 'reps' => 10],
                        ['weight' => 65, 'reps' => 8],
                        ['weight' => 65, 'reps' => 8],
                    ],
                    'notes' => null,
                ],
            ],
        ]);

        $workout = Workout::where('user_id', $user->id)->first();
        $this->assertNotNull($workout, 'Workout harus tersimpan');
        $this->assertEquals('completed', $workout->status);

        // Should redirect to show page, not 500
        $response->assertRedirect(route('workouts.show', $workout));
    }

    public function test_store_workout_with_ongoing_session_completes_it(): void
    {
        $user = User::factory()->create();
        $exercise = $this->makeExercise(['name' => 'Squat', 'category' => 'Legs']);

        $ongoing = Workout::create([
            'user_id' => $user->id,
            'name' => 'Latihan Berjalan',
            'date' => now()->format('Y-m-d'),
            'duration' => 10,
            'duration_seconds' => 600,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($user)->post(route('workouts.store'), [
            'workout_id' => $ongoing->id,
            'name' => 'Latihan Berjalan',
            'date' => now()->format('Y-m-d'),
            'duration_seconds' => 3000,
            'exercises' => [
                [
                    'exercise_id' => $exercise->id,
                    'weight' => 80,
                    'sets' => 4,
                    'reps' => 8,
                    'sets_list' => [
                        ['weight' => 80, 'reps' => 8],
                    ],
                ],
            ],
        ]);

        $response->assertStatus(302);
        $ongoing->refresh();
        $this->assertEquals('completed', $ongoing->status);
    }

    public function test_show_page_renders_after_finish(): void
    {
        $user = User::factory()->create();
        $exercise = $this->makeExercise(['name' => 'Deadlift', 'category' => 'Back']);

        $workout = Workout::create([
            'user_id' => $user->id,
            'name' => 'Latihan Selesai',
            'date' => now()->format('Y-m-d'),
            'duration' => 60,
            'duration_seconds' => 3600,
            'status' => 'completed',
        ]);

        $workout->workoutExercises()->create([
            'exercise_id' => $exercise->id,
            'weight' => 100,
            'sets' => 5,
            'reps' => 5,
            'sets_data' => [['set' => 1, 'weight' => 100, 'reps' => 5]],
        ]);

        $response = $this->actingAs($user)->get(route('workouts.show', $workout));
        $response->assertStatus(200);
    }
}
