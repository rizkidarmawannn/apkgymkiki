<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OngoingWorkoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_autosave_ongoing_workout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/workouts/autosave', [
            'name' => 'Sesi Latihan Kaki',
            'date' => '2026-10-13',
            'duration_seconds' => 125,
            'notes' => 'Pemanasan selesai',
            'exercises' => [
                [
                    'exercise_id' => 'custom',
                    'custom_name' => 'Squat Kustom',
                    'sets_list' => [
                        ['weight' => 60, 'reps' => 10],
                        ['weight' => 70, 'reps' => 8],
                    ],
                    'notes' => 'Kaki terasa kuat',
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $workoutId = $response->json('workout_id');
        $workout = Workout::find($workoutId);

        $this->assertNotNull($workout);
        $this->assertEquals('in_progress', $workout->status);
        $this->assertEquals('Sesi Latihan Kaki', $workout->name);
        $this->assertEquals(125, $workout->duration_seconds);
        $this->assertCount(1, $workout->workoutExercises);
    }

    public function test_ongoing_workout_appears_on_dashboard_and_workouts_index(): void
    {
        $user = User::factory()->create();
        $workout = Workout::create([
            'user_id' => $user->id,
            'name' => 'Pundak & Lengan',
            'date' => '2026-10-13',
            'duration_seconds' => 300,
            'status' => 'in_progress',
        ]);

        // Dashboard check
        $dashboardResponse = $this->actingAs($user)->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Pundak & Lengan');
        $dashboardResponse->assertSee('Lanjutkan Latihan');

        // Workouts index check
        $workoutsResponse = $this->actingAs($user)->get('/workouts');
        $workoutsResponse->assertStatus(200);
        $workoutsResponse->assertSee('Pundak & Lengan');
        $workoutsResponse->assertSee('Lanjutkan Latihan Ini');
    }

    public function test_user_can_resume_ongoing_workout(): void
    {
        $user = User::factory()->create();
        $workout = Workout::create([
            'user_id' => $user->id,
            'name' => 'Pundak & Lengan',
            'date' => '2026-10-13',
            'duration_seconds' => 300,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($user)->get(route('workouts.resume', $workout));
        $response->assertStatus(200);
        $response->assertSee('Pundak & Lengan');
        $response->assertSee('Melanjutkan Sesi');
    }

    public function test_user_can_finish_and_complete_ongoing_workout(): void
    {
        $user = User::factory()->create();
        $workout = Workout::create([
            'user_id' => $user->id,
            'name' => 'Full Body Blast',
            'date' => '2026-10-13',
            'duration_seconds' => 600,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($user)->post('/workouts', [
            'workout_id' => $workout->id,
            'name' => 'Full Body Blast Selesai',
            'date' => '2026-10-13',
            'duration_seconds' => 1800,
            'exercises' => [
                [
                    'exercise_id' => 'custom',
                    'custom_name' => 'Pull Up',
                    'sets_list' => [
                        ['weight' => 0, 'reps' => 12],
                    ],
                ]
            ]
        ]);

        $response->assertRedirect(route('workouts.show', $workout));

        $freshWorkout = $workout->fresh();
        $this->assertEquals('completed', $freshWorkout->status);
        $this->assertEquals(30, $freshWorkout->duration); // 1800s / 60
        $this->assertEquals('Full Body Blast Selesai', $freshWorkout->name);
    }

    public function test_user_can_cancel_ongoing_workout(): void
    {
        $user = User::factory()->create();
        $workout = Workout::create([
            'user_id' => $user->id,
            'name' => 'Latihan Dibatalkan',
            'date' => '2026-10-13',
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($user)->delete(route('workouts.cancel', $workout));
        $response->assertRedirect(route('workouts.index'));

        $this->assertDatabaseMissing('workouts', [
            'id' => $workout->id,
        ]);
    }

    public function test_user_can_fetch_calendar_data(): void
    {
        $user = User::factory()->create();

        // Create completed workout on 2026-10-10
        $workout = Workout::create([
            'user_id' => $user->id,
            'name' => 'Deadlift & Back',
            'date' => '2026-10-10',
            'duration' => 50,
            'status' => 'completed',
        ]);

        $exercise = Exercise::firstOrCreate(
            ['name' => 'Deadlift'],
            ['category' => 'Back', 'muscle' => 'Posterior Chain']
        );

        WorkoutExercise::create([
            'workout_id' => $workout->id,
            'exercise_id' => $exercise->id,
            'weight' => 100,
            'sets' => 4,
            'reps' => 6,
        ]);

        $response = $this->actingAs($user)->getJson(route('workouts.calendarData', ['month' => '2026-10']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'month',
            'month_label',
            'total_completed',
            'workouts_by_date',
        ]);

        $this->assertEquals(1, $response->json('total_completed'));
        $this->assertArrayHasKey('2026-10-10', $response->json('workouts_by_date'));
        $dayWorkouts = $response->json('workouts_by_date.2026-10-10');
        $this->assertEquals('Deadlift & Back', $dayWorkouts[0]['name']);
        $this->assertEquals('Deadlift', $dayWorkouts[0]['exercises'][0]['name']);
    }

    public function test_workouts_index_renders_calendar_view(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('workouts.index'));

        $response->assertStatus(200);
        $response->assertSee('workout-calendar-card', false);
        $response->assertSee('calendar-month-heading', false);
        $response->assertSee('calendar-days-grid', false);
    }

    public function test_user_can_view_calendar_menu_page(): void
    {
        $user = User::factory()->create();

        Workout::create([
            'user_id' => $user->id,
            'name' => 'Squat Day',
            'date' => '2026-10-13',
            'duration' => 60,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user)->get(route('calendar.index'));

        $response->assertStatus(200);
        $response->assertSee('hevy-year-card', false);
        $response->assertSee('hevy-weeks-container', false);
        $response->assertSee('Squat Day');
    }
}
