<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');
Route::resource('transactions', TransactionController::class)
    ->only(['index', 'store', 'destroy'])
    ->middleware(['auth']);

Route::get('/history', [TransactionController::class, 'history'])->name('transactions.history');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/dashboard', [TransactionController::class, 'index'])->name('dashboard');

    Route::resource('transactions', TransactionController::class)
        ->only(['store', 'edit', 'update', 'destroy']);

    // Membership routes
    Route::get('/membership', [\App\Http\Controllers\MembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/upgrade', [\App\Http\Controllers\MembershipController::class, 'upgrade'])->name('membership.upgrade');

    // Fitur 8 — Visualisasi Data (Pie Chart & Bar Chart) - Premium Only
    Route::middleware([\App\Http\Middleware\CheckPremiumMembership::class])->group(function () {
        Route::get('/charts', [ChartController::class, 'index'])->name('charts.index');
        Route::get('/charts/data', [ChartController::class, 'getData'])->name('charts.data');
    });

    // Fitur 11 — Recurring Transaction
    Route::get('/recurring', [RecurringTransactionController::class, 'index'])->name('recurring.index');
    Route::post('/recurring', [RecurringTransactionController::class, 'store'])->name('recurring.store');
    Route::get('/recurring/{id}/edit', [RecurringTransactionController::class, 'edit'])->name('recurring.edit');
    Route::put('/recurring/{id}', [RecurringTransactionController::class, 'update'])->name('recurring.update');
    Route::delete('/recurring/{id}', [RecurringTransactionController::class, 'destroy'])->name('recurring.destroy');
    Route::patch('/recurring/{id}/toggle', [RecurringTransactionController::class, 'toggleStatus'])->name('recurring.toggle');

    // Fitur Export Excel & PDF
    Route::get('/history/export/excel', [ExportController::class, 'historyExportExcel'])->name('history.export.excel');
    Route::get('/history/export/pdf', [ExportController::class, 'historyExportPdf'])->name('history.export.pdf');
    Route::get('/transactions/export/excel', [ExportController::class, 'transactionsExportExcel'])->name('transactions.export.excel');
    Route::get('/transactions/export/pdf', [ExportController::class, 'transactionsExportPdf'])->name('transactions.export.pdf');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

