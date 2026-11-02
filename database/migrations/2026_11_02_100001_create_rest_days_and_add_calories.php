<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create rest_days table
        if (!Schema::hasTable('rest_days')) {
            Schema::create('rest_days', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('date');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'date']);
            });
        }

        // 2. Add estimated_calories to workouts table
        if (Schema::hasTable('workouts') && !Schema::hasColumn('workouts', 'estimated_calories')) {
            Schema::table('workouts', function (Blueprint $table) {
                $table->integer('estimated_calories')->nullable()->after('duration_seconds');
            });
        }

        // 3. Insert Cardio exercises into exercises table if not exists
        $cardioExercises = [
            ['name' => 'Treadmill', 'category' => 'cardio', 'muscle' => 'cardiovascular'],
            ['name' => 'Sepeda Statis (Bike)', 'category' => 'cardio', 'muscle' => 'legs & cardio'],
            ['name' => 'Lari / Jogging', 'category' => 'cardio', 'muscle' => 'full body'],
            ['name' => 'Lompat Tali (Jump Rope)', 'category' => 'cardio', 'muscle' => 'calves & cardio'],
            ['name' => 'Elliptical Trainer', 'category' => 'cardio', 'muscle' => 'cardiovascular'],
            ['name' => 'Rowing Machine', 'category' => 'cardio', 'muscle' => 'back & cardio'],
            ['name' => 'Stair Climber', 'category' => 'cardio', 'muscle' => 'legs & glutes'],
            ['name' => 'Jalan Cepat (Brisk Walk)', 'category' => 'cardio', 'muscle' => 'cardiovascular'],
        ];

        foreach ($cardioExercises as $ce) {
            $exists = DB::table('exercises')->where('name', $ce['name'])->exists();
            if (!$exists) {
                DB::table('exercises')->insert(array_merge($ce, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rest_days');
        if (Schema::hasTable('workouts') && Schema::hasColumn('workouts', 'estimated_calories')) {
            Schema::table('workouts', function (Blueprint $table) {
                $table->dropColumn('estimated_calories');
            });
        }
    }
};
