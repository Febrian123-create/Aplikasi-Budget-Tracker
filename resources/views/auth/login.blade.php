<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke Aplikasi Budget Tracker - Kelola keuangan Anda dengan mudah dan cerdas.">
    <title>Login – Budget Tracker</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary:       #6C63FF;
            --primary-dark:  #4f46e5;
            --primary-glow:  rgba(108, 99, 255, 0.45);
            --accent:        #A78BFA;
            --bg-dark:       #0d0d1a;
            --bg-card:       rgba(255,255,255,0.06);
            --border-glass:  rgba(255,255,255,0.12);
            --text-main:     #f1f1f7;
            --text-muted:    #9ca3af;
            --input-bg:      rgba(255,255,255,0.07);
            --input-border:  rgba(255,255,255,0.15);
            --danger:        #f87171;
            --success:       #34d399;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            overflow: hidden;
        }

        /* ── ANIMATED BACKGROUND ──────────────────────────── */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-canvas::before {
            content: '';
            position: absolute;
            width: 700px; height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(108,99,255,.35) 0%, transparent 70%);
            top: -200px; left: -200px;
            animation: drift1 12s ease-in-out infinite alternate;
        }

        .bg-canvas::after {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(167,139,250,.25) 0%, transparent 70%);
            bottom: -150px; right: -150px;
            animation: drift2 14s ease-in-out infinite alternate;
        }

        .bg-blob {
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79,70,229,.2) 0%, transparent 70%);
            top: 50%; left: 60%;
            transform: translate(-50%,-50%);
            animation: drift3 10s ease-in-out infinite alternate;
        }

        @keyframes drift1  { to { transform: translate(60px, 80px); } }
        @keyframes drift2  { to { transform: translate(-60px, -80px); } }
        @keyframes drift3  { to { transform: translate(-50%,-50%) scale(1.3); } }

        /* ── GRID OVERLAY ─────────────────────────────────── */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 0;
        }

        /* ── LAYOUT ───────────────────────────────────────── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            height: 100vh;
        }

        /* LEFT PANEL */
        .left-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 60px;
        }

        .brand-icon {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 0 24px var(--primary-glow);
        }

        .brand-name {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            background: linear-gradient(90deg, #fff, var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-headline {
            font-size: clamp(2rem, 3.5vw, 3rem);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -1px;
            margin-bottom: 20px;
        }

        .hero-headline span {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.7;
            max-width: 400px;
            margin-bottom: 48px;
        }

        .stats-row {
            display: flex;
            gap: 32px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .stat-number {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* RIGHT PANEL */
        .right-panel {
            width: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        /* ── CARD ─────────────────────────────────────────── */
        .login-card {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 28px;
            padding: 44px 40px;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow:
                0 8px 40px rgba(0,0,0,0.5),
                0 0 0 1px rgba(255,255,255,0.04) inset;
            animation: slideUp .5s cubic-bezier(.22,1,.36,1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-header {
            margin-bottom: 32px;
        }

        .card-title {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .card-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        /* ── ALERT ────────────────────────────────────────── */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.88rem;
            margin-bottom: 24px;
            animation: fadeIn .4s ease;
        }

        .alert-success {
            background: rgba(52,211,153,.12);
            border: 1px solid rgba(52,211,153,.25);
            color: var(--success);
        }

        .alert-danger {
            background: rgba(248,113,113,.12);
            border: 1px solid rgba(248,113,113,.25);
            color: var(--danger);
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* ── FORM ─────────────────────────────────────────── */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.83rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.9rem;
            transition: color .2s;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 13px 16px 13px 44px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .form-control::placeholder { color: var(--text-muted); }

        .form-control:focus {
            border-color: var(--primary);
            background: rgba(108,99,255,.08);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .form-control:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        /* password toggle */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 0.9rem;
            transition: color .2s;
        }

        .toggle-password:hover { color: var(--primary); }

        .form-error {
            font-size: 0.78rem;
            color: var(--danger);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ── EXTRAS ───────────────────────────────────────── */
        .form-extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.88rem;
            color: var(--text-muted);
            user-select: none;
        }

        .remember-label input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 17px; height: 17px;
            border-radius: 5px;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            cursor: pointer;
            display: grid;
            place-content: center;
            transition: background .2s, border-color .2s;
        }

        .remember-label input[type="checkbox"]:checked {
            background: var(--primary);
            border-color: var(--primary);
        }

        .remember-label input[type="checkbox"]:checked::before {
            content: '✓';
            color: #fff;
            font-size: 11px;
            font-weight: 700;
        }

        .forgot-link {
            font-size: 0.88rem;
            color: var(--primary);
            text-decoration: none;
            transition: color .2s, text-shadow .2s;
        }

        .forgot-link:hover {
            color: var(--accent);
            text-shadow: 0 0 12px var(--primary-glow);
        }

        /* ── SUBMIT BUTTON ────────────────────────────────── */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            box-shadow: 0 4px 24px var(--primary-glow);
            transition: transform .15s, box-shadow .15s, filter .15s;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.15), transparent);
            transition: left .5s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px var(--primary-glow);
            filter: brightness(1.1);
        }

        .btn-login:hover::before { left: 100%; }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login .btn-spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            margin: 0 auto;
        }

        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .btn-spinner { display: block; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── DIVIDER ──────────────────────────────────────── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: var(--text-muted);
            font-size: 0.82rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-top: 1px solid var(--border-glass);
        }

        /* ── REGISTER LINK ────────────────────────────────── */
        .register-row {
            text-align: center;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .register-row a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color .2s;
        }

        .register-row a:hover { color: var(--accent); }

        /* ── RESPONSIVE ───────────────────────────────────── */
        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 24px; }
            html, body { overflow: auto; }
        }

        @media (max-width: 480px) {
            .login-card { padding: 32px 24px; border-radius: 20px; }
        }
    </style>
