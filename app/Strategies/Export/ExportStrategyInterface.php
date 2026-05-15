<?php

namespace App\Strategies\Export;

interface ExportStrategyInterface
{
    public function export(array $data, string $filename);
}
