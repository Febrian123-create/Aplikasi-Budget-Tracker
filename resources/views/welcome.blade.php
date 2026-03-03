<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BudgetFlow | Master Your Finances</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Take control of your money with BudgetFlow. The smartest way to track expenses, set budgets, and achieve your financial goals.">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Custom Premium Styles -->
    <style>
        :root {
            --primary-color: #10b981;
            --primary-dark: #059669;
            --accent-color: #84cc16;
            --bg-light: #f8fafc;
            --bg-dark: #020617;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-light: #020617;
                --text-main: #f8fafc;
                --text-muted: #94a3b8;
                --glass-bg: rgba(30, 41, 59, 0.7);
                --glass-border: rgba(255, 255, 255, 0.1);
            }
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }

        /* --- Glassmorphism Components --- */
        .glass-navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            transition: var(--transition);
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2rem;
            transition: var(--transition);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
        }

        /* --- Buttons --- */
        .btn-premium {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white !important;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.75rem;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: var(--transition);
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            filter: brightness(1.1);
        }

        .btn-outline-premium {
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            border-radius: 12px;
            padding: 0.75rem 1.75rem;
            font-weight: 600;
            background: transparent;
            transition: var(--transition);
        }

        .btn-outline-premium:hover {
            background: var(--glass-border);
            color: var(--primary-color);
        }

        /* --- Hero Section --- */
        .hero-section {
            padding: 180px 0 100px;
            position: relative;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.1;
            background: linear-gradient(to right, var(--text-main), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .hero-img {
            max-width: 100%;
            border-radius: 24px;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.25);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* --- Sections --- */
        .section-padding {
            padding: 100px 0;
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        .stats-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-color);
            font-family: 'Outfit', sans-serif;
        }

        /* --- CTA --- */
        .cta-banner {
            background: linear-gradient(135deg, var(--primary-dark), #064e3b);
            border-radius: 40px;
            padding: 80px 40px;
            color: white;
            text-align: center;
        }

        /* --- AOS Integration --- */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 1s ease-out, transform 1s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--glass-border);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top glass-navbar py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="#">
                <img src="/images/logo.png" alt="Logo" height="36" class="me-2 text-primary">
                <span class="font-heading fs-3">BudgetFlow</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-lg-3">
                    <li class="nav-item"><a class="nav-link fw-medium px-3 text-secondary" href="#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium px-3 text-secondary" href="#how-it-works">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium px-3 text-secondary" href="#">Login</a></li>
                    <li class="nav-item ms-lg-2"><a class="btn btn-premium" href="#">Coba Gratis</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 reveal">
                    <h1 class="hero-title">Kuasai Keuanganmu dengan Cerdas</h1>
                    <p class="fs-5 text-muted mb-4 opacity-75">Track pengeluaran, buat anggaran, dan capai tujuan finansialmu dengan cara yang lebih simple dan elegan (Data Dummy).</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="#" class="btn btn-premium px-4 py-3 fs-5">Mulai Sekarang</a>
                        <a href="#features" class="btn btn-outline-premium px-4 py-3 fs-5">Pelajari Fitur</a>
                    </div>
                </div>
                <div class="col-lg-6 reveal" style="transition-delay: 0.2s">
                    <img src="/images/hero-dashboard.png" alt="Dashboard Mockup" class="hero-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="section-padding bg-body-tertiary">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4 reveal">
                    <div class="stats-number">Rp 2.5jt</div>
                    <p class="text-muted fw-medium">Rata-rata Penghematan</p>
                </div>
                <div class="col-md-4 reveal" style="transition-delay: 0.1s">
                    <div class="stats-number">45k+</div>
                    <p class="text-muted fw-medium">Pengguna Dummy</p>
                </div>
                <div class="col-md-4 reveal" style="transition-delay: 0.2s">
                    <div class="stats-number">4.9/5</div>
                    <p class="text-muted fw-medium">Rating App Store</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="section-padding">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <h2 class="fs-1">Fitur Unggulan</h2>
                <p class="text-muted fs-5">Semua alat yang kamu butuhkan untuk jadi sultan yang hemat.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 reveal">
                    <div class="glass-card h-100">
                        <span class="feature-icon">📊</span>
                        <h3>Visualisasi Data</h3>
                        <p class="text-muted">Lihat kemana uangmu pergi dengan grafik interaktif yang mudah dimengerti.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal" style="transition-delay: 0.1s">
                    <div class="glass-card h-100 text-center border-primary shadow-sm">
                        <span class="feature-icon">🎯</span>
                        <h3>Target Menabung</h3>
                        <p class="text-muted">Atur tujuan finansialmu dan biarkan kami membantumu mencapainya lebih cepat.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal" style="transition-delay: 0.2s">
                    <div class="glass-card h-100">
                        <span class="feature-icon">📱</span>
                        <h3>Akses Mobile</h3>
                        <p class="text-muted">BudgetFlow tersedia di desktop dan mobile, sinkronisasi data secara real-time.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section-padding container">
        <div class="cta-banner reveal">
            <h2 class="display-5 mb-3">Siap untuk mulai berhemat?</h2>
            <p class="fs-5 opacity-75 mb-4 max-width-600 mx-auto">Bergabunglah dengan ribuan pengguna lainnya yang telah berhasil mengelola keuangan mereka dengan BudgetFlow.</p>
            <a href="#" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold text-success border-0">Daftar Sekarang — Gratis</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <a class="navbar-brand d-flex align-items-center fw-bold mb-3" href="#">
                        <img src="/images/logo.png" alt="Logo" height="30" class="me-2">
                        <span class="font-heading fs-4">BudgetFlow</span>
                    </a>
                    <p class="text-muted max-width-300">Aplikasi budget tracker statis dengan data dummy untuk demo fungsionalitas UI modern.</p>
                </div>
                <div class="col-lg-3 col-6">
                    <h5 class="mb-3">Produk</h5>
                    <ul class="nav flex-column gap-2">
                        <li class="nav-item"><a href="#" class="nav-link p-0 text-muted">Fitur</a></li>
                        <li class="nav-item"><a href="#" class="nav-link p-0 text-muted">Demo Eksklusif</a></li>
                        <li class="nav-item"><a href="#" class="nav-link p-0 text-muted">Harga Dummy</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-6">
                    <h5 class="mb-3">Lainnya</h5>
                    <ul class="nav flex-column gap-2">
                        <li class="nav-item"><a href="#" class="nav-link p-0 text-muted">Privacy Policy</a></li>
                        <li class="nav-item"><a href="#" class="nav-link p-0 text-muted">Contact Us</a></li>
                        <li class="nav-item"><a href="#" class="nav-link p-0 text-muted">Github</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-5 opacity-10">
            <div class="text-center text-muted">
                <p>&copy; 2026 BudgetFlow. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scroll Animation Script -->
    <script>
        function reveal() {
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }
        window.addEventListener("scroll", reveal);
        // Trigger once on load
        window.onload = reveal;
    </script>
</body>
</html>
