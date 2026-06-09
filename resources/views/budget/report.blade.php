@extends('layouts.master')

@section('content')
<div class="content-inner">

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-sm);">
        <div>
            <h1 style="font-family: var(--font-heading); font-weight: 800; color: var(--text-dark); margin: 0; font-size: var(--fs-2xl);">Laporan Budget Mingguan</h1>
            <p style="color: var(--text-muted); font-size: var(--fs-sm); margin: 0; margin-top: 2px;">
                Status overbudget/underbudget tiap minggu (Senin–Minggu), diakumulasikan jadi laporan bulanan
            </p>
        </div>
        <a href="{{ route('budget.index') }}" class="btn-bunrek btn-outline" style="font-size: var(--fs-sm);">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="bunrek-alert bunrek-alert-success" style="margin-bottom: var(--space-lg);">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bunrek-alert bunrek-alert-error" style="margin-bottom: var(--space-lg);">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Filter & Action Bar --}}
    <div class="bunrek-card" style="margin-bottom: var(--space-lg);">
        <div class="bunrek-card-body" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--space-md);">
            <form method="GET" action="{{ route('budget.report') }}" style="display: flex; gap: var(--space-sm); align-items: center;">
                <span style="font-size: var(--fs-sm); color: var(--text-muted); font-weight: 600;">Periode:</span>
                <select name="bulan" class="bunrek-select" style="width: auto;" onchange="this.form.submit()">
                    @foreach($availableMonths as $m)
                        <option value="{{ $m['value'] }}" {{ $m['selected'] ? 'selected' : '' }}>{{ $m['label'] }}</option>
                    @endforeach
                </select>
                <select name="tahun" class="bunrek-select" style="width: auto;" onchange="this.form.submit()">
                    @foreach($availableYears as $y)
                        <option value="{{ $y['value'] }}" {{ $y['selected'] ? 'selected' : '' }}>{{ $y['label'] }}</option>
                    @endforeach
                </select>
            </form>

            <div style="display: flex; gap: var(--space-sm);">
                <a href="{{ route('budget.report.export.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                   class="btn-bunrek btn-outline" style="font-size: var(--fs-sm);">
                    <i class="bi bi-file-earmark-pdf"></i> Download PDF
                </a>
                <form action="{{ route('budget.report.send-email') }}" method="POST" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <button type="submit" class="btn-bunrek btn-primary" style="font-size: var(--fs-sm);">
                        <i class="bi bi-envelope"></i> Kirim ke Email
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if($report['isEmpty'])
        <div class="bunrek-card">
            <div class="bunrek-card-body" style="text-align: center; padding: var(--space-2xl);">
                <i class="bi bi-calendar-week" style="font-size: 2.5rem; color: var(--text-light); display: block; margin-bottom: var(--space-sm);"></i>
                <p style="color: var(--text-muted); font-size: var(--fs-base); margin: 0;">Belum ada budget bulanan aktif untuk {{ $report['monthLabel'] }}.</p>
                <p style="color: var(--text-light); font-size: var(--fs-xs); margin: 4px 0 0;">Tambahkan budget dengan periode "Bulanan" di halaman Budget Planning.</p>
            </div>
        </div>
    @else
        @foreach($report['categories'] as $cat)
            @php $barColor = $cat['isOverBudget'] ? 'var(--color-expense)' : 'var(--color-income)'; @endphp
            <div class="bunrek-card" style="margin-bottom: var(--space-lg);">
                <div class="bunrek-card-body">

                    {{-- Header kategori --}}
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-sm); margin-bottom: var(--space-md);">
                        <div>
                            <div style="font-weight: 800; font-size: var(--fs-lg); color: var(--text-dark);">{{ $cat['category_name'] }}</div>
                            <div style="font-size: var(--fs-xs); color: var(--text-muted);">Budget bulanan: {{ $cat['monthly_amount_formatted'] }}</div>
                        </div>
                        <span style="font-size: var(--fs-xs); font-weight: 700; padding: 4px 14px; border-radius: 999px;
                            {{ $cat['isOverBudget'] ? 'background:#fee2e2;color:#b91c1c;' : 'background:#dcfce7;color:#15803d;' }}">
                            <i class="bi {{ $cat['isOverBudget'] ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' }}"></i>
                            {{ $cat['isOverBudget'] ? 'Overbudget Bulan Ini' : 'Underbudget Bulan Ini' }}
                        </span>
                    </div>

                    {{-- Ringkasan bulanan --}}
                    <div style="font-size: var(--fs-xl); font-weight: 800; color: {{ $barColor }}; margin-bottom: 2px;">
                        {{ $cat['monthly_spent_formatted'] }}
                        <span style="font-size: var(--fs-xs); font-weight: 500; color: var(--text-muted);">
                            dari {{ $cat['monthly_amount_formatted'] }} &bull; {{ $cat['monthly_pct'] }}%
                        </span>
                    </div>
                    <div style="background: var(--border-light); border-radius: 999px; height: 8px; margin-bottom: var(--space-md);">
                        <div style="width: {{ $cat['monthly_pct_bar'] }}%; background: {{ $barColor }}; height: 8px; border-radius: 999px; transition: width 0.4s;"></div>
                    </div>

                    {{-- Riwayat mingguan --}}
                    <div style="font-size: var(--fs-sm); font-weight: 700; color: var(--text-dark); margin-bottom: var(--space-sm);">
                        <i class="bi bi-clock-history"></i> Riwayat Mingguan &mdash; {{ $report['monthLabel'] }}
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($cat['weeks'] as $week)
                            @php $wColor = $week['isOverBudget'] ? 'var(--color-expense)' : 'var(--color-income)'; @endphp
                            <div style="border: 1px solid {{ $week['isLive'] ? '#c7d2fe' : 'var(--border-light)' }};
                                        border-radius: var(--radius-md); padding: 10px 14px;
                                        {{ $week['isLive'] ? 'background:#f0f4ff;' : '' }}">
                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px;">
                                    <div style="font-size: var(--fs-sm); font-weight: 600; color: var(--text-dark);">
                                        {{ $week['label'] }}
                                        @if($week['isLive'])
                                            <span style="font-size: 10px; font-weight: 700; color: #4338ca;
                                                         background: #e0e7ff; padding: 2px 8px; border-radius: 999px; margin-left: 6px;">
                                                <i class="bi bi-broadcast"></i> Sedang Berjalan
                                            </span>
                                        @endif
                                    </div>
                                    <span style="font-size: 10px; font-weight: 700; padding: 2px 10px; border-radius: 999px;
                                        {{ $week['isOverBudget'] ? 'background:#fee2e2;color:#b91c1c;' : 'background:#dcfce7;color:#15803d;' }}">
                                        {{ $week['isOverBudget'] ? 'Overbudget' : 'Underbudget' }}
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: var(--fs-xs); color: var(--text-muted); margin-top: 5px;">
                                    <span>Realisasi: <strong style="color: {{ $wColor }};">{{ $week['spent_formatted'] }}</strong></span>
                                    <span>Alokasi: <strong>{{ $week['allocated_formatted'] }}</strong></span>
                                </div>
                                <div style="background: var(--border-light); border-radius: 999px; height: 5px; margin-top: 6px;">
                                    <div style="width: {{ $week['week_pct'] }}%; background: {{ $wColor }}; height: 5px; border-radius: 999px;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        @endforeach
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    setTimeout(function() { $('.bunrek-alert').fadeOut(500); }, 5000);
});
</script>
@endpush
@endsection
