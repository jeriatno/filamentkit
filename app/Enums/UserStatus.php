<?php

namespace App\Enums;

enum UserStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    const ACTIVE_LABEL = 'Active';
    const INACTIVE_LABEL = 'Inactive';

    const VERIFIED = 'Verified';
    const UNVERIFIED = 'Unverified';

    public function label()
    {
        return match($this) {
            self::ACTIVE => self::ACTIVE_LABEL,
            self::INACTIVE => self::INACTIVE_LABEL,
        };
    }
}
