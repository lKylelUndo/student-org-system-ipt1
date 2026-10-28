@extends('layouts.authenticated')

@section('title', 'Create Organization')

@section('content')
    <div class="sos-container">
        <div class="mx-auto max-w-lg">
            <a href="{{ route('organizations.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-sos-blue/70 hover:text-sos-blue">
                &larr; Back to Organizations
            </a>
            <h1 class="sos-page-title mt-4">Create Organization</h1>
            <p class="sos-page-subtitle">Start a new student organization on campus</p>

            <form action="{{ route('organizations.store') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="org_name" class="sos-label">Organization Name</label>
                    <input type="text" id="org_name" name="org_name" class="sos-input" placeholder="e.g. Robotics Club" value="{{ old('org_name') }}" required>
                </div>
                <div>
                    <label for="description" class="sos-label">Description</label>
                    <textarea id="description" name="description" rows="5" class="sos-input resize-y" placeholder="Describe your organization's mission and activities..." required>{{ old('description') }}</textarea>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="sos-btn-yellow flex-1">Create Organization</button>
                    <a href="{{ route('organizations.index') }}" class="sos-btn-outline flex-1 text-center">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection