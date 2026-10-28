@extends('layouts.authenticated')

@section('title', 'Organizations')

@section('content')
    <div class="sos-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="sos-page-title">Organizations</h1>
                <p class="sos-page-subtitle">Browse campus organizations and find your community</p>
            </div>
            <a href="{{ route('organizations.create') }}" class="sos-btn-yellow shrink-0">Create Organization</a>
        </div>

        <form method="GET" action="{{ route('organizations.index') }}" class="mt-8">
            <label for="org-search" class="sr-only">Search organizations</label>
            <input
                type="search"
                id="org-search"
                name="search"
                class="sos-input max-w-md"
                placeholder="Search organizations..."
                value="{{ $search }}"
            >
        </form>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-2" id="org-grid">
            @forelse ($organizations as $org)
                @php
                    $membership = $userMemberships->get($org->id);
                @endphp
                <article class="sos-card flex flex-col">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-xl font-bold text-sos-blue">{{ $org->org_name }}</h2>
                        <span class="sos-badge-blue shrink-0">{{ $org->members_count }} members</span>
                    </div>
                    <p class="mt-3 flex-1 text-sm leading-relaxed text-sos-blue/75">{{ Str::limit($org->description, 160) }}</p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('organizations.show', $org) }}" class="sos-btn-outline text-sm">View Profile</a>
                        @if ($membership)
                            @if ($membership->status->value === 'pending')
                                <span class="sos-badge bg-amber-100 text-amber-800">Pending</span>
                            @elseif ($membership->status->value === 'active')
                                <span class="sos-badge bg-green-100 text-green-800">Member</span>
                            @else
                                <span class="sos-badge bg-red-100 text-red-800">Suspended</span>
                            @endif
                        @else
                            <form action="{{ route('organizations.join', $org) }}" method="POST">
                                @csrf
                                <button type="submit" class="sos-btn-yellow text-sm">Join</button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <p class="col-span-2 py-12 text-center text-sos-blue/60">No organizations found. Create the first one!</p>
            @endforelse
        </div>
    </div>
@endsection