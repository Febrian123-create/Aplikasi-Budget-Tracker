<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BUNREK | Aplikasi Pengatur Keuangan & Budget Tracker Cerdas</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">


    <meta name="description"
        content="Kuasai keuangan pribadi Anda dengan BUNREK. Pantau pengeluaran harian, atur anggaran bulanan, analisis grafik keuangan, dan ekspor laporan PDF/Excel secara instan.">
    <meta name="keywords"
        content="budget tracker, aplikasi keuangan, pencatat pengeluaran, dompet digital, hemat uang, excel pdf export, keuangan keluarga">
    <meta name="author" content="BUNREK Team">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    <style>
        :root {
            --primary-color: #6366f1;
            --primary-light: #818cf8;
            --primary-dark: #4f46e5;
            --accent-color: #a855f7;
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

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-heading {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--text-dark);
        }


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


        .mockup-container {
            position: relative;
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
        }

        .mockup-base {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 30px 60px -15px rgba(99, 102, 241, 0.15), 0 10px 20px -10px rgba(0, 0, 0, 0.05);
            padding: 0;
            position: relative;
            z-index: 2;
            transition: var(--transition-smooth);
            display: flex;
            overflow: hidden;
            min-height: 400px;
        }

        .mockup-base:hover {
            transform: scale(1.02) translateY(-2px);
        }

        .mockup-sidebar {
            width: 135px;
            background: #ffffff;
            border-right: 1px solid #f1f5f9;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: left;
        }

        .mockup-sidebar-logo {
            font-size: 0.85rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .mockup-sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .mockup-menu-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.5rem;
            font-size: 0.625rem;
            font-weight: 500;
            color: #64748b;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .mockup-menu-item.active {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
        }

        .mockup-sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding-top: 0.6rem;
            border-top: 1px solid #f1f5f9;
        }

        .mockup-avatar {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #a855f7;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
            font-weight: 700;
        }

        .mockup-user-info {
            line-height: 1.1;
            text-align: left;
        }

        .mockup-user-name {
            font-size: 0.55rem;
            font-weight: 600;
            color: #0f172a;
        }

        .mockup-user-badge {
            background: #eef2ff;
            color: #4f46e5;
            font-size: 0.4rem;
            font-weight: 700;
            padding: 0.05rem 0.25rem;
            border-radius: 3px;
        }

        .mockup-main {
            flex: 1;
            background: #f8fafc;
            padding: 0.85rem;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            min-width: 0;
            text-align: left;
        }

        .mockup-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mockup-page-title {
            font-size: 0.7rem;
            font-weight: 700;
            color: #0f172a;
        }

        .mockup-top-user {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.55rem;
            font-weight: 500;
            color: #475569;
        }

        .mockup-welcome-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        .mockup-welcome-text {
            font-size: 0.9rem;
            font-weight: 800;
            color: #0f172a;
        }

        .mockup-date {
            font-size: 0.5rem;
            color: #64748b;
        }

        .mockup-filters {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.4rem;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 0.3rem;
        }

        .mockup-filter-group {
            flex: 1;
        }

        .mockup-filter-label {
            font-size: 0.4rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .mockup-filter-select {
            font-size: 0.5rem;
            padding: 0.2rem 0.3rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            color: #0f172a;
            background: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mockup-btn-primary {
            background: #4f46e5;
            color: white;
            font-size: 0.5rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 0.15rem;
            border: none;
        }

        .mockup-btn-outline {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            font-size: 0.5rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
        }

        .mockup-card-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.4rem;
        }

        .mockup-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 50px;
        }

        .mockup-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.15rem;
        }

        .mockup-card-label {
            font-size: 0.4rem;
            color: #64748b;
            font-weight: 600;
            line-height: 1.1;
        }

        .mockup-card-icon {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.45rem;
        }

        .mockup-card-val {
            font-size: 0.7rem;
            font-weight: 800;
            margin-top: auto;
        }

        .mockup-highlight-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.6rem;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            flex: 1;
        }

        .mockup-highlight-title {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.55rem;
            font-weight: 700;
            color: #1e293b;
        }

        .mockup-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            flex: 1;
            padding: 0.35rem 0;
        }

        .mockup-empty-icon {
            color: #cbd5e1;
            font-size: 1.1rem;
            margin-bottom: 2px;
        }

        .mockup-empty-title {
            font-size: 0.55rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 1px;
        }

        .mockup-empty-desc {
            font-size: 0.45rem;
            color: #94a3b8;
            max-width: 160px;
            line-height: 1.25;
            margin-bottom: 6px;
        }


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
            0% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-10px) rotate(1deg);
            }

            100% {
                transform: translateY(0px) rotate(0deg);
            }
        }


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


        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }


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


    <nav class="navbar navbar-expand-lg navbar-glass fixed-top py-3" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold fs-3" href="#">
                <i class="bi bi-wallet2 text-primary me-2"></i>
                <span class="font-heading">BUNREK</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-center gap-lg-3">
                    <li class="nav-item"><a class="nav-link px-2 py-2" href="#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link px-2 py-2" href="#how-it-works">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link px-2 py-2" href="#pricing">Harga</a></li>
                    @if (Route::has('login'))
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-indigo w-100 mb-2 mb-lg-0" id="navBtnLogin"
                                href="{{ route('login') }}">Masuk</a>
                        </li>
                    @endif
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="btn btn-indigo w-100" id="navBtnRegister" href="{{ route('register') }}">Mulai
                                Gratis</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>


    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 reveal" id="heroLeftContent">
                    <div class="badge bg-indigo-subtle text-primary border border-indigo-subtle px-3 py-2 rounded-pill fw-semibold mb-3"
                        style="background: rgba(99,102,241,0.08);">
                        <i class="bi bi-sparkles me-1"></i> Pencatat Keuangan & Budgeting Cerdas
                    </div>
                    <h1 class="hero-title">Kuasai Finansial Anda Tanpa Ribet</h1>
                    <p class="hero-subtitle">BUNREK adalah asisten keuangan pribadi yang membantu Anda melacak
                        pengeluaran harian, membuat rencana anggaran bulanan, dan mencapai kemerdekaan finansial dengan
                        mudah.</p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ route('register') }}" class="btn btn-indigo btn-lg px-4 py-3"
                            id="heroBtnRegister">Mulai Sekarang Gratis</a>
                        <a href="#features" class="btn btn-outline-indigo btn-lg px-4 py-3" id="heroBtnFeatures"><i
                                class="bi bi-chevron-down me-1"></i> Lihat Fitur</a>
                    </div>
                </div>
                <div class="col-lg-6 reveal" id="heroRightContent" style="transition-delay: 0.15s">
                    <div class="mockup-container">

                        <div class="glass-card float-element-1 py-2 px-3 shadow"
                            style="border-radius: 16px; background: rgba(255,255,255,0.8);">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success text-white rounded-circle p-1 d-flex" style="font-size: 0.8rem;">
                                    <i class="bi bi-arrow-up-short"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.6rem;">Pemasukan
                                        Bulanan</small>
                                    <strong style="font-size: 0.75rem;">+Rp 15.000.000</strong>
                                </div>
                            </div>
                        </div>


                        <div class="glass-card float-element-2 py-2 px-3 shadow"
                            style="border-radius: 16px; background: rgba(255,255,255,0.85);">
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


                        <div class="mockup-base">
                            <!-- Sidebar -->
                            <div class="mockup-sidebar">
                                <div>
                                    <!-- Logo -->
                                    <div class="mockup-sidebar-logo">
                                        <i class="bi bi-wallet2 text-primary" style="font-size: 0.9rem;"></i>
                                        <span class="font-heading"
                                            style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">BUNREK</span>
                                    </div>
                                    <!-- Menu -->
                                    <div class="mockup-sidebar-menu">
                                        <div class="mockup-menu-item active">
                                            <i class="bi bi-columns-gap"></i>
                                            <span>Dashboard</span>
                                        </div>
                                        <div class="mockup-menu-item">
                                            <i class="bi bi-credit-card"></i>
                                            <span>Transaksi</span>
                                        </div>
                                        <div class="mockup-menu-item">
                                            <i class="bi bi-heart"></i>
                                            <span>Wishlist</span>
                                        </div>
                                        <div class="mockup-menu-item">
                                            <i class="bi bi-clock-history"></i>
                                            <span>Riwayat</span>
                                        </div>
                                        <div class="mockup-menu-item">
                                            <i class="bi bi-wallet2"></i>
                                            <span>Budget Kategori</span>
                                        </div>
                                        <div class="mockup-menu-item">
                                            <i class="bi bi-gem"></i>
                                            <span>Membership</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="mockup-menu-item" style="margin-bottom: 2px;">
                                        <i class="bi bi-gear"></i>
                                        <span>Pengaturan</span>
                                    </div>
                                    <div class="mockup-menu-item" style="margin-bottom: 6px;">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span>Keluar</span>
                                    </div>
                                    <!-- User info -->
                                    <div class="mockup-sidebar-user">
                                        <div class="mockup-avatar">TU</div>
                                        <div class="mockup-user-info">
                                            <div class="mockup-user-name">Test User</div>
                                            <span class="mockup-user-badge">FREE</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Main Content Panel -->
                            <div class="mockup-main">
                                <!-- Topbar -->
                                <div class="mockup-topbar">
                                    <div class="mockup-page-title">Dashboard</div>
                                    <div class="mockup-top-user">
                                        <span>Test User</span>
                                        <div class="mockup-avatar"
                                            style="width: 16px; height: 16px; font-size: 0.45rem;">TU</div>
                                    </div>
                                </div>

                                <!-- Welcome row -->
                                <div class="mockup-welcome-row">
                                    <div class="mockup-welcome-text">Selamat Datang, Test User!</div>
                                    <div class="mockup-date">Senin, 15 Juni 2026</div>
                                </div>

                                <!-- Filters -->
                                <div class="mockup-filters">
                                    <div class="d-flex gap-2" style="flex: 1;">
                                        <div class="mockup-filter-group">
                                            <div class="mockup-filter-label">Rentang Waktu</div>
                                            <div class="mockup-filter-select">
                                                <span>Bulanan</span>
                                                <i class="bi bi-chevron-down" style="font-size: 0.4rem;"></i>
                                            </div>
                                        </div>
                                        <div class="mockup-filter-group">
                                            <div class="mockup-filter-label">Bulan</div>
                                            <div class="mockup-filter-select">
                                                <span>Juni</span>
                                                <i class="bi bi-chevron-down" style="font-size: 0.4rem;"></i>
                                            </div>
                                        </div>
                                        <div class="mockup-filter-group">
                                            <div class="mockup-filter-label">Tahun</div>
                                            <div class="mockup-filter-select">
                                                <span>2026</span>
                                                <i class="bi bi-chevron-down" style="font-size: 0.4rem;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1" style="align-items: flex-end;">
                                        <button class="mockup-btn-primary">
                                            <i class="bi bi-funnel-fill"></i>
                                            <span>Filter</span>
                                        </button>
                                        <button class="mockup-btn-outline">Reset</button>
                                    </div>
                                </div>

                                <!-- Stats Cards -->
                                <div class="mockup-card-grid">
                                    <div class="mockup-stat-card">
                                        <div class="mockup-card-header">
                                            <span class="mockup-card-label">Total Pemasukan (Bulanan)</span>
                                            <div class="mockup-card-icon" style="background: #dcfce7; color: #15803d;">
                                                <i class="bi bi-arrow-down-left"></i>
                                            </div>
                                        </div>
                                        <div class="mockup-card-val text-success">Rp 0</div>
                                    </div>
                                    <div class="mockup-stat-card">
                                        <div class="mockup-card-header">
                                            <span class="mockup-card-label">Total Pengeluaran (Bulanan)</span>
                                            <div class="mockup-card-icon" style="background: #fee2e2; color: #b91c1c;">
                                                <i class="bi bi-arrow-up-right"></i>
                                            </div>
                                        </div>
                                        <div class="mockup-card-val text-danger">Rp 0</div>
                                    </div>
                                    <div class="mockup-stat-card">
                                        <div class="mockup-card-header">
                                            <span class="mockup-card-label">Total Balance (Bulanan)</span>
                                            <div class="mockup-card-icon" style="background: #f1f5f9; color: #475569;">
                                                <i class="bi bi-wallet2"></i>
                                            </div>
                                        </div>
                                        <div class="mockup-card-val text-primary">Rp 0</div>
                                    </div>
                                </div>

                                <!-- Highlight Budget -->
                                <div class="mockup-highlight-section">
                                    <div class="mockup-highlight-title">
                                        <i class="bi bi-folder" style="color: #6366f1;"></i>
                                        <span>Highlight Budget Kategori</span>
                                    </div>
                                    <div class="mockup-empty-state">
                                        <div class="mockup-empty-icon"><i class="bi bi-wallet2"></i></div>
                                        <div class="mockup-empty-title">Belum ada budget yang ditetapkan.</div>
                                        <div class="mockup-empty-desc">Mulai buat perencanaan keuanganmu agar
                                            pengeluaran lebih terkontrol.</div>
                                        <button class="mockup-btn-primary">
                                            <i class="bi bi-plus-lg"></i>
                                            <span>Atur Budget Kategori</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


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


    <section id="features" class="section-padding">
        <div class="container">
            <div class="text-center reveal">
                <h2 class="section-title">Fitur Andalan BUNREK</h2>
                <p class="section-subtitle">Semua alat finansial yang Anda butuhkan untuk mengatur keuangan harian
                    hingga ekspor laporan bulanan di satu tempat.</p>
            </div>

            <div class="row g-4">

                <div class="col-md-6 col-lg-4 reveal">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-journal-plus"></i>
                        </div>
                        <h4>Catat Transaksi</h4>
                        <p class="text-muted mb-0">Masukkan data pemasukan dan pengeluaran Anda dengan kategori lengkap,
                            memo kustom, serta tanggal transaksi instan.</p>
                    </div>
                </div>


                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.05s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4>Riwayat & Filter</h4>
                        <p class="text-muted mb-0">Telusuri seluruh riwayat transaksi terdahulu dengan fitur pencarian
                            cepat serta filter tanggal dan tipe transaksi yang fleksibel.</p>
                    </div>
                </div>


                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.1s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-pie-chart"></i>
                        </div>
                        <h4>Visualisasi Grafik</h4>
                        <p class="text-muted mb-0">Analisis pengeluaran Anda menggunakan Pie Chart interaktif dan Bar
                            Chart dinamis untuk melihat kategori mana yang paling boros.</p>
                    </div>
                </div>


                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.15s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                        </div>
                        <h4>Export PDF/Excel</h4>
                        <p class="text-muted mb-0">Unduh data laporan bulanan Anda dalam format PDF formal untuk
                            dicetak, atau file Excel/Spreadsheet untuk olah data mandiri.</p>
                    </div>
                </div>


                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.2s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h4>Transaksi Rutin</h4>
                        <p class="text-muted mb-0">Jadwalkan transaksi berulang (recurring) seperti gaji, tagihan
                            internet, kos, atau langganan streaming secara otomatis.</p>
                    </div>
                </div>


                <div class="col-md-6 col-lg-4 reveal" style="transition-delay: 0.25s">
                    <div class="glass-card h-100">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-gem"></i>
                        </div>
                        <h4>Membership Premium</h4>
                        <p class="text-muted mb-0">Dapatkan akses penuh ke visualisasi grafik detail, ekspor laporan
                            unlimited, serta fitur eksklusif tanpa batasan apa pun.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="how-it-works" class="section-padding bg-light bg-opacity-20"
        style="background-color: rgba(99, 102, 241, 0.01);">
        <div class="container">
            <div class="text-center reveal">
                <h2 class="section-title">Cara Kerja BUNREK</h2>
                <p class="section-subtitle">Mulailah langkah hemat dan rapi keuangan Anda hanya dalam tiga tahapan mudah
                    berikut.</p>
            </div>

            <div class="row g-4">

                <div class="col-md-4 reveal">
                    <div class="glass-card h-100 step-card">
                        <div class="step-number">01</div>
                        <div class="step-icon">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h4>Daftar Akun Gratis</h4>
                        <p class="text-muted mb-0">Klik tombol Mulai Gratis, isi nama dan email Anda untuk membuat akun
                            resmi dalam waktu 30 detik tanpa biaya tersembunyi.</p>
                    </div>
                </div>


                <div class="col-md-4 reveal" style="transition-delay: 0.1s">
                    <div class="glass-card h-100 step-card">
                        <div class="step-number">02</div>
                        <div class="step-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h4>Catat & Jadwalkan</h4>
                        <p class="text-muted mb-0">Input pengeluaran harian dan pemasukan Anda secara berkala, atau atur
                            pencatatan otomatis untuk tagihan rutin Anda.</p>
                    </div>
                </div>


                <div class="col-md-4 reveal" style="transition-delay: 0.2s">
                    <div class="glass-card h-100 step-card">
                        <div class="step-number">03</div>
                        <div class="step-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4>Analisis & Berhemat</h4>
                        <p class="text-muted mb-0">Pantau grafik analitik secara real-time untuk memotong anggaran yang
                            tidak perlu dan tingkatkan saldo tabungan Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="pricing" class="section-padding">
        <div class="container">
            <div class="text-center reveal">
                <h2 class="section-title">Paket Membership Hemat</h2>
                <p class="section-subtitle">Pilih lisensi yang sesuai dengan kebutuhan Anda. Upgrade kapan saja untuk
                    fitur analisis tanpa batas.</p>
            </div>

            <div class="row g-4 justify-content-center">

                <div class="col-md-5 col-lg-4 reveal">
                    <div class="glass-card h-100 pricing-card d-flex flex-column justify-content-between">
                        <div>
                            <span
                                class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-semibold mb-2">Free
                                Plan</span>
                            <h3 class="mt-2">Mulai Cerdas</h3>
                            <p class="text-muted small">Solusi basic untuk pencatatan harian sederhana.</p>
                            <div class="pricing-price">Rp 0<small> / selamanya</small></div>

                            <hr class="my-4 opacity-10">

                            <ul class="pricing-features-list">
                                <li><i class="bi bi-check-circle-fill text-success"></i> Catat Transaksi Pemasukan</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Catat Transaksi Pengeluaran
                                </li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Riwayat & Filter Standar</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Transaksi Rutin (Recurring)
                                </li>
                                <li class="text-muted opacity-50"><i class="bi bi-x-circle-fill text-danger"></i>
                                    Visualisasi Grafik Pie & Bar</li>
                                <li class="text-muted opacity-50"><i class="bi bi-x-circle-fill text-danger"></i> Export
                                    PDF & Excel Laporan</li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('register') }}" class="btn btn-outline-indigo w-100 py-3"
                                id="pricingBtnFree">Daftar Sekarang</a>
                        </div>
                    </div>
                </div>


                <div class="col-md-5 col-lg-4 reveal" style="transition-delay: 0.1s">
                    <div
                        class="glass-card h-100 pricing-card premium-highlight d-flex flex-column justify-content-between">
                        <div class="premium-ribbon">Rekomendasi</div>
                        <div>
                            <span class="badge bg-indigo-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2"
                                style="background: rgba(99,102,241,0.08);">Premium Plan</span>
                            <h3 class="mt-2">Sultan Hemat</h3>
                            <p class="text-muted small">Akses fitur analitik terlengkap & laporan siap ekspor.</p>
                            <div class="pricing-price">Rp {{ number_format($premiumPrice, 0, ',', '.') }}<small> /
                                    bulan</small></div>

                            <hr class="my-4 opacity-10">

                            <ul class="pricing-features-list">
                                <li><i class="bi bi-check-circle-fill text-success"></i> Semua Fitur Paket Free</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Visualisasi Grafik Pie & Bar
                                    Chart</li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Export Laporan ke Excel & PDF
                                </li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Bebas Iklan & Prioritas Akses
                                </li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Dukungan Bantuan Premium 24/7
                                </li>
                                <li><i class="bi bi-check-circle-fill text-success"></i> Lencana "Premium" di Profil
                                </li>
                            </ul>
                        </div>
                        <div>
                            <a href="{{ route('register') }}" class="btn btn-indigo w-100 py-3"
                                id="pricingBtnPremium">Langganan Premium</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="container mb-5 reveal">
        <div class="cta-container text-center">
            <div class="cta-content max-width-600 mx-auto">
                <h2 class="display-5 fw-bold mb-3">Siap untuk Mengontrol Finansial Anda?</h2>
                <p class="fs-5 opacity-75 mb-4 max-width-500 mx-auto">Bergabunglah dengan pengguna BUNREK lainnya yang
                    sukses berhemat jutaan rupiah tiap bulannya.</p>
                <a href="{{ route('register') }}"
                    class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold text-primary border-0 shadow-sm"
                    id="ctaBtnRegister" style="color: var(--primary-color) !important;">
                    Gabung Sekarang Gratis!
                </a>
            </div>
        </div>
    </section>


    <footer>
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-6 col-md-12">
                    <a class="footer-brand d-flex align-items-center fw-bold fs-4 text-white mb-3" href="#">
                        <i class="bi bi-wallet2 text-primary me-2"></i>
                        <span>BUNREK</span>
                    </a>
                    <p class="max-width-350" style="max-width: 380px;">Aplikasi pencatat keuangan modern yang didesain
                        untuk mempercepat efisiensi penganggaran harian serta visualisasi grafik aset masa depan Anda
                        secara akurat.</p>
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
                        <li><a href="https://github.com" target="_blank" rel="noopener noreferrer"><i
                                    class="bi bi-github me-1"></i> GitHub Project</a></li>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


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