<?php

namespace App\Features;

use App\Models\User;

class MembershipFactory
{
    public static function create(User $user): MembershipFeatureInterface
    {
        $membership = new StandardMembership();

        if ($user->membership_id == 2) {
            $membership = new PremiumMembership($membership);
        }

        return $membership;
    }
}
