@extends('layouts.app')

@section('title', 'Food')

@section('content')
@php
    $date = request('date', date('Y-m-d'));
    $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));
    $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));
    $displayDate = (date('Y-m-d') == $date) ? 'Today' : date('M d, Y', strtotime($date));
    
    $totalCal = $totalCalories ?? 0;
    $targetCal = $calorieTarget ?? 2000;
    $remainingCal = $targetCal - $totalCal;
    $pct = $targetCal > 0 ? min(100, ($totalCal / $targetCal) * 100) : 0;
    $progressColor = $totalCal > $targetCal ? 'progress-danger' : 'progress-success';
@endphp

<div class="page-header flex-between align-center mb-4">
    <h1>Food Diary</h1>
    <div class="date-navigation flex-center">
        <a href="{{ route('foods.index', ['date' => $prevDate]) }}" class="btn btn-outline btn-small">&lt;</a>
        <span class="date-display mx-3 fw-bold">{{ $displayDate }}</span>
        <a href="{{ route('foods.index', ['date' => $nextDate]) }}" class="btn btn-outline btn-small">&gt;</a>
    </div>
</div>

<div class="card calorie-summary-card mb-4">
    <div class="card-body">
        <div class="summary-stats d-flex justify-content-between mb-3">
            <div class="stat-item text-center">
                <small class="text-muted d-block">Today's Calories</small>
                <strong class="text-xl">{{ number_format($totalCal) }}</strong>
            </div>
            <div class="stat-item text-center">
                <small class="text-muted d-block">Target</small>
                <strong class="text-xl">{{ number_format($targetCal) }}</strong>
            </div>
            <div class="stat-item text-center">
                <small class="text-muted d-block">Remaining</small>
                <strong class="text-xl {{ $remainingCal < 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format(abs($remainingCal)) }} {{ $remainingCal < 0 ? 'over' : '' }}
                </strong>
            </div>
        </div>
        
        <div class="progress-container mb-2">
            <div class="progress-bar">
                <div class="progress-fill {{ $progressColor }}" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        <div class="text-center mt-2">
            <small class="text-muted">Total Protein: <strong>{{ number_format($totalProtein ?? 0, 1) }}g</strong></small>
        </div>
    </div>
</div>

<div class="card add-food-card mb-4">
    <div class="card-header">
        <h3 class="m-0">Add Food</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('foods.store') }}" method="POST" class="add-food-form">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            
            <div class="form-row">
                <div class="form-group col-12 col-md-4">
                    <label>Food Name</label>
                    <input type="text" name="food_name" class="form-control" required placeholder="e.g. Oatmeal">
                </div>
                <div class="form-group col-12 col-md-3">
                    <label>Meal</label>
                    <select name="meal" class="form-control" required>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>
                <div class="form-group col-4 col-md-2">
                    <label>Portion</label>
                    <input type="text" name="portion" class="form-control" placeholder="1 bowl" required>
                </div>
                <div class="form-group col-4 col-md-2">
                    <label>Calories</label>
                    <input type="number" name="calories" class="form-control" required placeholder="kcal">
                </div>
                <div class="form-group col-4 col-md-2">
                    <label>Protein (g)</label>
                    <input type="number" step="0.1" name="protein" class="form-control" placeholder="g">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2">+ Add Food</button>
        </form>
    </div>
</div>

<div class="food-log">
    @foreach(['breakfast', 'lunch', 'dinner', 'snack'] as $mealType)
        <div class="meal-section card mb-3">
            <div class="card-header flex-between bg-light">
                <h3 class="m-0 text-capitalize">{{ $mealType }}</h3>
                @php 
                    $mealFoods = isset($foodLogs[$mealType]) ? $foodLogs[$mealType] : collect(); 
                    $mealCal = $mealFoods->sum('calories');
                @endphp
                <span class="badge badge-secondary">{{ number_format($mealCal) }} kcal</span>
            </div>
            <div class="card-body p-0">
                @if($mealFoods->count() > 0)
                    <ul class="food-list list-group list-group-flush">
                        @foreach($mealFoods as $food)
                            <li class="list-group-item flex-between align-center p-3">
                                <div class="food-info">
                                    <h4 class="m-0 text-md">{{ $food->food_name }}</h4>
                                    <small class="text-muted">{{ $food->portion }} &bull; {{ $food->protein ?? 0 }}g protein</small>
                                </div>
                                <div class="food-actions d-flex align-center flex-gap">
                                    <span class="fw-bold mr-3">{{ $food->calories }} kcal</span>
                                    <a href="{{ route('foods.edit', $food) }}" class="btn btn-small btn-outline">Edit</a>
                                    <form action="{{ route('foods.destroy', $food) }}" method="POST" onsubmit="return confirm('Delete this food?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-small btn-danger">&times;</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-3 text-center text-muted">
                        <small>No food logged for {{ $mealType }}</small>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
