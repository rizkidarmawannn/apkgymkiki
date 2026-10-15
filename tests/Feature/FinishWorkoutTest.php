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

    public function test_autosave_does_not_resurrect_completed_workout(): void
    {
        $user = User::factory()->create();
        $exercise = $this->makeExercise();

        $workout = Workout::create([
            'user_id' => $user->id,
            'name' => 'Latihan Selesai',
            'date' => now()->format('Y-m-d'),
            'duration' => 30,
            'status' => 'completed',
        ]);

        // Simulating autosave triggering after workout is already completed
        $response = $this->actingAs($user)->postJson(route('workouts.autosave'), [
            'workout_id' => $workout->id,
            'name' => 'Latihan Selesai',
            'date' => now()->format('Y-m-d'),
            'exercises' => [
                [
                    'exercise_id' => $exercise->id,
                    'sets_list' => [['weight' => 50, 'reps' => 10]],
                ],
            ],
        ]);

        $response->assertJson(['success' => false]);
        $this->assertEquals(0, Workout::where('user_id', $user->id)->where('status', 'in_progress')->count());
    }

    public function test_create_does_not_hijack_in_progress_from_different_date(): void
    {
        $user = User::factory()->create();

        // Old in-progress workout from yesterday
        Workout::create([
            'user_id' => $user->id,
            'name' => 'Latihan Kemarin',
            'date' => now()->subDay()->format('Y-m-d'),
            'duration' => 10,
            'status' => 'in_progress',
        ]);

        // User clicks today on calendar to start workout today
        $response = $this->actingAs($user)->get(route('workouts.create', ['date' => now()->format('Y-m-d')]));
        $response->assertStatus(200);
        $response->assertViewHas('activeWorkout', null);
        $response->assertViewHas('selectedDate', now()->format('Y-m-d'));
    }
}
