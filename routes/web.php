<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\BudgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});



Route::middleware(['auth', 'role:user'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    
    Route::resource('transactions', TransactionController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::get('/history', [TransactionController::class, 'history'])
        ->name('transactions.history');

    
    Route::get('/membership', [\App\Http\Controllers\MembershipController::class, 'index'])
        ->name('membership.index');
    Route::post('/membership/upgrade', [\App\Http\Controllers\MembershipController::class, 'upgrade'])
        ->middleware('free')
        ->name('membership.upgrade');

    
    Route::middleware('premium')->group(function () {
        Route::get('/charts', [ChartController::class, 'index'])
            ->name('charts.index');
        Route::get('/charts/data', [ChartController::class, 'getData'])
            ->name('charts.data');
    });

    
    Route::get('/recurring', [RecurringTransactionController::class, 'index'])
        ->name('recurring.index');
    Route::post('/recurring', [RecurringTransactionController::class, 'store'])
        ->name('recurring.store');
    Route::get('/recurring/{id}/edit', [RecurringTransactionController::class, 'edit'])
        ->name('recurring.edit');
    Route::put('/recurring/{id}', [RecurringTransactionController::class, 'update'])
        ->name('recurring.update');
    Route::delete('/recurring/{id}', [RecurringTransactionController::class, 'destroy'])
        ->name('recurring.destroy');
    Route::patch('/recurring/{id}/toggle', [RecurringTransactionController::class, 'toggleStatus'])
        ->name('recurring.toggle');
    Route::get('/recurring/popups/unread', [RecurringTransactionController::class, 'unreadPopups'])
        ->name('recurring.popups.unread');
    Route::post('/recurring/popups/{logId}/read', [RecurringTransactionController::class, 'markPopupRead'])
        ->name('recurring.popups.read');

    
    Route::get('/history/export/excel', [ExportController::class, 'historyExportExcel'])
        ->name('history.export.excel');
    Route::get('/history/export/pdf', [ExportController::class, 'historyExportPdf'])
        ->name('history.export.pdf');
    Route::get('/transactions/export/excel', [ExportController::class, 'transactionsExportExcel'])
        ->name('transactions.export.excel');
    Route::get('/transactions/export/pdf', [ExportController::class, 'transactionsExportPdf'])
        ->name('transactions.export.pdf');

    
    Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index');
    Route::post('/budget', [BudgetController::class, 'store'])->name('budget.store');
    Route::delete('/budget/{id}', [BudgetController::class, 'destroy'])->name('budget.destroy');
    Route::get('/budget/settings', [BudgetController::class, 'settings'])->name('budget.settings');
    Route::post('/budget/settings', [BudgetController::class, 'saveSettings'])->name('budget.settings.save');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
    return view('admin.index');
    });
});

require __DIR__ . '/auth.php';