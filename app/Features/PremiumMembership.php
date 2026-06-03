<?php

namespace App\Features;

class PremiumMembership implements MembershipFeatureInterface
{
    protected MembershipFeatureInterface $baseMembership;

    public function __construct(MembershipFeatureInterface $baseMembership)
    {
        $this->baseMembership = $baseMembership;
    }

    public function canViewChart(): bool
    {
        return true;
    }

    public function canExportPdf(): bool
    {
        return true; // Unlocked untuk Premium
    }

    public function canUseRecurring(): bool
    {
        return true;
    }
}
