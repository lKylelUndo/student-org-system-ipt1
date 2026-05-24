<?php

namespace App\Models;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    protected $fillable = [
        'user_id',
        'organization_id',
        'role',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'role' => MembershipRole::class,
            'status' => MembershipStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function assignRole(MembershipRole|string $role): void
    {
        $this->role = $role instanceof MembershipRole ? $role : MembershipRole::from($role);
        $this->save();
    }

    public function scopeFilterByRole(Builder $query, ?string $role): Builder
    {
        if ($role) {
            $query->where('role', $role);
        }

        return $query;
    }

    public function canBeManagedBy(User $user): bool
    {
        $manager = Membership::query()
            ->where('organization_id', $this->organization_id)
            ->where('user_id', $user->id)
            ->where('status', MembershipStatus::Active)
            ->first();

        if (! $manager || ! $manager->role instanceof MembershipRole) {
            return false;
        }

        return $manager->role->canManageOrganization();
    }
}