<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Observers\OverviewSubject;
use App\Observers\IncomeOverviewObserver;
use App\Observers\ExpenseOverviewObserver;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Cek budget over-threshold saat dashboard dibuka → buat pop-up (dedup harian).
        app(\App\Services\BudgetService::class)->checkAllForUser(Auth::id());

        $today = Carbon::today();

        $filterType = $request->input('filter_type', 'bulanan');
        $year = $request->input('year') ? (int)$request->input('year') : $today->year;
        $month = $request->input('month') ? (int)$request->input('month') : $today->month;
        $week = $request->input('week') ? (int)$request->input('week') : 1;

        // Cek dan batasi agar input tidak melebihi tanggal hari ini
        if ($year > $today->year) {
            $year = $today->year;
        }
        if ($year === $today->year && $month > $today->month) {
            $month = $today->month;
        }
        if ($year === $today->year && $month === $today->month) {
            $todayDay = $today->day;
            $currentWeekNum = 5;
            if ($todayDay <= 7) $currentWeekNum = 1;
            elseif ($todayDay <= 14) $currentWeekNum = 2;
            elseif ($todayDay <= 21) $currentWeekNum = 3;
            elseif ($todayDay <= 28) $currentWeekNum = 4;

            if ($week > $currentWeekNum) {
                $week = $currentWeekNum;
            }
        }

        $subject = new OverviewSubject();
        $subject->subscribe(new IncomeOverviewObserver());
        $subject->subscribe(new ExpenseOverviewObserver());

        $subject->setFilter($filterType, $year, $month, $week, Auth::id());

        $totalIncome = $subject->getTotalIncome();
        $totalExpense = $subject->getTotalExpense();
        $balance = $subject->getBalance();
        $startDate = $subject->getStartDate();
        $endDate = $subject->getEndDate();

        // Greeting logic berdasarkan waktu
        $currentHour = Carbon::now()->hour;
        if ($currentHour >= 5 && $currentHour < 12) {
            $greeting = 'pagi';
        } elseif ($currentHour >= 12 && $currentHour < 15) {
            $greeting = 'siang';
        } elseif ($currentHour >= 15 && $currentHour < 18) {
            $greeting = 'sore';
        } else {
            $greeting = 'malam';
        }

        // Data greeting
        $userName = Auth::user()->name;
        $currentDate = Carbon::now()->locale('id')->isoFormat('dddd, DD MMMM YYYY');

        // Hitung transaksi hari ini
        $todayTransactionCount = Transaction::where('user_id', Auth::id())
            ->whereDate('transaction_date', Carbon::today()->toDateString())
            ->count();

        // Cek status membership user
        $user = Auth::user();
        $isPremium = false;
        if ($user && method_exists($user, 'membership') && $user->membership) {
            $isPremium = strtolower($user->membership->membership_name) === 'premium';
        }

        $budgets = collect();
        $categoryDistribution = null;
        $chartColors = null;

        if (!$isPremium) {
            // Kategori budget highlight untuk user free
            $budgets = \App\Models\Budget::where('user_id', Auth::id())
                ->with('category')
                ->get();
        } else {
            // Data chart distribusi pengeluaran untuk user membership (premium)
            $transactionRepository = new \App\Repositories\TransactionRepository();
            $chartService = new \App\Services\ChartService($transactionRepository);
            $categoryDistribution = $chartService->getCategoryDistribution(Auth::id(), $month, $year);
            $chartColors = \App\Helpers\ChartHelper::getChartColors();
        }

        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'balance',
            'startDate',
            'endDate',
            'filterType',
            'year',
            'month',
            'week',
            'greeting',
            'userName',
            'currentDate',
            'todayTransactionCount',
            'isPremium',
            'budgets',
            'categoryDistribution',
            'chartColors'
        ));
    }
}
