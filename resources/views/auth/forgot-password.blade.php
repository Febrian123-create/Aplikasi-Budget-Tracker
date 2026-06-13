<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - BUNREK</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
    
    
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
                <h1 class="auth-form-title">Lupa Password?</h1>
                <p class="auth-form-subtitle">Masukkan alamat email Anda untuk menerima kode OTP verifikasi reset password.</p>

                
                @if (session('status'))
                    <div class="bunrek-alert bunrek-alert-success">
                        <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    
                    <div class="bunrek-form-group" style="margin-bottom: var(--space-xl);">
                        <label for="email" class="bunrek-label">Email</label>
                        <input type="email" id="email" name="email" class="bunrek-input"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               required autofocus autocomplete="username">
                        @error('email')
                            <p class="bunrek-alert bunrek-alert-error" style="margin-top: 6px; padding: 4px 8px; font-size: var(--fs-xs);">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-bunrek btn-primary btn-w-full">
                        <i class="bi bi-envelope"></i> Kirim Kode OTP
                    </button>
                </form>

                <p style="text-align: center; margin-top: var(--space-xl); font-size: var(--fs-sm); color: var(--text-muted);">
                    Kembali ke <a href="{{ route('login') }}" style="font-weight: 600; color: var(--primary-color);">Halaman Masuk</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
