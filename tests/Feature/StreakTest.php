<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workout;
use App\Services\StreakService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_streak_zero_when_no_workouts()
    {
        $user = User::factory()->create();
        $streak = StreakService::getStreakData($user->id);

        $this->assertEquals(0, $streak['current_streak']);
        $this->assertEquals('extinguished', $streak['status']);
        $this->assertEquals('cold', $streak['flame_level']);
    }

    public function test_streak_lit_when_workout_today()
    {
        $user = User::factory()->create();

        Workout::create([
            'user_id' => $user->id,
            'name' => 'Latihan Dada',
            'date' => Carbon::today()->format('Y-m-d'),
            'status' => 'completed',
            'duration' => 45,
        ]);

        $streak = StreakService::getStreakData($user->id);

        $this->assertEquals(1, $streak['current_streak']);
        $this->assertEquals('lit', $streak['status']);
        $this->assertEquals('warm', $streak['flame_level']);
        $this->assertTrue($streak['is_active_today']);
    }

    public function test_streak_warning_when_workout_yesterday_but_none_today()
    {
        $user = User::factory()->create();

        Workout::create([
            'user_id' => $user->id,
            'name' => 'Latihan Punggung',
            'date' => Carbon::yesterday()->format('Y-m-d'),
            'status' => 'completed',
            'duration' => 50,
        ]);

        $streak = StreakService::getStreakData($user->id);

        $this->assertEquals(1, $streak['current_streak']);
        $this->assertEquals('warning', $streak['status']);
        $this->assertFalse($streak['is_active_today']);
    }

    public function test_streak_accumulates_consecutive_days()
    {
        $user = User::factory()->create();

        // 3 consecutive days: today, yesterday, 2 days ago
        Workout::create([
            'user_id' => $user->id,
            'name' => 'Hari Ini',
            'date' => Carbon::today()->format('Y-m-d'),
            'status' => 'completed',
        ]);
        Workout::create([
            'user_id' => $user->id,
            'name' => 'Kemarin',
            'date' => Carbon::yesterday()->format('Y-m-d'),
            'status' => 'completed',
        ]);
        Workout::create([
            'user_id' => $user->id,
            'name' => '2 Hari Lalu',
            'date' => Carbon::today()->subDays(2)->format('Y-m-d'),
            'status' => 'completed',
        ]);

        $streak = StreakService::getStreakData($user->id);

        $this->assertEquals(3, $streak['current_streak']);
        $this->assertEquals(3, $streak['best_streak']);
        $this->assertEquals('lit', $streak['status']);
    }

    public function test_dashboard_renders_streak_information()
    {
        $user = User::factory()->create();

        Workout::create([
            'user_id' => $user->id,
            'name' => 'Latihan Hari Ini',
            'date' => Carbon::today()->format('Y-m-d'),
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('HARI STREAK');
        $response->assertSee('streak-section');
    }
}
