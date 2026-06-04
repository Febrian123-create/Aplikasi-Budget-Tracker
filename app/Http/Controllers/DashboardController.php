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
        $filterType = $request->input('filter_type', 'bulanan');
        $year = $request->input('year') ? (int)$request->input('year') : Carbon::today()->year;
        $month = $request->input('month') ? (int)$request->input('month') : Carbon::today()->month;
        $week = $request->input('week') ? (int)$request->input('week') : 1;

        $subject = new OverviewSubject();
        $subject->subscribe(new IncomeOverviewObserver());
        $subject->subscribe(new ExpenseOverviewObserver());

        $subject->setFilter($filterType, $year, $month, $week, Auth::id());

        $totalIncome = $subject->getTotalIncome();
        $totalExpense = $subject->getTotalExpense();
        $balance = $subject->getBalance();
        $startDate = $subject->getStartDate();
        $endDate = $subject->getEndDate();

        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'balance',
            'startDate',
            'endDate',
            'filterType',
            'year',
            'month',
            'week'
        ));
    }
}

