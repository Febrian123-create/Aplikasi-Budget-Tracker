<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar — {{ config('app.name', 'Budget Tracker') }}</title>
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
            max-width: 400px;
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
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #444;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
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

        input[type="text"]:focus,
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

        .btn-register {
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
            margin-top: 8px;
        }

        .btn-register:hover { background: #3a58e0; }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.82rem;
            color: #888;
        }

        .login-link a {
            color: #4f6ef7;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover { text-decoration: underline; }

        .divider {
            height: 1px;
            background: #eee;
            margin: 20px 0;
        }

        .hint {
            font-size: 0.75rem;
            color: #aaa;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1 class="card-title">Buat akun</h1>
        <p class="card-subtitle">Daftar untuk mulai melacak budget Anda</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name --}}
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       placeholder="John Doe"
                       required autofocus autocomplete="name">
                @error('name')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="nama@email.com"
                       required autocomplete="username">
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Minimal 8 karakter"
                       required autocomplete="new-password">
                <p class="hint">Gunakan kombinasi huruf dan angka</p>
                @error('password')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Ulangi password"
                       required autocomplete="new-password">
                @error('password_confirmation')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-register">Daftar</button>
        </form>

        <p class="login-link">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </p>
    </div>
</body>
</html>
