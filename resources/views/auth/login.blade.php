@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<form method="POST" action="{{ route('login') }}" class="auth-form">
    @csrf
    
    <div class="form-group">
        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" class="form-control" required>
    </div>

    <div class="form-group form-check">
        <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
        <label for="remember" class="form-check-label">Remember Me</label>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Login</button>
    
    <div class="auth-links">
        <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
    </div>
</form>
@endsection
