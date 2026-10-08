@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<form method="POST" action="{{ route('register') }}" class="auth-form">
    @csrf
    
    <div class="form-group">
        <label for="name">Full Name</label>
        <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Register</button>
    
    <div class="auth-links">
        <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
    </div>
</form>
@endsection
