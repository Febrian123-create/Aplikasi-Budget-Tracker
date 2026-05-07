<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())
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

        return view('dashboard', compact('transactions', 'totalIncome', 'totalExpense', 'balance', 'categories'));
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

        $transactionTypeId = DB::table('transactiontype')
            ->where('name', $validated['type'])
            ->value('transactionType_id');

        Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category'],
            'transactionType_id' => $transactionTypeId,
            'total_amount' => $validated['amount'],
            'transaction_date' => $validated['date'],
            'description' => $validated['description'],
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus!');
    }
}
