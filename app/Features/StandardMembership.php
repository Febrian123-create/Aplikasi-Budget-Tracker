<?php

namespace App\Features;

class StandardMembership implements MembershipFeatureInterface
{
    public function canViewChart(): bool
    {
        return false;
    }

    public function canExportPdf(): bool
    {
        return true; // Base feature - semua user bisa export
    }

    public function canUseRecurring(): bool
    {
        return false; // only premium
    }
}
