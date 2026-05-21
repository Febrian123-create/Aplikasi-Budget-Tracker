<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi OTP Reset Password - BUNREK</title>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    
    <link rel="stylesheet" href="{{ asset('css/bunrek-tokens.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bunrek-app.css') }}" />

    <style>
        .otp-inputs {
            display: flex;
            gap: 12px;
            justify-content: space-between;
            margin-bottom: var(--space-lg);
        }
        .otp-digit {
            width: 50px;
            height: 56px;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            outline: none;
            transition: var(--transition-fast);
            font-family: var(--font-heading);
        }
        .otp-digit:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-50);
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        
        <div class="auth-brand-panel">
            <div class="auth-brand-content">
                <div class="auth-brand-logo">
                    <i class="bi bi-wallet2"></i>
                    <span>BUNREK</span>
                </div>
                <p class="auth-brand-tagline">
                    Kelola anggaran keuangan Anda dengan lebih pintar, cepat, dan mudah dalam satu platform terintegrasi.
                </p>
            </div>
        </div>

        
        <div class="auth-form-panel">
            <div class="auth-form-container">
                <h1 class="auth-form-title">Verifikasi Reset Password</h1>
                <p class="auth-form-subtitle">
                    Masukkan 6-digit kode OTP reset password yang dikirimkan ke email <strong>{{ $email }}</strong>
                </p>

                @if (session('status'))
                    <div class="bunrek-alert bunrek-alert-success">
                        <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
                    </div>
                @endif

                @error('otp')
                    <div class="bunrek-alert bunrek-alert-error" style="margin-bottom: var(--space-md);">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                    </div>
                @enderror

                <form method="POST" action="{{ url('reset-otp') }}" id="otpForm">
                    @csrf
                    <div class="otp-inputs">
                        <input type="text" name="otp[]" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autofocus>
                        <input type="text" name="otp[]" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                        <input type="text" name="otp[]" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                        <input type="text" name="otp[]" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                        <input type="text" name="otp[]" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                        <input type="text" name="otp[]" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    </div>

                    <button type="submit" class="btn-bunrek btn-primary btn-w-full">
                        <i class="bi bi-shield-check"></i> Verifikasi Kode
                    </button>
                </form>

                <div style="text-align: center; margin-top: var(--space-xl); font-size: var(--fs-sm); color: var(--text-muted);">
                    <div id="timerContainer" style="margin-bottom: var(--space-md);">
                        Kode berlaku dalam: <strong id="countdown">10:00</strong>
                    </div>
                    
                    <form method="POST" action="{{ route('password.resend-otp') }}" id="resendForm" style="display: none;">
                        @csrf
                        Belum menerima email? 
                        <button type="submit" class="btn-link" style="background: none; border: none; font-weight: 600; color: var(--primary-color); cursor: pointer; padding: 0; font-family: inherit; font-size: inherit;">
                            Kirim Ulang OTP
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-digit');
            const form = document.getElementById('otpForm');

            // Handle Input behavior (auto-advance)
            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    if (value.length > 0) {
                        // Move to next input
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (input.value.length === 0) {
                            // Move to previous input
                            if (index > 0) {
                                inputs[index - 1].focus();
                            }
                        }
                    }
                });

                // Paste behavior
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const text = e.clipboardData.getData('text').trim();
                    if (/^\d{6}$/.test(text)) {
                        inputs.forEach((inp, idx) => {
                            inp.value = text[idx];
                        });
                        inputs[5].focus();
                    }
                });
            });

            // Timer Countdown (10 minutes)
            let timeLeft = 600; // 10 minutes in seconds
            const countdownEl = document.getElementById('countdown');
            const timerContainer = document.getElementById('timerContainer');
            const resendForm = document.getElementById('resendForm');

            const timerInterval = setInterval(function() {
                const minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                countdownEl.textContent = `${minutes}:${seconds}`;
                timeLeft--;

                if (timeLeft < 0) {
                    clearInterval(timerInterval);
                    timerContainer.style.display = 'none';
                    resendForm.style.display = 'block';
                }
            }, 1000);
        });
    </script>
</body>
</html>
