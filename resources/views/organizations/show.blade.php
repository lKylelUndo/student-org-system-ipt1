@extends('layouts.authenticated')

@section('title', $organization->org_name)

@section('content')
    <div class="sos-container">
        <a href="{{ route('organizations.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-sos-blue/70 hover:text-sos-blue">
            &larr; Back to Organizations
        </a>

        <div class="mt-6 overflow-hidden rounded-lg border-4 border-sos-blue bg-white">
            <div class="bg-sos-blue px-6 py-8 text-white sm:px-10">
                <h1 class="text-3xl font-bold sm:text-4xl">{{ $organization->org_name }}</h1>
                <p class="mt-2 flex items-center gap-2 text-sos-yellow">
                    <span class="inline-block h-2 w-2 rounded-full bg-sos-yellow"></span>
                    {{ $organization->membersCount() }} members
                </p>
            </div>
            <div class="p-6 sm:p-10">
                @if ($organization->status->value !== 'approved')
                    <div class="mb-6 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        @if ($organization->status->value === 'pending')
                            This organization is waiting for admin approval.
                        @elseif ($organization->status->value === 'declined')
                            This organization was declined by an administrator.
                        @else
                            This organization is currently suspended.
                        @endif
                    </div>
                @endif

                <h2 class="text-lg font-semibold text-sos-blue">About</h2>
                <p class="mt-3 leading-relaxed text-sos-blue/80">{{ $organization->description }}</p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($userMembership)
                        @if ($userMembership->status->value === 'pending')
                            <span class="sos-badge bg-amber-100 text-amber-800">Join request pending</span>
                        @elseif ($userMembership->status->value === 'active')
                            <span class="sos-badge bg-green-100 text-green-800">You are a member</span>
                        @endif
                    @elseif ($organization->status->value === 'approved')
                        <form action="{{ route('organizations.join', $organization) }}" method="POST">
                            @csrf
                            <button type="submit" class="sos-btn-yellow">Join Organization</button>
                        </form>
                    @endif

                    @if ($canManage)
                        <a href="{{ route('organizations.edit', $organization) }}" class="sos-btn-outline">Edit Organization</a>
                        <a href="{{ route('members.index', ['organization' => $organization->org_name]) }}" class="sos-btn-blue">Manage Members</a>
                    @endif
                </div>
            </div>
        </div>

        <section class="mt-12">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-sos-blue">Members</h2>
                @if ($canManage)
                    <a href="{{ route('members.index', ['organization' => $organization->org_name]) }}" class="text-sm font-semibold text-sos-blue hover:underline">View all</a>
                @endif
            </div>

            <div class="mt-6 overflow-x-auto rounded-lg border border-sos-blue/15">
                <table class="sos-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $membership)
                            <tr>
                                <td class="font-medium">{{ $membership->user->name }}</td>
                                <td><span class="sos-badge-yellow">{{ $membership->role->value }}</span></td>
                                <td>
                                    @if ($membership->status->value === 'pending')
                                        <span class="text-amber-600">Pending</span>
                                    @elseif ($membership->status->value === 'suspended')
                                        <span class="text-red-600">Suspended</span>
                                    @else
                                        <span class="text-green-700">Active</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-sos-blue/60">No members yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection