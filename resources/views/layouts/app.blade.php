<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GymTracker - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Top Navigation (Desktop) -->
    <nav class="top-nav">
        <div class="nav-container">
            <a href="{{ route('dashboard') }}" class="nav-brand">💪 GymTracker</a>
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
                <a href="{{ route('workouts.index') }}" class="nav-link {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
                    <span class="nav-icon">🏋️</span> Workout
                </a>
                <a href="{{ route('foods.index') }}" class="nav-link {{ request()->routeIs('foods.*') ? 'active' : '' }}">
                    <span class="nav-icon">🍽️</span> Food
                </a>
                <a href="{{ route('progress.index') }}" class="nav-link {{ request()->routeIs('progress.*') ? 'active' : '' }}">
                    <span class="nav-icon">📈</span> Progress
                </a>
                <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span> Profile
                </a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="nav-logout">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Bottom Navigation (Mobile) -->
    <nav class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="bottom-nav-icon">📊</span>
            <span class="bottom-nav-label">Home</span>
        </a>
        <a href="{{ route('workouts.index') }}" class="bottom-nav-item {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon">🏋️</span>
            <span class="bottom-nav-label">Workout</span>
        </a>
        <a href="{{ route('foods.index') }}" class="bottom-nav-item {{ request()->routeIs('foods.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon">🍽️</span>
            <span class="bottom-nav-label">Food</span>
        </a>
        <a href="{{ route('progress.index') }}" class="bottom-nav-item {{ request()->routeIs('progress.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon">📈</span>
            <span class="bottom-nav-label">Progress</span>
        </a>
        <a href="{{ route('profile.index') }}" class="bottom-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon">👤</span>
            <span class="bottom-nav-label">Profile</span>
        </a>
    </nav>

    @stack('scripts')
</body>
</html>
