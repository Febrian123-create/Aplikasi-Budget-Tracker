<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ChartController;
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
        ->only(['store', 'destroy']);

    // Fitur 8 — Visualisasi Data (Pie Chart & Bar Chart)
    Route::get('/charts', [ChartController::class, 'index'])->name('charts.index');
    Route::get('/charts/data', [ChartController::class, 'getData'])->name('charts.data');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

