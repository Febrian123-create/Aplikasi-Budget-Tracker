<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login – DUMMY: selalu redirect ke dashboard dengan pesan sukses.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        // ── DUMMY: simulasi login berhasil ───────────────────────────────
        // Ganti blok ini dengan Auth::attempt() saat backend nyata siap
        // ────────────────────────────────────────────────────────────────
        return redirect()->route('dashboard')
                         ->with('success', 'Login berhasil! Selamat datang, ' . $request->email);
    }

    /**
     * Proses logout – DUMMY.
     */
    public function logout(Request $request)
    {
        // Auth::logout(); // aktifkan nanti
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Halaman register – DUMMY placeholder.
     */
    public function showRegister()
    {
        return redirect()->route('login')->with('success', 'Fitur registrasi segera hadir!');
    }

    /**
     * Halaman lupa password – DUMMY placeholder.
     */
    public function showForgotPassword()
    {
        return redirect()->route('login')->with('success', 'Fitur lupa password segera hadir!');
    }

    /**
     * Dashboard – DUMMY placeholder setelah login.
     */
    public function dashboard()
    {
        return view('dashboard');
    }
}
