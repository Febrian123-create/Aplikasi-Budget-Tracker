<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - BUNREK</title>
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
                <h1 class="auth-form-title">Selamat Datang Kembali</h1>
                <p class="auth-form-subtitle">Silakan masuk ke akun Budget Tracker Anda</p>

                
                @if (session('status'))
                    <div class="bunrek-alert bunrek-alert-success">
                        <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    
                    <div class="bunrek-form-group">
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

                    
                    <div class="bunrek-form-group" style="margin-bottom: var(--space-md);">
                        <label for="password" class="bunrek-label">Password</label>
                        <input type="password" id="password" name="password" class="bunrek-input"
                               placeholder="••••••••"
                               required autocomplete="current-password">
                        @error('password')
                            <p class="bunrek-alert bunrek-alert-error" style="margin-top: 6px; padding: 4px 8px; font-size: var(--fs-xs);">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-lg);">
                        @if (Route::has('password.request'))
                            <a class="forgot-link" href="{{ route('password.request') }}" style="font-size: var(--fs-sm); font-weight: 500;">Lupa password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-bunrek btn-primary btn-w-full">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>

                @if (Route::has('register'))
                    <p style="text-align: center; margin-top: var(--space-xl); font-size: var(--fs-sm); color: var(--text-muted);">
                        Belum punya akun? <a href="{{ route('register') }}" style="font-weight: 600; color: var(--primary-color);">Daftar sekarang</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
