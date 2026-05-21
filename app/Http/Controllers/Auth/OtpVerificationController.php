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
use Carbon\Carbon;

class OtpVerificationController extends Controller
{
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $pending = $request->session()->get('pending_registration');

        if (!$pending) {
            return redirect()->route('register')
                ->withErrors(['email' => 'Silakan daftarkan akun terlebih dahulu.']);
        }

        return view('auth.verify-otp', ['email' => $pending['email']]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('pending_registration');

        if (!$pending) {
            return redirect()->route('register');
        }

        // Ambil kode OTP dari input (bisa array digit atau string langsung)
        $otpCode = $request->input('otp_code');
        if (!$otpCode && $request->has('otp')) {
            $otpInput = $request->input('otp');
            $otpCode  = is_array($otpInput) ? implode('', $otpInput) : $otpInput;
        }

        if (empty($otpCode)) {
            return back()->withErrors(['otp' => 'Kode OTP harus diisi lengkap.']);
        }

        // Cek kecocokan OTP
        if ($pending['otp_code'] !== $otpCode) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        // Cek apakah OTP sudah kedaluwarsa
        if (Carbon::now()->isAfter(Carbon::parse($pending['otp_expires_at']))) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang kode baru.']);
        }

        // OTP valid — baru buat user di database
        $user = User::create([
            'name'              => $pending['name'],
            'email'             => $pending['email'],
            'password'          => $pending['password'], // sudah di-hash
            'email_verified_at' => now(),
            'otp_code'          => null,
            'otp_expires_at'    => null,
        ]);

        // Hapus data pending dari session
        $request->session()->forget('pending_registration');

        // Trigger event Registered & login otomatis
        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Email berhasil diverifikasi! Selamat datang di BUNREK.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('pending_registration');

        if (!$pending) {
            return redirect()->route('register');
        }

        // Buat OTP baru
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update session dengan OTP baru
        $pending['otp_code']       = $otpCode;
        $pending['otp_expires_at'] = Carbon::now()->addMinutes(10)->toDateTimeString();
        $request->session()->put('pending_registration', $pending);

        // Kirim ulang email
        Log::info("Resent OTP Code for {$pending['email']}: {$otpCode}");
        try {
            Mail::to($pending['email'])->send(new \App\Mail\VerificationOtpMail($otpCode, $pending['name']));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim ulang email OTP pendaftaran: ' . $e->getMessage());
            return back()->withErrors(['otp' => 'Gagal mengirim email verifikasi. Silakan coba sesaat lagi.']);
        }

        return back()->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
