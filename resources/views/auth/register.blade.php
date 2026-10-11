@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
<form method="POST" action="{{ route('register') }}" class="auth-form">
    @csrf

    <div class="form-group">
        <label for="name">Nama Lengkap</label>
        <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Nama kamu">
    </div>

    <div class="form-group">
        <label for="email">Alamat Email</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@kamu.com">
    </div>

    <div class="form-group">
        <label for="password">Kata Sandi</label>
        <input id="password" type="password" name="password" class="form-control" required placeholder="Min. 8 karakter">
    </div>

    <div class="form-group">
        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi kata sandi">
    </div>

    <button type="submit" class="btn btn-primary btn-block">Daftar</button>

    <div class="auth-links">
        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    </div>
</form>
@endsection
