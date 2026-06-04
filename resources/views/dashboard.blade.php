@extends('layouts.master')

@section('page_title', 'Dashboard')

@section('content')
    @php
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $weeks = [
            1 => 'Minggu 1 (Tanggal 1 - 7)',
            2 => 'Minggu 2 (Tanggal 8 - 14)',
            3 => 'Minggu 3 (Tanggal 15 - 21)',
            4 => 'Minggu 4 (Tanggal 22 - 28)',
            5 => 'Minggu 5 (Tanggal 29 - Akhir Bulan)'
        ];
        $labelMap = [
            'mingguan' => 'Mingguan',
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
            'keseluruhan' => 'Keseluruhan'
        ];
        $labelText = $labelMap[$filterType] ?? 'Bulanan';
    @endphp

    <!-- Form Filter Ringkasan -->
    <form action="{{ route('dashboard') }}" method="GET" style="margin-bottom: 24px;">
        <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; padding: var(--space-md) var(--space-lg); background: var(--bg-white); border-radius: var(--radius-lg); border: 1px solid var(--border-light); box-shadow: var(--shadow-sm);">
            
            <div style="flex: 1; min-width: 150px;">
                <label class="bunrek-label" style="margin-bottom: 6px;">Rentang Waktu</label>
                <select name="filter_type" id="filter_type" class="bunrek-select" onchange="toggleFilterFields()" style="cursor: pointer;">
                    <option value="mingguan" {{ $filterType === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $filterType === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $filterType === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    <option value="keseluruhan" {{ $filterType === 'keseluruhan' ? 'selected' : '' }}>Keseluruhan</option>
                </select>
            </div>

            <div id="month_wrapper" style="flex: 1; min-width: 150px;">
                <label class="bunrek-label" style="margin-bottom: 6px;">Bulan</label>
                <select name="month" id="filter_month" class="bunrek-select" style="cursor: pointer;">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $month === $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="week_wrapper" style="flex: 1; min-width: 180px;">
                <label class="bunrek-label" style="margin-bottom: 6px;">Minggu</label>
                <select name="week" id="filter_week" class="bunrek-select" style="cursor: pointer;">
                    @foreach($weeks as $num => $desc)
                        <option value="{{ $num }}" {{ $week === $num ? 'selected' : '' }}>{{ $desc }}</option>
                    @endforeach
                </select>
            </div>

            <div id="year_wrapper" style="flex: 1; min-width: 120px;">
                <label class="bunrek-label" style="margin-bottom: 6px;">Tahun</label>
                <select name="year" id="filter_year" class="bunrek-select" style="cursor: pointer;">
                    @foreach(range(\Carbon\Carbon::today()->year - 5, \Carbon\Carbon::today()->year + 2) as $y)
                        <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-bunrek btn-primary" style="height: 42px; padding: 0 20px;">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ route('dashboard') }}" class="btn-bunrek btn-outline" style="height: 42px; padding: 0 20px; display: inline-flex; align-items: center; justify-content: center;">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-label">Total Pemasukan ({{ $labelText }})</span>
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
                <span class="stat-card-label">Total Pengeluaran ({{ $labelText }})</span>
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
                <span class="stat-card-label">Total Balance ({{ $labelText }})</span>
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

    <div class="bunrek-card" style="margin-top: 24px;">
        <div class="bunrek-card-header">
            <h2 class="bunrek-card-title">
                Ringkasan &mdash; <span
                    style="font-weight: 500; font-size: 0.95rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</span>
            </h2>
        </div>
        <div class="bunrek-card-body">
            <p style="color: var(--text-muted); margin-bottom: 16px; font-size: var(--fs-sm);">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} hingga {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
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
@endsection

@push('scripts')
    <script>
        function toggleFilterFields() {
            const filterType = document.getElementById('filter_type').value;
            const monthWrapper = document.getElementById('month_wrapper');
            const weekWrapper = document.getElementById('week_wrapper');
            const yearWrapper = document.getElementById('year_wrapper');

            if (filterType === 'mingguan') {
                monthWrapper.style.display = 'block';
                weekWrapper.style.display = 'block';
                yearWrapper.style.display = 'block';
            } else if (filterType === 'bulanan') {
                monthWrapper.style.display = 'block';
                weekWrapper.style.display = 'none';
                yearWrapper.style.display = 'block';
            } else if (filterType === 'tahunan') {
                monthWrapper.style.display = 'none';
                weekWrapper.style.display = 'none';
                yearWrapper.style.display = 'block';
            } else if (filterType === 'keseluruhan') {
                monthWrapper.style.display = 'none';
                weekWrapper.style.display = 'none';
                yearWrapper.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleFilterFields();
        });
    </script>
@endpush