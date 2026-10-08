@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="page-header mb-4">
    <h1>Edit Profile</h1>
</div>

<div class="card max-w-md mx-auto mb-4">
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name ?? '') }}">
            </div>
            
            <div class="form-row">
                <div class="form-group col">
                    <label>Age</label>
                    <input type="number" name="age" class="form-control" value="{{ old('age', $user->age ?? '') }}">
                </div>
                <div class="form-group col">
                    <label>Height (cm)</label>
                    <input type="number" name="height" class="form-control" value="{{ old('height', $user->height ?? '') }}">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col">
                    <label>Current Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control" required value="{{ old('weight', $user->weight ?? '') }}">
                </div>
                <div class="form-group col">
                    <label>Target Weight (kg)</label>
                    <input type="number" step="0.1" name="target_weight" class="form-control" required value="{{ old('target_weight', $user->target_weight ?? '') }}">
                </div>
            </div>
            
            <div class="form-group">
                <label>Fitness Goal</label>
                <select name="fitness_goal" class="form-control" required>
                    <option value="lose" {{ (old('fitness_goal', $user->fitness_goal ?? '') == 'lose') ? 'selected' : '' }}>Lose Weight</option>
                    <option value="gain" {{ (old('fitness_goal', $user->fitness_goal ?? '') == 'gain') ? 'selected' : '' }}>Gain Muscle</option>
                    <option value="maintain" {{ (old('fitness_goal', $user->fitness_goal ?? '') == 'maintain') ? 'selected' : '' }}>Maintain Weight</option>
                </select>
            </div>
            
            <div class="form-actions mt-4 d-flex flex-gap">
                <button type="submit" class="btn btn-primary flex-1">Save Changes</button>
                <a href="{{ route('profile.index') }}" class="btn btn-outline flex-1 text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
