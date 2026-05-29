@extends('layouts.master')

@push('styles')
    <style>
        .chart-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            border: 1px solid #f0f0f0;
            padding: 28px;
            transition: box-shadow 0.3s;
        }

        .chart-card:hover {
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.10);
        }

        .metric-card {
            border-radius: 14px;
            padding: 22px 24px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .metric-card:hover {
            transform: translateY(-3px);
        }

        .metric-card .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 14px;
        }

        .metric-card .metric-label {
            font-size: 13px;
            font-weight: 500;
            opacity: 0.8;
            margin-bottom: 6px;
        }

        .metric-card .metric-value {
            font-size: 22px;
            font-weight: 700;
        }

        .mc-income {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #2e7d32;
        }

        .mc-income .metric-icon {
            background: rgba(46, 125, 50, 0.15);
            color: #2e7d32;
        }

        .mc-expense {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            color: #c62828;
        }

        .mc-expense .metric-icon {
            background: rgba(198, 40, 40, 0.15);
            color: #c62828;
        }

        .mc-saldo-pos {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #1565c0;
        }

        .mc-saldo-pos .metric-icon {
            background: rgba(21, 101, 192, 0.15);
            color: #1565c0;
        }

        .mc-saldo-neg {
            background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
            color: #ad1457;
        }

        .mc-saldo-neg .metric-icon {
            background: rgba(173, 20, 87, 0.15);
            color: #ad1457;
        }

        .mc-ratio {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            color: #e65100;
        }

        .mc-ratio .metric-icon {
            background: rgba(230, 81, 0, 0.15);
            color: #e65100;
        }

        .progress-bar-custom {
            height: 8px;
            border-radius: 4px;
            background: rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-bar-custom .fill {
            height: 100%;
            border-radius: 4px;
            transition: width 1s ease;
        }

        .fill-green {
            background: #43a047;
        }

        .fill-yellow {
            background: #fb8c00;
        }

        .fill-red {
            background: #e53935;
        }

        .warning-banner {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            border: 1px solid #ffb74d;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #e65100;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: #9e9e9e;
        }

        .empty-state .empty-icon {
            font-size: 56px;
            margin-bottom: 16px;
            opacity: 0.4;
        }

        .empty-state p {
            font-size: 15px;
            margin-bottom: 16px;
        }

        .empty-state .cta-btn {
            display: inline-block;
            padding: 10px 28px;
            background: #6366f1;
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }

        .empty-state .cta-btn:hover {
            background: #4f46e5;
            color: #fff;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 13px;
        }

        .legend-item:hover {
            background: #f5f5f5;
        }

        .legend-item.hidden-segment {
            opacity: 0.4;
            text-decoration: line-through;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .legend-name {
            flex: 1;
            font-weight: 500;
            color: #333;
        }

        .legend-amount {
            font-weight: 600;
            color: #555;
        }

        .legend-pct {
            font-size: 12px;
            color: #999;
            min-width: 45px;
            text-align: right;
        }

        .donut-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }

        .donut-center .total-label {
            font-size: 11px;
            color: #999;
            font-weight: 500;
        }

        .donut-center .total-value {
            font-size: 18px;
            font-weight: 700;
            color: #333;
        }

        .chart-wrapper {
            position: relative;
        }

        .filter-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-bar select,
        .filter-bar .toggle-btn {
            padding: 8px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background: #fff;
            font-size: 13px;
            color: #333;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .filter-bar select:focus,
        .filter-bar .toggle-btn:focus {
            border-color: #6366f1;
            outline: none;
        }

        .toggle-btn.active {
            background: #6366f1;
            color: #fff;
            border-color: #6366f1;
        }

        .surplus-label {
            color: #43a047;
            font-weight: 600;
            font-size: 11px;
        }

        .deficit-label {
            color: #e53935;
            font-weight: 600;
            font-size: 11px;
        }

        .blur-overlay {
            position: relative;
        }

        .blur-overlay::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(6px);
            border-radius: 16px;
            z-index: 2;
        }

        .upgrade-badge {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
            text-align: center;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #6366f1;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="page-inner">
            
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap:12px;">
                <div>
                    <h3 class="fw-bold mb-1" style="color:#1a1a2e;">Visualisasi Keuangan</h3>
                    <p class="text-muted mb-0">
                        {{ \App\Helpers\ChartHelper::formatBulanLengkap($bulan) }} {{ $tahun }}
                        @if(!$isPremium)
                            <span class="badge bg-secondary ms-2">Free</span>
                        @else
                            <span class="badge" style="background:#6366f1;">Premium</span>
                        @endif
                    </p>
                </div>
                @if($canSelectMonth)
                    <form method="GET" action="{{ route('charts.index') }}" class="filter-bar">
                        <select name="bulan" onchange="this.form.submit()">
                            @foreach($availableMonths as $m)
                                <option value="{{ $m['value'] }}" {{ $m['selected'] ? 'selected' : '' }}>{{ $m['label'] }}</option>
                            @endforeach
                        </select>
                        <select name="tahun" onchange="this.form.submit()">
                            @foreach($availableYears as $y)
                                <option value="{{ $y['value'] }}" {{ $y['selected'] ? 'selected' : '' }}>{{ $y['label'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="range" value="{{ $barRange }}">
                    </form>
                @endif
            </div>

            
            @if($metricCards['overBudgetAmount'])
                <div class="warning-banner">
                    <i class="fas fa-exclamation-triangle" style="font-size:20px;"></i>
                    <span>Pengeluaran melebihi pemasukan bulan ini sebesar
                        <strong>{{ $metricCards['overBudgetAmount'] }}</strong></span>
                </div>
            @endif

            
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="metric-card mc-income">
                        <div class="metric-icon"><i class="fas fa-arrow-down"></i></div>
                        <div class="metric-label">Total Pemasukan</div>
                        <div class="metric-value">{{ $metricCards['totalIncomeFormatted'] }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="metric-card mc-expense">
                        <div class="metric-icon"><i class="fas fa-arrow-up"></i></div>
                        <div class="metric-label">Total Pengeluaran</div>
                        <div class="metric-value">{{ $metricCards['totalExpenseFormatted'] }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="metric-card {{ $metricCards['isSaldoPositif'] ? 'mc-saldo-pos' : 'mc-saldo-neg' }}">
                        <div class="metric-icon"><i class="fas fa-wallet"></i></div>
                        <div class="metric-label">Saldo</div>
                        <div class="metric-value">
                            {{ $metricCards['isSaldoPositif'] ? '' : '-' }}{{ $metricCards['saldoFormatted'] }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="metric-card mc-ratio">
                        <div class="metric-icon"><i class="fas fa-percentage"></i></div>
                        <div class="metric-label">Rasio Pengeluaran</div>
                        <div class="metric-value">{{ number_format($metricCards['expensePercentage'], 1) }}%</div>
                        <div class="progress-bar-custom">
                            <div class="fill fill-{{ $metricCards['progressLevel'] }}"
                                style="width: {{ min($metricCards['expensePercentage'], 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row g-4">
                
                <div class="col-lg-5">
                    <div class="chart-card">
                        <div class="section-title"><i class="fas fa-chart-pie"></i> Distribusi Pengeluaran</div>
                        @if($categoryDistribution['isEmpty'] && $categoryDistribution['allIncome'])
                            <div class="empty-state">
                                <div class="empty-icon">🎉</div>
                                <p>Tidak ada pengeluaran bulan ini — bagus!</p>
                            </div>
                        @elseif($categoryDistribution['isEmpty'])
                            <div class="empty-state">
                                <div class="empty-icon">📊</div>
                                <p>Belum ada pengeluaran bulan ini.</p>
                                <a href="{{ route('dashboard') }}" class="cta-btn">Catat transaksi</a>
                            </div>
                        @else
                            <div class="chart-wrapper" style="max-width:240px;margin:0 auto;">
                                <canvas id="donutChart" width="240" height="240"></canvas>
                                <div class="donut-center">
                                    <div class="total-label">Total</div>
                                    <div class="total-value" id="donutTotal">{{ $categoryDistribution['totalFormatted'] }}</div>
                                </div>
                            </div>
                            <div class="mt-4" id="pieLegend">
                                @foreach($categoryDistribution['categories'] as $i => $cat)
                                    <div class="legend-item" data-index="{{ $i }}" onclick="togglePieSegment({{ $i }})">
                                        <span class="legend-dot"
                                            style="background:{{ $chartColors[$i % count($chartColors)] }}"></span>
                                        <span class="legend-name">{{ $cat['name'] }}</span>
                                        <span class="legend-amount">{{ $cat['formatted'] }}</span>
                                        <span class="legend-pct">{{ $cat['percentage'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                
                <div class="col-lg-7">
                    <div class="chart-card">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:10px;">
                            <div class="section-title mb-0"><i class="fas fa-chart-bar"></i> Tren Bulanan</div>
                            <div class="filter-bar">
                                <button class="toggle-btn active" id="btnNominal"
                                    onclick="setBarMode('nominal')">Nominal</button>
                                <button class="toggle-btn" id="btnGrowth" onclick="setBarMode('growth')">% MoM</button>
                                @if($isPremium)
                                    <select id="rangeSelect" onchange="changeRange(this.value)">
                                        <option value="3" {{ $barRange == 3 ? 'selected' : '' }}>3 Bulan</option>
                                        <option value="6" {{ $barRange == 6 ? 'selected' : '' }}>6 Bulan</option>
                                        <option value="12" {{ $barRange == 12 ? 'selected' : '' }}>Tahun ini</option>
                                    </select>
                                @else
                                    <span class="text-muted" style="font-size:12px;">Maks. 3 bulan</span>
                                @endif
                            </div>
                        </div>

                        @if($monthlyChartData['isEmpty'])
                            <div class="empty-state">
                                <div class="empty-icon">📈</div>
                                <p>Belum ada data transaksi.<br>Yuk catat transaksi pertamamu!</p>
                                <a href="{{ route('dashboard') }}" class="cta-btn">Catat transaksi pertamamu</a>
                            </div>
                        @else
                            @if($monthlyChartData['lessThanTwoMonths'])
                                <div class="alert alert-info" style="border-radius:10px;font-size:13px;">
                                    <i class="fas fa-info-circle me-1"></i> Butuh minimal 2 bulan data untuk melihat tren.
                                </div>
                            @endif
                            <div style="position:relative;">
                                <canvas id="barChart" height="280"></canvas>
                            </div>
                        @endif
                    </div>

                    
                    @if(!$isPremium && $barRange >= 3)
                        <div class="chart-card mt-3 blur-overlay" style="min-height:120px;">
                            <div class="upgrade-badge">
                                <i class="fas fa-crown me-2"></i> Upgrade ke Premium<br>
                                <small style="font-weight:400;opacity:0.9;">Lihat data hingga 12 bulan</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ========== DATA FROM PHP ==========
            const catData = @json($categoryDistribution);
            const monthData = @json($monthlyChartData);
            const colors = @json($chartColors);
            const currentMonth = {{ $bulan }};
            const currentYear = {{ $tahun }};

            // ========== PIE/DONUT CHART (PieChartRenderer) ==========
            let pieChart = null;
            let hiddenSegments = new Set();

            if (!catData.isEmpty && document.getElementById('donutChart')) {
                const ctx = document.getElementById('donutChart').getContext('2d');
                pieChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: catData.categories.map(c => c.name),
                        datasets: [{
                            data: catData.categories.map(c => c.amount),
                            backgroundColor: catData.categories.map((_, i) => colors[i % colors.length]),
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverBorderWidth: 3,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '68%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1a1a2e',
                                titleFont: { size: 13, weight: '600' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                cornerRadius: 10,
                                callbacks: {
                                    label: function (ctx) {
                                        const cat = catData.categories[ctx.dataIndex];
                                        return [cat.formatted, cat.percentage];
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Toggle pie segment visibility
            window.togglePieSegment = function (index) {
                if (!pieChart) return;
                const item = document.querySelector(`.legend-item[data-index="${index}"]`);
                if (hiddenSegments.has(index)) {
                    hiddenSegments.delete(index);
                    item.classList.remove('hidden-segment');
                    pieChart.show(0, index);
                } else {
                    hiddenSegments.add(index);
                    item.classList.add('hidden-segment');
                    pieChart.hide(0, index);
                }
                // Recalculate percentages for visible segments
                let visibleTotal = 0;
                catData.categories.forEach((c, i) => {
                    if (!hiddenSegments.has(i)) visibleTotal += c.amount;
                });
                document.querySelectorAll('.legend-item').forEach((el, i) => {
                    if (!hiddenSegments.has(i)) {
                        const pct = visibleTotal > 0 ? ((catData.categories[i].amount / visibleTotal) * 100).toFixed(1) : 0;
                        el.querySelector('.legend-pct').textContent = String(pct).replace('.', ',') + '%';
                    }
                });
                // Update center total
                const totalEl = document.getElementById('donutTotal');
                if (totalEl) totalEl.textContent = formatRupiah(visibleTotal);
            };

            // ========== BAR CHART (BarChartRenderer) ==========
            let barChart = null;
            let barMode = 'nominal';

            if (!monthData.isEmpty && document.getElementById('barChart')) {
                renderBarChart();
            }

            function renderBarChart() {
                const ctx = document.getElementById('barChart').getContext('2d');
                if (barChart) barChart.destroy();

                const months = monthData.months;
                const avgExpense = monthData.averageExpense;

                if (barMode === 'nominal') {
                    barChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: months.map(m => m.label),
                            datasets: [
                                {
                                    label: 'Pemasukan',
                                    data: months.map(m => m.pemasukan),
                                    backgroundColor: 'rgba(67, 160, 71, 0.8)',
                                    borderColor: '#43a047',
                                    borderWidth: 1,
                                    borderRadius: 6,
                                    barPercentage: 0.7,
                                    categoryPercentage: 0.6
                                },
                                {
                                    label: 'Pengeluaran',
                                    data: months.map(m => m.pengeluaran),
                                    backgroundColor: 'rgba(229, 57, 53, 0.8)',
                                    borderColor: '#e53935',
                                    borderWidth: 1,
                                    borderRadius: 6,
                                    barPercentage: 0.7,
                                    categoryPercentage: 0.6
                                },
                                {
                                    label: 'Rata-rata Pengeluaran',
                                    data: months.map(() => avgExpense),
                                    type: 'line',
                                    borderColor: '#ff9800',
                                    borderWidth: 2,
                                    borderDash: [6, 4],
                                    pointRadius: 0,
                                    fill: false
                                }
                            ]
                        },
                        options: getBarOptions('nominal', months)
                    });
                } else {
                    // Growth mode
                    barChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: months.map(m => m.label),
                            datasets: [
                                {
                                    label: '% Pemasukan',
                                    data: months.map(m => m.growthIncome ?? 0),
                                    backgroundColor: 'rgba(67, 160, 71, 0.8)',
                                    borderRadius: 6, barPercentage: 0.7, categoryPercentage: 0.6
                                },
                                {
                                    label: '% Pengeluaran',
                                    data: months.map(m => m.growthExpense ?? 0),
                                    backgroundColor: 'rgba(229, 57, 53, 0.8)',
                                    borderRadius: 6, barPercentage: 0.7, categoryPercentage: 0.6
                                }
                            ]
                        },
                        options: getBarOptions('growth', months)
                    });
                }
            }

            function getBarOptions(mode, months) {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10, font: { size: 12 } }
                        },
                        tooltip: {
                            backgroundColor: '#1a1a2e', padding: 14, cornerRadius: 10,
                            titleFont: { size: 13, weight: '600' },
                            bodyFont: { size: 12 },
                            callbacks: {
                                title: (items) => {
                                    const m = months[items[0].dataIndex];
                                    return m.labelLengkap;
                                },
                                label: (ctx) => {
                                    if (mode === 'nominal') {
                                        return ctx.dataset.label + ': ' + formatRupiah(ctx.raw);
                                    }
                                    return ctx.dataset.label + ': ' + (ctx.raw >= 0 ? '+' : '') + ctx.raw.toFixed(1) + '%';
                                },
                                afterBody: (items) => {
                                    if (mode !== 'nominal') return '';
                                    const m = months[items[0].dataIndex];
                                    return m.selisihFormatted;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                font: { size: 11 },
                                callback: (v) => mode === 'nominal' ? formatRupiahRingkas(v) : v + '%'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: function (ctx2) {
                                    const m = months[ctx2.index];
                                    if (m && m.bulan === currentMonth && m.tahun === currentYear) {
                                        return { size: 12, weight: 'bold' };
                                    }
                                    return { size: 11 };
                                }
                            }
                        }
                    }
                };
            }

            window.setBarMode = function (mode) {
                barMode = mode;
                document.getElementById('btnNominal').classList.toggle('active', mode === 'nominal');
                document.getElementById('btnGrowth').classList.toggle('active', mode === 'growth');
                renderBarChart();
            };

            window.changeRange = function (range) {
                const url = new URL(window.location);
                url.searchParams.set('range', range);
                window.location = url;
            };

            // ========== UTILITY FUNCTIONS ==========
            function formatRupiah(n) {
                return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function formatRupiahRingkas(n) {
                if (n >= 1e9) return (n / 1e9).toFixed(1).replace('.0', '').replace('.', ',') + 'M';
                if (n >= 1e6) return (n / 1e6).toFixed(1).replace('.0', '').replace('.', ',') + 'jt';
                if (n >= 1e3) return (n / 1e3).toFixed(1).replace('.0', '').replace('.', ',') + 'rb';
                return n.toString();
            }
        });
    </script>
@endpush