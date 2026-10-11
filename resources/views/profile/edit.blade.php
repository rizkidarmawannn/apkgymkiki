@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="page-header mb-4">
    <h1>Edit Profil</h1>
</div>

<div class="card max-w-md mx-auto mb-4">
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name ?? '') }}">
            </div>

            <div class="form-row">
                <div class="form-group col">
                    <label>Usia</label>
                    <input type="number" name="age" class="form-control" value="{{ old('age', $profile->age ?? $user->profile->age ?? '') }}" placeholder="thn">
                </div>
                <div class="form-group col">
                    <label>Tinggi (cm)</label>
                    <input type="number" name="height" class="form-control" value="{{ old('height', $profile->height ?? $user->profile->height ?? '') }}" placeholder="cm">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col">
                    <label>Berat Sekarang (kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control" value="{{ old('weight', $profile->weight ?? $user->profile->weight ?? '') }}" placeholder="kg">
                </div>
                <div class="form-group col">
                    <label>Target Berat (kg)</label>
                    <input type="number" step="0.1" name="target_weight" class="form-control" value="{{ old('target_weight', $profile->target_weight ?? $user->profile->target_weight ?? '') }}" placeholder="kg">
                </div>
            </div>

            <div class="form-group">
                <label>Tujuan Kebugaran</label>
                @php
                    $selectedGoal = old('goal', old('fitness_goal', $profile->goal ?? $user->profile->goal ?? 'maintain'));
                    if ($selectedGoal === 'lose') $selectedGoal = 'lose_weight';
                    if ($selectedGoal === 'gain') $selectedGoal = 'gain_muscle';
                @endphp
                <select name="goal" class="form-control" required>
                    <option value="lose_weight" {{ $selectedGoal == 'lose_weight' ? 'selected' : '' }}>Turun Berat Badan</option>
                    <option value="gain_muscle" {{ $selectedGoal == 'gain_muscle' ? 'selected' : '' }}>Naik Massa Otot</option>
                    <option value="maintain"    {{ $selectedGoal == 'maintain'    ? 'selected' : '' }}>Jaga Berat Badan</option>
                </select>
            </div>

            <div class="form-actions mt-4 d-flex flex-gap">
                <button type="submit" class="btn btn-primary flex-1">Simpan Perubahan</button>
                <a href="{{ route('profile.index') }}" class="btn btn-outline flex-1 text-center">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
