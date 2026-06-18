<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Category;
use App\Observers\TransactionSubject;
use App\Factories\TransactionFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $transactions = Transaction::where('user_id', Auth::id())
            ->whereDate('transaction_date', Carbon::today())
            ->with(['category', 'transactionType'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('transaction_id', 'desc')
            ->paginate(10);

        // Calculate today's income and expenses
        $totalIncomeToday = Transaction::where('user_id', Auth::id())
            ->whereDate('transaction_date', Carbon::today())
            ->where('transactionType_id', 1)
            ->sum('total_amount');

        $totalExpenseToday = Transaction::where('user_id', Auth::id())
            ->whereDate('transaction_date', Carbon::today())
            ->where('transactionType_id', 2)
            ->sum('total_amount');

        $categories = Category::whereNotIn('category_id', [10, 11])->get();

        return view('transactions.index', compact('transactions', 'categories', 'today', 'totalIncomeToday', 'totalExpenseToday'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'category' => $request->input('type') === 'expense' ? 'required|exists:category,category_id' : 'nullable|exists:category,category_id',
        ], [
            'date.before_or_equal' => 'Tanggal transaksi tidak boleh melebihi hari ini.',
        ]);

        $categoryId = null;
        if ($validated['type'] === 'expense') {
            $categoryId = $validated['category'];
        } else {
            $categoryId = $validated['category'] ?: 10;
        }

        $transaction = TransactionFactory::createRegularTransaction([
            'user_id'     => Auth::id(),
            'category_id' => $categoryId,
            'type'        => $validated['type'],
            'amount'      => $validated['amount'],
            'date'        => $validated['date'],
            'description' => $validated['description'],
        ]);

        TransactionSubject::getInstance()->notifyObservers('created', $transaction);

        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function edit(Transaction $transaction)
    {
        $categories = Category::whereNotIn('category_id', [10, 11])->get();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'category' => $request->input('type') === 'expense' ? 'required|exists:category,category_id' : 'nullable|exists:category,category_id',
        ], [
            'date.before_or_equal' => 'Tanggal transaksi tidak boleh melebihi hari ini.',
        ]);

        $transactionTypeId = TransactionType::where('name', $validated['type'])->value('transactionType_id');

        $categoryId = null;
        if ($validated['type'] === 'expense') {
            $categoryId = $validated['category'];
        } else {
            $categoryId = $validated['category'] ?: 10;
        }

        $transaction->update([
            'category_id' => $categoryId,
            'transactionType_id' => $transactionTypeId,
            'total_amount' => $validated['amount'],
            'transaction_date' => $validated['date'],
            'description' => $validated['description'],
        ]);


        TransactionSubject::getInstance()->notifyObservers('updated', $transaction);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $deletedTransaction = clone $transaction;
        $transaction->delete();


        TransactionSubject::getInstance()->notifyObservers('deleted', $deletedTransaction);

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus!');
    }

    public function history(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id());

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('transactionType_id')) {
            $query->where('transactionType_id', $request->transactionType_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->with(['category', 'transactionType'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('transaction_id', 'desc')
            ->paginate(10);

        $categories = Category::all();

        $isTypeFiltered = $request->filled('transactionType_id');
        $isCategoryFiltered = $request->filled('category_id');

        $totalIncome = 0;
        $totalExpense = 0;
        $totalNominal = 0;
        $summaryTitle = 'Total Nominal';

        if ($isTypeFiltered) {
            $totalQuery = Transaction::where('user_id', Auth::id())
                ->where('transactionType_id', $request->transactionType_id);

            if ($isCategoryFiltered) {
                $totalQuery->where('category_id', $request->category_id);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $totalQuery->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
            }

            $totalNominal = $totalQuery->sum('total_amount');
            $summaryTitle = $request->transactionType_id == 1 ? 'Total Pemasukan' : 'Total Pengeluaran';
        } else {
            $incomeQuery = Transaction::where('user_id', Auth::id())
                ->where('transactionType_id', 1);

            $expenseQuery = Transaction::where('user_id', Auth::id())
                ->where('transactionType_id', 2);

            if ($isCategoryFiltered) {
                $incomeQuery->where('category_id', $request->category_id);
                $expenseQuery->where('category_id', $request->category_id);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $incomeQuery->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
                $expenseQuery->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
            }

            $totalIncome = $incomeQuery->sum('total_amount');
            $totalExpense = $expenseQuery->sum('total_amount');
            $totalNominal = $totalIncome + $totalExpense;
        }

        return view('transactions.history', compact(
            'transactions',
            'categories',
            'totalIncome',
            'totalExpense',
            'totalNominal',
            'summaryTitle',
            'isTypeFiltered'
        ));
    }
}
