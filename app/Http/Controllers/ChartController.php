<?php

namespace App\Http\Controllers;

use App\Helpers\ChartHelper;
use App\Observers\ChartObserver;
use App\Observers\TransactionSubject;
use App\Repositories\TransactionRepository;
use App\Services\ChartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * ChartController — Single Responsibility: terima request,
 * panggil ChartService, dan kembalikan view/data.
 */
class ChartController extends Controller
{
    private ChartService $chartService;
    private ChartObserver $chartObserver;

    public function __construct()
    {
        $transactionRepository = new TransactionRepository();
        $this->chartService = new ChartService($transactionRepository);
        $this->chartObserver = new ChartObserver($this->chartService);
    }

    /**
     * Halaman utama visualisasi data (Fitur 8).
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $now = Carbon::now();

        // Subscribe ChartObserver ke TransactionSubject
        $this->chartObserver->subscribe();

        // Tentukan bulan & tahun aktif
        $bulan = (int) $request->get('bulan', $now->month);
        $tahun = (int) $request->get('tahun', $now->year);

        // Cek membership user
        $user = Auth::user();
        $isPremium = false;

        // Cek apakah user punya relasi membership
        if ($user && method_exists($user, 'membership') && $user->membership) {
            $isPremium = strtolower($user->membership->membership_name) === 'premium';
        }

        // Batasi akses berdasarkan membership
        $canSelectMonth = $isPremium; // Free: hanya bulan aktif

        // Tentukan rentang bar chart
        $barRange = (int) $request->get('range', 3);
        $maxRange = $isPremium ? 12 : 3;
        $barRange = min($barRange, $maxRange);

        $startDate = Carbon::create($tahun, $bulan, 1)
            ->subMonths($barRange - 1)
            ->startOfMonth()
            ->format('Y-m-d');

        // Ambil data dari ChartService
        $metricCards = $this->chartService->getMetricCards($userId, $bulan, $tahun);
        $categoryDistribution = $this->chartService->getCategoryDistribution($userId, $bulan, $tahun);
        $monthlyChartData = $this->chartService->getMonthlyChartData($userId, $startDate);

        // Warna chart
        $chartColors = ChartHelper::getChartColors();

        // Reset observer
        $this->chartObserver->resetRefresh();

        // Bulan-bulan untuk selector (Premium)
        $availableMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            $availableMonths[] = [
                'value' => $m,
                'label' => ChartHelper::formatBulanLengkap($m),
                'selected' => $m === $bulan,
            ];
        }

        // Tahun untuk selector
        $currentYear = $now->year;
        $availableYears = [];
        for ($y = $currentYear - 2; $y <= $currentYear; $y++) {
            $availableYears[] = [
                'value' => $y,
                'label' => (string) $y,
                'selected' => $y === $tahun,
            ];
        }

        return view('charts.index', compact(
            'metricCards',
            'categoryDistribution',
            'monthlyChartData',
            'chartColors',
            'bulan',
            'tahun',
            'isPremium',
            'canSelectMonth',
            'barRange',
            'maxRange',
            'availableMonths',
            'availableYears'
        ));
    }

    /**
     * API endpoint untuk refresh data chart via AJAX.
     * Digunakan oleh ChartObserver untuk auto-refresh.
     */
    public function getData(Request $request)
    {
        $userId = Auth::id();
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        // Cek membership
        $user = Auth::user();
        $isPremium = false;
        if ($user && method_exists($user, 'membership') && $user->membership) {
            $isPremium = strtolower($user->membership->membership_name) === 'premium';
        }

        $barRange = (int) $request->get('range', 3);
        $maxRange = $isPremium ? 12 : 3;
        $barRange = min($barRange, $maxRange);

        $startDate = Carbon::create($tahun, $bulan, 1)
            ->subMonths($barRange - 1)
            ->startOfMonth()
            ->format('Y-m-d');

        $metricCards = $this->chartService->getMetricCards($userId, $bulan, $tahun);
        $categoryDistribution = $this->chartService->getCategoryDistribution($userId, $bulan, $tahun);
        $monthlyChartData = $this->chartService->getMonthlyChartData($userId, $startDate);

        return response()->json([
            'metricCards' => $metricCards,
            'categoryDistribution' => $categoryDistribution,
            'monthlyChartData' => $monthlyChartData,
            'chartColors' => ChartHelper::getChartColors(),
        ]);
    }
}
