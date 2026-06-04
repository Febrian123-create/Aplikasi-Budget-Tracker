<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Cek budget over-threshold saat dashboard dibuka → buat pop-up (dedup harian).
        app(\App\Services\BudgetService::class)->checkAllForUser(Auth::id());

        // Ambil range 1 bulan terakhir
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subMonth();

        // Total Pemasukan 1 bulan
        $totalIncome = Transaction::where('user_id', Auth::id())
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('transactionType_id', function ($query) {
                $query->select('transactionType_id')
                    ->from('transactiontype')
                    ->where('name', 'income');
            })
            ->sum('total_amount');

        // Total Pengeluaran 1 bulan
        $totalExpense = Transaction::where('user_id', Auth::id())
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('transactionType_id', function ($query) {
                $query->select('transactionType_id')
                    ->from('transactiontype')
                    ->where('name', 'expense');
            })
            ->sum('total_amount');

        // Total Balance
        $balance = $totalIncome - $totalExpense;

        return view('dashboard', compact('totalIncome', 'totalExpense', 'balance', 'startDate', 'endDate'));
    }
}
