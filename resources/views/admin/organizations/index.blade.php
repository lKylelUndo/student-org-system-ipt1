@extends('layouts.admin')

@section('title', 'Organizations')

@section('content')
    <div class="sos-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="sos-page-title">Organizations</h1>
                <p class="sos-page-subtitle">Review, approve, decline, suspend, or delete campus organizations</p>
            </div>
            <p class="sos-badge-blue text-sm">{{ $organizations->count() }} organization(s)</p>
        </div>

        <form method="GET" action="{{ route('admin.organizations.index') }}" class="mt-8 flex flex-col gap-4 sm:flex-row">
            <div class="flex-1">
                <label for="org-search" class="sos-label">Search Organization</label>
                <input type="search" id="org-search" name="search" class="sos-input" placeholder="Organization name..." value="{{ $search }}">
            </div>
            <div class="sm:w-48">
                <label for="status-filter" class="sos-label">Status</label>
                <select id="status-filter" name="status" class="sos-input">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $orgStatus)
                        <option value="{{ $orgStatus->value }}" @selected($status === $orgStatus->value)>{{ $orgStatus->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label for="leader-filter" class="sos-label">Filter by Leader</label>
                <input type="search" id="leader-filter" name="leader" class="sos-input" placeholder="Leader name or email..." value="{{ $leader }}">
            </div>
            <div class="flex items-end">
                <button type="submit" class="sos-btn-blue w-full sm:w-auto">Search</button>
            </div>
        </form>

        <div class="mt-8 overflow-x-auto rounded-lg border border-sos-blue/15 bg-white shadow-sm">
            <table class="sos-table">
                <thead>
                    <tr>
                        <th>Organization</th>
                        <th>Leader</th>
                        <th>Members</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($organizations as $org)
                        <tr>
                            <td class="font-medium text-sos-blue">{{ $org->org_name }}</td>
                            <td>
                                <div>{{ $org->creator?->name ?? '—' }}</div>
                                <div class="text-xs text-sos-blue/60">{{ $org->creator?->email }}</div>
                            </td>
                            <td>{{ $org->members_count }}</td>
                            <td>
                                @if ($org->status->value === 'pending')
                                    <span class="sos-badge bg-amber-100 text-amber-800">Pending</span>
                                @elseif ($org->status->value === 'approved')
                                    <span class="sos-badge bg-green-100 text-green-800">Approved</span>
                                @elseif ($org->status->value === 'declined')
                                    <span class="sos-badge bg-red-100 text-red-800">Declined</span>
                                @else
                                    <span class="sos-badge bg-gray-100 text-gray-800">Suspended</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.organizations.show', $org) }}" class="sos-btn-outline px-3 py-1.5 text-xs">View</a>
                                    @if ($org->status->value === 'pending')
                                        <form action="{{ route('admin.organizations.approve', $org) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="sos-btn-yellow px-3 py-1.5 text-xs">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.organizations.decline', $org) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="sos-btn-danger px-3 py-1.5 text-xs">Decline</button>
                                        </form>
                                    @elseif ($org->status->value === 'approved')
                                        <form action="{{ route('admin.organizations.suspend', $org) }}" method="POST" onsubmit="return confirm('Suspend this organization?');">
                                            @csrf
                                            <button type="submit" class="sos-btn-danger px-3 py-1.5 text-xs">Suspend</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.organizations.destroy', $org) }}" method="POST" onsubmit="return confirm('Delete this organization permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sos-btn-danger px-3 py-1.5 text-xs">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-sos-blue/60">No organizations match your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
