@extends('layouts.authenticated')

@section('title', 'My Organizations')

@section('content')
    @php
        $selectedOrg = request('organization', '');
    @endphp

    <div class="sos-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="sos-page-title">My Organizations</h1>
                <p class="sos-page-subtitle">Manage members, roles, and join requests for organizations you lead</p>
            </div>
            <p class="sos-badge-blue text-sm">
                {{ $memberships->count() }} record(s)
            </p>
        </div>

        @if ($organizations->isEmpty())
            <p class="mt-8 rounded-lg border border-sos-blue/15 bg-white p-8 text-center text-sos-blue/70">
                You do not manage any organizations yet. Create an organization or become a President/Secretary to manage members.
            </p>
        @else
            <form method="GET" action="{{ route('members.index') }}" class="mt-8 flex flex-col gap-4 sm:flex-row">
                <div class="flex-1">
                    <label for="member-search" class="sos-label">Search Members</label>
                    <input
                        type="search"
                        id="member-search"
                        name="search"
                        class="sos-input"
                        placeholder="Search by name or email..."
                        value="{{ request('search') }}"
                    >
                </div>
                <div class="sm:w-48">
                    <label for="role-filter" class="sos-label">Filter by Role</label>
                    <select id="role-filter" name="role" class="sos-input">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:w-56">
                    <label for="org-filter" class="sos-label">Organization</label>
                    <select id="org-filter" name="organization" class="sos-input">
                        <option value="">All Organizations</option>
                        @foreach ($organizations as $org)
                            <option value="{{ $org->org_name }}" @selected($selectedOrg === $org->org_name)>{{ $org->org_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="sos-btn-blue w-full sm:w-auto">Apply</button>
                </div>
            </form>

            <div class="mt-8 overflow-x-auto rounded-lg border border-sos-blue/15 bg-white shadow-sm">
                <table class="sos-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Organization</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($memberships as $membership)
                            <tr>
                                <td class="font-medium text-sos-blue">{{ $membership->user->name }}</td>
                                <td>{{ $membership->user->email }}</td>
                                <td>{{ $membership->organization->org_name }}</td>
                                <td>
                                    <form action="{{ route('memberships.role', $membership) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="sos-input max-w-[140px] py-1 text-sm" onchange="this.form.submit()" aria-label="Change role for {{ $membership->user->name }}">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role }}" @selected($membership->role->value === $role)>{{ $role }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if ($membership->status->value === 'pending')
                                        <span class="sos-badge bg-amber-100 text-amber-800">Pending</span>
                                    @elseif ($membership->status->value === 'suspended')
                                        <span class="sos-badge bg-red-100 text-red-800">Suspended</span>
                                    @else
                                        <span class="sos-badge bg-green-100 text-green-800">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        @if ($membership->status->value === 'pending')
                                            <form action="{{ route('memberships.approve', $membership) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="sos-btn-yellow px-3 py-1.5 text-xs">Approve</button>
                                            </form>
                                            <form action="{{ route('memberships.decline', $membership) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="sos-btn-danger px-3 py-1.5 text-xs">Decline</button>
                                            </form>
                                        @elseif ($membership->status->value === 'active')
                                            <form action="{{ route('memberships.suspend', $membership) }}" method="POST" onsubmit="return confirm('Suspend this member?');">
                                                @csrf
                                                <button type="submit" class="sos-btn-danger px-3 py-1.5 text-xs">Suspend</button>
                                            </form>
                                            <form action="{{ route('memberships.remove', $membership) }}" method="POST" onsubmit="return confirm('Remove this member from the organization?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="sos-btn-danger px-3 py-1.5 text-xs">Remove</button>
                                            </form>
                                        @elseif ($membership->status->value === 'suspended')
                                            <form action="{{ route('memberships.remove', $membership) }}" method="POST" onsubmit="return confirm('Remove this member from the organization?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="sos-btn-danger px-3 py-1.5 text-xs">Remove</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-sos-blue/60">No members match your filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection