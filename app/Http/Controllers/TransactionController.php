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

        
        $transactions = Transaction::where('user_id', Auth::id())
            ->where('transaction_date', $today)
            ->with(['category', 'transactionType'])
            ->orderBy('transaction_date', 'desc')
            ->get();

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

        
        $transactionTypeId = TransactionType::where('name', $validated['type'])->value('transactionType_id');

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category'],
            'transactionType_id' => $transactionTypeId,
            'total_amount' => $validated['amount'],
            'transaction_date' => $validated['date'],
            'description' => $validated['description'],
        ]);

        
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
            ->get();

        $categories = Category::all();
        
        // Hitung total jika kategori dipilih
        $totalByCategory = 0;
        $isCategoryFiltered = $request->filled('category_id');
        if ($isCategoryFiltered) {
            $totalQuery = Transaction::where('user_id', Auth::id())
                ->where('category_id', $request->category_id);
            
            if ($request->filled('transactionType_id')) {
                $totalQuery->where('transactionType_id', $request->transactionType_id);
            }
            
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $totalQuery->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
            }
            
            $totalByCategory = $totalQuery->sum('total_amount');
        }

        // Hitung balance berdasarkan filter jenis
        $totalIncome = 0;
        $totalExpense = 0;
        $isTypeFiltered = $request->filled('transactionType_id');
        
        if ($isTypeFiltered) {
            // Jika jenis dipilih, hitung total untuk jenis tersebut
            $balanceQuery = Transaction::where('user_id', Auth::id())
                ->where('transactionType_id', $request->transactionType_id);
            
            if ($request->filled('category_id')) {
                $balanceQuery->where('category_id', $request->category_id);
            }
            
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $balanceQuery->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
            }
            
            $balance = $balanceQuery->sum('total_amount');
            
            if ($request->transactionType_id == 1) {
                $totalIncome = $balance;
            } else {
                $totalExpense = $balance;
            }
        } else {
            // Jika hanya tanggal atau tidak ada filter, hitung income dan expense terpisah
            $incomeQuery = Transaction::where('user_id', Auth::id())
                ->where('transactionType_id', 1);
            
            $expenseQuery = Transaction::where('user_id', Auth::id())
                ->where('transactionType_id', 2);
            
            if ($request->filled('category_id')) {
                $incomeQuery->where('category_id', $request->category_id);
                $expenseQuery->where('category_id', $request->category_id);
            }
            
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $incomeQuery->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
                $expenseQuery->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
            }
            
            $totalIncome = $incomeQuery->sum('total_amount');
            $totalExpense = $expenseQuery->sum('total_amount');
        }

        return view('transactions.history', compact(
            'transactions',
            'categories',
            'totalByCategory',
            'isCategoryFiltered',
            'totalIncome',
            'totalExpense',
            'isTypeFiltered'
        ));
    }
}
