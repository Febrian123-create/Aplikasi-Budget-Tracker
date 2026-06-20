<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Strategies\Export\ExportStrategyInterface;
use App\Strategies\Export\ExcelExportStrategy;
use App\Strategies\Export\PdfExportStrategy;

class ExportController extends Controller
{
    
    private function buildFilteredQuery(Request $request)
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

        return $query->with(['category', 'transactionType'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('transaction_id', 'desc');
    }

    
    private function prepareExportData($transactions)
    {
        $totalIncome = $transactions->where('transactionType_id', 1)->sum('total_amount');
        $totalExpense = $transactions->where('transactionType_id', 2)->sum('total_amount');
        $balance = $totalIncome - $totalExpense;

        return [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'exportDate' => Carbon::now()->format('d-m-Y H:i:s'),
            'exportDateFile' => Carbon::now()->format('Y-m-d'),
        ];
    }

    
    private function export(ExportStrategyInterface $strategy, array $data, string $filename)
    {
        return $strategy->export($data, $filename);
    }

    
    public function historyExportExcel(Request $request)
    {
        $transactions = $this->buildFilteredQuery($request)->get();
        $data = $this->prepareExportData($transactions);
        $data['title'] = 'Laporan History Transaksi';
        $data['filters'] = $this->getActiveFilters($request);
        $filename = 'history_' . $data['exportDateFile'] . '.xlsx';
        return $this->export(new ExcelExportStrategy(), $data, $filename);
    }

    
    public function historyExportPdf(Request $request)
    {
        $transactions = $this->buildFilteredQuery($request)->get();
        $data = $this->prepareExportData($transactions);
        $data['title'] = 'Laporan History Transaksi';
        $data['filters'] = $this->getActiveFilters($request);
        $filename = 'history_' . $data['exportDateFile'] . '.pdf';
        return $this->export(new PdfExportStrategy(), $data, $filename);
    }

    
    public function transactionsExportExcel(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $transactions = Transaction::where('user_id', Auth::id())
            ->where('transaction_date', $today)
            ->with(['category', 'transactionType'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('transaction_id', 'desc')
            ->get();
        $data = $this->prepareExportData($transactions);
        $data['title'] = 'Laporan Transaksi Hari Ini';
        $data['filters'] = ['Tanggal: ' . Carbon::today()->format('d-m-Y')];
        $filename = 'transaksi_harian_' . $data['exportDateFile'] . '.xlsx';
        return $this->export(new ExcelExportStrategy(), $data, $filename);
    }

    
    public function transactionsExportPdf(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $transactions = Transaction::where('user_id', Auth::id())
            ->where('transaction_date', $today)
            ->with(['category', 'transactionType'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('transaction_id', 'desc')
            ->get();
        $data = $this->prepareExportData($transactions);
        $data['title'] = 'Laporan Transaksi Hari Ini';
        $data['filters'] = ['Tanggal: ' . Carbon::today()->format('d-m-Y')];
        $filename = 'transaksi_harian_' . $data['exportDateFile'] . '.pdf';
        return $this->export(new PdfExportStrategy(), $data, $filename);
    }

    
    private function getActiveFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('category_id')) {
            $category = Category::find($request->category_id);
            $filters[] = 'Kategori: ' . ($category->category_name ?? $request->category_id);
        }

        if ($request->filled('transactionType_id')) {
            $type = $request->transactionType_id == 1 ? 'Pemasukan' : 'Pengeluaran';
            $filters[] = 'Jenis: ' . $type;
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filters[] = 'Periode: ' . Carbon::parse($request->start_date)->format('d-m-Y') .
                ' s/d ' . Carbon::parse($request->end_date)->format('d-m-Y');
        }

        return $filters;
    }
}
