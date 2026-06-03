@extends('layouts.master')

@section('page_title', 'Dashboard')

@section('content')
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-label">Total Pemasukan (1 Bulan)</span>
                <div class="stat-card-icon income">
                    <i class="bi bi-arrow-down-left-circle"></i>
                </div>
            </div>
            <div class="stat-card-value text-income">
                Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-label">Total Pengeluaran (1 Bulan)</span>
                <div class="stat-card-icon expense">
                    <i class="bi bi-arrow-up-right-circle"></i>
                </div>
            </div>
            <div class="stat-card-value text-expense">
                Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-label">Total Balance (1 Bulan)</span>
                <div class="stat-card-icon primary">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <div class="stat-card-value" style="color: var(--primary-color);">
                Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>

    @php
        $membershipFeature = app(\App\Features\MembershipFeatureInterface::class);
    @endphp

    @if (!$membershipFeature->canViewChart())
        <div id="chart-section" class="premium-cta">
            <div class="premium-cta-icon">
                <i class="bi bi-lock-fill text-warning"></i>
            </div>
            <h4 style="font-family: var(--font-heading); font-weight: 700; margin-bottom: 8px;">
                Visualisasi Grafik Khusus Premium
            </h4>
            <p class="premium-cta-text">
                Dapatkan visualisasi grafik pengeluaran dan pemasukan interaktif yang detail dengan upgrade ke Premium.
            </p>
            <a href="{{ route('membership.index') }}" class="btn-bunrek btn-primary">
                <i class="bi bi-gem"></i> Upgrade Sekarang
            </a>
        </div>
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.location.hash === '#chart-section') {
                        window.location.href = '{{ route('membership.index') }}';
                    }
                });
            </script>
        @endpush
    @else
        <div id="chart-section" class="premium-cta"
            style="background: linear-gradient(135deg, var(--primary-50), rgba(99, 102, 241, 0.02)); border-style: solid;">
            <div class="premium-cta-icon">
                <i class="bi bi-bar-chart-line-fill text-primary"></i>
            </div>
            <h4 style="font-family: var(--font-heading); font-weight: 700; margin-bottom: 8px;">
                Visualisasi Grafik Siap Digunakan
            </h4>
            <p class="premium-cta-text">
                Analisis keuangan Anda secara visual dengan diagram interaktif kami.
            </p>
            <a href="{{ route('charts.index') }}" class="btn-bunrek btn-primary">
                <i class="bi bi-arrow-right-short"></i> Lihat Grafik Visualisasi
            </a>
        </div>
    @endif

    <div class="content-grid">
        <div class="bunrek-card">
            <div class="bunrek-card-header">
                <h2 class="bunrek-card-title">
                    Ringkasan &mdash; <span
                        style="font-weight: 500; font-size: 0.95rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</span>
                </h2>
            </div>
            <div class="bunrek-card-body">
                <p style="color: var(--text-muted); margin-bottom: 16px; font-size: var(--fs-sm);">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d MMMM Y') }} hingga {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d MMMM Y') }}
                </p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <!-- Pemasukan -->
                    <div style="padding: 16px; background: var(--bg-light); border-radius: var(--radius-base); border-left: 4px solid var(--color-income);">
                        <p style="color: var(--text-muted); font-size: var(--fs-sm); margin-bottom: 8px;">Pemasukan</p>
                        <h3 style="color: var(--color-income); font-size: 24px; font-weight: 700;">
                            Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>

                    <!-- Pengeluaran -->
                    <div style="padding: 16px; background: var(--bg-light); border-radius: var(--radius-base); border-left: 4px solid var(--color-expense);">
                        <p style="color: var(--text-muted); font-size: var(--fs-sm); margin-bottom: 8px;">Pengeluaran</p>
                        <h3 style="color: var(--color-expense); font-size: 24px; font-weight: 700;">
                            Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>

                    <!-- Balance -->
                    <div style="padding: 16px; background: var(--bg-light); border-radius: var(--radius-base); border-left: 4px solid var(--primary-color);">
                        <p style="color: var(--text-muted); font-size: var(--fs-sm); margin-bottom: 8px;">Balance</p>
                        <h3 style="color: var(--primary-color); font-size: 24px; font-weight: 700;">
                            Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>

                <div style="display: flex; gap: var(--space-xs); flex-wrap: wrap;">
                    <a href="{{ route('transactions.index') }}" class="btn-bunrek btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Transaksi
                    </a>
                    <a href="{{ route('transactions.history') }}" class="btn-bunrek btn-secondary">
                        <i class="bi bi-clock-history"></i> Lihat Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection