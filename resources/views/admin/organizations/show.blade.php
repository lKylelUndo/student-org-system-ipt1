@extends('layouts.admin')

@section('title', $organization->org_name)

@section('content')
    <div class="sos-container">
        <div class="mb-6">
            <a href="{{ route('admin.organizations.index') }}" class="text-sm font-medium text-sos-blue hover:underline">&larr; Back to organizations</a>
        </div>

        <div class="sos-card">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="sos-page-title">{{ $organization->org_name }}</h1>
                    <p class="mt-3 text-sm leading-relaxed text-sos-blue/75">{{ $organization->description }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($organization->status->value === 'pending')
                        <span class="sos-badge bg-amber-100 text-amber-800">Pending</span>
                    @elseif ($organization->status->value === 'approved')
                        <span class="sos-badge bg-green-100 text-green-800">Approved</span>
                    @elseif ($organization->status->value === 'declined')
                        <span class="sos-badge bg-red-100 text-red-800">Declined</span>
                    @else
                        <span class="sos-badge bg-gray-100 text-gray-800">Suspended</span>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-sos-blue/50">Leader</p>
                    <p class="mt-1 font-medium text-sos-blue">{{ $organization->creator?->name ?? '—' }}</p>
                    <p class="text-sm text-sos-blue/70">{{ $organization->creator?->email }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-sos-blue/50">Total Members</p>
                    <p class="mt-1 font-medium text-sos-blue">{{ $memberships->count() }}</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                @if ($organization->status->value === 'pending')
                    <form action="{{ route('admin.organizations.approve', $organization) }}" method="POST">
                        @csrf
                        <button type="submit" class="sos-btn-yellow">Approve</button>
                    </form>
                    <form action="{{ route('admin.organizations.decline', $organization) }}" method="POST">
                        @csrf
                        <button type="submit" class="sos-btn-danger">Decline</button>
                    </form>
                @elseif ($organization->status->value === 'approved')
                    <form action="{{ route('admin.organizations.suspend', $organization) }}" method="POST" onsubmit="return confirm('Suspend this organization?');">
                        @csrf
                        <button type="submit" class="sos-btn-danger">Suspend</button>
                    </form>
                @endif
                <form action="{{ route('admin.organizations.destroy', $organization) }}" method="POST" onsubmit="return confirm('Delete this organization permanently? All members will be removed.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="sos-btn-danger">Delete</button>
                </form>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-xl font-bold text-sos-blue">Members</h2>
            <p class="mt-1 text-sm text-sos-blue/75">Filter by member name, email, or role</p>

            <form method="GET" action="{{ route('admin.organizations.show', $organization) }}" class="mt-6 flex flex-col gap-4 sm:flex-row">
                <div class="flex-1">
                    <label for="member-search" class="sos-label">Search Member</label>
                    <input type="search" id="member-search" name="member" class="sos-input" placeholder="Name or email..." value="{{ $memberSearch }}">
                </div>
                <div class="sm:w-48">
                    <label for="role-filter" class="sos-label">Role</label>
                    <select id="role-filter" name="role" class="sos-input">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected($roleFilter === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="sos-btn-blue">Apply</button>
                    <a href="{{ route('admin.organizations.show', $organization) }}" class="sos-btn-outline">Clear</a>
                </div>
            </form>

            <div class="mt-6 overflow-x-auto rounded-lg border border-sos-blue/15 bg-white shadow-sm">
                <table class="sos-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($memberships as $membership)
                            <tr>
                                <td class="font-medium text-sos-blue">{{ $membership->user->name }}</td>
                                <td>{{ $membership->user->email }}</td>
                                <td>{{ $membership->role->value }}</td>
                                <td>
                                    @if ($membership->status->value === 'pending')
                                        <span class="sos-badge bg-amber-100 text-amber-800">Pending</span>
                                    @elseif ($membership->status->value === 'suspended')
                                        <span class="sos-badge bg-red-100 text-red-800">Suspended</span>
                                    @else
                                        <span class="sos-badge bg-green-100 text-green-800">Active</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-sos-blue/60">No members match your filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
