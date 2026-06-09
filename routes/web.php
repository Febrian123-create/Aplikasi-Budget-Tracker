<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BudgetReportController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    $premiumPrice = \App\Models\Membership::where('membership_name', 'Premium')->value('price') ?? 0;
    return view('welcome', compact('premiumPrice'));
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
        Route::post('/recurring/{id}/confirm', [RecurringTransactionController::class, 'confirmPayment'])
            ->name('recurring.confirm');
        Route::post('/recurring/{id}/skip', [RecurringTransactionController::class, 'skipPayment'])
            ->name('recurring.skip');

        // Google Calendar OAuth (Premium only)
        Route::get('/google/connect', [GoogleCalendarController::class, 'connect'])->name('google.connect');
        Route::get('/google/callback', [GoogleCalendarController::class, 'callback'])->name('google.callback');
        Route::post('/google/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('google.disconnect');
    });

    // Pop-up notifikasi — semua user (Free & Premium) agar budget alert juga muncul.
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

    Route::get('/budget/report', [BudgetReportController::class, 'index'])->name('budget.report');
    Route::get('/budget/report/export/pdf', [BudgetReportController::class, 'exportPdf'])->name('budget.report.export.pdf');
    Route::post('/budget/report/send-email', [BudgetReportController::class, 'sendEmail'])->name('budget.report.send-email');

    // Wishlist Routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::put('/wishlist/{wishlist}', [WishlistController::class, 'update'])->name('wishlist.update');
    Route::post('/wishlist/{wishlist}/alokasi', [WishlistController::class, 'alokasi'])->name('wishlist.alokasi');
    Route::post('/wishlist/{wishlist}/konfirmasi', [WishlistController::class, 'konfirmasiPembelian'])->name('wishlist.konfirmasi');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

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
