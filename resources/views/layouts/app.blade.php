<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gym Kiki — @yield('title', 'Beranda')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('favicon-192.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('gymtracker-theme') ||
                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', savedTheme);

            const savedColor = localStorage.getItem('gymtracker-color') || 'blue';
            if (savedColor !== 'blue') {
                document.documentElement.setAttribute('data-color', savedColor);
            }

            const savedLang = localStorage.getItem('gymtracker-lang') || 'id';
            document.documentElement.setAttribute('lang', savedLang);
        })();
    </script>
    @stack('styles')
</head>
<body>
    <!-- Top Navigation (Desktop & Mobile Brand Bar) -->
    <nav class="top-nav">
        <div class="nav-container">
            <a href="{{ route('dashboard') }}" class="nav-brand">
                <img src="{{ asset('images/logo-icon-transparent.png') }}" alt="GYM KIKI" class="nav-brand-logo">
                <span class="nav-brand-text">GYM <span class="brand-accent">KIKI</span></span>
            </a>
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span data-i18n="nav_home">Beranda</span>
                </a>
                <a href="{{ route('workouts.index') }}" class="nav-link {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
                    <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m6.5 6.5 11 11"/><path d="m21 21-1-1"/><path d="m3 3 1 1"/><path d="m18 22 4-4"/><path d="m2 6 4-4"/><path d="m3 10 7-7"/><path d="m14 21 7-7"/></svg>
                    <span data-i18n="nav_workout">Latihan</span>
                </a>
                <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                    <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><circle cx="8" cy="14" r="1" fill="currentColor"/><circle cx="12" cy="14" r="1" fill="currentColor"/><circle cx="16" cy="14" r="1" fill="currentColor"/></svg>
                    <span data-i18n="nav_calendar">Kalender</span>
                </a>
                <a href="{{ route('foods.index') }}" class="nav-link {{ request()->routeIs('foods.*') ? 'active' : '' }}">
                    <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v20"/><path d="M18 7c1.66 0 3-1.34 3-3V2h-3v5Z"/><path d="M6 2v6a3 3 0 0 0 6 0V2"/><line x1="9" x2="9" y1="8" y2="22"/></svg>
                    <span data-i18n="nav_food">Makanan</span>
                </a>
                <a href="{{ route('progress.index') }}" class="nav-link {{ request()->routeIs('progress.*') ? 'active' : '' }}">
                    <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                    <span data-i18n="nav_progress">Progres</span>
                </a>
                <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span data-i18n="nav_profile">Profil</span>
                </a>
            </div>
            <div class="d-flex align-center flex-gap-sm">
                @if(isset($streakData))
                    <a href="{{ route('dashboard') }}#streak-section" class="streak-pill streak-pill-{{ $streakData['status'] }}" title="Streak Konsistensi: {{ $streakData['current_streak'] }} Hari ({{ $streakData['flame_title'] }})">
                        <span class="streak-flame-icon">{{ $streakData['status'] === 'extinguished' ? '🕯️' : '🔥' }}</span>
                        <span class="streak-count">{{ $streakData['current_streak'] }}</span>
                    </a>
                @endif
                <!-- Settings Button (Theme, Color, Language) -->
                <button type="button" class="btn-theme-toggle open-settings-btn" title="Pengaturan Tampilan & Bahasa">
                    <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="nav-logout m-0">
                    @csrf
                    <button type="submit" class="btn-logout" data-i18n="nav_logout">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 13px; height: 13px; vertical-align: -1px; margin-right: 3px; display: inline-block;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if(isset($activeWorkout) && $activeWorkout && !request()->routeIs('workouts.create') && !request()->routeIs('workouts.resume'))
            <div class="ongoing-workout-floating-banner">
                <div class="ongoing-banner-content">
                    <span class="ongoing-pulse-dot"></span>
                    <div>
                        <strong class="d-block" style="font-size: 0.95rem;">⚡ Sesi Latihan Sedang Berjalan: {{ $activeWorkout->name }}</strong>
                        <small class="text-muted">{{ $activeWorkout->workoutExercises->count() }} gerakan dicatat &bull; Progres otomatis tersimpan</small>
                    </div>
                </div>
                <div class="ongoing-banner-action">
                    <a href="{{ route('workouts.resume', $activeWorkout) }}" class="btn btn-warning btn-small" style="font-weight: 700; border-radius: 9999px;">
                        ▶️ Lanjutkan Latihan
                    </a>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
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
            <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span class="bottom-nav-label" data-i18n="nav_home">Beranda</span>
        </a>
        <a href="{{ route('workouts.index') }}" class="bottom-nav-item {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
            <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m6.5 6.5 11 11"/><path d="m21 21-1-1"/><path d="m3 3 1 1"/><path d="m18 22 4-4"/><path d="m2 6 4-4"/><path d="m3 10 7-7"/><path d="m14 21 7-7"/></svg>
            <span class="bottom-nav-label" data-i18n="nav_workout">Latihan</span>
        </a>
        <a href="{{ route('calendar.index') }}" class="bottom-nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
            <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><circle cx="8" cy="14" r="1" fill="currentColor"/><circle cx="12" cy="14" r="1" fill="currentColor"/><circle cx="16" cy="14" r="1" fill="currentColor"/></svg>
            <span class="bottom-nav-label" data-i18n="nav_calendar">Kalender</span>
        </a>
        <a href="{{ route('foods.index') }}" class="bottom-nav-item {{ request()->routeIs('foods.*') ? 'active' : '' }}">
            <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v20"/><path d="M18 7c1.66 0 3-1.34 3-3V2h-3v5Z"/><path d="M6 2v6a3 3 0 0 0 6 0V2"/><line x1="9" x2="9" y1="8" y2="22"/></svg>
            <span class="bottom-nav-label" data-i18n="nav_food">Makanan</span>
        </a>
        <a href="{{ route('progress.index') }}" class="bottom-nav-item {{ request()->routeIs('progress.*') ? 'active' : '' }}">
            <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
            <span class="bottom-nav-label" data-i18n="nav_progress">Progres</span>
        </a>
        <a href="{{ route('profile.index') }}" class="bottom-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span class="bottom-nav-label" data-i18n="nav_profile">Profil</span>
        </a>
        <!-- Pengaturan -->
        <button type="button" class="bottom-nav-item open-settings-btn" style="background: none; border: none; cursor: pointer;">
            <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span class="bottom-nav-label" data-i18n="nav_settings">Pengaturan</span>
        </button>
    </nav>

    <!-- Modal Pengaturan (Tema, Warna, Bahasa) -->
    <div id="settings-modal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3 data-i18n="settings_title" style="display: flex; align-items: center; gap: 8px;">
                    <svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>Pengaturan Tampilan</span>
                </h3>
                <button type="button" class="modal-close-btn" id="close-settings-btn">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Group 1: Mode Tema (Gelap / Terang) -->
                <div class="settings-group">
                    <div class="settings-title">
                        <span>🌓</span> <span data-i18n="settings_mode">Mode Tampilan</span>
                    </div>
                    <div class="segmented-control">
                        <button type="button" class="segmented-btn" data-theme-val="light" id="theme-btn-light">
                            <span>☀️</span> <span data-i18n="settings_light">Terang</span>
                        </button>
                        <button type="button" class="segmented-btn" data-theme-val="dark" id="theme-btn-dark">
                            <span>🌙</span> <span data-i18n="settings_dark">Gelap</span>
                        </button>
                    </div>
                </div>

                <!-- Group 2: Pilihan Warna Tema -->
                <div class="settings-group">
                    <div class="settings-title">
                        <span>🎨</span> <span data-i18n="settings_color">Warna Aksen</span>
                    </div>
                    <div class="color-picker-grid">
                        <button type="button" class="color-swatch-btn" data-color-val="blue">
                            <span class="color-dot" style="background: #2563EB;"></span>
                            <span class="color-name">Biru</span>
                        </button>
                        <button type="button" class="color-swatch-btn" data-color-val="emerald">
                            <span class="color-dot" style="background: #059669;"></span>
                            <span class="color-name">Hijau</span>
                        </button>
                        <button type="button" class="color-swatch-btn" data-color-val="purple">
                            <span class="color-dot" style="background: #7C3AED;"></span>
                            <span class="color-name">Ungu</span>
                        </button>
                        <button type="button" class="color-swatch-btn" data-color-val="orange">
                            <span class="color-dot" style="background: #EA580C;"></span>
                            <span class="color-name">Oranye</span>
                        </button>
                        <button type="button" class="color-swatch-btn" data-color-val="rose">
                            <span class="color-dot" style="background: #E11D48;"></span>
                            <span class="color-name">Merah</span>
                        </button>
                    </div>
                </div>

                <!-- Group 3: Pilihan Bahasa -->
                <div class="settings-group">
                    <div class="settings-title">
                        <span>🌐</span> <span data-i18n="settings_lang">Bahasa / Language</span>
                    </div>
                    <div class="language-control">
                        <button type="button" class="segmented-btn" data-lang-val="id" id="lang-btn-id">
                            <span>🇮🇩</span> <span>Indonesia</span>
                        </button>
                        <button type="button" class="segmented-btn" data-lang-val="en" id="lang-btn-en">
                            <span>🇬🇧</span> <span>English</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Pengaturan Tema, Warna & Kamus Bahasa -->
    <script>
        const i18nDictionary = {
            id: {
                nav_home: "Beranda",
                nav_workout: "Latihan",
                nav_calendar: "Kalender",
                nav_food: "Makanan",
                nav_progress: "Progres",
                nav_profile: "Profil",
                nav_settings: "Pengaturan",
                nav_logout: "Keluar",
                settings_title: "⚙️ Pengaturan Tampilan",
                settings_mode: "Mode Tampilan",
                settings_light: "Terang",
                settings_dark: "Gelap",
                settings_color: "Warna Aksen",
                settings_lang: "Bahasa / Language"
            },
            en: {
                nav_home: "Home",
                nav_workout: "Workout",
                nav_calendar: "Calendar",
                nav_food: "Food",
                nav_progress: "Progress",
                nav_profile: "Profile",
                nav_settings: "Settings",
                nav_logout: "Logout",
                settings_title: "⚙️ Appearance & Language",
                settings_mode: "Theme Mode",
                settings_light: "Light",
                settings_dark: "Dark",
                settings_color: "Accent Color",
                settings_lang: "Language / Bahasa"
            }
        };

        function applyLanguage(lang) {
            localStorage.setItem('gymtracker-lang', lang);
            document.documentElement.setAttribute('lang', lang);

            const dict = i18nDictionary[lang] || i18nDictionary.id;
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (dict[key]) {
                    el.textContent = dict[key];
                }
            });

            // Update active state di modal
            document.querySelectorAll('#lang-btn-id, #lang-btn-en').forEach(btn => {
                if (btn.getAttribute('data-lang-val') === lang) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        function applyTheme(theme) {
            localStorage.setItem('gymtracker-theme', theme);
            document.documentElement.setAttribute('data-theme', theme);

            document.querySelectorAll('#theme-btn-light, #theme-btn-dark').forEach(btn => {
                if (btn.getAttribute('data-theme-val') === theme) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        function applyColor(color) {
            localStorage.setItem('gymtracker-color', color);
            if (color === 'blue') {
                document.documentElement.removeAttribute('data-color');
            } else {
                document.documentElement.setAttribute('data-color', color);
            }

            document.querySelectorAll('.color-swatch-btn').forEach(btn => {
                if (btn.getAttribute('data-color-val') === color) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi status aktif dari localStorage
            const curTheme = document.documentElement.getAttribute('data-theme') || 'light';
            applyTheme(curTheme);

            const curColor = localStorage.getItem('gymtracker-color') || 'blue';
            applyColor(curColor);

            const curLang = localStorage.getItem('gymtracker-lang') || 'id';
            applyLanguage(curLang);

            // Handler Buka & Tutup Modal
            const modal = document.getElementById('settings-modal');
            const closeBtn = document.getElementById('close-settings-btn');

            document.querySelectorAll('.open-settings-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    modal.classList.add('active');
                });
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.classList.remove('active');
                });
            }

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });

            // Klik Ubah Tema (Terang / Gelap)
            document.querySelectorAll('#theme-btn-light, #theme-btn-dark').forEach(btn => {
                btn.addEventListener('click', function() {
                    applyTheme(this.getAttribute('data-theme-val'));
                });
            });

            // Klik Ubah Warna Aksen
            document.querySelectorAll('.color-swatch-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    applyColor(this.getAttribute('data-color-val'));
                });
            });

            // Klik Ubah Bahasa
            document.querySelectorAll('#lang-btn-id, #lang-btn-en').forEach(btn => {
                btn.addEventListener('click', function() {
                    applyLanguage(this.getAttribute('data-lang-val'));
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
