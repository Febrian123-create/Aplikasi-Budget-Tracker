<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->reset_otp_code = $otpCode;
            $user->reset_otp_expires_at = now()->addMinutes(10);
            $user->save();

            \Illuminate\Support\Facades\Log::info("Reset Password OTP Code for {$user->email}: {$otpCode}");
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResetPasswordOtpMail($otpCode, $user->name));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim email reset password OTP: ' . $e->getMessage());
            }
        }

        $request->session()->put('pending_reset_email', $request->email);

        return redirect()->route('password.otp')->with('status', 'Jika email terdaftar, kode verifikasi OTP akan dikirim.');
    }
}
