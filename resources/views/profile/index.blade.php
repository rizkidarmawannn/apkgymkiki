@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="page-header mb-4">
    <h1>Profile</h1>
</div>

<div class="card profile-card max-w-md mx-auto mb-4">
    <div class="card-body text-center p-4">
        <div class="profile-avatar mb-3">
            <div class="avatar-circle">
                {{ substr($user->name ?? 'U', 0, 1) }}
            </div>
        </div>
        <h2 class="mb-1">{{ $user->name ?? 'User Name' }}</h2>
        <p class="text-muted mb-4">{{ $user->email ?? 'email@example.com' }}</p>
        
        <div class="profile-stats-grid grid-2-col text-left bg-light p-3 rounded-lg mb-4">
            <div class="stat-item">
                <small class="text-muted d-block">Age</small>
                <strong>{{ $user->age ?? '-' }} yrs</strong>
            </div>
            <div class="stat-item">
                <small class="text-muted d-block">Height</small>
                <strong>{{ $user->height ?? '-' }} cm</strong>
            </div>
            <div class="stat-item">
                <small class="text-muted d-block">Current Weight</small>
                <strong>{{ $user->weight ?? '-' }} kg</strong>
            </div>
            <div class="stat-item">
                <small class="text-muted d-block">Target Weight</small>
                <strong>{{ $user->target_weight ?? '-' }} kg</strong>
            </div>
        </div>
        
        <div class="goal-badge mb-4">
            <small class="text-muted d-block mb-1">Fitness Goal</small>
            @php 
                $goalMap = [
                    'lose' => 'Lose Weight',
                    'gain' => 'Gain Muscle',
                    'maintain' => 'Maintain Weight'
                ];
                $goalLabel = $goalMap[$user->fitness_goal ?? 'maintain'] ?? 'Maintain Weight';
            @endphp
            <span class="badge badge-primary badge-lg px-3 py-2 text-md">{{ $goalLabel }}</span>
        </div>
        
        <div class="profile-actions d-flex flex-column flex-gap">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block">Edit Profile</a>
            
            <form action="{{ route('logout') }}" method="POST" class="w-100">
                @csrf
                <button type="submit" class="btn btn-outline btn-danger btn-block">Logout</button>
            </form>
        </div>
    </div>
</div>
@endsection
