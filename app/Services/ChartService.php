<?php

namespace App\Services;

use App\Helpers\ChartHelper;
use App\Repositories\TransactionRepository;


class ChartService
{
    public function __construct(
        private TransactionRepository $transactionRepository
    ) {}

    
    public function getCategoryDistribution(int $userId, int $bulan, int $tahun): array
    {
        $data  = $this->transactionRepository->getCategoryExpenses($userId, $bulan, $tahun);
        $total = $data->sum('total');

        
        $totalIncome = $this->transactionRepository->getTotalIncome($userId, $bulan, $tahun);

        if ($data->isEmpty()) {
            return [
                'categories' => [],
                'total'      => 0,
                'isEmpty'    => true,
                'allIncome'  => $totalIncome > 0,
            ];
        }

        $categories = [];

        if ($data->count() <= 6) {
            
            foreach ($data as $item) {
                $categories[] = [
                    'name'       => $item->category_name,
                    'amount'     => (float) $item->total,
                    'formatted'  => ChartHelper::formatRupiah($item->total),
                    'percentage' => ChartHelper::hitungPersentase($item->total, $total),
                    'percentNum' => $total > 0 ? round(($item->total / $total) * 100, 1) : 0,
                ];
            }
        } else {
            
            $top5  = $data->take(5);
            $rest  = $data->slice(5);
            $lainnyaTotal = $rest->sum('total');

            foreach ($top5 as $item) {
                $categories[] = [
                    'name'       => $item->category_name,
                    'amount'     => (float) $item->total,
                    'formatted'  => ChartHelper::formatRupiah($item->total),
                    'percentage' => ChartHelper::hitungPersentase($item->total, $total),
                    'percentNum' => $total > 0 ? round(($item->total / $total) * 100, 1) : 0,
                ];
            }

            $categories[] = [
                'name'       => 'Lainnya',
                'amount'     => (float) $lainnyaTotal,
                'formatted'  => ChartHelper::formatRupiah($lainnyaTotal),
                'percentage' => ChartHelper::hitungPersentase($lainnyaTotal, $total),
                'percentNum' => $total > 0 ? round(($lainnyaTotal / $total) * 100, 1) : 0,
            ];
        }

        return [
            'categories' => $categories,
            'total'      => $total,
            'totalFormatted' => ChartHelper::formatRupiah($total),
            'isEmpty'    => false,
            'allIncome'  => false,
        ];
    }

    
    public function getMonthlyChartData(int $userId, string $startDate): array
    {
        $data = $this->transactionRepository->getMonthlyData($userId, $startDate);

        if ($data->isEmpty()) {
            return [
                'months'            => [],
                'averageExpense'    => 0,
                'isEmpty'           => true,
                'lessThanTwoMonths' => true,
            ];
        }

        $totalExpenseAllMonths = $data->sum('total_pengeluaran');
        $averageExpense = $data->count() > 0
            ? $totalExpenseAllMonths / $data->count()
            : 0;

        $months    = [];
        $prevIncome  = null;
        $prevExpense = null;

        foreach ($data as $item) {
            $pemasukan   = (float) $item->total_pemasukan;
            $pengeluaran = (float) $item->total_pengeluaran;
            $selisih     = $pemasukan - $pengeluaran;
            $isSurplus   = $selisih >= 0;

            
            $growthIncome  = null;
            $growthExpense = null;

            if ($prevIncome !== null && $prevIncome > 0) {
                $growthIncome = (($pemasukan - $prevIncome) / $prevIncome) * 100;
            }
            if ($prevExpense !== null && $prevExpense > 0) {
                $growthExpense = (($pengeluaran - $prevExpense) / $prevExpense) * 100;
            }

            $months[] = [
                'bulan'             => (int) $item->bulan,
                'tahun'             => (int) $item->tahun,
                'label'             => ChartHelper::formatBulan($item->bulan),
                'labelLengkap'      => ChartHelper::formatBulanLengkap($item->bulan) . ' ' . $item->tahun,
                'pemasukan'         => $pemasukan,
                'pengeluaran'       => $pengeluaran,
                'pemasukanFormatted'   => ChartHelper::formatRupiah($pemasukan),
                'pengeluaranFormatted' => ChartHelper::formatRupiah($pengeluaran),
                'selisih'           => abs($selisih),
                'selisihFormatted'  => ($isSurplus ? '+' : '-') . ChartHelper::formatRupiah(abs($selisih)),
                'isSurplus'         => $isSurplus,
                'isDefisit'         => !$isSurplus,
                'growthIncome'      => $growthIncome !== null ? round($growthIncome, 1) : null,
                'growthExpense'     => $growthExpense !== null ? round($growthExpense, 1) : null,
            ];

            $prevIncome  = $pemasukan;
            $prevExpense = $pengeluaran;
        }

        return [
            'months'            => $months,
            'averageExpense'    => $averageExpense,
            'averageFormatted'  => ChartHelper::formatRupiah($averageExpense),
            'isEmpty'           => false,
            'lessThanTwoMonths' => count($months) < 2,
        ];
    }

