@extends('layouts.master')

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
                                <div style="font-size: var(--fs-xs); color: var(--text-muted); text-transform: capitalize;">{{ $budget->period }}</div>
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
                            <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
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
                        <select name="period" class="bunrek-select" required>
                            <option value="bulanan">Bulanan</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="tahunan">Tahunan</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="bunrek-input" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Tanggal Berakhir <small style="color: var(--text-muted);">(Opsional)</small></label>
                        <input type="date" name="end_date" class="bunrek-input">
                    </div>
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
    setTimeout(function() { $('.bunrek-alert').fadeOut(500); }, 5000);
});
</script>
@endpush
