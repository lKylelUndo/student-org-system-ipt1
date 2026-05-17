@extends('layouts.app')

@section('title', 'Profile Setup')

@section('content')
    <div class="sos-container">
        <div class="mx-auto max-w-lg">
            <span class="sos-badge-yellow">Step 2 of 2</span>
            <h1 class="sos-page-title mt-3">Complete Your Profile</h1>
            <p class="sos-page-subtitle">Tell us a bit about yourself to personalize your organization experience.</p>

            <form action="{{ route('profile.setup') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="student_id" class="sos-label">Student ID</label>
                    <input type="text" id="student_id" name="student_id" class="sos-input" placeholder="2024-00001" value="{{ old('student_id', $user->student_id) }}" required>
                </div>
                <div>
                    <label for="course" class="sos-label">Course / Program</label>
                    <input type="text" id="course" name="course" class="sos-input" placeholder="BS Computer Science" value="{{ old('course', $user->course) }}" required>
                </div>
                <div>
                    <label for="year_level" class="sos-label">Year Level</label>
                    <select id="year_level" name="year_level" class="sos-input" required>
                        <option value="">Select year level</option>
                        @foreach ([1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('year_level', $user->year_level) == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bio" class="sos-label">Short Bio</label>
                    <textarea id="bio" name="bio" rows="4" class="sos-input resize-y" placeholder="Share your interests and goals...">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <button type="submit" class="sos-btn-yellow w-full">Save &amp; Continue</button>
            </form>
        </div>
    </div>
@endsection