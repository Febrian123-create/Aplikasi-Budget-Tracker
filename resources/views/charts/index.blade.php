@extends('layouts.master')

@push('styles')
    <style>
        .chart-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06); border: 1px solid #f0f0f0;
            padding: 28px; transition: box-shadow .3s;
        }
        .chart-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.10); }
        .metric-card {
            border-radius: 14px; padding: 22px 24px;
            position: relative; overflow: hidden; transition: transform .2s;
        }
        .metric-card:hover { transform: translateY(-3px); }
        .metric-card .metric-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 14px;
        }
        .metric-card .metric-label { font-size: 13px; font-weight: 500; opacity: .8; margin-bottom: 6px; }
        .metric-card .metric-value { font-size: 22px; font-weight: 700; }
        .mc-income  { background: linear-gradient(135deg,#e8f5e9,#c8e6c9); color:#2e7d32; }
        .mc-income  .metric-icon { background: rgba(46,125,50,.15); color:#2e7d32; }
        .mc-expense { background: linear-gradient(135deg,#ffebee,#ffcdd2); color:#c62828; }
        .mc-expense .metric-icon { background: rgba(198,40,40,.15); color:#c62828; }
        .mc-saldo-pos { background: linear-gradient(135deg,#e3f2fd,#bbdefb); color:#1565c0; }
        .mc-saldo-pos .metric-icon { background: rgba(21,101,192,.15); color:#1565c0; }
        .mc-saldo-neg { background: linear-gradient(135deg,#fce4ec,#f8bbd0); color:#ad1457; }
        .mc-saldo-neg .metric-icon { background: rgba(173,20,87,.15); color:#ad1457; }
        .mc-ratio { background: linear-gradient(135deg,#fff3e0,#ffe0b2); color:#e65100; }
        .mc-ratio .metric-icon { background: rgba(230,81,0,.15); color:#e65100; }
        .progress-bar-custom { height:8px; border-radius:4px; background:rgba(0,0,0,.1); overflow:hidden; margin-top:10px; }
        .progress-bar-custom .fill { height:100%; border-radius:4px; transition:width 1s ease; }
        .fill-green { background:#43a047; } .fill-yellow { background:#fb8c00; } .fill-red { background:#e53935; }
        .warning-banner {
            background: linear-gradient(135deg,#fff3e0,#ffe0b2);
            border:1px solid #ffb74d; border-radius:12px; padding:16px 20px;
            display:flex; align-items:center; gap:12px; color:#e65100; font-weight:500; margin-bottom:24px;
        }
        .empty-state { text-align:center; padding:48px 24px; color:#9e9e9e; }
        .empty-state .empty-icon { font-size:56px; margin-bottom:16px; opacity:.4; }
        .empty-state p { font-size:15px; margin-bottom:16px; }
        .empty-state .cta-btn {
            display:inline-block; padding:10px 28px; background:#6366f1;
            color:#fff; border-radius:10px; text-decoration:none; font-weight:600;
        }
        .section-title {
            font-size:18px; font-weight:700; color:#1a1a2e;
            margin-bottom:20px; display:flex; align-items:center; gap:10px;
        }
        .section-title i { color:#6366f1; }
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
        /* Monefy */
        #monefyOuter { position:relative; width:380px; height:380px; margin:0 auto; }
        #donutChart  { position:absolute; top:70px; left:70px; width:240px; height:240px; }
        #iconSvg     { position:absolute; top:0; left:0; width:380px; height:380px; pointer-events:none; }
        #monefyCenter { position:absolute; text-align:center; pointer-events:none; width:120px; }
        .monefy-icon  { position:absolute; pointer-events:none; }
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
<div class="container">
  <div class="page-inner">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap:12px;">
      <div>
        <h3 class="fw-bold mb-1" style="color:#1a1a2e;">Visualisasi Keuangan</h3>
        <p class="text-muted mb-0">
          {{ \App\Helpers\ChartHelper::formatBulanLengkap($bulan) }} {{ $tahun }}
          @if(!$isPremium)
            <span class="badge bg-secondary ms-2">Free</span>
          @else
            <span class="badge ms-2" style="background:#6366f1;">Premium</span>
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
        <i class="bi bi-exclamation-triangle-fill" style="font-size:20px;"></i>
        <span>Pengeluaran melebihi pemasukan sebesar <strong>{{ $metricCards['overBudgetAmount'] }}</strong></span>
      </div>
    @endif

    {{-- METRIC CARDS --}}
    <div class="row g-3 mb-4">
      <div class="col-lg-3 col-md-6">
        <div class="metric-card mc-income">
          <div class="metric-icon"><i class="bi bi-arrow-down-circle"></i></div>
          <div class="metric-label">Total Pemasukan</div>
          <div class="metric-value">{{ $metricCards['totalIncomeFormatted'] }}</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="metric-card mc-expense">
          <div class="metric-icon"><i class="bi bi-arrow-up-circle"></i></div>
          <div class="metric-label">Total Pengeluaran</div>
          <div class="metric-value">{{ $metricCards['totalExpenseFormatted'] }}</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="metric-card {{ $metricCards['isSaldoPositif'] ? 'mc-saldo-pos' : 'mc-saldo-neg' }}">
          <div class="metric-icon"><i class="bi bi-wallet2"></i></div>
          <div class="metric-label">Saldo</div>
          <div class="metric-value">{{ $metricCards['isSaldoPositif'] ? '' : '-' }}{{ $metricCards['saldoFormatted'] }}</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="metric-card mc-ratio">
          <div class="metric-icon"><i class="bi bi-percent"></i></div>
          <div class="metric-label">Rasio Pengeluaran</div>
          <div class="metric-value">{{ number_format($metricCards['expensePercentage'], 1) }}%</div>
          <div class="progress-bar-custom">
            <div class="fill fill-{{ $metricCards['progressLevel'] }}" style="width:{{ min($metricCards['expensePercentage'],100) }}%"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- ROW 1: MONEFY DONUT + AREA CHART --}}
    <div class="row g-4 mb-4">
      <div class="col-lg-5">
        <div class="chart-card h-100">
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
              <canvas id="donutChart" width="240" height="240"></canvas>
              <svg id="iconSvg"></svg>
              <div id="monefyCenter">
                <div style="font-size:10px;color:#aaa;font-weight:500;letter-spacing:.5px;">PEMASUKAN</div>
                <div style="font-size:13px;font-weight:700;color:#22C55E;line-height:1.2;">{{ $metricCards['totalIncomeFormatted'] }}</div>
                <div style="border-top:1px solid #eee;margin:5px auto;width:70px;"></div>
                <div style="font-size:10px;color:#aaa;font-weight:500;letter-spacing:.5px;">PENGELUARAN</div>
                <div style="font-size:13px;font-weight:700;color:#EF4444;line-height:1.2;">{{ $metricCards['totalExpenseFormatted'] }}</div>
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
      </div>

      <div class="col-lg-7">
        <div class="chart-card">
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
          <div class="chart-card mt-3 blur-overlay" style="min-height:80px;">
            <div class="upgrade-badge"><i class="bi bi-gem me-2"></i> Upgrade ke Premium<br><small style="font-weight:400;opacity:.9;">Lihat data hingga 12 bulan</small></div>
          </div>
        @endif
      </div>
    </div>

    {{-- ROW 2: HEATMAP --}}
    <div class="row mb-4">
      <div class="col-12">
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
    </div>

    {{-- ROW 3: COMPARISON + HEALTH SCORE --}}
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="chart-card h-100">
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

      <div class="col-lg-6">
        <div class="chart-card h-100">
          <div class="section-title"><i class="bi bi-heart-pulse-fill"></i> Financial Health Score</div>
          @if($healthScore['isEmpty'])
            <div class="empty-state" style="padding:32px 0;"><div class="empty-icon">🏥</div><p>{{ $healthScore['tips'] }}</p></div>
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
                  <div class="health-comp-name">{{ $comp['name'] }} <span style="color:#bbb;font-size:10px;">({{ $comp['weight'] }})</span></div>
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
    const healthData = @json($healthScore);
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
    function getCategoryIcon(name) {
        const map = {
            'food':'bi bi-egg-fried','transport':'bi bi-car-front-fill',
            'bills':'bi bi-receipt-cutoff','clothes':'bi bi-bag-fill',
            'health':'bi bi-heart-pulse-fill','grocery':'bi bi-cart3',
            'house':'bi bi-house-fill','communications':'bi bi-telephone-fill',
            'other':'bi bi-three-dots','lainnya':'bi bi-collection-fill',
        };
        return map[name.toLowerCase()] || 'bi bi-tag-fill';
    }

    // ===== MONEFY DONUT =====
    let pieChart = null;
    let hiddenSegments = new Set();

    function positionIcons(chart) {
        const outer = document.getElementById('monefyOuter');
        const svg   = document.getElementById('iconSvg');
        const centerEl = document.getElementById('monefyCenter');
        if (!outer || !svg) return;
        svg.innerHTML = '';
        outer.querySelectorAll('.monefy-icon').forEach(e => e.remove());
        const meta = chart.getDatasetMeta(0);
        if (!meta.data.length) return;
        const OFFSET = 70;
        const ccx = meta.data[0].x, ccy = meta.data[0].y;
        const outerR = meta.data[0].outerRadius;
        const wcx = OFFSET + ccx, wcy = OFFSET + ccy;
        const iconR = outerR + 50, pctR = outerR + 80, iconSz = 36;

        catData.categories.forEach((cat, i) => {
            const arc = meta.data[i]; if (!arc) return;
            const mid = (arc.startAngle + arc.endAngle) / 2;
            const col = colors[i % colors.length];
            const ix = wcx + Math.cos(mid)*iconR, iy = wcy + Math.sin(mid)*iconR;
            const iconDiv = document.createElement('div');
            iconDiv.className = 'monefy-icon';
            Object.assign(iconDiv.style, {
                left:(ix-iconSz/2)+'px', top:(iy-iconSz/2)+'px',
                width:iconSz+'px', height:iconSz+'px', borderRadius:'50%',
                background:col+'22', border:'2px solid '+col,
                display:'flex', alignItems:'center', justifyContent:'center',
            });
            iconDiv.innerHTML = `<i class="${getCategoryIcon(cat.name)}" style="color:${col};font-size:15px;"></i>`;
            outer.appendChild(iconDiv);
            const px = wcx+Math.cos(mid)*pctR, py = wcy+Math.sin(mid)*pctR;
            const pctDiv = document.createElement('div');
            pctDiv.className = 'monefy-icon';
            Object.assign(pctDiv.style, { left:(px-18)+'px', top:(py-9)+'px', fontSize:'11px', fontWeight:'700', color:col, whiteSpace:'nowrap' });
            pctDiv.textContent = cat.percentNum+'%';
            outer.appendChild(pctDiv);
            const x1 = wcx+Math.cos(mid)*(outerR+3), y1 = wcy+Math.sin(mid)*(outerR+3);
            const line = document.createElementNS('http://www.w3.org/2000/svg','line');
            line.setAttribute('x1',x1); line.setAttribute('y1',y1);
            line.setAttribute('x2',ix); line.setAttribute('y2',iy);
            line.setAttribute('stroke',col); line.setAttribute('stroke-width','1.5'); line.setAttribute('stroke-opacity','0.5');
            svg.appendChild(line);
        });
        if (centerEl) { centerEl.style.left=(wcx-60)+'px'; centerEl.style.top=(wcy-42)+'px'; }
    }

    if (!catData.isEmpty && document.getElementById('donutChart')) {
        const ctx = document.getElementById('donutChart').getContext('2d');
        pieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: catData.categories.map(c=>c.name),
                datasets: [{ data: catData.categories.map(c=>c.amount), backgroundColor: catData.categories.map((_,i)=>colors[i%colors.length]), borderWidth:2, borderColor:'#fff', hoverOffset:8 }]
            },
            options: {
                responsive: false, cutout: '62%',
                animation: { onComplete: function(e){ positionIcons(e.chart); } },
                plugins: {
                    legend: { display:false },
                    tooltip: {
                        backgroundColor:'#1a1a2e', padding:12, cornerRadius:10,
                        callbacks: { label: ctx => { const c=catData.categories[ctx.dataIndex]; return [c.formatted, c.percentage]; } }
                    }
                }
            }
        });
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
        setTimeout(()=>{ if(pieChart) positionIcons(pieChart); }, 450);
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
