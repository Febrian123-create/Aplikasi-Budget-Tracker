<?php

namespace App\Features;

interface MembershipFeatureInterface
{
    public function canViewChart(): bool;    // premium feature
    public function canUseRecurring(): bool; // premium feature
}