    public function getDailySpending(int $userId, int $bulan, int $tahun): array
    {
        return $this->transactionRepository->getDailySpending($userId, $bulan, $tahun);
    }

    public function getMonthComparison(int $userId, int $bulan, int $tahun): array
    {
        $current = $this->transactionRepository->getCategoryExpenses($userId, $bulan, $tahun);

        $prevBulan = $bulan === 1 ? 12 : $bulan - 1;
        $prevTahun = $bulan === 1 ? $tahun - 1 : $tahun;
        $previous  = $this->transactionRepository->getCategoryExpenses($userId, $prevBulan, $prevTahun);

        $currentMap = [];
        foreach ($current as $item) { $currentMap[$item->category_name] = (float) $item->total; }
        $previousMap = [];
        foreach ($previous as $item) { $previousMap[$item->category_name] = (float) $item->total; }

        $allCategories = array_unique(array_merge(array_keys($currentMap), array_keys($previousMap)));

        if (empty($allCategories)) {
            return [
                'comparison'   => [],
                'currentLabel' => ChartHelper::formatBulanLengkap($bulan) . ' ' . $tahun,
                'prevLabel'    => ChartHelper::formatBulanLengkap($prevBulan) . ' ' . $prevTahun,
                'isEmpty'      => true,
            ];
        }

        $comparison = [];
        foreach ($allCategories as $cat) {
            $cur    = $currentMap[$cat] ?? 0;
            $prev   = $previousMap[$cat] ?? 0;
            $change = $prev > 0 ? round((($cur - $prev) / $prev) * 100, 1) : null;
            $comparison[] = [
                'name'             => $cat,
                'current'          => $cur,
                'previous'         => $prev,
                'currentFormatted' => ChartHelper::formatRupiah($cur),
                'prevFormatted'    => ChartHelper::formatRupiah($prev),
                'change'           => $change,
                'isIncrease'       => $change !== null ? $change > 0 : null,
            ];
        }
        usort($comparison, fn($a, $b) => $b['current'] <=> $a['current']);

        return [
            'comparison'   => $comparison,
            'currentLabel' => ChartHelper::formatBulanLengkap($bulan) . ' ' . $tahun,
            'prevLabel'    => ChartHelper::formatBulanLengkap($prevBulan) . ' ' . $prevTahun,
            'isEmpty'      => false,
        ];
    }

