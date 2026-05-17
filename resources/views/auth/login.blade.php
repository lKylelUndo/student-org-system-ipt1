@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="sos-container">
        <div class="mx-auto max-w-md">
            <h1 class="sos-page-title text-center">Welcome Back</h1>
            <p class="sos-page-subtitle text-center">Sign in to your Student Organization System account</p>

            <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="email" class="sos-label">Email Address</label>
                    <input type="email" id="email" name="email" class="sos-input" placeholder="you@school.edu" value="{{ old('email', 'demo@student.edu') }}" autocomplete="email" required>
                </div>
                <div>
                    <label for="password" class="sos-label">Password</label>
                    <input type="password" id="password" name="password" class="sos-input" placeholder="Enter your password" autocomplete="current-password" required>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-sos-blue/80">
                        <input type="checkbox" name="remember" class="rounded border-sos-blue/30 text-sos-yellow focus:ring-sos-yellow">
                        Remember me
                    </label>
                </div>
                <button type="submit" class="sos-btn-yellow w-full">Login</button>
            </form>

            <p class="mt-6 text-center text-sm text-sos-blue/75">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-semibold text-sos-blue hover:underline">Register</a>
            </p>
        </div>
    </div>
@endsection