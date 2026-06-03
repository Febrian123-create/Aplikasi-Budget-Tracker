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

    
    public function index(Request $request)
    {
        $userId = Auth::id();
        $now = Carbon::now();

        
        $this->chartObserver->subscribe();

        
        $bulan = (int) $request->get('bulan', $now->month);
        $tahun = (int) $request->get('tahun', $now->year);

        
        $user = Auth::user();
        $isPremium = false;

        
        if ($user && method_exists($user, 'membership') && $user->membership) {
            $isPremium = strtolower($user->membership->membership_name) === 'premium';
        }

        
        $canSelectMonth = $isPremium; 

        
        $barRange = (int) $request->get('range', 3);
        $maxRange = $isPremium ? 12 : 3;
        $barRange = min($barRange, $maxRange);

        $startDate = Carbon::create($tahun, $bulan, 1)
            ->subMonths($barRange - 1)
            ->startOfMonth()
            ->format('Y-m-d');

        
        $metricCards          = $this->chartService->getMetricCards($userId, $bulan, $tahun);
        $categoryDistribution = $this->chartService->getCategoryDistribution($userId, $bulan, $tahun);
        $monthlyChartData     = $this->chartService->getMonthlyChartData($userId, $startDate);
        $dailySpending        = $this->chartService->getDailySpending($userId, $bulan, $tahun);
        $monthComparison      = $this->chartService->getMonthComparison($userId, $bulan, $tahun);
        $healthScore          = $this->chartService->getHealthScore($userId, $bulan, $tahun);

        $chartColors = ChartHelper::getChartColors();

        
        $this->chartObserver->resetRefresh();

        
        $availableMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            $availableMonths[] = [
                'value' => $m,
                'label' => ChartHelper::formatBulanLengkap($m),
                'selected' => $m === $bulan,
            ];
        }

        
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
            'dailySpending',
            'monthComparison',
            'healthScore',
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

    
    public function getData(Request $request)
    {
        $userId = Auth::id();
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        
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

        $metricCards          = $this->chartService->getMetricCards($userId, $bulan, $tahun);
        $categoryDistribution = $this->chartService->getCategoryDistribution($userId, $bulan, $tahun);
        $monthlyChartData     = $this->chartService->getMonthlyChartData($userId, $startDate);
        $dailySpending        = $this->chartService->getDailySpending($userId, $bulan, $tahun);
        $monthComparison      = $this->chartService->getMonthComparison($userId, $bulan, $tahun);
        $healthScore          = $this->chartService->getHealthScore($userId, $bulan, $tahun);

        return response()->json([
            'metricCards'          => $metricCards,
            'categoryDistribution' => $categoryDistribution,
            'monthlyChartData'     => $monthlyChartData,
            'dailySpending'        => $dailySpending,
            'monthComparison'      => $monthComparison,
            'healthScore'          => $healthScore,
            'chartColors'          => ChartHelper::getChartColors(),
        ]);
    }
}
