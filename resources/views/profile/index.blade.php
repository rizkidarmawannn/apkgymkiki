@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="page-header mb-4">
    <h1>Profil</h1>
</div>

<div class="card profile-card max-w-md mx-auto mb-4">
    <div class="card-body text-center p-4">
        <div class="profile-avatar mb-3">
            <div class="avatar-circle">
                {{ substr($user->name ?? 'U', 0, 1) }}
            </div>
        </div>
        <h2 class="mb-1">{{ $user->name ?? 'Nama Pengguna' }}</h2>
        <p class="text-muted mb-4">{{ $user->email ?? 'email@contoh.com' }}</p>

        <div class="grid-2-col bg-light p-3 rounded-lg mb-4" style="text-align: left;">
            <div class="stat-item">
                <small class="text-muted d-block">Usia</small>
                <strong>{{ $profile->age ?? $user->profile->age ?? '-' }} thn</strong>
            </div>
            <div class="stat-item">
                <small class="text-muted d-block">Tinggi</small>
                <strong>{{ $profile->height ?? $user->profile->height ?? '-' }} cm</strong>
            </div>
            <div class="stat-item">
                <small class="text-muted d-block">Berat Sekarang</small>
                <strong>{{ $profile->weight ?? $user->profile->weight ?? '-' }} kg</strong>
            </div>
            <div class="stat-item">
                <small class="text-muted d-block">Target Berat</small>
                <strong>{{ $profile->target_weight ?? $user->profile->target_weight ?? '-' }} kg</strong>
            </div>
        </div>

        <div class="goal-badge mb-4">
            <small class="text-muted d-block mb-1">Tujuan Kebugaran</small>
            @php
                $goalMap = [
                    'lose'           => 'Turun Berat Badan',
                    'lose_weight'    => 'Turun Berat Badan',
                    'gain'           => 'Naik Massa Otot',
                    'gain_muscle'    => 'Naik Massa Otot',
                    'maintain'       => 'Jaga Berat Badan',
                    'maintain_weight'=> 'Jaga Berat Badan',
                ];
                $currentGoal = $profile->goal ?? $user->profile->goal ?? 'maintain';
                $goalLabel = $goalMap[$currentGoal] ?? 'Jaga Berat Badan';
            @endphp
            <span class="badge badge-primary badge-lg px-3 py-2">{{ $goalLabel }}</span>
        </div>

        <div class="profile-actions d-flex flex-column flex-gap">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block">Edit Profil</a>

            <form action="{{ route('logout') }}" method="POST" class="w-100">
                @csrf
                <button type="submit" class="btn btn-outline btn-danger btn-block">Keluar</button>
            </form>
        </div>
    </div>
</div>
@endsection
