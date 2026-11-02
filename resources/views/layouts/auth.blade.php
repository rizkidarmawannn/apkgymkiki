<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Kiki — @yield('title', 'Masuk')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}?v={{ time() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v={{ time() }}">
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
        })();
    </script>
</head>
<body class="auth-body">
    <div style="position: absolute; top: 16px; right: 16px;">
        <button type="button" class="btn-theme-toggle" id="auth-theme-toggle" title="Ganti Mode Gelap/Terang">
            <span id="auth-theme-icon">🌙</span>
        </button>
    </div>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo-box">
                    <img src="{{ asset('images/logo-badge.png') }}?v={{ time() }}" alt="AGK GYM KIKI" class="auth-logo-img">
                </div>
                <h1 class="auth-title">AGK <span class="brand-accent">GYM KIKI</span></h1>
                <p class="auth-subtitle">Aplikasi Pelacak Gym &amp; Kebugaran</p>
            </div>
            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            @yield('content')
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('auth-theme-toggle');
            const icon = document.getElementById('auth-theme-icon');
            if (!toggleBtn || !icon) return;

            function updateIcon(theme) {
                icon.textContent = theme === 'dark' ? '☀️' : '🌙';
            }

            const current = document.documentElement.getAttribute('data-theme') || 'light';
            updateIcon(current);

            toggleBtn.addEventListener('click', function() {
                const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('gymtracker-theme', next);
                updateIcon(next);
            });
        });
    </script>
</body>
</html>