</head>
<body>

<!-- Background -->
<div class="bg-canvas">
    <div class="bg-blob"></div>
</div>
<div class="grid-overlay"></div>

<div class="page-wrapper">

    <!-- ══ LEFT PANEL ══════════════════════════════════ -->
    <div class="left-panel">
        <div class="brand">
            <div class="brand-icon">
                <i class="fa-solid fa-wallet" style="color:#fff;"></i>
            </div>
            <span class="brand-name">Budget Tracker</span>
        </div>

        <h1 class="hero-headline">
            Kelola Keuangan<br>
            Lebih <span>Cerdas &amp; Efisien</span>
        </h1>
        <p class="hero-desc">
            Pantau pemasukan, pengeluaran, dan tabungan Anda dalam satu dashboard yang intuitif dan real-time.
        </p>

        <div class="stats-row">
            <div class="stat-item">
                <span class="stat-number">12K+</span>
                <span class="stat-label">Pengguna Aktif</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">98%</span>
                <span class="stat-label">Kepuasan User</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">4.9★</span>
                <span class="stat-label">Rating App</span>
            </div>
        </div>
    </div>

    <!-- ══ RIGHT PANEL ══════════════════════════════════ -->
    <div class="right-panel">
        <div class="login-card">

            <div class="card-header">
                <h2 class="card-title">Selamat Datang 👋</h2>
                <p class="card-subtitle">Masuk ke akun Budget Tracker Anda</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error') || $errors->has('login'))
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ session('error') ?? $errors->first('login') }}
                </div>
            @endif

            <form id="loginForm" action="{{ route('login.post') }}" method="POST" novalidate>
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope input-icon"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="email@contoh.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                    </div>
                    @error('email')
                        <div class="form-error">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                            style="padding-right: 44px;"
                        >
                        <button type="button" class="toggle-password" id="togglePwd" aria-label="Toggle password visibility">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="form-error">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="form-extras">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="btn-text"><i class="fa-solid fa-arrow-right-to-bracket" style="margin-right:8px;"></i>Masuk</span>
                    <div class="btn-spinner"></div>
                </button>

                <div class="divider">atau</div>

                <div class="register-row">
                    Belum punya akun?
                    <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
    // Toggle password visibility
    const togglePwd = document.getElementById('togglePwd');
    const pwdInput  = document.getElementById('password');
    const eyeIcon   = document.getElementById('eyeIcon');

    togglePwd.addEventListener('click', () => {
        const isText = pwdInput.type === 'text';
        pwdInput.type = isText ? 'password' : 'text';
        eyeIcon.className = isText ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
    });

    // Loading state on submit
    const loginForm = document.getElementById('loginForm');
    const loginBtn  = document.getElementById('loginBtn');

    loginForm.addEventListener('submit', (e) => {
        // Basic client-side check
        const email    = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!email || !password) return; // Let HTML5 handle it

        loginBtn.classList.add('loading');
        loginBtn.disabled = true;
    });
</script>

</body>
</html>
