<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Category;
use App\Observers\TransactionSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // Transaksi harian — hanya hari ini
        $transactions = Transaction::where('user_id', Auth::id())
            ->where('transaction_date', $today)
            ->with(['category', 'transactionType'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Total keseluruhan (semua waktu) untuk summary cards
        $totalIncome = Transaction::where('user_id', Auth::id())
            ->whereIn('transactionType_id', function ($query) {
                $query->select('transactionType_id')
                    ->from('transactiontype')
                    ->where('name', 'income');
            })
            ->sum('total_amount');

        $totalExpense = Transaction::where('user_id', Auth::id())
            ->whereIn('transactionType_id', function ($query) {
                $query->select('transactionType_id')
                    ->from('transactiontype')
                    ->where('name', 'expense');
            })
            ->sum('total_amount');

        $balance = $totalIncome - $totalExpense;
        $categories = Category::all();

        return view('transactions.index', compact('transactions', 'totalIncome', 'totalExpense', 'balance', 'categories', 'today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'category' => 'required|exists:category,category_id',
        ]);

        // Ambil ID langsung dari model TransactionType
        $transactionTypeId = TransactionType::where('name', $validated['type'])->value('transactionType_id');

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category'],
            'transactionType_id' => $transactionTypeId,
            'total_amount' => $validated['amount'],
            'transaction_date' => $validated['date'],
            'description' => $validated['description'],
        ]);

        // Notify observers (Observer Pattern — Fitur 8)
        TransactionSubject::getInstance()->notifyObservers('created', $transaction);

        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function edit(Transaction $transaction)
    {
        $categories = Category::all();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'category' => 'required|exists:category,category_id',
        ]);

        $transactionTypeId = TransactionType::where('name', $validated['type'])->value('transactionType_id');

        $transaction->update([
            'category_id' => $validated['category'],
            'transactionType_id' => $transactionTypeId,
            'total_amount' => $validated['amount'],
            'transaction_date' => $validated['date'],
            'description' => $validated['description'],
        ]);

        // Notify observers (Observer Pattern — Fitur 8)
        TransactionSubject::getInstance()->notifyObservers('updated', $transaction);

        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $deletedTransaction = clone $transaction;
        $transaction->delete();

        // Notify observers (Observer Pattern — Fitur 8)
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
            ->get();

        $totalIncome = $transactions->where('transactionType_id', 1)->sum('total_amount');
        $totalExpense = $transactions->where('transactionType_id', 2)->sum('total_amount');
        $balance = $totalIncome - $totalExpense;

        $totalFiltered = $transactions->sum('total_amount');

        $categories = Category::all();

        return view('transactions.history', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'totalFiltered',
            'categories'
        ));
    }

}
