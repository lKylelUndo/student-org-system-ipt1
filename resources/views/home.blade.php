@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <section class="sos-container text-center">
        <h1 class="text-4xl font-bold text-sos-blue sm:text-5xl lg:text-6xl">Student Organization System</h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-sos-blue/80">
            Connect with campus organizations, discover new opportunities, and build your future together.
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a href="{{ url('/register') }}" class="sos-btn-outline min-w-[140px]">Join Now</a>
            <a href="{{ auth()->check() ? route('organizations.index') : route('login') }}" class="sos-btn-yellow min-w-[140px]">Explore</a>
        </div>

        <div class="sos-hero-image mt-12 overflow-hidden">
            <img
                src="{{ asset('images/hero.png') }}"
                alt="Students collaborating in a campus organization meeting"
                class="h-auto w-full object-cover"
            >
        </div>
    </section>
@endsection