<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Budget - {{ $monthLabel }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 11px;
    color: #1e293b;
    background: #fff;
    padding: 28px 32px;
}

/* ── HEADER ── */
.page-header {
    border-bottom: 3px solid #1b2838;
    padding-bottom: 14px;
    margin-bottom: 18px;
    text-align: center;
}
.page-header h1 {
    font-size: 22px;
    font-weight: 700;
    color: #1b2838;
    letter-spacing: 1.5px;
    margin-bottom: 5px;
}
.page-header .sub {
    font-size: 10px;
    color: #64748b;
}

/* ── META BOX ── */
.meta-box {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 9px 14px;
    margin-bottom: 20px;
    font-size: 10px;
    color: #475569;
}
.meta-box strong { color: #1e293b; }

/* ── CATEGORY CARD ── */
.cat-card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 22px;
    page-break-inside: avoid;
    overflow: hidden;
}

/* Category header bar */
.cat-head {
    background: #1b2838;
    padding: 10px 16px;
}
.cat-head-name {
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* Summary row (3 boxes) */
.summary-table {
    width: 100%;
    border-collapse: collapse;
    border-bottom: 1px solid #e2e8f0;
}
.summary-table td {
    width: 33.33%;
    padding: 10px 14px;
    vertical-align: middle;
}
.summary-table td + td {
    border-left: 1px solid #e2e8f0;
}
.s-label {
    font-size: 8px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #94a3b8;
    margin-bottom: 3px;
}
.s-value {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
}
.s-sub {
    font-size: 9px;
    color: #64748b;
    margin-top: 1px;
}
.badge-over {
    display: inline-block;
    background: #fee2e2;
    color: #991b1b;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.3px;
}
.badge-under {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

/* Progress bar */
.progress-wrap {
    padding: 0 14px 10px;
    background: #fff;
}
.progress-bg {
    background: #e2e8f0;
    border-radius: 99px;
    height: 6px;
    width: 100%;
    margin-top: 2px;
}
.progress-fill-over  { background: #ef4444; height: 6px; border-radius: 99px; }
.progress-fill-under { background: #22c55e; height: 6px; border-radius: 99px; }

/* Weekly table */
.weekly-wrap { padding: 0 14px 14px; }
.weekly-label {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 8px 0 5px;
}
table.weekly {
    width: 100%;
    border-collapse: collapse;
}
table.weekly thead tr {
    background: #f1f5f9;
}
table.weekly thead th {
    color: #475569;
    padding: 7px 10px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    text-align: left;
    border-bottom: 2px solid #e2e8f0;
}
table.weekly tbody tr {
    border-bottom: 1px solid #f1f5f9;
}
table.weekly tbody tr:last-child { border-bottom: none; }
table.weekly tbody td {
    padding: 7px 10px;
    font-size: 10px;
    color: #334155;
}
.amt { text-align: right; font-weight: 600; }
.live-tag {
    font-size: 7.5px;
    font-weight: 700;
    color: #4338ca;
    background: #e0e7ff;
    padding: 1px 6px;
    border-radius: 20px;
    margin-left: 5px;
}

/* ── EMPTY / NO DATA ── */
.no-data {
    text-align: center;
    padding: 32px;
    color: #94a3b8;
    font-style: italic;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
}

/* ── FOOTER ── */
.page-footer {
    margin-top: 28px;
    text-align: center;
    font-size: 8.5px;
    color: #94a3b8;
    border-top: 1px solid #e2e8f0;
    padding-top: 10px;
    line-height: 1.6;
}
</style>
</head>
<body>

{{-- HEADER --}}
<div class="page-header">
    <h1>LAPORAN BUDGET BULANAN</h1>
    <div class="sub">Periode: {{ $monthLabel }} &nbsp;&bull;&nbsp; Dicetak: {{ $generatedAt }} &nbsp;&bull;&nbsp; BUNREK &ndash; Aplikasi Budget Tracker</div>
</div>

{{-- META --}}
@if(isset($user))
<div class="meta-box">
    <strong>Akun:</strong> {{ $user->name }} &nbsp;({{ $user->email }})
</div>
@endif

{{-- CATEGORIES --}}
@if(empty($categories))
    <div class="no-data">Belum ada budget bulanan aktif untuk periode {{ $monthLabel }}.</div>
@else
    @foreach($categories as $cat)
    @php
        $pct     = $cat['monthly_pct'];
        $barPct  = min($pct, 100);
        $isOver  = $cat['isOverBudget'];
    @endphp
    <div class="cat-card">

        {{-- Category header --}}
        <div class="cat-head">
            <div class="cat-head-name">{{ $cat['category_name'] }}</div>
        </div>

        {{-- Summary row --}}
        <table class="summary-table">
            <tr>
                <td>
                    <div class="s-label">Budget Bulanan</div>
                    <div class="s-value">{{ $cat['monthly_amount_formatted'] }}</div>
                </td>
                <td>
                    <div class="s-label">Total Terpakai</div>
                    <div class="s-value">{{ $cat['monthly_spent_formatted'] }}</div>
                    <div class="s-sub">{{ $pct }}% dari budget</div>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    @if($isOver)
                        <span class="badge-over">&#9650; OVERBUDGET BULAN INI</span>
                    @else
                        <span class="badge-under">&#10003; UNDERBUDGET BULAN INI</span>
                    @endif
                </td>
            </tr>
        </table>

        {{-- Progress bar --}}
        <div class="progress-wrap">
            <div class="progress-bg">
                <div class="{{ $isOver ? 'progress-fill-over' : 'progress-fill-under' }}"
                     style="width: {{ $barPct }}%;"></div>
            </div>
        </div>

        {{-- Weekly breakdown --}}
        <div class="weekly-wrap">
            <div class="weekly-label">Riwayat Mingguan</div>
            <table class="weekly">
                <thead>
                    <tr>
                        <th style="width:36%">Minggu (Senin &ndash; Minggu)</th>
                        <th style="width:20%" class="amt">Alokasi</th>
                        <th style="width:20%" class="amt">Realisasi</th>
                        <th style="width:24%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cat['weeks'] as $week)
                    <tr>
                        <td>
                            {{ $week['label'] }}
                            @if($week['isLive'])<span class="live-tag">SEDANG BERJALAN</span>@endif
                        </td>
                        <td class="amt">{{ $week['allocated_formatted'] }}</td>
                        <td class="amt">{{ $week['spent_formatted'] }}</td>
                        <td>
                            @if($week['isOverBudget'])
                                <span class="badge-over">OVERBUDGET</span>
                            @else
                                <span class="badge-under">UNDERBUDGET</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    @endforeach
@endif

{{-- FOOTER --}}
<div class="page-footer">
    Di-generate otomatis oleh BUNREK &mdash; {{ $generatedAt }}<br>
    Alokasi mingguan dihitung proporsional dari budget bulanan berdasarkan jumlah hari tiap minggu dalam bulan tersebut.
</div>

</body>
</html>
