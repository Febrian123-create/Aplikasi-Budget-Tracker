<?php

namespace App\Strategies\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class ExcelExportStrategy implements ExportStrategyInterface
{
    public function export(array $data, string $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Transaksi');

        // === TITLE ROW ===
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', $data['title'] ?? 'Laporan Transaksi');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new Color('1B2838'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // === EXPORT DATE ROW ===
        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A2', 'Tanggal Export: ' . $data['exportDate']);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new Color('6C757D'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // === FILTER INFO ROW ===
        $currentRow = 3;
        if (!empty($data['filters'])) {
            $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", 'Filter: ' . implode(' | ', $data['filters']));
            $sheet->getStyle("A{$currentRow}")->getFont()->setItalic(true)->setSize(9)->setColor(new Color('495057'));
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentRow++;
        }

        // Empty separator row
        $currentRow++;

        // === HEADER ROW ===
        $headerRow = $currentRow;
        $headers = ['No', 'Tanggal', 'Kategori', 'Jenis', 'Deskripsi', 'Jumlah (Rp)'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($headers as $index => $header) {
            $cell = $columns[$index] . $headerRow;
            $sheet->setCellValue($cell, $header);
        }

        // Header styling
        $headerRange = "A{$headerRow}:F{$headerRow}";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1B2838'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1B2838'],
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(25);

        // === DATA ROWS ===
        $currentRow = $headerRow + 1;
        $no = 1;
        foreach ($data['transactions'] as $transaction) {
            $jenis = $transaction->transactionType_id == 1 ? 'Pemasukan' : 'Pengeluaran';
            $categoryName = $transaction->category->category_name ?? '-';

            $sheet->setCellValue("A{$currentRow}", $no);
            $sheet->setCellValue("B{$currentRow}", Carbon::parse($transaction->transaction_date)->format('d-m-Y'));
            $sheet->setCellValue("C{$currentRow}", $categoryName);
            $sheet->setCellValue("D{$currentRow}", $jenis);
            $sheet->setCellValue("E{$currentRow}", $transaction->description);
            $sheet->setCellValue("F{$currentRow}", $transaction->total_amount);

            // Number format
            $sheet->getStyle("F{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');

            // Alternating row colors
            if ($no % 2 == 0) {
                $sheet->getStyle("A{$currentRow}:F{$currentRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            // Color-code the Jenis column
            if ($transaction->transactionType_id == 1) {
                $sheet->getStyle("D{$currentRow}")->getFont()->setColor(new Color('28A745'));
                $sheet->getStyle("D{$currentRow}")->getFont()->setBold(true);
            } else {
                $sheet->getStyle("D{$currentRow}")->getFont()->setColor(new Color('DC3545'));
                $sheet->getStyle("D{$currentRow}")->getFont()->setBold(true);
            }

            // Alignments
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $no++;
            $currentRow++;
        }

        // Data area borders
        $lastDataRow = $currentRow - 1;
        if ($lastDataRow >= $headerRow + 1) {
            $dataRange = "A" . ($headerRow + 1) . ":F{$lastDataRow}";
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DEE2E6'],
                    ],
                ],
            ]);
        }

        // === SUMMARY SECTION ===
        $currentRow++; 

        // Total Pemasukan
        $sheet->mergeCells("D{$currentRow}:E{$currentRow}");
        $sheet->setCellValue("D{$currentRow}", 'Total Pemasukan');
        $sheet->setCellValue("F{$currentRow}", $data['totalIncome']);
        $sheet->getStyle("D{$currentRow}:F{$currentRow}")->getFont()->setBold(true);
        $sheet->getStyle("F{$currentRow}")->getFont()->setColor(new Color('28A745'));
        $sheet->getStyle("F{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $currentRow++;

        // Total Pengeluaran
        $sheet->mergeCells("D{$currentRow}:E{$currentRow}");
        $sheet->setCellValue("D{$currentRow}", 'Total Pengeluaran');
        $sheet->setCellValue("F{$currentRow}", $data['totalExpense']);
        $sheet->getStyle("D{$currentRow}:F{$currentRow}")->getFont()->setBold(true);
        $sheet->getStyle("F{$currentRow}")->getFont()->setColor(new Color('DC3545'));
        $sheet->getStyle("F{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $currentRow++;

        // Saldo
        $sheet->mergeCells("D{$currentRow}:E{$currentRow}");
        $sheet->setCellValue("D{$currentRow}", 'Saldo');
        $sheet->setCellValue("F{$currentRow}", $data['balance']);
        $sheet->getStyle("D{$currentRow}:F{$currentRow}")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("F{$currentRow}")->getFont()->setColor(new Color('1B2838'));
        $sheet->getStyle("F{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Summary borders
        $summaryStart = $currentRow - 2;
        $sheet->getStyle("D{$summaryStart}:F{$currentRow}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '1B2838'],
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // === COLUMN WIDTHS ===
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(20);

        // === WRITE AND DOWNLOAD ===
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
