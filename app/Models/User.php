<?php

namespace App\Models;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'course',
        'year_level',
        'bio',
        'profile_completed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_completed_at' => 'datetime',
            'year_level' => 'integer',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function organizationsCreated(): HasMany
    {
        return $this->hasMany(Organization::class, 'created_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasCompletedProfile(): bool
    {
        return $this->profile_completed_at !== null;
    }

    public function membershipFor(Organization $organization): ?Membership
    {
        return $this->memberships()
            ->where('organization_id', $organization->id)
            ->first();
    }

    public function canManageOrganization(Organization $organization): bool
    {
        if ($organization->created_by === $this->id) {
            return true;
        }

        $membership = $this->membershipFor($organization);

        if (! $membership || $membership->status !== MembershipStatus::Active) {
            return false;
        }

        return $membership->role instanceof MembershipRole
            && $membership->role->canManageOrganization();
    }

    public function manageableOrganizationIds(): array
    {
        $fromRole = $this->memberships()
            ->where('status', MembershipStatus::Active)
            ->get()
            ->filter(fn (Membership $m) => $m->role instanceof MembershipRole && $m->role->canManageOrganization())
            ->pluck('organization_id');

        $created = $this->organizationsCreated()->pluck('id');

        return $fromRole->merge($created)->unique()->values()->all();
    }
}