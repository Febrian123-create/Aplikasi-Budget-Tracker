<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');
Route::resource('transactions', TransactionController::class)
    ->only(['index', 'store', 'destroy'])
    ->middleware(['auth']);

Route::get('/history', function () {
    return view('transactions.history');
})->middleware(['auth'])->name('transactions.history');

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

    Route::get('/history', function () {
        return view('transactions.history');
    })->name('transactions.history');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
