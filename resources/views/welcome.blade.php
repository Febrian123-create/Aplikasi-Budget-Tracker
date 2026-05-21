<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BUNREK | Aplikasi Pengatur Keuangan & Budget Tracker Cerdas</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Kuasai keuangan pribadi Anda dengan BUNREK. Pantau pengeluaran harian, atur anggaran bulanan, analisis grafik keuangan, dan ekspor laporan PDF/Excel secara instan.">
    <meta name="keywords" content="budget tracker, aplikasi keuangan, pencatat pengeluaran, dompet digital, hemat uang, excel pdf export, keuangan keluarga">
    <meta name="author" content="BUNREK Team">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Premium Styles -->
    <style>
        :root {
            --primary-color: #6366f1; /* Indigo */
            --primary-light: #818cf8;
            --primary-dark: #4f46e5;
            --accent-color: #a855f7; /* Purple */
            --bg-light: #fdfdff;
            --bg-glass-white: rgba(255, 255, 255, 0.45);
            --bg-glass-white-thick: rgba(255, 255, 255, 0.75);
            --border-glass: rgba(255, 255, 255, 0.6);
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
            --transition-smooth: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.06) 0px, transparent 50%),
                radial-gradient(at 80% 20%, rgba(99, 102, 241, 0.04) 0px, transparent 40%);
            background-attachment: fixed;
        }

        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--text-dark);
        }

        /* --- Navbar Sticky Glassmorphism --- */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.02);
            transition: var(--transition-smooth);
        }
        
        .navbar-glass.scrolled {
            background: rgba(255, 255, 255, 0.85);
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
        }

        .navbar-brand span {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            position: relative;
            color: var(--text-dark);
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--primary-color);
            transition: var(--transition-smooth);
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        /* --- Glassmorphism Card --- */
        .glass-card {
            background: var(--bg-glass-white);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            padding: 2.25rem;
            box-shadow: 0 10px 30px -10px rgba(99, 102, 241, 0.05), 0 1px 3px rgba(0, 0, 0, 0.01);
            transition: var(--transition-smooth);
        }

        .glass-card:hover {
            transform: translateY(-8px);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 20px 40px -15px rgba(99, 102, 241, 0.12);
        }

        /* --- Buttons --- */
        .btn-indigo {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white !important;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.75rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.25);
            transition: var(--transition-smooth);
        }

        .btn-indigo:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
            filter: brightness(1.05);
        }

        .btn-outline-indigo {
            border: 2px solid var(--primary-color);
            color: var(--primary-color) !important;
            background: transparent;
            border-radius: 12px;
            padding: 0.65rem 1.65rem;
            font-weight: 600;
            transition: var(--transition-smooth);
        }

        .btn-outline-indigo:hover {
            background: rgba(99, 102, 241, 0.05);
            transform: translateY(-2px);
        }

        /* --- Hero Section --- */
        .hero-section {
            padding: 160px 0 100px;
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 3.8rem);
            line-height: 1.15;
            font-weight: 800;
            background: linear-gradient(135deg, var(--text-dark) 40%, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.125rem;
            color: var(--text-muted);
            line-height: 1.65;
            margin-bottom: 2.25rem;
            max-width: 550px;
        }

        /* --- Dashboard Mockup (Pure CSS) --- */
        .mockup-container {
            position: relative;
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
        }

        .mockup-base {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 28px;
            box-shadow: 0 30px 60px -15px rgba(99, 102, 241, 0.15), 0 10px 20px -10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            position: relative;
            z-index: 2;
            transition: var(--transition-smooth);
        }

        .mockup-base:hover {
            transform: scale(1.02) rotate(1deg);
        }

        .mockup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(99, 102, 241, 0.08);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .mockup-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .mockup-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .mockup-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .mockup-balance {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .mockup-amount {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
        }

        .mockup-trend {
            font-size: 0.75rem;
            color: #10b981;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .mockup-card-balance {
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            color: white;
            border-radius: 20px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px -5px rgba(49, 46, 129, 0.3);
        }

        .mockup-card-balance::before {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(99, 102, 241, 0.15);
            border-radius: 50%;
            top: -50px;
            right: -50px;
            filter: blur(20px);
        }

        /* CSS Chart */
        .mockup-chart-container {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 1rem;
            margin-bottom: 1.25rem;
        }

        .mockup-chart-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
        }

        .mockup-bars {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            height: 70px;
            padding-top: 10px;
        }

        .mockup-bar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .mockup-bar {
            width: 18px;
            border-radius: 6px 6px 0 0;
            background: rgba(99, 102, 241, 0.2);
            transition: height 1s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .mockup-bar.active {
            background: linear-gradient(to top, var(--primary-color), var(--primary-light));
        }

        .mockup-bar.active-accent {
            background: linear-gradient(to top, var(--accent-color), #d8b4fe);
        }

        .mockup-bar-label {
            font-size: 0.6rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Mock Transactions */
        .mockup-transaction-list {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .mockup-transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            padding: 0.6rem 0.8rem;
            font-size: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .mockup-tx-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mockup-tx-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .mockup-tx-name {
            font-weight: 600;
            color: var(--text-dark);
        }

        .mockup-tx-desc {
            font-size: 0.65rem;
            color: var(--text-muted);
        }

        .mockup-tx-amount {
            font-weight: 700;
        }

        /* Decorative Float Items */
        .float-element-1 {
            position: absolute;
            top: -20px;
            left: -30px;
            z-index: 3;
            animation: float-y 5s ease-in-out infinite;
        }

        .float-element-2 {
            position: absolute;
            bottom: -15px;
            right: -25px;
            z-index: 3;
            animation: float-y 6s ease-in-out infinite alternate;
        }

        @keyframes float-y {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        /* --- Stats Bar --- */
        .stats-section {
            padding: 3rem 0;
            position: relative;
            z-index: 2;
        }

        .stats-inner {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--border-glass);
            border-radius: 30px;
            padding: 2rem;
            box-shadow: 0 15px 35px -10px rgba(99, 102, 241, 0.04);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: var(--font-heading);
            margin-bottom: 0.25rem;
        }

        /* --- Section Styling --- */
        .section-padding {
            padding: 100px 0;
        }

        .section-title {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
            position: relative;
        }

        .section-subtitle {
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 3.5rem;
            font-size: 1.05rem;
        }

        /* --- Features Section --- */
        .feature-icon-wrapper {
            width: 56px;
            height: 56px;
            background: rgba(99, 102, 241, 0.08);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            transition: var(--transition-smooth);
        }

        .glass-card:hover .feature-icon-wrapper {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        /* --- How It Works --- */
        .step-card {
            position: relative;
        }

        .step-number {
            font-size: 4.5rem;
            font-weight: 900;
            line-height: 1;
            color: rgba(99, 102, 241, 0.08);
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-family: var(--font-heading);
            transition: var(--transition-smooth);
        }

        .glass-card:hover .step-number {
            color: rgba(99, 102, 241, 0.16);
            transform: scale(1.1);
        }

        .step-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
        }

        /* --- Pricing Section --- */
        .pricing-card {
            border-radius: 26px;
            position: relative;
            overflow: hidden;
        }

        .pricing-card.premium-highlight {
            border: 2px solid var(--primary-color);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 20px 40px -15px rgba(99, 102, 241, 0.18);
        }

        .premium-ribbon {
            position: absolute;
            top: 20px;
            right: -35px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.35rem 2.5rem;
            transform: rotate(45deg);
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .pricing-price {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 1.25rem 0;
            font-family: var(--font-heading);
        }

        .pricing-price small {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .pricing-features-list {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .pricing-features-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.925rem;
            color: #475569;
        }

        .pricing-features-list li i {
            font-size: 1.1rem;
        }

        /* --- CTA Banner --- */
        .cta-container {
            position: relative;
            overflow: hidden;
            border-radius: 36px;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 5.5rem 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .cta-container h2 {
            color: #ffffff !important;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .cta-container p {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .cta-container::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            top: -100px;
            left: -100px;
            border-radius: 50%;
        }

        .cta-container::after {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.1) 0%, transparent 70%);
            bottom: -150px;
            right: -100px;
            border-radius: 50%;
        }

        .cta-content {
            position: relative;
            z-index: 2;
        }

        /* --- Footer --- */
        footer {
            background-color: #090d16;
            color: #94a3b8;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 5rem 0 2.5rem;
            font-size: 0.925rem;
        }

        footer h5 {
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.25rem;
        }

        footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        footer a:hover {
            color: white;
            transform: translateX(3px);
        }

        footer .footer-brand span {
            background: linear-gradient(135deg, var(--primary-light), #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* --- Scroll Reveal System --- */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Make responsive adjustments */
        @media (max-width: 991.98px) {
            .hero-section {
                padding: 130px 0 60px;
                text-align: center;
            }
            .hero-subtitle {
                margin-left: auto;
                margin-right: auto;
            }
            .navbar-collapse {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 18px;
                padding: 1.5rem;
                margin-top: 1rem;
                border: 1px solid rgba(99, 102, 241, 0.1);
            }
            .nav-link::after {
                display: none;
            }
            .mockup-container {
                margin-top: 3.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar Sticky Glassmorphism -->
    <nav class="navbar navbar-expand-lg navbar-glass fixed-top py-3" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold fs-3" href="#">
                <i class="bi bi-wallet2 text-primary me-2"></i>
                <span class="font-heading">BUNREK</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-center gap-lg-3">
                    <li class="nav-item"><a class="nav-link px-2 py-2" href="#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link px-2 py-2" href="#how-it-works">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link px-2 py-2" href="#pricing">Harga</a></li>
                    @if (Route::has('login'))
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-indigo w-100 mb-2 mb-lg-0" id="navBtnLogin" href="{{ route('login') }}">Masuk</a>
                        </li>
                    @endif
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="btn btn-indigo w-100" id="navBtnRegister" href="{{ route('register') }}">Mulai Gratis</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 reveal" id="heroLeftContent">
                    <div class="badge bg-indigo-subtle text-primary border border-indigo-subtle px-3 py-2 rounded-pill fw-semibold mb-3" style="background: rgba(99,102,241,0.08);">
                        <i class="bi bi-sparkles me-1"></i> Pencatat Keuangan & Budgeting Cerdas
                    </div>
                    <h1 class="hero-title">Kuasai Finansial Anda Tanpa Ribet</h1>
                    <p class="hero-subtitle">BUNREK adalah asisten keuangan pribadi yang membantu Anda melacak pengeluaran harian, membuat rencana anggaran bulanan, dan mencapai kemerdekaan finansial dengan mudah.</p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ route('register') }}" class="btn btn-indigo btn-lg px-4 py-3" id="heroBtnRegister">Mulai Sekarang Gratis</a>
                        <a href="#features" class="btn btn-outline-indigo btn-lg px-4 py-3" id="heroBtnFeatures"><i class="bi bi-chevron-down me-1"></i> Lihat Fitur</a>
                    </div>
                </div>
                <div class="col-lg-6 reveal" id="heroRightContent" style="transition-delay: 0.15s">
                    <div class="mockup-container">
                        <!-- Floating graphic card 1 -->
                        <div class="glass-card float-element-1 py-2 px-3 shadow" style="border-radius: 16px; background: rgba(255,255,255,0.8);">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success text-white rounded-circle p-1 d-flex" style="font-size: 0.8rem;">
                                    <i class="bi bi-arrow-up-short"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.6rem;">Pemasukan Bulanan</small>
                                    <strong style="font-size: 0.75rem;">+Rp 15.000.000</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Floating graphic card 2 -->
                        <div class="glass-card float-element-2 py-2 px-3 shadow" style="border-radius: 16px; background: rgba(255,255,255,0.85);">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-warning text-white rounded-circle p-1 d-flex" style="font-size: 0.7rem;">
                                    <i class="bi bi-gem"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.6rem;">Membership</small>
                                    <strong class="text-primary" style="font-size: 0.75rem;">Premium Active</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Mockup Base Screen -->
                        <div class="mockup-base">
                            <div class="mockup-header">
                                <div class="mockup-profile">
                                    <div class="mockup-avatar">F</div>
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.8rem; margin-bottom: 1px;">Halo, Febrian</div>
                                        <div class="mockup-badge">User Premium</div>
                                    </div>
                                </div>
                                <i class="bi bi-bell text-muted" style="font-size: 1.1rem;"></i>
                            </div>

                            <div class="mockup-card-balance">
                                <div class="mockup-balance">Total Saldo Aktif</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="mockup-amount">Rp 14.250.000</h3>
                                    <span class="mockup-trend bg-white bg-opacity-20 px-2 py-1 rounded text-white" style="font-size: 0.65rem;">
                                        <i class="bi bi-arrow-up-right"></i> +12%
                                    </span>
                                </div>
                            </div>

                            <!-- Interactive Mock Chart -->
                            <div class="mockup-chart-container">
                                <div class="mockup-chart-title">
                                    <span>Alokasi Budget Pengeluaran</span>
                                    <span class="text-primary" style="font-size: 0.65rem;">Mei 2026</span>
                                </div>
                                <div class="mockup-bars">
                                    <div class="mockup-bar-wrapper">
                                        <div class="mockup-bar active" style="height: 35px;"></div>
                                        <span class="mockup-bar-label">Makan</span>
                                    </div>
                                    <div class="mockup-bar-wrapper">
                                        <div class="mockup-bar active-accent" style="height: 55px;"></div>
                                        <span class="mockup-bar-label">Kost</span>
                                    </div>
                                    <div class="mockup-bar-wrapper">
                                        <div class="mockup-bar active" style="height: 20px;"></div>
                                        <span class="mockup-bar-label">Kopi</span>
                                    </div>
                                    <div class="mockup-bar-wrapper">
                                        <div class="mockup-bar active-accent" style="height: 45px;"></div>
                                        <span class="mockup-bar-label">Belanja</span>
                                    </div>
                                    <div class="mockup-bar-wrapper">
                                        <div class="mockup-bar active" style="height: 15px;"></div>
                                        <span class="mockup-bar-label">Sosmed</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Transaction Mini list -->
                            <div class="mockup-transaction-list">
                                <div class="mockup-transaction-item">
                                    <div class="mockup-tx-info">
                                        <div class="mockup-tx-icon bg-success-subtle text-success">
                                            <i class="bi bi-arrow-down-left"></i>
                                        </div>
                                        <div>
                                            <div class="mockup-tx-name">Gaji Bulanan</div>
                                            <span class="mockup-tx-desc">Pemasukan • Rutin</span>
                                        </div>
                                    </div>
                                    <span class="mockup-tx-amount text-success">+15.000.000</span>
                                </div>
                                <div class="mockup-transaction-item">
                                    <div class="mockup-tx-info">
                                        <div class="mockup-tx-icon bg-danger-subtle text-danger">
                                            <i class="bi bi-cup-hot"></i>
                                        </div>
                                        <div>
                                            <div class="mockup-tx-name">Starbucks Cafe</div>
                                            <span class="mockup-tx-desc">Pengeluaran • Hiburan</span>
                                        </div>
                                    </div>
                                    <span class="mockup-tx-amount text-danger">-55.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-inner reveal" id="statsBar">
                <div class="row text-center g-4">
                    <div class="col-md-4">
                        <div class="stats-number">Rp 1.2jt+</div>
                        <p class="text-muted fw-semibold mb-0">Rata-rata Penghematan / Bln</p>
                    </div>
                    <div class="col-md-4 border-start border-end border-light-subtle">
                        <div class="stats-number">15.000+</div>
                        <p class="text-muted fw-semibold mb-0">Pengguna Terdaftar</p>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-number">4.9 / 5.0</div>
                        <p class="text-muted fw-semibold mb-0">Rating Pengguna</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section-padding">
        <div class="container">
            <div class="text-center reveal">
                <h2 class="section-title">Fitur Andalan BUNREK</h2>
                <p class="section-subtitle">Semua alat finansial yang Anda butuhkan untuk mengatur keuangan harian hingga ekspor laporan bulanan di satu tempat.</p>
            </div>
            
            <div class="row g-4">
                <!-- Feature 1: Catat Transaksi -->
                <div class="col-md-6 col-lg-4 reveal">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-journal-plus"></i>
                        </div>
                        <h4>Catat Transaksi</h4>
                        <p class="text-muted mb-0">Masukkan data pemasukan dan pengeluaran Anda dengan kategori lengkap, memo kustom, serta tanggal transaksi instan.</p>
                    </div>
                </div>
                
                <!-- Feature 2: Riwayat & Filter -->
                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.05s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4>Riwayat & Filter</h4>
                        <p class="text-muted mb-0">Telusuri seluruh riwayat transaksi terdahulu dengan fitur pencarian cepat serta filter tanggal dan tipe transaksi yang fleksibel.</p>
                    </div>
                </div>

                <!-- Feature 3: Visualisasi Grafik -->
                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.1s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-pie-chart"></i>
                        </div>
                        <h4>Visualisasi Grafik</h4>
                        <p class="text-muted mb-0">Analisis pengeluaran Anda menggunakan Pie Chart interaktif dan Bar Chart dinamis untuk melihat kategori mana yang paling boros.</p>
                    </div>
                </div>

                <!-- Feature 4: Export PDF/Excel -->
                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.15s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                        </div>
                        <h4>Export PDF/Excel</h4>
                        <p class="text-muted mb-0">Unduh data laporan bulanan Anda dalam format PDF formal untuk dicetak, atau file Excel/Spreadsheet untuk olah data mandiri.</p>
                    </div>
                </div>

                <!-- Feature 5: Transaksi Rutin -->
                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.2s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h4>Transaksi Rutin</h4>
                        <p class="text-muted mb-0">Jadwalkan transaksi berulang (recurring) seperti gaji, tagihan internet, kos, atau langganan streaming secara otomatis.</p>
                    </div>
                </div>

                <!-- Feature 6: Membership Premium -->
                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.25s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-gem"></i>
                        </div>
                        <h4>Membership Premium</h4>
                        <p class="text-muted mb-0">Dapatkan akses penuh ke visualisasi grafik detail, ekspor laporan unlimited, serta fitur eksklusif tanpa batasan apa pun.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="section-padding bg-light bg-opacity-20" style="background-color: rgba(99, 102, 241, 0.01);">
        <div class="container">
            <div class="text-center reveal">
                <h2 class="section-title">Cara Kerja BUNREK</h2>
                <p class="section-subtitle">Mulailah langkah hemat dan rapi keuangan Anda hanya dalam tiga tahapan mudah berikut.</p>
            </div>

            <div class="row g-4">
                <!-- Step 1 -->
                <div class="col-md-4 reveal">
                    <div class="glass-card h-100 step-card">
                        <div class="step-number">01</div>
                        <div class="step-icon">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h4>Daftar Akun Gratis</h4>
                        <p class="text-muted mb-0">Klik tombol Mulai Gratis, isi nama dan email Anda untuk membuat akun resmi dalam waktu 30 detik tanpa biaya tersembunyi.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-4 reveal" style="transition-delay: 0.1s">
                    <div class="glass-card h-100 step-card">
                        <div class="step-number">02</div>
                        <div class="step-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h4>Catat & Jadwalkan</h4>
                        <p class="text-muted mb-0">Input pengeluaran harian dan pemasukan Anda secara berkala, atau atur pencatatan otomatis untuk tagihan rutin Anda.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-md-4 reveal" style="transition-delay: 0.2s">
                    <div class="glass-card h-100 step-card">
                        <div class="step-number">03</div>
                        <div class="step-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4>Analisis & Berhemat</h4>
                        <p class="text-muted mb-0">Pantau grafik analitik secara real-time untuk memotong anggaran yang tidak perlu dan tingkatkan saldo tabungan Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="section-padding">
        <div class="container">
            <div class="text-center reveal">
                <h2 class="section-title">Paket Membership Hemat</h2>
                <p class="section-subtitle">Pilih lisensi yang sesuai dengan kebutuhan Anda. Upgrade kapan saja untuk fitur analisis tanpa batas.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Plan 1: Free -->
                <div class="col-md-5 col-lg-4 reveal">
                    <div class="glass-card h-100 pricing-card d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-semibold mb-2">Free Plan</span>
                            <h3 class="mt-2">Mulai Cerdas</h3>
                            <p class="text-muted small">Solusi basic untuk pencatatan harian sederhana.</p>
                            <div class="pricing-price">Rp 0<small> / selamanya</small></div>
                            
                            <hr class="my-4 opacity-10">
                            
                            <ul class="pricing-features-list">
                                <li><i class="bi bi-check-circle-fill text-success"></i> Catat Transaksi Pemasukan</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Catat Transaksi Pengeluaran</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Riwayat & Filter Standar</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Transaksi Rutin (Recurring)</li>
                                <li class="text-muted opacity-50"><i class="bi bi-x-circle-fill text-danger"></i> Visualisasi Grafik Pie & Bar</li>
                                <li class="text-muted opacity-50"><i class="bi bi-x-circle-fill text-danger"></i> Export PDF & Excel Laporan</li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('register') }}" class="btn btn-outline-indigo w-100 py-3" id="pricingBtnFree">Daftar Sekarang</a>
                        </div>
                    </div>
                </div>

                <!-- Plan 2: Premium -->
                <div class="col-md-5 col-lg-4 reveal" style="transition-delay: 0.1s">
                    <div class="glass-card h-100 pricing-card premium-highlight d-flex flex-column justify-content-between">
                        <div class="premium-ribbon">Rekomendasi</div>
                        <div>
                            <span class="badge bg-indigo-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2" style="background: rgba(99,102,241,0.08);">Premium Plan</span>
                            <h3 class="mt-2">Sultan Hemat</h3>
                            <p class="text-muted small">Akses fitur analitik terlengkap & laporan siap ekspor.</p>
                            <div class="pricing-price">Rp 99.000<small> / bulan</small></div>
                            
                            <hr class="my-4 opacity-10">
                            
                            <ul class="pricing-features-list">
                                <li><i class="bi bi-check-circle-fill text-success"></i> Semua Fitur Paket Free</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Visualisasi Grafik Pie & Bar Chart</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Export Laporan ke Excel & PDF</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Bebas Iklan & Prioritas Akses</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Dukungan Bantuan Premium 24/7</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Lencana "Premium" di Profil</li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('register') }}" class="btn btn-indigo w-100 py-3" id="pricingBtnPremium">Langganan Premium</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="container mb-5 reveal">
        <div class="cta-container text-center">
            <div class="cta-content max-width-600 mx-auto">
                <h2 class="display-5 fw-bold mb-3">Siap untuk Mengontrol Finansial Anda?</h2>
                <p class="fs-5 opacity-75 mb-4 max-width-500 mx-auto">Bergabunglah dengan pengguna BUNREK lainnya yang sukses berhemat jutaan rupiah tiap bulannya.</p>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold text-primary border-0 shadow-sm" id="ctaBtnRegister" style="color: var(--primary-color) !important;">
                    Gabung Sekarang Gratis!
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-6 col-md-12">
                    <a class="footer-brand d-flex align-items-center fw-bold fs-4 text-white mb-3" href="#">
                        <i class="bi bi-wallet2 text-primary me-2"></i>
                        <span>BUNREK</span>
                    </a>
                    <p class="max-width-350" style="max-width: 380px;">Aplikasi pencatat keuangan modern yang didesain untuk mempercepat efisiensi penganggaran harian serta visualisasi grafik aset masa depan Anda secara akurat.</p>
                </div>
                <div class="col-lg-3 col-6 col-md-6">
                    <h5>Navigasi</h5>
                    <ul class="nav flex-column gap-2">
                        <li><a href="#features">Fitur Utama</a></li>
                        <li><a href="#how-it-works">Cara Kerja</a></li>
                        <li><a href="#pricing">Paket Harga</a></li>
                        @if (Route::has('login'))
                            <li><a href="{{ route('login') }}">Masuk Ke Akun</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-lg-3 col-6 col-md-6">
                    <h5>Halaman Hukum</h5>
                    <ul class="nav flex-column gap-2">
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="#">Hubungi Tim Support</a></li>
                        <li><a href="https://github.com" target="_blank" rel="noopener noreferrer"><i class="bi bi-github me-1"></i> GitHub Project</a></li>
                    </ul>
                </div>
            </div>
            <hr class="opacity-10 my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                <div class="text-sm">&copy; 2026 BUNREK. Hak Cipta Dilindungi.</div>
                <div class="d-flex gap-3">
                    <a href="#" class="fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="fs-5"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="fs-5"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scroll Animation Script (Intersection Observer) -->
    <script>
        // Navbar Scrolled Class
        const navbar = document.getElementById('mainNavbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Intersection Observer for Scroll Reveal
        document.addEventListener("DOMContentLoaded", () => {
            const reveals = document.querySelectorAll('.reveal');
            
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Optional: stop observing once shown
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            reveals.forEach(el => observer.observe(el));
            
            // Trigger animation on load for elements that are already in view
            setTimeout(() => {
                const heroLeft = document.getElementById('heroLeftContent');
                const heroRight = document.getElementById('heroRightContent');
                if (heroLeft) heroLeft.classList.add('active');
                if (heroRight) heroRight.classList.add('active');
            }, 100);
        });
    </script>
</body>
</html>
