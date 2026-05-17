<?php

namespace App\Http\Controllers;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Membership;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();

        $organizations = Organization::query()
            ->withCount(['memberships as members_count' => function ($query) {
                $query->where('status', MembershipStatus::Active->value);
            }])
            ->when($search->isNotEmpty(), fn ($q) => $q->where('org_name', 'like', '%'.$search.'%'))
            ->latest()
            ->get();

        $userMemberships = auth()->user()
            ->memberships()
            ->get()
            ->keyBy('organization_id');

        return view('organizations.index', compact('organizations', 'userMemberships', 'search'));
    }

    public function show(Organization $organization): View
    {
        $organization->load(['memberships.user']);

        $members = $organization->memberships()
            ->with('user')
            ->whereIn('status', [MembershipStatus::Active, MembershipStatus::Pending])
            ->latest()
            ->take(10)
            ->get();

        $userMembership = auth()->user()->membershipFor($organization);
        $canManage = auth()->user()->canManageOrganization($organization);

        return view('organizations.show', compact('organization', 'members', 'userMembership', 'canManage'));
    }

    public function create(): View
    {
        return view('organizations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'org_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $organization = Organization::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        Membership::create([
            'user_id' => auth()->id(),
            'organization_id' => $organization->id,
            'role' => MembershipRole::President,
            'status' => MembershipStatus::Active,
        ]);

        return redirect()
            ->route('organizations.show', $organization)
            ->with('success', 'Organization created successfully.');
    }

    public function edit(Organization $organization): View|RedirectResponse
    {
        if (! auth()->user()->canManageOrganization($organization)) {
            abort(403, 'You are not allowed to edit this organization.');
        }

        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        if (! auth()->user()->canManageOrganization($organization)) {
            abort(403, 'You are not allowed to edit this organization.');
        }

        $validated = $request->validate([
            'org_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $organization->update($validated);

        return redirect()
            ->route('organizations.show', $organization)
            ->with('success', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        if ($organization->created_by !== auth()->id()) {
            abort(403, 'Only the organization creator can delete it.');
        }

        $organization->delete();

        return redirect()
            ->route('organizations.index')
            ->with('success', 'Organization deleted.');
    }
}