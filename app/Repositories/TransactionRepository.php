<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * TransactionRepository — Data Access Layer untuk transaksi.
 * Menyediakan query-query yang dibutuhkan ChartService.
 */
class TransactionRepository
{
    /**
     * Ambil distribusi pengeluaran per kategori untuk bulan & tahun tertentu.
     *
     * @return Collection<int, object{category_name: string, total: float}>
     */
    public function getCategoryExpenses(int $userId, int $bulan, int $tahun): Collection
    {
        // Cari transactionType_id untuk 'expense'
        $expenseTypeId = TransactionType::where('name', 'expense')->value('transactionType_id');

        return DB::table('transaction as t')
            ->join('category as c', 't.category_id', '=', 'c.category_id')
            ->select('c.category_name', DB::raw('SUM(t.total_amount) as total'))
            ->where('t.user_id', $userId)
            ->where('t.transactionType_id', $expenseTypeId)
            ->whereMonth('t.transaction_date', $bulan)
            ->whereYear('t.transaction_date', $tahun)
            ->groupBy('c.category_id', 'c.category_name')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Ambil data bulanan pemasukan vs pengeluaran.
     *
     * @return Collection<int, object{bulan: int, tahun: int, total_pemasukan: float, total_pengeluaran: float}>
     */
    public function getMonthlyData(int $userId, string $startDate): Collection
    {
        $incomeTypeId  = TransactionType::where('name', 'income')->value('transactionType_id');
        $expenseTypeId = TransactionType::where('name', 'expense')->value('transactionType_id');

        return DB::table('transaction as t')
            ->select(
                DB::raw('MONTH(t.transaction_date) as bulan'),
                DB::raw('YEAR(t.transaction_date) as tahun'),
                DB::raw("SUM(CASE WHEN t.transactionType_id = {$incomeTypeId} THEN t.total_amount ELSE 0 END) as total_pemasukan"),
                DB::raw("SUM(CASE WHEN t.transactionType_id = {$expenseTypeId} THEN t.total_amount ELSE 0 END) as total_pengeluaran")
            )
            ->where('t.user_id', $userId)
            ->where('t.transaction_date', '>=', $startDate)
            ->groupBy(DB::raw('YEAR(t.transaction_date)'), DB::raw('MONTH(t.transaction_date)'))
            ->orderBy(DB::raw('YEAR(t.transaction_date)'), 'asc')
            ->orderBy(DB::raw('MONTH(t.transaction_date)'), 'asc')
            ->get();
    }

    /**
     * Ambil total pemasukan bulan ini.
     */
    public function getTotalIncome(int $userId, int $bulan, int $tahun): float
    {
        $incomeTypeId = TransactionType::where('name', 'income')->value('transactionType_id');

        return (float) DB::table('transaction')
            ->where('user_id', $userId)
            ->where('transactionType_id', $incomeTypeId)
            ->whereMonth('transaction_date', $bulan)
            ->whereYear('transaction_date', $tahun)
            ->sum('total_amount');
    }

    /**
     * Ambil total pengeluaran bulan ini.
     */
    public function getTotalExpense(int $userId, int $bulan, int $tahun): float
    {
        $expenseTypeId = TransactionType::where('name', 'expense')->value('transactionType_id');

        return (float) DB::table('transaction')
            ->where('user_id', $userId)
            ->where('transactionType_id', $expenseTypeId)
            ->whereMonth('transaction_date', $bulan)
            ->whereYear('transaction_date', $tahun)
            ->sum('total_amount');
    }
}
