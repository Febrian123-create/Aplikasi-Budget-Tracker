<?php

namespace App\Features;

interface MembershipFeatureInterface
{
    public function canViewChart(): bool;    // premium feature
    public function canExportPdf(): bool;    // base feature - semua user
    public function canUseRecurring(): bool; // premium feature
}
