@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<form method="POST" action="{{ route('login') }}" class="auth-form">
    @csrf

    <div class="form-group">
        <label for="email">Alamat Email</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="email@kamu.com">
    </div>

    <div class="form-group">
        <label for="password">Kata Sandi</label>
        <input id="password" type="password" name="password" class="form-control" required placeholder="••••••••">
    </div>

    <div class="form-group form-check">
        <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
        <label for="remember" class="form-check-label">Ingat Saya</label>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Masuk</button>

    <div class="auth-links">
        <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
    </div>
</form>
@endsection
