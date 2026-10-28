<header class="relative border-b border-sos-blue/10 bg-white">
    <nav class="sos-container flex flex-wrap items-center justify-between gap-4 py-4">
        <a href="{{ route('admin.organizations.index') }}" class="text-2xl font-bold tracking-tight text-sos-blue">SOS Admin</a>

        <ul class="flex items-center gap-8">
            <li>
                <a href="{{ route('admin.organizations.index') }}" class="sos-nav-link {{ request()->routeIs('admin.organizations.*') ? 'sos-nav-link-active' : '' }}">Organizations</a>
            </li>
        </ul>

        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="sos-btn-outline">Logout</button>
        </form>
    </nav>
</header>
