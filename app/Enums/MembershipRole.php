<?php

namespace App\Enums;

enum MembershipRole: string
{
    case President = 'President';
    case Secretary = 'Secretary';
    case Treasurer = 'Treasurer';
    case Member = 'Member';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function canManageOrganization(): bool
    {
        return in_array($this, [self::President, self::Secretary], true);
    }
}