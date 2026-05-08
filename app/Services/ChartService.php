<?php

namespace App\Services;

use App\Helpers\ChartHelper;
use App\Repositories\TransactionRepository;

/**
 * ChartService — Single Responsibility: ambil & format data untuk grafik.
 * Menerima TransactionRepository via Dependency Injection.
 */
class ChartService
{
    public function __construct(
        private TransactionRepository $transactionRepository
    ) {}

    /**
     * Ambil distribusi pengeluaran per kategori (untuk Pie/Donut chart).
     * Jika kategori > 6, gabung terkecil menjadi "Lainnya".
     *
     * @return array{categories: array, total: float, isEmpty: bool, allIncome: bool}
     */
    public function getCategoryDistribution(int $userId, int $bulan, int $tahun): array
    {
        $data  = $this->transactionRepository->getCategoryExpenses($userId, $bulan, $tahun);
        $total = $data->sum('total');

        // Cek apakah ada pemasukan tapi tidak ada pengeluaran
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
            // Semua kategori ditampilkan
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
            // Ambil top 5, sisanya gabung jadi "Lainnya"
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

    /**
     * Ambil data tren bulanan pemasukan vs pengeluaran (untuk Bar chart).
     *
     * @return array{months: array, averageExpense: float, isEmpty: bool, lessThanTwoMonths: bool}
     */
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

            // Hitung MoM Growth
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

    /**
     * Ambil data metrik ringkasan bulan aktif (untuk Kartu Metrik).
     *
     * @return array{totalIncome: float, totalExpense: float, saldo: float, ...}
     */
    public function getMetricCards(int $userId, int $bulan, int $tahun): array
    {
        $totalIncome  = $this->transactionRepository->getTotalIncome($userId, $bulan, $tahun);
        $totalExpense = $this->transactionRepository->getTotalExpense($userId, $bulan, $tahun);
        $saldo        = $totalIncome - $totalExpense;

        // Persentase pengeluaran dari pemasukan
        $expensePercentage = $totalIncome > 0
            ? round(($totalExpense / $totalIncome) * 100, 1)
            : ($totalExpense > 0 ? 100 : 0);

        // Tentukan level warna progress bar
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
