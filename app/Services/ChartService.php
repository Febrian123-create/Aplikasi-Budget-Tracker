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
