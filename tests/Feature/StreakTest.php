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

    public function test_rest_day_preserves_streak()
    {
        $user = User::factory()->create();

        // Workout yesterday + Rest Day today => streak is 2 and status is 'rest'
        Workout::create([
            'user_id' => $user->id,
            'name' => 'Kemarin',
            'date' => Carbon::yesterday()->format('Y-m-d'),
            'status' => 'completed',
        ]);

        \App\Models\RestDay::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d'),
        ]);

        $streak = StreakService::getStreakData($user->id);

        $this->assertEquals(2, $streak['current_streak']);
        $this->assertEquals('rest', $streak['status']);
        $this->assertTrue($streak['is_rest_today']);
    }

    public function test_toggle_rest_day_route()
    {
        $user = User::factory()->create();
        $today = Carbon::today()->format('Y-m-d');

        // Toggle ON
        $response = $this->actingAs($user)->postJson(route('rest-days.toggle'), [
            'date' => $today,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_rest_day' => true]);
        $this->assertEquals(1, \App\Models\RestDay::where('user_id', $user->id)->count());

        // Toggle OFF
        $response2 = $this->actingAs($user)->postJson(route('rest-days.toggle'), [
            'date' => $today,
        ]);

        $response2->assertStatus(200);
        $response2->assertJson(['success' => true, 'is_rest_day' => false]);
        $this->assertEquals(0, \App\Models\RestDay::where('user_id', $user->id)->count());
    }
}
