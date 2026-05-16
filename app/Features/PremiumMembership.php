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
        // Add Premium capability on top of base membership
        return true;
    }

    public function canExportPdf(): bool
    {
        // Add Premium capability on top of base membership
        return true;
    }
}
