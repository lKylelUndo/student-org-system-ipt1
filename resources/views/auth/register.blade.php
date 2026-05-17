@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="sos-container">
        <div class="mx-auto max-w-md">
            <h1 class="sos-page-title text-center">Create Account</h1>
            <p class="sos-page-subtitle text-center">Join the campus community and start connecting with organizations</p>

            <form action="{{ route('register') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="name" class="sos-label">Full Name</label>
                    <input type="text" id="name" name="name" class="sos-input" placeholder="Juan Dela Cruz" value="{{ old('name') }}" autocomplete="name" required>
                </div>
                <div>
                    <label for="email" class="sos-label">Email Address</label>
                    <input type="email" id="email" name="email" class="sos-input" placeholder="you@school.edu" value="{{ old('email') }}" autocomplete="email" required>
                </div>
                <div>
                    <label for="password" class="sos-label">Password</label>
                    <input type="password" id="password" name="password" class="sos-input" placeholder="Create a password" autocomplete="new-password" required>
                </div>
                <div>
                    <label for="password_confirmation" class="sos-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="sos-input" placeholder="Confirm your password" autocomplete="new-password" required>
                </div>
                <button type="submit" class="sos-btn-yellow w-full">Sign Up</button>
            </form>

            <p class="mt-6 text-center text-sm text-sos-blue/75">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-sos-blue hover:underline">Login</a>
            </p>
        </div>
    </div>
@endsection