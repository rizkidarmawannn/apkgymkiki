<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymTracker — @yield('title', 'Masuk')</title>
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
                <h1>💪 GymTracker</h1>
                <p>Pelacak Gym &amp; Kesehatan Pribadimu</p>
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
