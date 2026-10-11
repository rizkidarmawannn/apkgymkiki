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
    <script>
        (function() {
            const savedTheme = localStorage.getItem('gymtracker-theme') || 
                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
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
            <div class="d-flex align-center flex-gap-sm">
                <button type="button" class="btn-theme-toggle theme-toggle-btn" title="Toggle Dark/Light Mode">
                    <span class="theme-icon">🌙</span>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="nav-logout m-0">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
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
        <button type="button" class="bottom-nav-item theme-toggle-btn" style="background: none; border: none; cursor: pointer;">
            <span class="bottom-nav-icon theme-icon">🌙</span>
            <span class="bottom-nav-label">Theme</span>
        </button>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeIcons(currentTheme);

            document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', activeTheme);
                    localStorage.setItem('gymtracker-theme', activeTheme);
                    updateThemeIcons(activeTheme);
                });
            });

            function updateThemeIcons(theme) {
                document.querySelectorAll('.theme-icon').forEach(icon => {
                    icon.textContent = theme === 'dark' ? '☀️' : '🌙';
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
