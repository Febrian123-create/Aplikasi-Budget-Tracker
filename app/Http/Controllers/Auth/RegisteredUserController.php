<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan data pendaftaran di session — belum masuk DB
        $request->session()->put('pending_registration', [
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'otp_code'       => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(10)->toDateTimeString(),
        ]);

        // Kirim OTP ke email
        Log::info("OTP Code for {$request->email}: {$otpCode}");
        try {
            Mail::to($request->email)->send(new \App\Mail\VerificationOtpMail($otpCode, $request->name));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email OTP pendaftaran: ' . $e->getMessage());
        }

        return redirect()->route('verification.otp');
    }
}
