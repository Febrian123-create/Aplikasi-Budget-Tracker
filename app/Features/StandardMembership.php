<?php

namespace App\Features;

class StandardMembership implements MembershipFeatureInterface
{
    public function canViewChart(): bool
    {
        return false;
    }

    public function canUseRecurring(): bool
    {
        return false; // only premium
    }
}
