<?php

namespace App\Http\Controllers;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Enums\OrganizationStatus;
use App\Models\Membership;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipController extends Controller
{
    public function index(Request $request): View
    {
        $manageableIds = auth()->user()->manageableOrganizationIds();

        $query = Membership::query()
            ->with(['user', 'organization'])
            ->whereIn('organization_id', $manageableIds);

        if ($orgFilter = $request->string('organization')->trim()) {
            $query->whereHas('organization', fn ($q) => $q->where('org_name', $orgFilter));
        }

        if ($roleFilter = $request->string('role')->trim()) {
            $query->filterByRole($roleFilter);
        }

        if ($search = $request->string('search')->trim()) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        $memberships = $query->latest()->get();
        $organizations = Organization::query()
            ->whereIn('id', $manageableIds)
            ->orderBy('org_name')
            ->get();
        $roles = MembershipRole::values();

        return view('members.index', compact('memberships', 'organizations', 'roles'));
    }

    public function join(Organization $organization): RedirectResponse
    {
        if ($organization->status !== OrganizationStatus::Approved) {
            return back()->with('error', 'This organization is not open for membership yet.');
        }

        $user = auth()->user();
        $existing = $user->membershipFor($organization);

        if ($existing) {
            $message = match ($existing->status) {
                MembershipStatus::Pending => 'Your join request is already pending approval.',
                MembershipStatus::Active => 'You are already a member of this organization.',
                MembershipStatus::Suspended => 'Your membership is suspended. Contact an officer.',
            };

            return back()->with('error', $message);
        }

        Membership::create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'role' => MembershipRole::Member,
            'status' => MembershipStatus::Pending,
        ]);

        return back()->with('success', 'Join request submitted. An officer will review it soon.');
    }

    public function approve(Membership $membership): RedirectResponse
    {
        $this->authorizeManage($membership);

        if ($membership->status !== MembershipStatus::Pending) {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $membership->update(['status' => MembershipStatus::Active]);

        return back()->with('success', $membership->user->name.' has been approved.');
    }

    public function decline(Membership $membership): RedirectResponse
    {
        $this->authorizeManage($membership);

        if ($membership->status !== MembershipStatus::Pending) {
            return back()->with('error', 'Only pending requests can be declined.');
        }

        $name = $membership->user->name;
        $membership->delete();

        return back()->with('success', $name.'\'s join request was declined.');
    }

    public function suspend(Membership $membership): RedirectResponse
    {
        $this->authorizeManage($membership);

        if ($membership->status !== MembershipStatus::Active) {
            return back()->with('error', 'Only active members can be suspended.');
        }

        if ($membership->role === MembershipRole::President) {
            return back()->with('error', 'Cannot suspend the organization president.');
        }

        $membership->update(['status' => MembershipStatus::Suspended]);

        return back()->with('success', $membership->user->name.' has been suspended.');
    }

    public function remove(Membership $membership): RedirectResponse
    {
        $this->authorizeManage($membership);

        if ($membership->status === MembershipStatus::Pending) {
            return back()->with('error', 'Use decline to reject pending join requests.');
        }

        if ($membership->role === MembershipRole::President) {
            return back()->with('error', 'Cannot remove the organization president.');
        }

        $name = $membership->user->name;
        $membership->delete();

        return back()->with('success', $name.' has been removed from the organization.');
    }

    public function updateRole(Request $request, Membership $membership): RedirectResponse
    {
        $this->authorizeManage($membership);

        $validated = $request->validate([
            'role' => ['required', 'in:'.implode(',', MembershipRole::values())],
        ]);

        if ($membership->role === MembershipRole::President && $validated['role'] !== MembershipRole::President->value) {
            $presidentCount = Membership::query()
                ->where('organization_id', $membership->organization_id)
                ->where('role', MembershipRole::President)
                ->where('status', MembershipStatus::Active)
                ->where('id', '!=', $membership->id)
                ->count();

            if ($presidentCount === 0) {
                return back()->with('error', 'Assign another president before changing this role.');
            }
        }

        $membership->assignRole($validated['role']);

        return back()->with('success', 'Member role updated.');
    }

    private function authorizeManage(Membership $membership): void
    {
        if (! auth()->user()->canManageOrganization($membership->organization)) {
            abort(403, 'You are not allowed to manage members of this organization.');
        }
    }
}