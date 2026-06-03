<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - BUNREK</title>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    
    <link rel="stylesheet" href="{{ asset('css/bunrek-tokens.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bunrek-app.css') }}" />
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
                <h1 class="auth-form-title">Mulai Perjalanan Anda</h1>
                <p class="auth-form-subtitle">Daftarkan akun untuk melacak budget Anda</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    
                    <div class="bunrek-form-group">
                        <label for="name" class="bunrek-label">Nama Lengkap</label>
                        <input type="text" id="name" name="name" class="bunrek-input"
                               value="{{ old('name') }}"
                               placeholder="John Doe"
                               required autofocus autocomplete="name">
                        @error('name')
                            <p class="bunrek-alert bunrek-alert-error" style="margin-top: 6px; padding: 4px 8px; font-size: var(--fs-xs);">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    
                    <div class="bunrek-form-group">
                        <label for="email" class="bunrek-label">Email</label>
                        <input type="email" id="email" name="email" class="bunrek-input"
                               value="{{ old('email') }}"
                               placeholder="nama@gmail.com / nama@univ.ac.id"
                               pattern="[a-zA-Z0-9._%+-]+@(gmail\.com|[a-zA-Z0-9.-]+\.ac\.id)"
                               title="Email harus menggunakan domain @gmail.com atau berakhiran .ac.id."
                               required autocomplete="username">
                        <p id="email-error" class="bunrek-alert bunrek-alert-error" style="display: none; margin-top: 6px; padding: 4px 8px; font-size: var(--fs-xs);">
                            <i class="bi bi-exclamation-triangle-fill"></i> <span id="email-error-text">Email harus menggunakan domain @gmail.com atau berakhiran .ac.id.</span>
                        </p>
                        @error('email')
                            <p class="bunrek-alert bunrek-alert-error server-error" style="margin-top: 6px; padding: 4px 8px; font-size: var(--fs-xs);">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    
                    <div class="bunrek-form-group">
                        <label for="password" class="bunrek-label">Password</label>
                        <input type="password" id="password" name="password" class="bunrek-input"
                               placeholder="Minimal 8 karakter"
                               required autocomplete="new-password">
                        <p style="font-size: 0.75rem; color: var(--text-light); margin-top: 4px;">Gunakan kombinasi huruf dan angka</p>
                        @error('password')
                            <p class="bunrek-alert bunrek-alert-error" style="margin-top: 6px; padding: 4px 8px; font-size: var(--fs-xs);">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    
                    <div class="bunrek-form-group" style="margin-bottom: var(--space-xl);">
                        <label for="password_confirmation" class="bunrek-label">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="bunrek-input"
                               placeholder="Ulangi password"
                               required autocomplete="new-password">
                        @error('password_confirmation')
                            <p class="bunrek-alert bunrek-alert-error" style="margin-top: 6px; padding: 4px 8px; font-size: var(--fs-xs);">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-bunrek btn-primary btn-w-full">
                        <i class="bi bi-person-plus"></i> Daftar
                    </button>
                </form>

                <p style="text-align: center; margin-top: var(--space-xl); font-size: var(--fs-sm); color: var(--text-muted);">
                    Sudah punya akun? <a href="{{ route('login') }}" style="font-weight: 600; color: var(--primary-color);">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('email-error');
            const emailErrorText = document.getElementById('email-error-text');
            const serverError = document.querySelector('.server-error');
            const form = emailInput.closest('form');

            // Regex untuk validasi email harus @gmail.com atau berakhiran .ac.id
            const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|[a-zA-Z0-9.-]+\.ac\.id)$/i;

            function validateEmail() {
                const emailValue = emailInput.value.trim();
                
                // Sembunyikan error dari server saat user mengedit
                if (serverError) {
                    serverError.style.display = 'none';
                }

                if (emailValue === '') {
                    hideError();
                    return true;
                }

                if (!emailRegex.test(emailValue)) {
                    showError('Email harus menggunakan domain @gmail.com atau berakhiran .ac.id.');
                    return false;
                } else {
                    hideError();
                    return true;
                }
            }

            function showError(message) {
                emailErrorText.textContent = message;
                emailError.style.display = 'block';
                emailInput.style.borderColor = '#ef4444';
                emailInput.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.15)';
            }

            function hideError() {
                emailError.style.display = 'none';
                emailInput.style.borderColor = '';
                emailInput.style.boxShadow = '';
            }

            // Validasi real-time saat mengetik
            emailInput.addEventListener('input', function () {
                validateEmail();
            });

            // Validasi saat input kehilangan fokus
            emailInput.addEventListener('blur', function () {
                validateEmail();
            });

            // Mencegah submit form jika email tidak valid
            form.addEventListener('submit', function (event) {
                if (!validateEmail()) {
                    event.preventDefault();
                    emailInput.focus();
                }
            });
        });
    </script>
</body>
</html>
