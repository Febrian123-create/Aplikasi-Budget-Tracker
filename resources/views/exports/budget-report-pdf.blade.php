<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Budget - {{ $monthLabel }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }
.header { text-align: center; padding: 20px 0 15px; border-bottom: 3px solid #1b2838; margin-bottom: 15px; }
.header h1 { font-size: 20px; font-weight: 700; color: #1b2838; margin-bottom: 4px; letter-spacing: 1px; }
.header .sub { font-size: 10px; color: #6c757d; }
.meta-box { background: #f0f4f8; border: 1px solid #d1dce6; border-radius: 5px; padding: 8px 14px; margin-bottom: 16px; font-size: 10px; }
.meta-box strong { color: #1b2838; }
.cat-block { margin-bottom: 20px; page-break-inside: avoid; }
.cat-title { font-size: 13px; font-weight: 700; color: #1b2838; padding-bottom: 4px; border-bottom: 2px solid #1b2838; margin-bottom: 8px; }
.summary-grid { display: table; width: 100%; margin-bottom: 8px; }
.summary-cell { display: table-cell; width: 33%; padding: 6px 10px; vertical-align: middle; }
.summary-box { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 6px 10px; }
.s-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
.s-value { font-size: 12px; font-weight: 700; color: #1b2838; }
.badge-over { background: #f8d7da; color: #721c24; padding: 3px 8px; border-radius: 8px; font-size: 8px; font-weight: 700; }
.badge-under { background: #d4edda; color: #155724; padding: 3px 8px; border-radius: 8px; font-size: 8px; font-weight: 700; }
table.weekly { width: 100%; border-collapse: collapse; }
table.weekly thead tr { background: #1b2838; }
table.weekly thead th { color: #fff; padding: 6px 8px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; text-align: left; }
table.weekly tbody tr { border-bottom: 1px solid #e9ecef; }
table.weekly tbody tr:nth-child(even) { background: #f8f9fa; }
table.weekly tbody td { padding: 5px 8px; font-size: 9.5px; color: #343a40; }
.amt { text-align: right; font-weight: 600; }
.live-tag { font-size: 7px; font-weight: 700; color: #4338ca; background: #e0e7ff; padding: 1px 5px; border-radius: 6px; }
.footer { margin-top: 24px; text-align: center; font-size: 8px; color: #adb5bd; border-top: 1px solid #e9ecef; padding-top: 8px; }
.no-data { text-align: center; padding: 24px; color: #6c757d; font-style: italic; }
</style>
</head>
<body>
<div class="header">
    <h1>LAPORAN BUDGET BULANAN</h1>
    <div class="sub">Periode: {{ $monthLabel }} &nbsp;|&nbsp; Dicetak: {{ $generatedAt }} &nbsp;|&nbsp; BUNREK – Aplikasi Budget Tracker</div>
</div>

@if(isset($user))
<div class="meta-box">
    <strong>Akun:</strong> {{ $user->name }} ({{ $user->email }})
</div>
@endif

@if(empty($categories))
    <div class="no-data">Belum ada budget bulanan aktif untuk periode {{ $monthLabel }}.</div>
@else
    @foreach($categories as $cat)
    <div class="cat-block">
        <div class="cat-title">{{ $cat['category_name'] }}</div>

        <div class="summary-grid">
            <div class="summary-cell">
                <div class="summary-box">
                    <div class="s-label">Budget Bulanan</div>
                    <div class="s-value">{{ $cat['monthly_amount_formatted'] }}</div>
                </div>
            </div>
            <div class="summary-cell">
                <div class="summary-box">
                    <div class="s-label">Total Terpakai (akumulasi mingguan)</div>
                    <div class="s-value">{{ $cat['monthly_spent_formatted'] }} &nbsp;({{ $cat['monthly_pct'] }}%)</div>
                </div>
            </div>
            <div class="summary-cell" style="text-align:center; vertical-align:middle;">
                @if($cat['isOverBudget'])
                    <span class="badge-over">OVERBUDGET BULAN INI</span>
                @else
                    <span class="badge-under">UNDERBUDGET BULAN INI</span>
                @endif
            </div>
        </div>

        <table class="weekly">
            <thead>
                <tr>
                    <th style="width:34%">Minggu (Senin – Minggu)</th>
                    <th style="width:22%" class="amt">Alokasi Minggu</th>
                    <th style="width:22%" class="amt">Realisasi Pengeluaran</th>
                    <th style="width:22%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cat['weeks'] as $week)
                <tr>
                    <td>
                        {{ $week['label'] }}
                        @if($week['isLive']) <span class="live-tag">SEDANG BERJALAN</span> @endif
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
    @endforeach
@endif

<div class="footer">
    Di-generate otomatis oleh BUNREK &mdash; {{ $generatedAt }}<br>
    Alokasi mingguan dihitung proporsional dari budget bulanan berdasarkan jumlah hari tiap minggu dalam bulan tersebut.
</div>
</body>
</html>
