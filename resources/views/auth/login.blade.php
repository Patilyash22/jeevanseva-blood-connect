
@extends('layouts.app')

@section('content')
<section class="login-section">
    <div class="container">
        <div class="auth-container">
            <h1>Login to JeevanSeva</h1>
            <p class="section-intro">Access your account to find donors or manage your donations.</p>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('status'))
                <div class="alert alert-{{ session('status_type', 'success') }}">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group form-remember">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account yet?</p>
                <div class="auth-options">
                    <a href="{{ route('register', ['type' => 'recipient']) }}" class="btn btn-outline">Register as Recipient</a>
                    <a href="{{ route('donor.register') }}" class="btn btn-outline">Register as Donor</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
