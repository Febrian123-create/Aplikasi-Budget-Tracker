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
        return false; // Fitur export dibatasi untuk Free
    }

    public function canUseRecurring(): bool
    {
        return false; // only premium
    }
}
