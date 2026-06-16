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
            12 => 'Desember',
        ];
        $weeks = [
            1 => 'Minggu 1 (Tanggal 1 - 7)',
            2 => 'Minggu 2 (Tanggal 8 - 14)',
            3 => 'Minggu 3 (Tanggal 15 - 21)',
            4 => 'Minggu 4 (Tanggal 22 - 28)',
            5 => 'Minggu 5 (Tanggal 29 - Akhir Bulan)',
        ];
        $labelMap = [
            'mingguan' => 'Mingguan',
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
            'keseluruhan' => 'Keseluruhan',
        ];
        $labelText = $labelMap[$filterType] ?? 'Bulanan';
    @endphp

    <!-- Greeting Section -->
    <div style="margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <h1 style="margin: 0; font-size: 2rem; font-weight: 700; color: var(--text-dark); letter-spacing: -0.5px;">
                Selamat Datang, {{ $userName }}!
            </h1>
            <p style="margin: 0; color: var(--text-muted); font-size: var(--fs-base); font-weight: 500;">
                {{ $currentDate }}
            </p>
        </div>
        <hr style="margin: 16px 0; border: none; border-top: 1px solid var(--border-light);">
    </div>

    <!-- Form Filter Ringkasan -->
    <form action="{{ route('dashboard') }}" method="GET" style="margin-bottom: 24px;">
        <div
            style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; padding: var(--space-md) var(--space-lg); background: var(--bg-white); border-radius: var(--radius-lg); border: 1px solid var(--border-light); box-shadow: var(--shadow-sm);">

            <div style="flex: 1; min-width: 150px;">
                <label class="bunrek-label" style="margin-bottom: 6px;">Rentang Waktu</label>
                <select name="filter_type" id="filter_type" class="bunrek-select" onchange="toggleFilterFields(); restrictFutureDates();"
                    style="cursor: pointer;">
                    <option value="mingguan" {{ $filterType === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $filterType === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $filterType === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    <option value="keseluruhan" {{ $filterType === 'keseluruhan' ? 'selected' : '' }}>Keseluruhan</option>
                </select>
            </div>

            <div id="month_wrapper" style="flex: 1; min-width: 150px;">
                <label class="bunrek-label" style="margin-bottom: 6px;">Bulan</label>
                <select name="month" id="filter_month" class="bunrek-select" onchange="restrictFutureDates()" style="cursor: pointer;">
                    @foreach ($months as $num => $name)
                        @php
                            $isFutureMonth = ($year == \Carbon\Carbon::today()->year && $num > \Carbon\Carbon::today()->month);
                        @endphp
                        <option value="{{ $num }}" {{ $month === $num ? 'selected' : '' }} {{ $isFutureMonth ? 'disabled' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="week_wrapper" style="flex: 1; min-width: 180px;">
                <label class="bunrek-label" style="margin-bottom: 6px;">Minggu</label>
                <select name="week" id="filter_week" class="bunrek-select" style="cursor: pointer;">
                    @foreach ($weeks as $num => $desc)
                        @php
                            $todayDay = \Carbon\Carbon::today()->day;
                            $currentWeekNum = 5;
                            if ($todayDay <= 7) $currentWeekNum = 1;
                            elseif ($todayDay <= 14) $currentWeekNum = 2;
                            elseif ($todayDay <= 21) $currentWeekNum = 3;
                            elseif ($todayDay <= 28) $currentWeekNum = 4;
                            $isFutureWeek = ($year == \Carbon\Carbon::today()->year && $month == \Carbon\Carbon::today()->month && $num > $currentWeekNum);
                        @endphp
                        <option value="{{ $num }}" {{ $week === $num ? 'selected' : '' }} {{ $isFutureWeek ? 'disabled' : '' }}>
                            {{ $desc }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="year_wrapper" style="flex: 1; min-width: 120px;">
                <label class="bunrek-label" style="margin-bottom: 6px;">Tahun</label>
                <select name="year" id="filter_year" class="bunrek-select" onchange="restrictFutureDates()" style="cursor: pointer;">
                    @foreach (range(\Carbon\Carbon::today()->year - 5, \Carbon\Carbon::today()->year) as $y)
                        <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-bunrek btn-primary" style="height: 42px; padding: 0 20px;">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ route('dashboard') }}" class="btn-bunrek btn-outline"
                    style="height: 42px; padding: 0 20px; display: inline-flex; align-items: center; justify-content: center;">
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
            <div class="stat-card-value" style="color: var(--primary-color); margin-bottom: 4px;">
                Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
            </div>
            @if(($savingBalance ?? 0) > 0)
                <div style="font-size: var(--fs-xs); color: var(--text-muted); font-weight: 500; margin-top: 4px;">
                    <i class="bi bi-info-circle-fill" style="color: var(--primary-color);"></i> Rp {{ number_format($savingBalance, 0, ',', '.') }} terpakai untuk saving balance
                </div>
            @endif
        </div>
    </div>

    @if(!$isPremium)
        <!-- Free User: Highlight Budget Kategori -->
        <div class="bunrek-card" style="margin-top: 24px;">
            <div class="bunrek-card-header">
                <h2 class="bunrek-card-title">
                    <i class="bi bi-wallet2" style="color: var(--primary-color); margin-right: 8px;"></i> Highlight Budget Kategori
                </h2>
            </div>
            <div class="bunrek-card-body">
                @if($budgets->isEmpty())
                    <div style="text-align: center; padding: var(--space-xl); background: var(--bg-light); border-radius: var(--radius-base);">
                        <i class="bi bi-wallet2" style="font-size: 2.5rem; color: var(--text-light); display: block; margin-bottom: var(--space-sm);"></i>
                        <p style="color: var(--text-muted); font-size: var(--fs-base); margin: 0;">Belum ada budget yang ditetapkan.</p>
                        <p style="color: var(--text-light); font-size: var(--fs-xs); margin: 4px 0 16px;">Mulai buat perencanaan keuanganmu agar pengeluaran lebih terkontrol.</p>
                        <a href="{{ route('budget.index') }}" class="btn-bunrek btn-primary" style="font-size: var(--fs-sm);">
                            <i class="bi bi-plus-lg"></i> Atur Budget Kategori
                        </a>
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--space-lg);">
                        @foreach($budgets as $budget)
                            @php
                                $pct = min(100, $budget->getUsagePercentage());
                                $spent = $budget->getCurrentSpending();
                                $barColor = $pct >= 100 ? 'var(--color-expense)' : ($pct >= 80 ? '#f59e0b' : 'var(--color-income)');
                            @endphp
                            <div class="bunrek-card" style="padding: 0; overflow: hidden; border: 1px solid var(--border-light); box-shadow: var(--shadow-sm);">
                                <div style="padding: var(--space-md) var(--space-lg);">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-sm);">
                                        <div>
                                            <div style="font-weight: 700; font-size: var(--fs-base); color: var(--text-dark);">
                                                {{ $budget->category->category_name ?? 'Kategori #' . $budget->category_id }}
                                            </div>
                                            <div style="font-size: var(--fs-xs); color: var(--text-muted); text-transform: capitalize;">{{ $budget->period }}</div>
                                        </div>
                                    </div>

                                    <div style="font-size: var(--fs-lg); font-weight: 800; color: {{ $barColor }}; margin-bottom: 4px;">
                                        Rp {{ number_format($spent, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: var(--fs-xs); color: var(--text-muted);">
                                        dari <strong>Rp {{ number_format($budget->amount, 0, ',', '.') }}</strong>
                                    </div>

                                    <div style="margin-top: var(--space-sm); background: var(--border-light); border-radius: 999px; height: 8px;">
                                        <div style="width: {{ $pct }}%; background: {{ $barColor }}; height: 8px; border-radius: 999px; transition: width 0.4s;"></div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-top: 4px;">
                                        <span style="font-size: var(--fs-xs); color: var(--text-muted);">{{ $pct }}% terpakai</span>
                                        @if($pct >= 100)
                                            <span style="font-size: var(--fs-xs); font-weight: 700; color: var(--color-expense);">Melebihi batas!</span>
                                        @elseif($pct >= 80)
                                            <span style="font-size: var(--fs-xs); font-weight: 700; color: #f59e0b;">Hampir habis</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Membership User: Ringkasan & Chart -->
        <div class="bunrek-card" style="margin-top: 24px;">
            <div class="bunrek-card-header">
                <h2 class="bunrek-card-title">
                    Ringkasan &mdash; <span style="color: var(--text-muted); margin-bottom: 16px; font-size: var(--fs-sm);">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} hingga
                    {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</span>

                </h2>   
            </div>
            <div class="bunrek-card-body">
                @if($categoryDistribution && !$categoryDistribution['isEmpty'])
                    <div>
                        <h3 style="font-size: var(--fs-base); font-weight: 700; color: var(--text-dark); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-pie-chart-fill" style="color: var(--primary-color);"></i> Distribusi Pengeluaran
                        </h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; align-items: center;">
                            <div style="position: relative; width: 100%; max-width: 200px; aspect-ratio: 1/1; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                <canvas id="donutChartDashboard" width="180" height="180"></canvas>
                            </div>
                            <div>
                                <div style="display: flex; flex-direction: column; gap: 8px; max-height: 180px; overflow-y: auto; padding-right: 8px;">
                                    @foreach($categoryDistribution['categories'] as $i => $cat)
                                        <div style="display: flex; align-items: center; gap: 8px; font-size: var(--fs-sm);">
                                            <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $chartColors[$i % count($chartColors)] }}; flex-shrink: 0;"></span>
                                            <span style="flex-grow: 1; color: var(--text-dark); font-weight: 500;">{{ $cat['name'] }}</span>
                                            <span style="color: var(--text-muted); font-weight: 600;">{{ $cat['percentage'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div style="display: flex; gap: var(--space-xs); flex-wrap: wrap; margin-top: 24px;">
                    <a href="{{ route('transactions.index') }}" class="btn-bunrek btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Transaksi
                    </a>
                    <a href="{{ route('transactions.history') }}" class="btn-bunrek btn-secondary">
                        <i class="bi bi-clock-history"></i> Lihat Riwayat
                    </a>
                </div>
            </div>
        </div>
    @endif
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

        function restrictFutureDates() {
            const today = new Date();
            const currentYear = today.getFullYear();
            const currentMonth = today.getMonth() + 1; // 1-indexed
            const currentDay = today.getDate();

            let currentWeek = 5;
            if (currentDay <= 7) currentWeek = 1;
            else if (currentDay <= 14) currentWeek = 2;
            else if (currentDay <= 21) currentWeek = 3;
            else if (currentDay <= 28) currentWeek = 4;

            const filterYear = document.getElementById('filter_year');
            const filterMonth = document.getElementById('filter_month');
            const filterWeek = document.getElementById('filter_week');

            if (!filterYear) return;

            const selectedYear = parseInt(filterYear.value);
            const selectedMonth = parseInt(filterMonth ? filterMonth.value : 0);

            // Batasi tahun
            Array.from(filterYear.options).forEach(opt => {
                const val = parseInt(opt.value);
                if (val > currentYear) {
                    opt.disabled = true;
                    if (opt.selected) {
                        filterYear.value = currentYear;
                    }
                }
            });

            // Batasi bulan
            if (filterMonth) {
                Array.from(filterMonth.options).forEach(opt => {
                    const val = parseInt(opt.value);
                    if (selectedYear > currentYear || (selectedYear === currentYear && val > currentMonth)) {
                        opt.disabled = true;
                        if (opt.selected) {
                            filterMonth.value = currentMonth;
                        }
                    } else {
                        opt.disabled = false;
                    }
                });
            }

            // Batasi minggu
            if (filterWeek) {
                const updatedSelectedMonth = parseInt(filterMonth ? filterMonth.value : 0);
                Array.from(filterWeek.options).forEach(opt => {
                    const val = parseInt(opt.value);
                    if (selectedYear > currentYear || 
                        (selectedYear === currentYear && updatedSelectedMonth > currentMonth) ||
                        (selectedYear === currentYear && updatedSelectedMonth === currentMonth && val > currentWeek)) {
                        opt.disabled = true;
                        if (opt.selected) {
                            filterWeek.value = currentWeek;
                        }
                    } else {
                        opt.disabled = false;
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleFilterFields();
            restrictFutureDates();
        });

        @if($isPremium && $categoryDistribution && !$categoryDistribution['isEmpty'])
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('donutChartDashboard').getContext('2d');
            const catData = @json($categoryDistribution);
            const colors = @json($chartColors);
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: catData.categories.map(c => c.name),
                    datasets: [{
                        data: catData.categories.map(c => c.amount),
                        backgroundColor: catData.categories.map((_, i) => colors[i % colors.length]),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
        @endif
    </script>
@endpush
