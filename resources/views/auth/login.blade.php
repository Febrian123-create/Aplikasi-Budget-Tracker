<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ config('app.name', 'Budget Tracker') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.10);
            padding: 40px 36px;
            width: 100%;
            max-width: 380px;
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 6px;
        }

        .card-subtitle {
            font-size: 0.875rem;
            color: #777;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #444;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 10px 13px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #1a1a1a;
            background: #fafafa;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #4f6ef7;
            background: #fff;
        }

        .error-msg {
            color: #e53e3e;
            font-size: 0.78rem;
            margin-top: 4px;
        }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            color: #555;
            cursor: pointer;
        }

        input[type="checkbox"] {
            accent-color: #4f6ef7;
            width: 15px;
            height: 15px;
        }

        .forgot-link {
            font-size: 0.82rem;
            color: #4f6ef7;
            text-decoration: none;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            padding: 11px;
            background: #4f6ef7;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-login:hover { background: #3a58e0; }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.82rem;
            color: #888;
        }

        .register-link a {
            color: #4f6ef7;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover { text-decoration: underline; }

        .alert-status {
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 8px;
            padding: 10px 13px;
            font-size: 0.83rem;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1 class="card-title">Selamat datang</h1>
        <p class="card-subtitle">Masuk ke akun Budget Tracker Anda</p>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="alert-status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="nama@email.com"
                       required autofocus autocomplete="username">
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••"
                       required autocomplete="current-password">
                @error('password')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="row-between">
                <label class="remember-label">
                    <input type="checkbox" id="remember_me" name="remember">
                    Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">Lupa password?</a>
                @endif
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>

        @if (Route::has('register'))
            <p class="register-link">
                Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
            </p>
        @endif
    </div>
</body>
</html>
