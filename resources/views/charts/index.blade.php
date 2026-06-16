@extends('layouts.master')

@push('styles')
    <style>
        .charts-page-wrapper {
            width: 100%;
        }
        .chart-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,.03); border: 1px solid #f0f0f0;
            padding: 24px; transition: box-shadow .3s;
            display: flex; flex-direction: column;
        }
        .chart-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,.08); }
        .metric-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: var(--space-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
            transition: var(--transition-fast), transform .2s;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .metric-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
        }
        .metric-card .metric-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 14px;
        }
        .metric-card .metric-label {
            font-size: var(--fs-xs);
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 6px;
        }
        .metric-card .metric-value {
            font-family: var(--font-heading);
            font-size: var(--fs-xl);
            font-weight: 800;
            line-height: 1.2;
        }
        .metric-trend {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: var(--fs-xs);
            font-weight: 700;
            padding: 2px 8px;
            border-radius: var(--radius-full);
            margin-top: 10px;
            width: fit-content;
        }
        .metric-trend.trend-good { background: var(--color-income-bg); color: var(--color-income); }
        .metric-trend.trend-bad  { background: var(--color-expense-bg); color: var(--color-expense); }
        .metric-trend.trend-flat { background: var(--bg-light); color: var(--text-muted); }
        .metric-trend i { font-size: 0.85rem; }
        .mc-income .metric-value  { color: var(--color-income); }
        .mc-income .metric-icon { background: var(--color-income-bg); color: var(--color-income); }
        .mc-expense .metric-value { color: var(--color-expense); }
        .mc-expense .metric-icon { background: var(--color-expense-bg); color: var(--color-expense); }
        .mc-saldo-pos .metric-value { color: var(--primary-color); }
        .mc-saldo-pos .metric-icon { background: var(--primary-100); color: var(--primary-color); }
        .mc-saldo-neg .metric-value { color: var(--color-expense); }
        .mc-saldo-neg .metric-icon { background: var(--color-expense-bg); color: var(--color-expense); }
        .mc-ratio .metric-value { color: var(--color-warning); }
        .mc-ratio .metric-icon { background: var(--color-warning-bg); color: var(--color-warning); }
        .progress-bar-custom {
            height: 8px;
            border-radius: var(--radius-full);
            background: var(--border-light);
            overflow: hidden;
            margin-top: 10px;
        }
        .progress-bar-custom .fill {
            height: 100%;
            border-radius: var(--radius-full);
            transition: width 1s ease;
        }
        .fill-green { background: var(--color-income); }
        .fill-yellow { background: var(--color-warning); }
        .fill-red { background: var(--color-expense); }
        .warning-banner {
            background: var(--color-warning-bg);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #92400e;
            font-weight: 500;
            margin-bottom: 24px;
        }
        .empty-state { text-align:center; padding:48px 24px; color:#9e9e9e; }
        .empty-state .empty-icon { font-size:56px; margin-bottom:16px; opacity:.4; }
        .empty-state p { font-size:15px; margin-bottom:16px; }
        .empty-state .cta-btn {
            display:inline-block; padding:10px 28px; background:#6366f1;
            color:#fff; border-radius:10px; text-decoration:none; font-weight:600;
        }
        .section-title {
            font-size: var(--fs-md); font-weight: 700; color: var(--text-dark);
            margin-bottom:20px; display:flex; align-items:center; gap:10px;
        }
        .section-title i { color: var(--primary-color); }
        .filter-bar { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .filter-bar select, .filter-bar .toggle-btn {
            padding:8px 16px; border:1px solid #e0e0e0; border-radius:10px;
            background:#fff; font-size:13px; color:#333; cursor:pointer; transition:border-color .2s;
        }
        .toggle-btn.active { background:#6366f1; color:#fff; border-color:#6366f1; }
        .legend-item {
            display:flex; align-items:center; gap:10px; padding:8px 12px;
            border-radius:8px; cursor:pointer; transition:background .2s; font-size:13px;
        }
        .legend-item:hover { background:#f5f5f5; }
        .legend-item.hidden-segment { opacity:.4; text-decoration:line-through; }
        .legend-dot { width:12px; height:12px; border-radius:4px; flex-shrink:0; }
        .legend-name { flex:1; font-weight:500; color:#333; }
        .legend-amount { font-weight:600; color:#555; }
        .legend-pct { font-size:12px; color:#999; min-width:45px; text-align:right; }
        /* Donut */
        #monefyOuter { position:relative; width:100%; max-width:260px; aspect-ratio:1/1; margin:8px auto 4px; }
        #donutChart  { position:relative; z-index:1; }
        #monefyCenter {
            position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
            text-align:center; pointer-events:none; z-index:2; width:62%;
            display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px;
        }
        #monefyCenter .center-label { font-size:10px; color:var(--text-muted); font-weight:600; letter-spacing:.8px; }
        #monefyCenter .center-value { font-family:var(--font-heading); font-size:0.92rem; font-weight:800; color:var(--text-dark); line-height:1.15; white-space:nowrap; }
        
        /* CSS Grid Layouts */
        .grid-metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-bottom: 24px; }
        .grid-charts-1 { display: grid; grid-template-columns: 1fr 1.5fr; gap: 24px; margin-bottom: 24px; }
        .grid-charts-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        @media (max-width: 992px) {
            .grid-charts-1, .grid-charts-2 { grid-template-columns: 1fr; }
        }
        /* Area chart blur */
        .blur-overlay { position:relative; }
        .blur-overlay::after {
            content:''; position:absolute; inset:0;
            background:rgba(255,255,255,.7); backdrop-filter:blur(6px);
            border-radius:16px; z-index:2;
        }
        .upgrade-badge {
            position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
            z-index:3; background:linear-gradient(135deg,#6366f1,#8b5cf6);
            color:#fff; padding:14px 28px; border-radius:12px;
            font-weight:700; font-size:14px; box-shadow:0 4px 20px rgba(99,102,241,.4); text-align:center;
        }
        /* Heatmap */
        .cal-header { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:6px; }
        .cal-header span { text-align:center; font-size:11px; font-weight:700; color:#9e9e9e; padding:4px 0; }
        .cal-grid  { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
        .cal-day {
            border-radius:8px; padding:6px 4px; min-height:52px;
            display:flex; flex-direction:column; align-items:center; justify-content:flex-start;
            transition:transform .15s; cursor:default;
        }
        .cal-day:hover { transform:scale(1.06); z-index:2; }
        .cal-day .cal-num { font-size:12px; font-weight:700; color:#444; line-height:1.2; }
        .cal-day.cal-empty { background:transparent !important; }
        .cal-day .cal-amt { font-size:9px; font-weight:600; color:#fff; margin-top:3px; text-align:center; text-shadow:0 1px 2px rgba(0,0,0,.25); }
        .cal-day.has-spend .cal-num { color:#fff; text-shadow:0 1px 2px rgba(0,0,0,.2); }
        .cal-today { outline:2px solid #6366f1; outline-offset:1px; }
        .cal-legend { display:flex; align-items:center; gap:8px; margin-top:14px; font-size:11px; color:#9e9e9e; }
        .cal-legend-bar { display:flex; gap:2px; }
        .cal-legend-bar span { width:18px; height:10px; border-radius:3px; }
        /* Comparison */
        .change-badge { display:inline-flex; align-items:center; gap:3px; font-size:11px; font-weight:700; padding:2px 7px; border-radius:20px; }
        .change-up   { background:#ffebee; color:#e53935; }
        .change-down { background:#e8f5e9; color:#43a047; }
        .change-nil  { background:#f5f5f5; color:#9e9e9e; }
    </style>
@endpush

@section('content')
<div class="charts-page-wrapper">

    {{-- HEADER --}}
    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-md);">
      <div>
        <h1 style="font-family: var(--font-heading); font-weight: 800; color: var(--text-dark); margin: 0; font-size: var(--fs-2xl);">Visualisasi Keuangan</h1>
        <p style="color: var(--text-muted); font-size: var(--fs-sm); margin: 2px 0 0 0;">
          {{ \App\Helpers\ChartHelper::formatBulanLengkap($bulan) }} {{ $tahun }}
          @if(!$isPremium)
            <span style="font-size: 0.7rem; font-weight: 700; background: var(--border-color); color: var(--text-muted); padding: 2px 8px; border-radius: var(--radius-full); margin-left: var(--space-xs);">FREE</span>
          @else
            <span style="font-size: 0.7rem; font-weight: 700; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); color: white; padding: 2px 8px; border-radius: var(--radius-full); margin-left: var(--space-xs);">PREMIUM</span>
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

    {{-- WARNING --}}
    @if($metricCards['overBudgetAmount'])
      <div class="warning-banner">
        <i class="bi bi-exclamation-triangle-fill" style="font-size:20px; flex-shrink:0;"></i>
        <span>Pengeluaran melebihi pemasukan sebesar <strong>{{ $metricCards['overBudgetAmount'] }}</strong></span>
      </div>
    @endif

    {{-- METRIC CARDS --}}
    <div class="grid-metrics">
      <div class="metric-card mc-income">
        <div class="metric-icon"><i class="bi bi-arrow-down-circle"></i></div>
        <div class="metric-label">Total Pemasukan</div>
        <div class="metric-value">{{ $metricCards['totalIncomeFormatted'] }}</div>
        @include('charts.partials.metric-trend', ['trend' => $metricCards['incomeTrend']])
      </div>
      <div class="metric-card mc-expense">
        <div class="metric-icon"><i class="bi bi-arrow-up-circle"></i></div>
        <div class="metric-label">Total Pengeluaran</div>
        <div class="metric-value">{{ $metricCards['totalExpenseFormatted'] }}</div>
        @include('charts.partials.metric-trend', ['trend' => $metricCards['expenseTrend']])
      </div>
      <div class="metric-card {{ $metricCards['isSaldoPositif'] ? 'mc-saldo-pos' : 'mc-saldo-neg' }}">
        <div class="metric-icon"><i class="bi bi-wallet2"></i></div>
        <div class="metric-label">Saldo</div>
        <div class="metric-value">{{ $metricCards['isSaldoPositif'] ? '' : '-' }}{{ $metricCards['saldoFormatted'] }}</div>
        @include('charts.partials.metric-trend', ['trend' => $metricCards['saldoTrend']])
      </div>
      <div class="metric-card mc-ratio">
        <div class="metric-icon"><i class="bi bi-percent"></i></div>
        <div class="metric-label">Rasio Pengeluaran</div>
        <div class="metric-value">{{ number_format($metricCards['expensePercentage'], 1) }}%</div>
        <div class="progress-bar-custom">
          <div class="fill fill-{{ $metricCards['progressLevel'] }}" style="width:{{ min($metricCards['expensePercentage'],100) }}%"></div>
        </div>
        @include('charts.partials.metric-trend', ['trend' => $metricCards['ratioTrend']])
      </div>
    </div>

    {{-- ROW 1: MONEFY DONUT + AREA CHART --}}
    <div class="grid-charts-1">
      <div class="chart-card">
        <div class="section-title"><i class="bi bi-pie-chart-fill"></i> Distribusi Pengeluaran</div>
        @if($categoryDistribution['isEmpty'] && $categoryDistribution['allIncome'])
          <div class="empty-state"><div class="empty-icon">🎉</div><p>Tidak ada pengeluaran bulan ini!</p></div>
        @elseif($categoryDistribution['isEmpty'])
          <div class="empty-state">
            <div class="empty-icon">📊</div><p>Belum ada pengeluaran bulan ini.</p>
            <a href="{{ route('transactions.index') }}" class="cta-btn">Catat transaksi</a>
          </div>
        @else
          <div id="monefyOuter">
            <canvas id="donutChart"></canvas>
            <div id="monefyCenter">
              <div class="center-label">TOTAL</div>
              <div class="center-value">{{ $metricCards['totalExpenseFormatted'] }}</div>
            </div>
          </div>
          <div class="mt-3" id="pieLegend">
            @foreach($categoryDistribution['categories'] as $i => $cat)
              <div class="legend-item" data-index="{{ $i }}" onclick="togglePieSegment({{ $i }})">
                <span class="legend-dot" style="background:{{ $chartColors[$i % count($chartColors)] }}"></span>
                <span class="legend-name">{{ $cat['name'] }}</span>
                <span class="legend-amount">{{ $cat['formatted'] }}</span>
                <span class="legend-pct">{{ $cat['percentage'] }}</span>
              </div>
            @endforeach
          </div>
        @endif
      </div>
      <div class="d-flex flex-column gap-3">
        <div class="chart-card" style="position: relative; flex: 1;">
          <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:10px;">
            <div class="section-title mb-0"><i class="bi bi-graph-up-arrow"></i> Tren Keuangan</div>
            <div class="filter-bar">
              <button class="toggle-btn active" id="btnArea" onclick="setAreaMode('area')">Tren</button>
              <button class="toggle-btn" id="btnMom"  onclick="setAreaMode('mom')">% MoM</button>
              @if($isPremium)
                <select id="rangeSelect" onchange="changeRange(this.value)">
                  <option value="3"  {{ $barRange==3  ? 'selected':'' }}>3 Bulan</option>
                  <option value="6"  {{ $barRange==6  ? 'selected':'' }}>6 Bulan</option>
                  <option value="12" {{ $barRange==12 ? 'selected':'' }}>Tahun ini</option>
                </select>
              @else
                <span class="text-muted" style="font-size:12px;">Maks. 3 bulan</span>
              @endif
            </div>
          </div>
          @if($monthlyChartData['isEmpty'])
            <div class="empty-state">
              <div class="empty-icon">📈</div><p>Belum ada data transaksi.</p>
              <a href="{{ route('transactions.index') }}" class="cta-btn">Catat transaksi</a>
            </div>
          @else
            @if($monthlyChartData['lessThanTwoMonths'])
              <div class="alert alert-info" style="border-radius:10px;font-size:13px;">
                <i class="bi bi-info-circle me-1"></i> Butuh minimal 2 bulan data untuk melihat tren.
              </div>
            @endif
            <div style="position:relative;"><canvas id="areaChart" height="280"></canvas></div>
          @endif
        </div>
        @if(!$isPremium && $barRange >= 3)
          <div class="chart-card blur-overlay" style="min-height:80px;">
            <div class="upgrade-badge"><i class="bi bi-gem me-2"></i> Upgrade ke Premium<br><small style="font-weight:400;opacity:.9;">Lihat data hingga 12 bulan</small></div>
          </div>
        @endif
      </div>
    </div>

    {{-- ROW 2: HEATMAP --}}
    <div style="margin-bottom: 24px;">
      <div class="chart-card">
        <div class="section-title">
          <i class="bi bi-calendar3"></i> Heatmap Pengeluaran Harian
          <span style="font-size:13px;color:#9e9e9e;font-weight:400;">— {{ \App\Helpers\ChartHelper::formatBulanLengkap($bulan) }} {{ $tahun }}</span>
        </div>
        @php
          $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
          $firstDow    = \Carbon\Carbon::create($tahun, $bulan, 1)->dayOfWeek;
          $startOffset = ($firstDow + 6) % 7;
          $maxSpend    = !empty($dailySpending) ? max($dailySpending) : 1;
          $today       = \Carbon\Carbon::today();
        @endphp
        <div class="cal-header">
          @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $d)<span>{{ $d }}</span>@endforeach
        </div>
        <div class="cal-grid">
          @for($e=0; $e<$startOffset; $e++)<div class="cal-day cal-empty"></div>@endfor
          @for($day=1; $day<=$daysInMonth; $day++)
            @php
              $spend   = $dailySpending[$day] ?? 0;
              $opacity = $spend > 0 ? max(0.18, min(0.95, $spend/$maxSpend)) : 0;
              $bg      = $spend > 0 ? "rgba(239,68,68,{$opacity})" : '#f5f5f5';
              $isToday = ($today->year==$tahun && $today->month==$bulan && $today->day==$day);
            @endphp
            <div class="cal-day {{ $spend>0 ? 'has-spend':'' }} {{ $isToday ? 'cal-today':'' }}"
                 style="background:{{ $bg }};"
                 title="{{ $spend>0 ? number_format($spend,0,',','.') : 'Tidak ada pengeluaran' }}">
              <span class="cal-num">{{ $day }}</span>
              @if($spend > 0)
                <span class="cal-amt">{{ \App\Helpers\ChartHelper::formatRupiahRingkas($spend) }}</span>
              @endif
            </div>
          @endfor
        </div>
        <div class="cal-legend">
          <span>Sedikit</span>
          <div class="cal-legend-bar">
            @foreach([0.18,0.35,0.55,0.75,0.95] as $op)
              <span style="background:rgba(239,68,68,{{ $op }})"></span>
            @endforeach
          </div>
          <span>Banyak</span>
          @if(empty($dailySpending))
            <span style="margin-left:12px;color:#bbb;">— Belum ada data pengeluaran</span>
          @endif
        </div>
      </div>
    </div>

    {{-- ROW 3: COMPARISON --}}
    <div style="margin-bottom: 24px;">
      <div class="chart-card">
        <div class="section-title">
          <i class="bi bi-bar-chart-steps"></i> Perbandingan Kategori
          <span style="font-size:12px;color:#9e9e9e;font-weight:400;">vs {{ $monthComparison['prevLabel'] }}</span>
        </div>
        @if($monthComparison['isEmpty'])
          <div class="empty-state" style="padding:32px 0;"><div class="empty-icon">📉</div><p>Belum ada data untuk dibandingkan.</p></div>
        @else
          <div style="position:relative;"><canvas id="comparisonChart" height="260"></canvas></div>
          <div class="mt-3 d-flex flex-wrap gap-2">
            @foreach($monthComparison['comparison'] as $item)
              @if($item['change'] !== null)
                <span class="change-badge {{ $item['isIncrease'] ? 'change-up':'change-down' }}">
                  <i class="bi {{ $item['isIncrease'] ? 'bi-arrow-up-short':'bi-arrow-down-short' }}"></i>
                  {{ $item['name'] }}: {{ $item['isIncrease'] ? '+':'' }}{{ $item['change'] }}%
                </span>
              @else
                <span class="change-badge change-nil">{{ $item['name'] }}: baru</span>
              @endif
            @endforeach
          </div>
        @endif
      </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const catData    = @json($categoryDistribution);
    const monthData  = @json($monthlyChartData);
    const compData   = @json($monthComparison);
    const colors     = @json($chartColors);
    const currentMonth = {{ $bulan }};
    const currentYear  = {{ $tahun }};

    function formatRupiah(n) {
        return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    function formatRupiahRingkas(n) {
        if (n >= 1e9) return (n/1e9).toFixed(1).replace('.0','').replace('.',',')+'M';
        if (n >= 1e6) return (n/1e6).toFixed(1).replace('.0','').replace('.',',')+'jt';
        if (n >= 1e3) return (n/1e3).toFixed(1).replace('.0','').replace('.',',')+'rb';
        return n.toString();
    }
    // ===== DONUT =====
    let pieChart = null;
    let hiddenSegments = new Set();

    // Tempatkan teks tengah tepat di pusat donut (apa pun layout-nya).
    function centerDonutText(chart) {
        const el = document.getElementById('monefyCenter');
        const canvas = document.getElementById('donutChart');
        const outer = document.getElementById('monefyOuter');
        if (!el || !canvas || !outer) return;
        const meta = chart.getDatasetMeta(0);
        if (!meta || !meta.data.length) return;
        const arc = meta.data[0];
        const outerRect = outer.getBoundingClientRect();
        const canvasRect = canvas.getBoundingClientRect();
        el.style.left = ((canvasRect.left - outerRect.left) + arc.x) + 'px';
        el.style.top  = ((canvasRect.top  - outerRect.top)  + arc.y) + 'px';
    }

    if (!catData.isEmpty && document.getElementById('donutChart')) {
        const ctx = document.getElementById('donutChart').getContext('2d');
        pieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: catData.categories.map(c=>c.name),
                datasets: [{ data: catData.categories.map(c=>c.amount), backgroundColor: catData.categories.map((_,i)=>colors[i%colors.length]), borderWidth:3, borderColor:'#fff', hoverOffset:6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '68%',
                animation: { onComplete: e => centerDonutText(e.chart) },
                onResize: chart => centerDonutText(chart),
                plugins: {
                    legend: { display:false },
                    tooltip: {
                        backgroundColor:'#1a1a2e', padding:12, cornerRadius:10, usePointStyle:true,
                        callbacks: { label: ctx => { const c=catData.categories[ctx.dataIndex]; return ' ' + c.formatted + ' (' + c.percentage + ')'; } }
                    }
                }
            }
        });
        window.addEventListener('resize', () => { if (pieChart) centerDonutText(pieChart); });
    }

    window.togglePieSegment = function(index) {
        if (!pieChart) return;
        const item = document.querySelector(`.legend-item[data-index="${index}"]`);
        if (hiddenSegments.has(index)) { hiddenSegments.delete(index); item.classList.remove('hidden-segment'); pieChart.show(0,index); }
        else { hiddenSegments.add(index); item.classList.add('hidden-segment'); pieChart.hide(0,index); }
        let vis=0; catData.categories.forEach((c,i)=>{ if(!hiddenSegments.has(i)) vis+=c.amount; });
        document.querySelectorAll('.legend-item').forEach((el,i)=>{
            if(!hiddenSegments.has(i)){
                const p = vis>0?((catData.categories[i].amount/vis)*100).toFixed(1):0;
                el.querySelector('.legend-pct').textContent = String(p).replace('.',',')+' %';
            }
        });
    };

    // ===== AREA CHART =====
    let areaChart = null, areaMode = 'area';

    function renderAreaChart() {
        const canvasEl = document.getElementById('areaChart');
        if (!canvasEl || monthData.isEmpty) return;
        const ctx = canvasEl.getContext('2d');
        if (areaChart) areaChart.destroy();
        const months = monthData.months;

        if (areaMode === 'area') {
            const ig = ctx.createLinearGradient(0,0,0,260);
            ig.addColorStop(0,'rgba(34,197,94,.45)'); ig.addColorStop(1,'rgba(34,197,94,.02)');
            const eg = ctx.createLinearGradient(0,0,0,260);
            eg.addColorStop(0,'rgba(239,68,68,.40)'); eg.addColorStop(1,'rgba(239,68,68,.02)');
            areaChart = new Chart(ctx, {
                type:'line',
                data: { labels: months.map(m=>m.label), datasets: [
                    { label:'Pemasukan', data:months.map(m=>m.pemasukan), borderColor:'#22C55E', borderWidth:2.5, backgroundColor:ig, fill:true, tension:0.4, pointRadius:4, pointBackgroundColor:'#22C55E', pointHoverRadius:7 },
                    { label:'Pengeluaran', data:months.map(m=>m.pengeluaran), borderColor:'#EF4444', borderWidth:2.5, backgroundColor:eg, fill:true, tension:0.4, pointRadius:4, pointBackgroundColor:'#EF4444', pointHoverRadius:7 }
                ]},
                options: {
                    responsive:true, maintainAspectRatio:false,
                    interaction:{ mode:'index', intersect:false },
                    plugins: {
                        legend:{ position:'bottom', labels:{ padding:16, usePointStyle:true, pointStyleWidth:10, font:{size:12} } },
                        tooltip:{ backgroundColor:'#1a1a2e', padding:14, cornerRadius:10, titleFont:{size:13,weight:'600'}, bodyFont:{size:12},
                            callbacks:{ title:items=>months[items[0].dataIndex].labelLengkap, label:ctx=>ctx.dataset.label+': '+formatRupiah(ctx.raw), afterBody:items=>months[items[0].dataIndex].selisihFormatted } }
                    },
                    scales:{ y:{ beginAtZero:true, grid:{color:'rgba(0,0,0,.05)'}, ticks:{font:{size:11}, callback:v=>formatRupiahRingkas(v)} }, x:{ grid:{display:false}, ticks:{font:ctx2=>{ const m=months[ctx2.index]; return m&&m.bulan===currentMonth&&m.tahun===currentYear?{size:12,weight:'bold'}:{size:11}; }} } }
                }
            });
        } else {
            areaChart = new Chart(ctx, {
                type:'bar',
                data:{ labels:months.map(m=>m.label), datasets:[
                    { label:'% Pemasukan', data:months.map(m=>m.growthIncome??0), backgroundColor:'rgba(34,197,94,.8)', borderRadius:6, barPercentage:0.7, categoryPercentage:0.6 },
                    { label:'% Pengeluaran', data:months.map(m=>m.growthExpense??0), backgroundColor:'rgba(239,68,68,.8)', borderRadius:6, barPercentage:0.7, categoryPercentage:0.6 }
                ]},
                options:{ responsive:true, maintainAspectRatio:false, interaction:{mode:'index',intersect:false},
                    plugins:{ legend:{position:'bottom',labels:{padding:16,usePointStyle:true,pointStyleWidth:10,font:{size:12}}},
                        tooltip:{backgroundColor:'#1a1a2e',padding:14,cornerRadius:10,callbacks:{title:items=>months[items[0].dataIndex].labelLengkap,label:ctx=>ctx.dataset.label+': '+(ctx.raw>=0?'+':'')+ctx.raw.toFixed(1)+'%'}}},
                    scales:{ y:{grid:{color:'rgba(0,0,0,.05)'},ticks:{font:{size:11},callback:v=>v+'%'}}, x:{grid:{display:false},ticks:{font:{size:11}}} }
                }
            });
        }
    }
    window.setAreaMode = function(mode) {
        areaMode = mode;
        document.getElementById('btnArea').classList.toggle('active', mode==='area');
        document.getElementById('btnMom').classList.toggle('active', mode==='mom');
        renderAreaChart();
    };
    window.changeRange = function(range) { const url=new URL(window.location); url.searchParams.set('range',range); window.location=url; };
    renderAreaChart();

    // ===== COMPARISON CHART =====
    if (!compData.isEmpty && document.getElementById('comparisonChart')) {
        const ctx = document.getElementById('comparisonChart').getContext('2d');
        const cats = compData.comparison;
        new Chart(ctx, {
            type:'bar',
            data:{ labels:cats.map(c=>c.name.length>12?c.name.substring(0,12)+'…':c.name), datasets:[
                { label:compData.currentLabel, data:cats.map(c=>c.current), backgroundColor:'rgba(99,102,241,.85)', borderRadius:5, borderSkipped:false },
                { label:compData.prevLabel,    data:cats.map(c=>c.previous), backgroundColor:'rgba(99,102,241,.25)', borderRadius:5, borderSkipped:false }
            ]},
            options:{ indexAxis:'y', responsive:true, maintainAspectRatio:false, interaction:{mode:'index',intersect:false},
                plugins:{ legend:{position:'bottom',labels:{padding:16,usePointStyle:true,pointStyleWidth:10,font:{size:12}}},
                    tooltip:{backgroundColor:'#1a1a2e',padding:12,cornerRadius:10,callbacks:{label:ctx=>ctx.dataset.label+': '+formatRupiah(ctx.raw)}} },
                scales:{ x:{beginAtZero:true,grid:{color:'rgba(0,0,0,.05)'},ticks:{font:{size:11},callback:v=>formatRupiahRingkas(v)}}, y:{grid:{display:false},ticks:{font:{size:11}}} }
            }
        });
    }

});

</script>
@endpush
