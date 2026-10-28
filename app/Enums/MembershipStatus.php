<?php

namespace App\Enums;

enum MembershipStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
}