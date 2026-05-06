<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// ── Redirect root ke login ───────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Auth Routes ──────────────────────────────────────────────────────────────
Route::get('/login',           [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',          [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',         [AuthController::class, 'logout'])->name('logout');

// Register (dummy placeholder)
Route::get('/register',        [AuthController::class, 'showRegister'])->name('register');

// Forgot password (dummy placeholder)
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');

// ── Dashboard (setelah login berhasil) – DUMMY ───────────────────────────────
Route::get('/dashboard',       [AuthController::class, 'dashboard'])->name('dashboard');