    public function getHealthScore(int $userId, int $bulan, int $tahun): array
    {
        $totalIncome  = $this->transactionRepository->getTotalIncome($userId, $bulan, $tahun);
        $totalExpense = $this->transactionRepository->getTotalExpense($userId, $bulan, $tahun);

        if ($totalIncome === 0.0 && $totalExpense === 0.0) {
            return [
                'score' => 0, 'label' => 'Belum Ada Data', 'color' => '#9e9e9e',
                'components' => [],
                'tips'    => 'Mulai catat transaksi untuk melihat skor kesehatan finansialmu.',
                'isEmpty' => true,
            ];
        }

        $savingsScore = 0.0;
        if ($totalIncome > 0) {
            $savingsRate  = (($totalIncome - $totalExpense) / $totalIncome) * 100;
            $savingsScore = max(0.0, min(100.0, ($savingsRate / 30) * 100));
        }

        $diversityScore = 70.0;
        $categories = $this->transactionRepository->getCategoryExpenses($userId, $bulan, $tahun);
        if (!$categories->isEmpty() && $totalExpense > 0) {
            $maxCatPct      = ($categories->max('total') / $totalExpense) * 100;
            $diversityScore = max(0.0, min(100.0, ((80 - $maxCatPct) / 40) * 100));
        }

        $budgetScore = 70.0;
        $budgets = \App\Models\Budget::where('user_id', $userId)->get();
        if ($budgets->isNotEmpty()) {
            $onTrack     = $budgets->filter(fn($b) => !$b->isExceeded())->count();
            $budgetScore = ($onTrack / $budgets->count()) * 100;
        }

        $totalScore = (int) round(($savingsScore * 0.4) + ($diversityScore * 0.3) + ($budgetScore * 0.3));

        if ($totalScore >= 70)      { $label = 'Sehat';            $color = '#22C55E'; }
        elseif ($totalScore >= 40)  { $label = 'Cukup';            $color = '#F59E0B'; }
        else                        { $label = 'Perlu Perhatian';   $color = '#EF4444'; }

        $scores    = ['savings' => $savingsScore, 'diversity' => $diversityScore, 'budget' => $budgetScore];
        $lowestKey = (string) array_search(min($scores), $scores);
        $tips = match ($lowestKey) {
            'savings'   => 'Savings rate masih rendah. Coba kurangi pengeluaran atau cari sumber pemasukan tambahan.',
            'diversity' => 'Pengeluaran terlalu terpusat di satu kategori. Coba distribusikan lebih merata.',
            'budget'    => 'Ada budget yang sudah terlampaui. Cek halaman Budget untuk menyesuaikan.',
            default     => 'Pertahankan kebiasaan finansial yang baik!',
        };

        return [
            'score'      => $totalScore,
            'label'      => $label,
            'color'      => $color,
            'components' => [
                ['name' => 'Savings Rate',    'score' => (int) round($savingsScore),   'weight' => '40%'],
                ['name' => 'Diversifikasi',    'score' => (int) round($diversityScore), 'weight' => '30%'],
                ['name' => 'Kepatuhan Budget', 'score' => (int) round($budgetScore),    'weight' => '30%'],
            ],
            'tips'    => $tips,
            'isEmpty' => false,
        ];
    }


    public function getMetricCards(int $userId, int $bulan, int $tahun): array
    {
        $totalIncome  = $this->transactionRepository->getTotalIncome($userId, $bulan, $tahun);
        $totalExpense = $this->transactionRepository->getTotalExpense($userId, $bulan, $tahun);
        $saldo        = $totalIncome - $totalExpense;

        
        $expensePercentage = $totalIncome > 0
            ? round(($totalExpense / $totalIncome) * 100, 1)
            : ($totalExpense > 0 ? 100 : 0);

        
        $progressLevel = 'green';
        if ($expensePercentage > 90) {
            $progressLevel = 'red';
        } elseif ($expensePercentage >= 70) {
            $progressLevel = 'yellow';
        }

        return [
            'totalIncome'          => $totalIncome,
            'totalIncomeFormatted' => ChartHelper::formatRupiah($totalIncome),
            'totalExpense'          => $totalExpense,
            'totalExpenseFormatted' => ChartHelper::formatRupiah($totalExpense),
            'saldo'                 => $saldo,
            'saldoFormatted'        => ChartHelper::formatRupiah(abs($saldo)),
            'isSaldoPositif'        => $saldo >= 0,
            'expensePercentage'     => min($expensePercentage, 100),
            'progressLevel'         => $progressLevel,
            'overBudgetAmount'      => $saldo < 0 ? ChartHelper::formatRupiah(abs($saldo)) : null,
        ];
    }
}
