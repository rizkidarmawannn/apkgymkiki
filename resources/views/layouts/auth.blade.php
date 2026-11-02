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
        <button type="button" class="btn-theme-toggle" id="auth-theme-toggle" title="Ganti Mode Gelap/Terang" style="display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background: var(--bg-card); border: 1px solid var(--border); color: var(--text-secondary); cursor: pointer;">
            <span id="auth-theme-icon" style="display: inline-flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </span>
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
                if (theme === 'dark') {
                    icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
                } else {
                    icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
                }
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
