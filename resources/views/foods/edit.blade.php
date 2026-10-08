@extends('layouts.app')

@section('title', 'Edit Food')

@section('content')
<div class="page-header mb-4">
    <h1>Edit Food</h1>
</div>

<div class="card max-w-md mx-auto">
    <div class="card-body">
        <form action="{{ route('foods.update', $food->id ?? 0) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Food Name</label>
                <input type="text" name="food_name" class="form-control" required value="{{ old('food_name', $food->food_name ?? '') }}">
            </div>
            
            <div class="form-group">
                <label>Meal</label>
                <select name="meal" class="form-control" required>
                    <option value="breakfast" {{ (old('meal', $food->meal ?? '') == 'breakfast') ? 'selected' : '' }}>Breakfast</option>
                    <option value="lunch" {{ (old('meal', $food->meal ?? '') == 'lunch') ? 'selected' : '' }}>Lunch</option>
                    <option value="dinner" {{ (old('meal', $food->meal ?? '') == 'dinner') ? 'selected' : '' }}>Dinner</option>
                    <option value="snack" {{ (old('meal', $food->meal ?? '') == 'snack') ? 'selected' : '' }}>Snack</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Portion</label>
                <input type="text" name="portion" class="form-control" required value="{{ old('portion', $food->portion ?? '') }}">
            </div>
            
            <div class="form-row">
                <div class="form-group col">
                    <label>Calories (kcal)</label>
                    <input type="number" name="calories" class="form-control" required value="{{ old('calories', $food->calories ?? 0) }}">
                </div>
                
                <div class="form-group col">
                    <label>Protein (g)</label>
                    <input type="number" step="0.1" name="protein" class="form-control" value="{{ old('protein', $food->protein ?? 0) }}">
                </div>
            </div>
            
            <div class="form-actions mt-4 d-flex flex-gap">
                <button type="submit" class="btn btn-primary flex-1">Save Changes</button>
                <a href="{{ route('foods.index') }}" class="btn btn-outline flex-1 text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
