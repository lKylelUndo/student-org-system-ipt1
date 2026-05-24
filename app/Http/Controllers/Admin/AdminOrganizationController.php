<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MembershipRole;
use App\Enums\OrganizationStatus;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();
        $status = $request->string('status')->trim();
        $leader = $request->string('leader')->trim();

        $organizations = Organization::query()
            ->with('creator')
            ->withCount(['memberships as members_count'])
            ->when($search->isNotEmpty(), fn ($q) => $q->where('org_name', 'like', '%'.$search.'%'))
            ->when($status->isNotEmpty(), fn ($q) => $q->where('status', $status))
            ->when($leader->isNotEmpty(), function ($q) use ($leader) {
                $q->whereHas('creator', function ($creator) use ($leader) {
                    $creator->where('name', 'like', '%'.$leader.'%')
                        ->orWhere('email', 'like', '%'.$leader.'%');
                });
            })
            ->latest()
            ->get();

        $statuses = OrganizationStatus::cases();

        return view('admin.organizations.index', compact('organizations', 'search', 'status', 'leader', 'statuses'));
    }

    public function show(Request $request, Organization $organization): View
    {
        $organization->load('creator');

        $memberSearch = $request->string('member')->trim();
        $roleFilter = $request->string('role')->trim();

        $memberships = $organization->memberships()
            ->with('user')
            ->when($memberSearch->isNotEmpty(), function ($q) use ($memberSearch) {
                $q->whereHas('user', function ($user) use ($memberSearch) {
                    $user->where('name', 'like', '%'.$memberSearch.'%')
                        ->orWhere('email', 'like', '%'.$memberSearch.'%');
                });
            })
            ->when($roleFilter->isNotEmpty(), fn ($q) => $q->where('role', $roleFilter))
            ->latest()
            ->get();

        $roles = MembershipRole::values();

        return view('admin.organizations.show', compact('organization', 'memberships', 'memberSearch', 'roleFilter', 'roles'));
    }

    public function approve(Organization $organization): RedirectResponse
    {
        if ($organization->status !== OrganizationStatus::Pending) {
            return back()->with('error', 'Only pending organizations can be approved.');
        }

        $organization->update(['status' => OrganizationStatus::Approved]);

        return back()->with('success', $organization->org_name.' has been approved.');
    }

    public function decline(Organization $organization): RedirectResponse
    {
        if ($organization->status !== OrganizationStatus::Pending) {
            return back()->with('error', 'Only pending organizations can be declined.');
        }

        $organization->update(['status' => OrganizationStatus::Declined]);

        return back()->with('success', $organization->org_name.' has been declined.');
    }

    public function suspend(Organization $organization): RedirectResponse
    {
        if ($organization->status !== OrganizationStatus::Approved) {
            return back()->with('error', 'Only approved organizations can be suspended.');
        }

        $organization->update(['status' => OrganizationStatus::Suspended]);

        return back()->with('success', $organization->org_name.' has been suspended.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $name = $organization->org_name;
        $organization->delete();

        return redirect()
            ->route('admin.organizations.index')
            ->with('success', $name.' has been deleted.');
    }
}
