<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'org_name',
        'description',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function getMembers()
    {
        return $this->memberships()
            ->with('user')
            ->where('status', MembershipStatus::Active->value)
            ->get();
    }

    public function membersCount(): int
    {
        return $this->memberships()
            ->where('status', MembershipStatus::Active->value)
            ->count();
    }

    public function activeMemberships(): HasMany
    {
        return $this->memberships()->where('status', MembershipStatus::Active->value);
    }
}