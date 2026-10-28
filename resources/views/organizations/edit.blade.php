@extends('layouts.authenticated')

@section('title', 'Edit Organization')

@section('content')
    <div class="sos-container">
        <div class="mx-auto max-w-lg">
            <a href="{{ route('organizations.show', $organization) }}" class="inline-flex items-center gap-1 text-sm font-medium text-sos-blue/70 hover:text-sos-blue">
                &larr; Back to Profile
            </a>
            <h1 class="sos-page-title mt-4">Edit Organization</h1>
            <p class="sos-page-subtitle">Update {{ $organization->org_name }} details</p>

            <form action="{{ route('organizations.update', $organization) }}" method="POST" class="mt-8 space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label for="org_name" class="sos-label">Organization Name</label>
                    <input type="text" id="org_name" name="org_name" class="sos-input" value="{{ old('org_name', $organization->org_name) }}" required>
                </div>
                <div>
                    <label for="description" class="sos-label">Description</label>
                    <textarea id="description" name="description" rows="5" class="sos-input resize-y" required>{{ old('description', $organization->description) }}</textarea>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="sos-btn-yellow flex-1">Save Changes</button>
                    <a href="{{ route('organizations.show', $organization) }}" class="sos-btn-outline flex-1 text-center">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection