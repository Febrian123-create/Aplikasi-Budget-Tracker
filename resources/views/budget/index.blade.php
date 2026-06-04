@extends('layouts.master')

@push('styles')
<style>
.bunrek-modal-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,0.35);
    backdrop-filter: blur(8px); z-index: 2000;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: all 0.35s cubic-bezier(0.16,1,0.3,1);
}
.bunrek-modal-overlay.active { opacity: 1; pointer-events: auto; }
.bunrek-modal-card {
    background: var(--bg-white); border-radius: var(--radius-lg);
    width: 92%; max-width: 520px; max-height: 90vh;
    display: flex; flex-direction: column;
    box-shadow: var(--shadow-xl); border: 1px solid var(--border-color);
    transform: scale(0.95) translateY(15px);
    transition: all 0.35s cubic-bezier(0.16,1,0.3,1); overflow: hidden;
}
.bunrek-modal-overlay.active .bunrek-modal-card { transform: scale(1) translateY(0); }
.bunrek-modal-header {
    padding: var(--space-lg) var(--space-xl);
    border-bottom: 1px solid var(--border-light);
    display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
}
.bunrek-modal-body {
    padding: var(--space-lg) var(--space-xl); overflow-y: auto; flex: 1;
    scrollbar-width: thin; scrollbar-color: var(--border-color) transparent;
}
.bunrek-modal-body::-webkit-scrollbar { width: 5px; }
.bunrek-modal-body::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 99px; }
.bunrek-modal-close {
    background: transparent; border: none; font-size: 1.4rem;
    color: var(--text-light); cursor: pointer; transition: var(--transition-fast);
}
.bunrek-modal-close:hover { color: var(--color-expense); transform: scale(1.1); }
</style>
@endpush

@section('content')
<div class="content-inner">

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-sm);">
        <div>
            <h1 style="font-family: var(--font-heading); font-weight: 800; color: var(--text-dark); margin: 0; font-size: var(--fs-2xl);">Budget Planning</h1>
            <p style="color: var(--text-muted); font-size: var(--fs-sm); margin: 0; margin-top: 2px;">Tetapkan batas pengeluaran per kategori</p>
        </div>
        <div style="display: flex; gap: var(--space-sm);">
            <a href="{{ route('budget.settings') }}" class="btn-bunrek btn-outline" style="font-size: var(--fs-sm);">
                <i class="bi bi-gear"></i> Pengaturan Alert
            </a>
            <button id="btnAddBudget" class="btn-bunrek btn-primary" style="font-size: var(--fs-sm);">
                <i class="bi bi-plus-lg"></i> Tambah Budget
            </button>
        </div>
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

    @if($budgets->isEmpty())
        <div class="bunrek-card">
            <div class="bunrek-card-body" style="text-align: center; padding: var(--space-2xl);">
                <i class="bi bi-wallet2" style="font-size: 2.5rem; color: var(--text-light); display: block; margin-bottom: var(--space-sm);"></i>
                <p style="color: var(--text-muted); font-size: var(--fs-base); margin: 0;">Belum ada budget yang ditetapkan.</p>
                <p style="color: var(--text-light); font-size: var(--fs-xs); margin: 4px 0 0;">Klik "+ Tambah Budget" untuk mulai mengatur batas pengeluaranmu.</p>
            </div>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--space-lg);">
            @foreach($budgets as $budget)
                @php
                    $pct = min(100, $budget->getUsagePercentage());
                    $spent = $budget->getCurrentSpending();
                    $barColor = $pct >= 100 ? 'var(--color-expense)' : ($pct >= 80 ? '#f59e0b' : 'var(--color-income)');
                @endphp
                <div class="bunrek-card" style="padding: 0; overflow: hidden;">
                    <div style="padding: var(--space-md) var(--space-lg);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-sm);">
                            <div>
                                <div style="font-weight: 700; font-size: var(--fs-base); color: var(--text-dark);">
                                    {{ $budget->category->category_name ?? 'Kategori #' . $budget->category_id }}
                                </div>
                                <div style="font-size: var(--fs-xs); color: var(--text-muted);">{{ $budget->periodLabel() }}</div>
                            </div>
                            <form action="{{ route('budget.destroy', $budget->budget_id) }}" method="POST"
                                  onsubmit="return confirm('Hapus budget ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: var(--text-light); cursor: pointer; font-size: 1rem;" title="Hapus">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
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

{{-- Modal Tambah Budget --}}
<div id="addBudgetModal" class="bunrek-modal-overlay">
    <div class="bunrek-modal-card">
        <div class="bunrek-modal-header">
            <h3 style="font-family: var(--font-heading); font-weight: 800; color: var(--text-dark); margin: 0; font-size: var(--fs-lg); display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-wallet2" style="color: var(--primary-color);"></i> Tambah Budget
            </h3>
            <button id="btnCloseBudgetModal" class="bunrek-modal-close"><i class="bi bi-x"></i></button>
        </div>
        <div class="bunrek-modal-body">
            <form action="{{ route('budget.store') }}" method="POST">
                @csrf

                <div class="bunrek-form-group">
                    <label class="bunrek-label">Kategori</label>
                    <select name="category_id" class="bunrek-select" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            @if(!in_array($cat->category_id, [10, 11]))
                                <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Batas Pengeluaran (Rp)</label>
                        <input type="number" name="amount" class="bunrek-input" required min="1000" placeholder="0">
                    </div>
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Periode</label>
                        <select name="period" id="budgetPeriod" class="bunrek-select" required>
                            <option value="bulanan">Bulanan</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="tahunan">Tahunan</option>
                        </select>
                    </div>
                </div>

                {{-- Mingguan: tanggal manual --}}
                <div id="budgetDateFields" style="display: none; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="budgetStartDate" class="bunrek-input" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Tanggal Berakhir</label>
                        <input type="date" name="end_date" id="budgetEndDate" class="bunrek-input">
                    </div>
                </div>

                {{-- Bulanan/Tahunan: durasi opsional --}}
                <div id="budgetDurationField" class="bunrek-form-group">
                    <label class="bunrek-label">Berlaku Selama <small style="color: var(--text-muted);">(Opsional)</small></label>
                    <div style="display: flex; align-items: center; gap: var(--space-sm);">
                        <input type="number" name="duration" id="budgetDuration" class="bunrek-input" min="1" max="60" placeholder="Kosongkan = selamanya" style="flex: 1;">
                        <span id="budgetDurationUnit" style="font-weight: 600; color: var(--text-muted); min-width: 48px;">bulan</span>
                    </div>
                    <small style="color: var(--text-light); font-size: var(--fs-xs);">Budget otomatis mengikuti periode berjalan dan reset tiap periode.</small>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-lg); border-top: 1px solid var(--border-light); padding-top: var(--space-md);">
                    <button type="button" id="btnCancelBudgetModal" class="btn-bunrek btn-outline">Batal</button>
                    <button type="submit" class="btn-bunrek btn-primary"><i class="bi bi-check-lg"></i> Simpan Budget</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#btnAddBudget').on('click', function() { $('#addBudgetModal').addClass('active'); });
    $('#btnCloseBudgetModal, #btnCancelBudgetModal').on('click', function() { $('#addBudgetModal').removeClass('active'); });
    $('#addBudgetModal').on('click', function(e) {
        if ($(e.target).is('#addBudgetModal')) $('#addBudgetModal').removeClass('active');
    });

    function toggleBudgetPeriodFields() {
        var period = $('#budgetPeriod').val();
        if (period === 'mingguan') {
            $('#budgetDateFields').css('display', 'grid');
            $('#budgetDurationField').hide();
            $('#budgetStartDate, #budgetEndDate').prop('disabled', false);
            $('#budgetDuration').prop('disabled', true);
        } else {
            $('#budgetDateFields').hide();
            $('#budgetDurationField').show();
            $('#budgetStartDate, #budgetEndDate').prop('disabled', true);
            $('#budgetDuration').prop('disabled', false);
            $('#budgetDurationUnit').text(period === 'tahunan' ? 'tahun' : 'bulan');
        }
    }
    $('#budgetPeriod').on('change', toggleBudgetPeriodFields);
    toggleBudgetPeriodFields();

    setTimeout(function() { $('.bunrek-alert').fadeOut(500); }, 5000);
});
</script>
@endpush
