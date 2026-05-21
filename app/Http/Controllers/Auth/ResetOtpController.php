<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ResetOtpController extends Controller
{
    
    public function showOtpForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('pending_reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-otp', ['email' => $request->session()->get('pending_reset_email')]);
    }

    
    public function verifyOtp(Request $request): RedirectResponse
    {
        if (!$request->session()->has('pending_reset_email')) {
            return redirect()->route('password.request');
        }

        $email = $request->session()->get('pending_reset_email');
        $user = User::where('email', $email)->first();

        
        $otpCode = $request->input('otp_code');
        if (!$otpCode && $request->has('otp')) {
            $otpInput = $request->input('otp');
            if (is_array($otpInput)) {
                $otpCode = implode('', $otpInput);
            } else {
                $otpCode = $otpInput;
            }
        }

        $request->validate([
            'otp_code' => 'sometimes|string|size:6',
        ]);

        if (empty($otpCode)) {
            return back()->withErrors(['otp' => 'Kode OTP harus diisi lengkap.']);
        }

        
        if (!$user || $user->reset_otp_code !== $otpCode) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        
        if ($user->reset_otp_expires_at && $user->reset_otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang kode baru.']);
        }

        
        $user->reset_otp_code = null;
        $user->reset_otp_expires_at = null;
        $user->save();

        
        $request->session()->put('reset_password_allowed_email', $user->email);
        $request->session()->forget('pending_reset_email');

        return redirect()->route('password.reset.form');
    }

    
    public function resendOtp(Request $request): RedirectResponse
    {
        if (!$request->session()->has('pending_reset_email')) {
            return redirect()->route('password.request');
        }

        $email = $request->session()->get('pending_reset_email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->reset_otp_code = $otpCode;
            $user->reset_otp_expires_at = now()->addMinutes(10);
            $user->save();

            try {
                Mail::to($user->email)->send(new \App\Mail\ResetPasswordOtpMail($otpCode, $user->name));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim ulang email OTP reset password: ' . $e->getMessage());
                return back()->withErrors(['otp' => 'Gagal mengirim email verifikasi. Silakan coba lagi.']);
            }
        }

        return back()->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    
    public function showResetForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('reset_password_allowed_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Silakan verifikasi email Anda terlebih dahulu.']);
        }

        $email = $request->session()->get('reset_password_allowed_email');

        return view('auth.reset-password', ['email' => $email]);
    }

    
    public function resetPassword(Request $request): RedirectResponse
    {
        if (!$request->session()->has('reset_password_allowed_email')) {
            return redirect()->route('password.request');
        }

        $email = $request->session()->get('reset_password_allowed_email');
        
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        
        $request->session()->forget('reset_password_allowed_email');

        return redirect()->route('login')->with('status', 'Password Anda berhasil diperbarui. Silakan masuk.');
    }
}
