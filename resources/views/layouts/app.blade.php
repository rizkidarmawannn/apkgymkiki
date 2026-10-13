<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GymTracker — @yield('title', 'Beranda')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <!-- Top Navigation (Desktop) -->
    <nav class="top-nav">
        <div class="nav-container">
            <a href="{{ route('dashboard') }}" class="nav-brand">💪 GymTracker</a>
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> <span data-i18n="nav_home">Beranda</span>
                </a>
                <a href="{{ route('workouts.index') }}" class="nav-link {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
                    <span class="nav-icon">🏋️</span> <span data-i18n="nav_workout">Latihan</span>
                </a>
                <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                    <span class="nav-icon">📅</span> <span data-i18n="nav_calendar">Kalender</span>
                </a>
                <a href="{{ route('foods.index') }}" class="nav-link {{ request()->routeIs('foods.*') ? 'active' : '' }}">
                    <span class="nav-icon">🍽️</span> <span data-i18n="nav_food">Makanan</span>
                </a>
                <a href="{{ route('progress.index') }}" class="nav-link {{ request()->routeIs('progress.*') ? 'active' : '' }}">
                    <span class="nav-icon">📈</span> <span data-i18n="nav_progress">Progres</span>
                </a>
                <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span> <span data-i18n="nav_profile">Profil</span>
                </a>
            </div>
            <div class="d-flex align-center flex-gap-sm">
                <!-- Settings Button (Theme, Color, Language) -->
                <button type="button" class="btn-theme-toggle open-settings-btn" title="Pengaturan Tampilan & Bahasa">
                    <span>⚙️</span>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="nav-logout m-0">
                    @csrf
                    <button type="submit" class="btn-logout" data-i18n="nav_logout">Keluar</button>
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
            <span class="bottom-nav-icon">📊</span>
            <span class="bottom-nav-label" data-i18n="nav_home">Beranda</span>
        </a>
        <a href="{{ route('workouts.index') }}" class="bottom-nav-item {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon">🏋️</span>
            <span class="bottom-nav-label" data-i18n="nav_workout">Latihan</span>
        </a>
        <a href="{{ route('calendar.index') }}" class="bottom-nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon">📅</span>
            <span class="bottom-nav-label" data-i18n="nav_calendar">Kalender</span>
        </a>
        <a href="{{ route('foods.index') }}" class="bottom-nav-item {{ request()->routeIs('foods.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon">🍽️</span>
            <span class="bottom-nav-label" data-i18n="nav_food">Makanan</span>
        </a>
        <a href="{{ route('progress.index') }}" class="bottom-nav-item {{ request()->routeIs('progress.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon">📈</span>
            <span class="bottom-nav-label" data-i18n="nav_progress">Progres</span>
        </a>
        <a href="{{ route('profile.index') }}" class="bottom-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="bottom-nav-icon">👤</span>
            <span class="bottom-nav-label" data-i18n="nav_profile">Profil</span>
        </a>
        <!-- Pengaturan (menggantikan theme button tunggal) -->
        <button type="button" class="bottom-nav-item open-settings-btn" style="background: none; border: none; cursor: pointer;">
            <span class="bottom-nav-icon">⚙️</span>
            <span class="bottom-nav-label" data-i18n="nav_settings">Pengaturan</span>
        </button>
    </nav>

    <!-- Modal Pengaturan (Tema, Warna, Bahasa) -->
    <div id="settings-modal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3 data-i18n="settings_title">⚙️ Pengaturan Tampilan</h3>
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
