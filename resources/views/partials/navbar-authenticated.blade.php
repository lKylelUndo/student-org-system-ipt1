@php
    $organizationsUrl = Route::has('organizations.index') ? route('organizations.index') : url('/organizations');
    $membersUrl = Route::has('members.index') ? route('members.index') : url('/members');
    $homeUrl = Route::has('dashboard') ? route('dashboard') : url('/');
@endphp

<header class="relative border-b border-sos-blue/10 bg-white">
    <nav class="sos-container flex flex-wrap items-center justify-between gap-4 py-4">
        <a href="{{ $homeUrl }}" class="text-2xl font-bold tracking-tight text-sos-blue">SOS</a>

        <button type="button" id="nav-toggle" class="inline-flex items-center justify-center rounded-md p-2 text-sos-blue hover:bg-sos-blue/5 lg:hidden" aria-label="Toggle navigation" aria-expanded="false">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path id="nav-icon-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path id="nav-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <ul class="hidden flex-1 items-center justify-center gap-8 lg:flex" id="nav-links-desktop">
            <li>
                <a href="{{ $organizationsUrl }}" class="sos-nav-link {{ request()->is('organizations*') ? 'sos-nav-link-active' : '' }}">Organizations</a>
            </li>
            <li>
                <a href="{{ $membersUrl }}" class="sos-nav-link {{ request()->is('members*') ? 'sos-nav-link-active' : '' }}">Members</a>
            </li>
        </ul>

        <div class="hidden items-center gap-3 lg:flex">
            @include('partials.logout-button')
        </div>

        <div class="hidden w-full flex-col items-center gap-4 border-t border-sos-blue/10 pt-4 lg:hidden" id="nav-menu-mobile">
            <ul class="flex flex-col items-center gap-4">
                <li>
                    <a href="{{ $organizationsUrl }}" class="sos-nav-link {{ request()->is('organizations*') ? 'sos-nav-link-active' : '' }}">Org</a>
                </li>
                <li>
                    <a href="{{ $membersUrl }}" class="sos-nav-link {{ request()->is('members*') ? 'sos-nav-link-active' : '' }}">Members</a>
                </li>
            </ul>
            <div class="flex flex-col items-center gap-3">
                @include('partials.logout-button', ['class' => 'sos-btn-outline w-full max-w-xs text-center'])
            </div>
        </div>
    </nav>
</header>