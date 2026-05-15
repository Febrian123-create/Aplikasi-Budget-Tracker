<?php

namespace App\Strategies\Export;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfExportStrategy implements ExportStrategyInterface
{
    public function export(array $data, string $filename)
    {
        $pdf = Pdf::loadView('exports.pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }
}
