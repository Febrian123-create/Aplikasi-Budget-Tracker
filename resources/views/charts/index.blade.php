@extends('layouts.master')

@section('page_title', 'Visualisasi')

@push('styles')
    <style>
        .chart-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
            padding: var(--space-lg);
            transition: var(--transition-fast);
            display: flex;
            flex-direction: column;
        }
        .chart-card:hover { box-shadow: var(--shadow-md); }
        .stats-row-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: var(--space-lg);
            margin-bottom: var(--space-xl);
        }
        .stat-card-icon.warning {
            background: var(--color-warning-bg);
            color: var(--color-warning);
        }
        .stat-progress-bar {
            height: 8px;
            border-radius: 4px;
            background: var(--border-light);
            overflow: hidden;
            margin-top: var(--space-sm);
        }
        .stat-progress-fill { height: 100%; border-radius: 4px; transition: width 1s ease; }
        .fill-green { background: var(--color-income); }
        .fill-yellow { background: var(--color-warning); }
        .fill-red { background: var(--color-expense); }
        .warning-banner {
            background: var(--color-warning-bg);
            border: 1px solid var(--color-warning);
            border-radius: var(--radius-md);
            padding: var(--space-md) var(--space-lg);
            display: flex;
            align-items: center;
            gap: var(--space-md);
            color: var(--color-warning);
            font-weight: 500;
            margin-bottom: var(--space-lg);
        }
        .empty-state { text-align: center; padding: var(--space-2xl) var(--space-lg); color: var(--text-light); }
        .empty-state .empty-icon { font-size: 3.5rem; margin-bottom: var(--space-md); opacity: .4; }
        .empty-state p { font-size: var(--fs-base); margin-bottom: var(--space-md); color: var(--text-muted); }
        .empty-state .cta-btn {
            display: inline-block;
            padding: 10px 28px;
            background: var(--primary-color);
            color: #fff;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
        }
        .section-title {
            font-size: var(--fs-md);
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: var(--space-lg);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }
        .section-title i { color: var(--primary-color); }
        .charts-filter-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: var(--space-md);
            margin-bottom: var(--space-lg);
        }
        .charts-period {
            margin: 0;
            color: var(--text-muted);
            font-size: var(--fs-base);
            font-weight: 500;
        }
        .filter-bar { display: flex; align-items: center; gap: var(--space-sm); flex-wrap: wrap; }
        .filter-bar .bunrek-select { min-width: 130px; height: 42px; }
        .filter-bar .toggle-btn {
            padding: 8px 16px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background: var(--bg-white);
            font-size: var(--fs-sm);
            color: var(--text-body);
            cursor: pointer;
            transition: var(--transition-fast);
        }
        .toggle-btn.active { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }
        #pieLegend {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: var(--space-md);
        }
        .legend-item {
            display: grid;
            grid-template-columns: 12px 1fr auto 56px;
            gap: var(--space-sm);
            align-items: center;
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background .2s;
            font-size: var(--fs-sm);
        }
        .legend-item:hover { background: var(--bg-light); }
        .legend-item.hidden-segment { opacity: .4; text-decoration: line-through; }
        .legend-dot { width: 12px; height: 12px; border-radius: 4px; flex-shrink: 0; }
        .legend-name { font-weight: 500; color: var(--text-body); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .legend-amount { font-weight: 600; color: var(--text-body); white-space: nowrap; }
        .legend-pct { font-size: var(--fs-xs); color: var(--text-muted); text-align: right; }
        .donut-chart-wrap {
            position: relative;
            width: 100%;
            min-height: 320px;
            aspect-ratio: 1 / 1;
            max-height: 440px;
            margin: 0 auto;
        }
        #donutChart { width: 100% !important; height: 100% !important; }
        .donut-center {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            text-align: center;
            padding: 28%;
        }
        .donut-center-label {
            font-size: var(--fs-xs);
            color: var(--text-muted);
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .donut-center-value {
            font-size: var(--fs-xl);
            font-weight: 800;
            color: var(--color-expense);
            line-height: 1.2;
            margin-top: 4px;
        }
        .area-chart-wrap {
            position: relative;
            height: 380px;
            width: 100%;
        }
        .grid-charts-1 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-lg);
            margin-bottom: var(--space-lg);
        }
        .grid-charts-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-lg);
            margin-bottom: var(--space-lg);
        }
        @media (max-width: 992px) {
            .stats-row-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-charts-1, .grid-charts-2 { grid-template-columns: 1fr; }
            .donut-chart-wrap { max-height: none; }
        }
        @media (max-width: 576px) {
            .stats-row-4 { grid-template-columns: 1fr; }
            .legend-item { grid-template-columns: 12px 1fr auto; }
            .legend-pct { display: none; }
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
        /* Health Score */
        .health-gauge-wrap { position:relative; width:200px; height:200px; margin:0 auto 8px; }
        .health-gauge-wrap canvas { width:200px !important; height:200px !important; }
        .health-gauge-center { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; pointer-events:none; }
        .health-score-num   { font-size:36px; font-weight:800; line-height:1; color:#1a1a2e; }
        .health-score-label { font-size:12px; font-weight:600; margin-top:4px; }
        .health-comp-row    { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
        .health-comp-name   { font-size:12px; color:#555; flex:1; }
        .health-comp-bar-wrap { width:90px; height:6px; background:#f0f0f0; border-radius:3px; overflow:hidden; }
        .health-comp-bar    { height:100%; border-radius:3px; transition:width .8s ease; }
        .health-comp-score  { font-size:11px; font-weight:700; color:#555; width:28px; text-align:right; }
        .tips-box { background:#f8f9ff; border:1px solid #e8eaff; border-radius:10px; padding:12px 14px; font-size:12px; color:#4f46e5; margin-top:14px; display:flex; gap:8px; align-items:flex-start; }
        .tips-box i { flex-shrink:0; margin-top:2px; }
    </style>
@endpush

@section('content')

    {{-- PERIOD & FILTER --}}
    <div class="charts-filter-bar">
      <p class="charts-period">
        {{ \App\Helpers\ChartHelper::formatBulanLengkap($bulan) }} {{ $tahun }}
        @if(!$isPremium)
          <span class="badge bg-secondary ms-2">Free</span>
        @else
          <span class="badge ms-2" style="background:var(--primary-color);">Premium</span>
        @endif
      </p>
      @if($canSelectMonth)
        <form method="GET" action="{{ route('charts.index') }}" class="filter-bar">
          <select name="bulan" class="bunrek-select" onchange="this.form.submit()">
            @foreach($availableMonths as $m)
              <option value="{{ $m['value'] }}" {{ $m['selected'] ? 'selected' : '' }}>{{ $m['label'] }}</option>
            @endforeach
          </select>
          <select name="tahun" class="bunrek-select" onchange="this.form.submit()">
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
        <i class="bi bi-exclamation-triangle-fill" style="font-size:20px;"></i>
        <span>Pengeluaran melebihi pemasukan sebesar <strong>{{ $metricCards['overBudgetAmount'] }}</strong></span>
      </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="stats-row-4">
      <div class="stat-card">
        <div class="stat-card-header">
          <span class="stat-card-label">Total Pemasukan</span>
          <div class="stat-card-icon income">
            <i class="bi bi-arrow-down-left-circle"></i>
          </div>
        </div>
        <div class="stat-card-value text-income">{{ $metricCards['totalIncomeFormatted'] }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-header">
          <span class="stat-card-label">Total Pengeluaran</span>
          <div class="stat-card-icon expense">
            <i class="bi bi-arrow-up-right-circle"></i>
          </div>
        </div>
        <div class="stat-card-value text-expense">{{ $metricCards['totalExpenseFormatted'] }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-header">
          <span class="stat-card-label">Saldo</span>
          <div class="stat-card-icon balance">
            <i class="bi bi-wallet2"></i>
          </div>
        </div>
        <div class="stat-card-value" style="color: {{ $metricCards['isSaldoPositif'] ? 'var(--primary-color)' : 'var(--color-expense)' }};">
          {{ $metricCards['isSaldoPositif'] ? '' : '-' }}{{ $metricCards['saldoFormatted'] }}
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-header">
          <span class="stat-card-label">Rasio Pengeluaran</span>
          <div class="stat-card-icon warning">
            <i class="bi bi-percent"></i>
          </div>
        </div>
        <div class="stat-card-value" style="color: var(--color-warning);">{{ number_format($metricCards['expensePercentage'], 1) }}%</div>
        <div class="stat-progress-bar">
          <div class="stat-progress-fill fill-{{ $metricCards['progressLevel'] }}" style="width:{{ min($metricCards['expensePercentage'],100) }}%"></div>
        </div>
      </div>
    </div>

    {{-- ROW 1: DONUT + AREA CHART --}}
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
          <div class="donut-chart-wrap">
            <canvas id="donutChart"></canvas>
            <div class="donut-center">
              <div class="donut-center-label">Total Pengeluaran</div>
              <div class="donut-center-value">{{ $categoryDistribution['totalFormatted'] }}</div>
            </div>
          </div>
          <div id="pieLegend">
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

      <div class="chart-card">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:10px;">
          <div class="section-title mb-0"><i class="bi bi-graph-up-arrow"></i> Tren Keuangan</div>
          <div class="filter-bar">
            <button class="toggle-btn active" id="btnArea" onclick="setAreaMode('area')">Tren</button>
            <button class="toggle-btn" id="btnMom" onclick="setAreaMode('mom')">% MoM</button>
            @if($isPremium)
              <select id="rangeSelect" class="bunrek-select" style="min-width:110px;height:38px;" onchange="changeRange(this.value)">
                <option value="3"  {{ $barRange==3  ? 'selected':'' }}>3 Bulan</option>
                <option value="6"  {{ $barRange==6  ? 'selected':'' }}>6 Bulan</option>
                <option value="12" {{ $barRange==12 ? 'selected':'' }}>Tahun ini</option>
              </select>
            @else
              <span style="font-size:var(--fs-xs);color:var(--text-muted);">Maks. 3 bulan</span>
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
            <div class="alert alert-info" style="border-radius:var(--radius-md);font-size:var(--fs-sm);">
              <i class="bi bi-info-circle me-1"></i> Butuh minimal 2 bulan data untuk melihat tren.
            </div>
          @endif
          <div class="area-chart-wrap"><canvas id="areaChart"></canvas></div>
        @endif
        @if(!$isPremium && $barRange >= 3)
          <div class="blur-overlay" style="position:relative;min-height:80px;margin-top:var(--space-md);border-radius:var(--radius-md);">
            <div class="upgrade-badge"><i class="bi bi-gem me-2"></i> Upgrade ke Premium<br><small style="font-weight:400;opacity:.9;">Lihat data hingga 12 bulan</small></div>
          </div>
        @endif
      </div>
    </div>

    {{-- ROW 2: HEATMAP --}}
    <div style="margin-bottom: var(--space-lg);">
      <div class="chart-card">
          <div class="section-title">
            <i class="bi bi-calendar3"></i> Heatmap Pengeluaran Harian
            <span style="font-size:var(--fs-sm);color:var(--text-muted);font-weight:400;">— {{ \App\Helpers\ChartHelper::formatBulanLengkap($bulan) }} {{ $tahun }}</span>
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

    {{-- ROW 3: COMPARISON + HEALTH SCORE --}}
    <div class="grid-charts-2">
      <div class="chart-card">
        <div class="section-title">
          <i class="bi bi-bar-chart-steps"></i> Perbandingan Kategori
          <span style="font-size:var(--fs-xs);color:var(--text-muted);font-weight:400;">vs {{ $monthComparison['prevLabel'] }}</span>
        </div>
        @if($monthComparison['isEmpty'])
          <div class="empty-state" style="padding:var(--space-xl) 0;"><div class="empty-icon">📉</div><p>Belum ada data untuk dibandingkan.</p></div>
        @else
          <div class="area-chart-wrap" style="height:260px;"><canvas id="comparisonChart"></canvas></div>
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

      <div class="chart-card">
        <div class="section-title"><i class="bi bi-heart-pulse-fill"></i> Financial Health Score</div>
        @if($healthScore['isEmpty'])
          <div class="empty-state" style="padding:var(--space-xl) 0;"><div class="empty-icon">🏥</div><p>{{ $healthScore['tips'] }}</p></div>
        @else
          <div class="health-gauge-wrap">
            <canvas id="healthGauge"></canvas>
            <div class="health-gauge-center">
              <div class="health-score-num" style="color:{{ $healthScore['color'] }};">{{ $healthScore['score'] }}</div>
              <div class="health-score-label" style="color:{{ $healthScore['color'] }};">{{ $healthScore['label'] }}</div>
            </div>
          </div>
          <div class="mt-3">
            @foreach($healthScore['components'] as $comp)
              <div class="health-comp-row">
                <div class="health-comp-name">{{ $comp['name'] }} <span style="color:var(--text-light);font-size:10px;">({{ $comp['weight'] }})</span></div>
                <div class="health-comp-bar-wrap">
                  <div class="health-comp-bar" style="width:{{ $comp['score'] }}%;background:{{ $comp['score']>=70?'#22C55E':($comp['score']>=40?'#F59E0B':'#EF4444') }};"></div>
                </div>
                <div class="health-comp-score">{{ $comp['score'] }}</div>
              </div>
            @endforeach
          </div>
          <div class="tips-box"><i class="bi bi-lightbulb-fill"></i><span>{{ $healthScore['tips'] }}</span></div>
        @endif
      </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const catData    = @json($categoryDistribution);
    const monthData  = @json($monthlyChartData);
    const compData   = @json($monthComparison);
    const healthData = @json($healthScore);
    const colors     = @json($chartColors);
    const currentMonth = {{ $bulan }};
    const currentYear  = {{ $tahun }};

    function formatRupiah(n) {
        return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    function formatRupiahAxis(n) {
        const abs = Math.abs(n);
        if (abs >= 1e9) {
            const val = (n / 1e9).toFixed(1).replace('.0', '').replace('.', ',');
            return 'Rp ' + val + 'M';
        }
        if (abs >= 1e6) {
            const val = (n / 1e6).toFixed(1).replace('.0', '').replace('.', ',');
            return 'Rp ' + val + 'jt';
        }
        if (abs >= 1e3) {
            const val = (n / 1e3).toFixed(1).replace('.0', '').replace('.', ',');
            return 'Rp ' + val + 'rb';
        }
        return formatRupiah(n);
    }

    // ===== DONUT CHART =====
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
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: '#1a1a2e',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            title: items => catData.categories[items[0].dataIndex].name,
                            label: ctx => {
                                const c = catData.categories[ctx.dataIndex];
                                return [c.formatted, c.percentage];
                            }
                        }
                    }
                }
            }
        });
    }

    window.togglePieSegment = function(index) {
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
        let vis = 0;
        catData.categories.forEach((c, i) => { if (!hiddenSegments.has(i)) vis += c.amount; });
        document.querySelectorAll('.legend-item').forEach((el, i) => {
            if (!hiddenSegments.has(i)) {
                const p = vis > 0 ? ((catData.categories[i].amount / vis) * 100).toFixed(1) : 0;
                el.querySelector('.legend-pct').textContent = String(p).replace('.', ',') + ' %';
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
            const chartHeight = 380;
            const ig = ctx.createLinearGradient(0, 0, 0, chartHeight);
            ig.addColorStop(0, 'rgba(34,197,94,.45)'); ig.addColorStop(1, 'rgba(34,197,94,.02)');
            const eg = ctx.createLinearGradient(0, 0, 0, chartHeight);
            eg.addColorStop(0, 'rgba(239,68,68,.40)'); eg.addColorStop(1, 'rgba(239,68,68,.02)');
            areaChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months.map(m => m.label),
                    datasets: [
                        { label: 'Pemasukan', data: months.map(m => m.pemasukan), borderColor: '#22C55E', borderWidth: 2.5, backgroundColor: ig, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#22C55E', pointHoverRadius: 7 },
                        { label: 'Pengeluaran', data: months.map(m => m.pengeluaran), borderColor: '#EF4444', borderWidth: 2.5, backgroundColor: eg, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#EF4444', pointHoverRadius: 7 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10, font: { size: 12 } } },
                        tooltip: {
                            backgroundColor: '#1a1a2e',
                            padding: 14,
                            cornerRadius: 10,
                            titleFont: { size: 13, weight: '600' },
                            bodyFont: { size: 12 },
                            callbacks: {
                                title: items => months[items[0].dataIndex].labelLengkap,
                                label: ctx => ctx.dataset.label + ': ' + formatRupiah(ctx.raw),
                                afterBody: items => months[items[0].dataIndex].selisihFormatted
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,.05)' },
                            ticks: { font: { size: 11 }, callback: v => formatRupiahAxis(v) }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: ctx2 => {
                                    const m = months[ctx2.index];
                                    return m && m.bulan === currentMonth && m.tahun === currentYear
                                        ? { size: 12, weight: 'bold' }
                                        : { size: 11 };
                                }
                            }
                        }
                    }
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
                scales: { x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 11 }, callback: v => formatRupiahAxis(v) } }, y: { grid: { display: false }, ticks: { font: { size: 11 } } } }
            }
        });
    }

    // ===== HEALTH GAUGE =====
    if (!healthData.isEmpty && document.getElementById('healthGauge')) {
        const ctx = document.getElementById('healthGauge').getContext('2d');
        new Chart(ctx, {
            type:'doughnut',
            data:{ datasets:[{ data:[healthData.score, 100-healthData.score], backgroundColor:[healthData.color,'#f0f0f0'], borderWidth:0 }] },
            options:{ responsive:false, cutout:'74%', rotation:-90, circumference:360, animation:{duration:1200,easing:'easeInOutQuart'}, plugins:{legend:{display:false},tooltip:{enabled:false}} }
        });
    }
});
</script>
@endpush
