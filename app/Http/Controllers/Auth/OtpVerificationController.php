<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('pending_user_id')) {
            return redirect()->route('register')->withErrors(['email' => 'Silakan daftarkan akun terlebih dahulu.']);
        }

        $userId = $request->session()->get('pending_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', ['email' => $user->email]);
    }

    
    public function verify(Request $request): RedirectResponse
    {
        if (!$request->session()->has('pending_user_id')) {
            return redirect()->route('register');
        }

        $userId = $request->session()->get('pending_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('register');
        }

        
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

        
        if ($user->otp_code !== $otpCode) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        
        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang kode baru.']);
        }

        
        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        
        event(new Registered($user));

        
        Auth::login($user);

        
        $request->session()->forget('pending_user_id');

        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi! Selamat datang di BUNREK.');
    }

    
    public function resend(Request $request): RedirectResponse
    {
        if (!$request->session()->has('pending_user_id')) {
            return redirect()->route('register');
        }

        $userId = $request->session()->get('pending_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('register');
        }

        
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->otp_code = $otpCode;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            Mail::to($user->email)->send(new \App\Mail\VerificationOtpMail($otpCode, $user->name));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim ulang email OTP pendaftaran: ' . $e->getMessage());
            return back()->withErrors(['otp' => 'Gagal mengirim email verifikasi. Silakan coba sesaat lagi.']);
        }

        return back()->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
