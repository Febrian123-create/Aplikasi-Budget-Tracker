<?php

namespace App\Http\Controllers;

use App\Helpers\ChartHelper;
use App\Mail\BudgetMonthlyReportMail;
use App\Services\BudgetReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BudgetReportController extends Controller
{
    public function __construct(private BudgetReportService $budgetReportService) {}

    public function index(Request $request)
    {
        $now   = Carbon::now();
        $bulan = (int) $request->get('bulan', $now->month);
        $tahun = (int) $request->get('tahun', $now->year);

        $report = $this->budgetReportService->getMonthlyReport(Auth::id(), $bulan, $tahun);

        $availableMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            $availableMonths[] = ['value' => $m, 'label' => ChartHelper::formatBulanLengkap($m), 'selected' => $m === $bulan];
        }

        $currentYear    = $now->year;
        $availableYears = [];
        for ($y = $currentYear - 2; $y <= $currentYear; $y++) {
            $availableYears[] = ['value' => $y, 'label' => (string) $y, 'selected' => $y === $tahun];
        }

        return view('budget.report', compact('report', 'bulan', 'tahun', 'availableMonths', 'availableYears'));
    }

    public function exportPdf(Request $request)
    {
        $bulan  = (int) $request->get('bulan', Carbon::now()->month);
        $tahun  = (int) $request->get('tahun', Carbon::now()->year);
        $report = $this->budgetReportService->getMonthlyReport(Auth::id(), $bulan, $tahun);
        $report['user'] = Auth::user();

        $pdf = Pdf::loadView('exports.budget-report-pdf', $report)->setPaper('A4', 'portrait');
        return $pdf->download('laporan_budget_' . $bulan . '_' . $tahun . '.pdf');
    }

    public function sendEmail(Request $request)
    {
        $bulan  = (int) $request->get('bulan', Carbon::now()->month);
        $tahun  = (int) $request->get('tahun', Carbon::now()->year);
        $user   = Auth::user();
        $report = $this->budgetReportService->getMonthlyReport($user->id, $bulan, $tahun);
        $report['user'] = $user;

        if ($report['isEmpty']) {
            return redirect()->route('budget.report', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('error', 'Belum ada budget bulanan aktif untuk dikirim laporannya.');
        }

        $filename  = 'laporan_budget_' . $bulan . '_' . $tahun . '.pdf';
        $pdfBinary = Pdf::loadView('exports.budget-report-pdf', $report)->setPaper('A4', 'portrait')->output();

        Mail::to($user->email)->send(new BudgetMonthlyReportMail($user, $report, $pdfBinary, $filename));

        return redirect()->route('budget.report', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Laporan budget berhasil dikirim ke email kamu!');
    }
}
