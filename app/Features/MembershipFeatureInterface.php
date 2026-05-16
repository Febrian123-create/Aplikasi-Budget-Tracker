<?php

namespace App\Features;

interface MembershipFeatureInterface
{
    public function canViewChart(): bool;
    public function canExportPdf(): bool;
}
