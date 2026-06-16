<?php

namespace App\Http\Controllers;

use App\Helpers\ChartHelper;
use App\Mail\BudgetMonthlyReportMail;
use App\Models\Budget;
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
        $now    = Carbon::now();
        $userId = Auth::id();

        // created_at = kapan user membuat budget, bukan start_date (yg bisa mundur ke awal tahun/bulan)
        $earliest = Budget::where('user_id', $userId)->orderBy('created_at')->value('created_at');
        $minDate  = $earliest ? Carbon::parse($earliest)->startOfMonth() : $now->copy()->startOfMonth();

        $bulan = (int) $request->query('bulan', $now->month);
        $tahun = (int) $request->query('tahun', $now->year);

        // Clamp requested period to the allowed range
        $requested = Carbon::create($tahun, $bulan, 1);
        if ($requested->lt($minDate)) {
            $bulan = $minDate->month;
            $tahun = $minDate->year;
        }
        if ($requested->gt($now->copy()->startOfMonth())) {
            $bulan = $now->month;
            $tahun = $now->year;
        }

        $report = $this->budgetReportService->getMonthlyReport($userId, $bulan, $tahun);

        // Build available months/years from minDate to current month
        $availableMonths = [];
        $availableYears  = [];
        $cursor = $minDate->copy();
        $end    = $now->copy()->startOfMonth();

        $yearsSet = [];
        while ($cursor->lte($end)) {
            if (!in_array($cursor->year, $yearsSet)) {
                $yearsSet[] = $cursor->year;
                $availableYears[] = [
                    'value'    => $cursor->year,
                    'label'    => (string) $cursor->year,
                    'selected' => $cursor->year === $tahun,
                ];
            }
            $cursor->addMonth();
        }

        // Months valid for selected year
        $minMonthForYear = ($tahun === $minDate->year) ? $minDate->month : 1;
        $maxMonthForYear = ($tahun === $now->year)     ? $now->month     : 12;
        for ($m = $minMonthForYear; $m <= $maxMonthForYear; $m++) {
            $availableMonths[] = [
                'value'    => $m,
                'label'    => ChartHelper::formatBulanLengkap($m),
                'selected' => $m === $bulan,
            ];
        }

        return view('budget.report', compact('report', 'bulan', 'tahun', 'availableMonths', 'availableYears'));
    }

    public function exportPdf(Request $request)
    {
        $bulan  = (int) $request->query('bulan', Carbon::now()->month);
        $tahun  = (int) $request->query('tahun', Carbon::now()->year);
        $report = $this->budgetReportService->getMonthlyReport(Auth::id(), $bulan, $tahun);
        $report['user'] = Auth::user();

        $pdf = Pdf::loadView('exports.budget-report-pdf', $report)->setPaper('A4', 'portrait');
        return $pdf->download('laporan_budget_' . $bulan . '_' . $tahun . '.pdf');
    }

    public function sendEmail(Request $request)
    {
        $bulan  = (int) $request->query('bulan', Carbon::now()->month);
        $tahun  = (int) $request->query('tahun', Carbon::now()->year);
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
