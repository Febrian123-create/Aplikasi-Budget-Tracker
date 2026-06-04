@extends('layouts.master')

@push('styles')
<style>
/* Custom Modal Overlay (Glassmorphism & Slide-up animation) */
.bunrek-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.35);
    backdrop-filter: blur(8px);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.bunrek-modal-overlay.active {
    opacity: 1;
    pointer-events: auto;
}

.bunrek-modal-card {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    width: 92%;
    max-width: 560px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: var(--shadow-xl);
    border: 1px solid var(--border-color);
    transform: scale(0.95) translateY(15px);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
}

.bunrek-modal-overlay.active .bunrek-modal-card {
    transform: scale(1) translateY(0);
}

.bunrek-modal-header {
    padding: var(--space-lg) var(--space-xl);
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

.bunrek-modal-body {
    padding: var(--space-lg) var(--space-xl);
    overflow-y: auto;
    flex: 1;
    /* Scrollbar styling */
    scrollbar-width: thin;
    scrollbar-color: var(--border-color) transparent;
}

.bunrek-modal-body::-webkit-scrollbar {
    width: 5px;
}
.bunrek-modal-body::-webkit-scrollbar-track {
    background: transparent;
}
.bunrek-modal-body::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 99px;
}

.bunrek-modal-close {
    background: transparent;
    border: none;
    font-size: 1.4rem;
    color: var(--text-light);
    cursor: pointer;
    transition: var(--transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
}

.bunrek-modal-close:hover {
    color: var(--color-expense);
    transform: scale(1.1);
}

/* Card item list */
.recurring-card-item {
    background: var(--bg-white);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    padding: var(--space-md) var(--space-lg);
    box-shadow: var(--shadow-sm);
    transition: var(--transition-smooth);
    margin-bottom: var(--space-md);
    position: relative;
    overflow: hidden;
}

.recurring-card-item:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
    border-color: var(--border-color);
}

.recurring-card-item.due-today {
    border-left: 4px solid var(--color-warning);
}

.recurring-card-item.due-tomorrow {
    border-left: 4px solid var(--color-info);
}

.icon-wrapper-circle {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.icon-income {
    background-color: var(--color-income-bg);
    color: var(--color-income);
}

.icon-expense {
    background-color: var(--color-expense-bg);
    color: var(--color-expense);
}

.badge-freq {
    background-color: var(--primary-50);
    color: var(--primary-color);
    padding: 3px 8px;
    border-radius: var(--radius-sm);
    font-size: var(--fs-xs);
    font-weight: 600;
}

.badge-cat {
    background-color: var(--border-light);
    color: var(--text-muted);
    padding: 3px 8px;
    border-radius: var(--radius-sm);
    font-size: var(--fs-xs);
    font-weight: 500;
}

.status-indicator {
    padding: 4px 10px;
    border-radius: var(--radius-full);
    font-size: var(--fs-xs);
    font-weight: 700;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background-color: var(--color-income-bg);
    color: var(--color-income);
}

.status-paused {
    background-color: var(--color-warning-bg);
    color: var(--color-warning);
}

.status-ended {
    background-color: var(--border-light);
    color: var(--text-light);
}

.action-icon-btn {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-sm);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    border: 1.5px solid var(--border-color);
    background: transparent;
    color: var(--text-muted);
    transition: var(--transition-fast);
    cursor: pointer;
}

.action-icon-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    background: var(--primary-50);
}

.action-icon-btn.btn-delete:hover {
    border-color: var(--color-expense);
    color: var(--color-expense);
    background: var(--color-expense-bg);
}

.action-icon-btn.btn-pause:hover {
    border-color: var(--color-warning);
    color: var(--color-warning);
    background: var(--color-warning-bg);
}

.metric-mini-card {
    background: var(--bg-white);
    border-radius: var(--radius-md);
    padding: var(--space-md);
    border: 1px solid var(--border-light);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: var(--space-md);
}

.metric-mini-card .metric-icon {
    width: 38px;
    height: 38px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
}

.metric-mini-card.total .metric-icon { background: var(--primary-50); color: var(--primary-color); }
.metric-mini-card.active .metric-icon { background: var(--color-income-bg); color: var(--color-income); }
.metric-mini-card.paused .metric-icon { background: var(--color-warning-bg); color: var(--color-warning); }

.metric-mini-card .metric-info {
    display: flex;
    flex-direction: column;
}

.metric-mini-card .metric-num {
    font-size: var(--fs-lg);
    font-weight: 800;
    color: var(--text-dark);
    line-height: 1.2;
}

.metric-mini-card .metric-lbl {
    font-size: var(--fs-xs);
    color: var(--text-muted);
    font-weight: 500;
}

.mini-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-md);
    margin-bottom: var(--space-lg);
}

@media (max-width: 576px) {
    .mini-grid {
        grid-template-columns: 1fr;
    }
}

.preview-box {
    background: var(--primary-50);
    border: 1px dashed var(--primary-light);
    border-radius: var(--radius-sm);
    padding: var(--space-sm) var(--space-md);
    font-size: var(--fs-xs);
    color: var(--primary-color);
    font-weight: 600;
    margin-top: var(--space-sm);
    display: flex;
    align-items: center;
    gap: var(--space-xs);
}

.warning-banner {
    background-color: var(--color-warning-bg);
    color: #92400e;
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: var(--radius-sm);
    padding: var(--space-sm) var(--space-md);
    font-size: var(--fs-xs);
    font-weight: 500;
    margin-top: var(--space-xs);
    display: flex;
    align-items: center;
    gap: var(--space-xs);
}

.premium-card-upgrade {
    background: linear-gradient(135deg, #4f46e5, #8b5cf6);
    color: white;
    border-radius: var(--radius-lg);
    padding: var(--space-lg);
    text-align: center;
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
    margin-bottom: var(--space-md);
}

.premium-card-upgrade::before {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
    top: -50px;
    right: -50px;
    border-radius: 50%;
}

.premium-card-upgrade h5 {
    color: white;
    font-family: var(--font-heading);
    font-weight: 800;
    margin-bottom: var(--space-xs);
    font-size: var(--fs-lg);
}

.premium-card-upgrade p {
    font-size: var(--fs-sm);
    opacity: 0.9;
    margin-bottom: var(--space-md);
    line-height: 1.5;
}

.premium-card-upgrade .btn-upgrade {
    background: white;
    color: #4f46e5;
    font-weight: 700;
    border-radius: var(--radius-md);
    padding: 0.5rem 1.25rem;
    font-size: var(--fs-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--space-xs);
    border: none;
    transition: var(--transition-fast);
}

.premium-card-upgrade .btn-upgrade:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    background: var(--border-light);
}

/* Override the 1fr 2fr content grid on index page for a wider list view */
.index-container {
    width: 100%;
}
</style>
@endpush

@section('content')
<div class="content-inner index-container">

    <!-- Page Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-sm);">
        <div>
            <h1 style="font-family: var(--font-heading); font-weight: 800; color: var(--text-dark); margin: 0; font-size: var(--fs-2xl);">Transaksi Rutin</h1>
            <p style="color: var(--text-muted); font-size: var(--fs-sm); margin: 0; margin-top: 2px;">
                Catat pengeluaran dan pemasukan otomatis secara periodik
                @if(!$isPremium)
                    <span style="font-size: 0.7rem; font-weight: 700; background: var(--border-color); color: var(--text-muted); padding: 2px 8px; border-radius: var(--radius-full); margin-left: var(--space-xs);">FREE</span>
                @else
                    <span style="font-size: 0.7rem; font-weight: 700; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); color: white; padding: 2px 8px; border-radius: var(--radius-full); margin-left: var(--space-xs);">PREMIUM</span>
                @endif
            </p>
        </div>
        
        <!-- Add Button / Trigger Modal -->
        @if($canCreate)
            <button id="btnOpenAddModal" class="btn-bunrek btn-primary" style="border-radius: var(--radius-md); font-weight: 700; padding: 0.6rem 1.4rem;">
                <i class="bi bi-plus-lg" style="font-size: 1rem; font-weight: bold; margin-right: 4px;"></i> Tambah Recurring
            </button>
        @endif
    </div>

    <!-- Notifications Banner -->
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

    @if($pendingCount > 0)
        <div class="bunrek-alert" style="margin-bottom: var(--space-lg); background: #fffbeb; color: #92400e; border: 1px solid #fcd34d; border-radius: var(--radius-md);">
            <i class="bi bi-bell-fill"></i> {{ $pendingCount }} transaksi rutin menunggu konfirmasi pembayaran dari kamu.
        </div>
    @endif

    <!-- Free limit upgrade warning banner (Only if limit reached) -->
    @if(!$isPremium && $activeCount >= $maxFreeRecurring)
        <div class="premium-card-upgrade">
            <div style="font-size: 2.2rem; margin-bottom: var(--space-sm);">👑</div>
            <h5>Batas Transaksi Rutin Terpenuhi</h5>
            <p>Kamu sudah menggunakan limit gratis {{ $activeCount }}/{{ $maxFreeRecurring }} transaksi rutin. Upgrade sekarang untuk mendapatkan limit tidak terbatas & semua pilihan frekuensi.</p>
            <a href="{{ route('membership.index') }}" class="btn-upgrade">
                <i class="bi bi-gem"></i> Upgrade Premium
            </a>
        </div>
    @endif

    <!-- Main Content Area: Spacious Table / List -->
    <div>

        {{-- ===== SECTION: MENUNGGU KONFIRMASI ===== --}}
        @if($pendingConfirmations->isNotEmpty())
        <div class="bunrek-card" style="margin-bottom: var(--space-lg); border: 2px solid #f59e0b; background: #fffbeb;">
            <div class="bunrek-card-header" style="background: #fef3c7; border-bottom: 1px solid #fcd34d;">
                <h2 class="bunrek-card-title" style="color: #92400e; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-hourglass-split" style="color: #f59e0b;"></i>
                    Menunggu Konfirmasi Pembayaran
                    <span style="background: #f59e0b; color: white; font-size: 0.7rem; padding: 2px 8px; border-radius: var(--radius-full); font-weight: 700;">{{ $pendingConfirmations->count() }}</span>
                </h2>
                <p style="font-size: var(--fs-xs); color: #92400e; margin: 4px 0 0 0; opacity: 0.8;">Konfirmasi apakah kamu sudah membayar transaksi-transaksi berikut.</p>
            </div>
            <div class="bunrek-card-body" style="padding: var(--space-md) var(--space-lg);">
                @foreach($pendingConfirmations as $pending)
                @php
                    $isToday = $pending->next_run_date && $pending->next_run_date->isToday();
                    $isOverdue = $pending->isOverdue();
                @endphp
                <div style="display: flex; align-items: center; justify-content: space-between; padding: var(--space-md); background: white; border: 1px solid #fcd34d; border-radius: var(--radius-md); margin-bottom: var(--space-sm); flex-wrap: wrap; gap: var(--space-sm);">
                    <div style="display: flex; align-items: center; gap: var(--space-md);">
                        <div class="icon-wrapper-circle {{ $pending->amount_type === 'pemasukan' ? 'icon-income' : 'icon-expense' }}">
                            <i class="bi {{ $pending->amount_type === 'pemasukan' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }}"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: var(--text-dark); font-size: var(--fs-base);">{{ $pending->description }}</div>
                            <div style="font-size: var(--fs-xs); color: var(--text-muted); margin-top: 2px;">
                                <strong style="color: {{ $pending->amount_type === 'pemasukan' ? 'var(--color-income)' : 'var(--color-expense)' }}">
                                    Rp {{ number_format($pending->amount, 0, ',', '.') }}
                                </strong>
                                &bull;
                                @if($isToday)
                                    <span style="color: #f59e0b; font-weight: 600;">Jatuh tempo hari ini</span>
                                @elseif($isOverdue)
                                    <span style="color: var(--color-expense); font-weight: 600;">Terlambat — {{ $pending->next_run_date->format('d M Y') }}</span>
                                @endif
                                @if($pending->category)
                                    &bull; 📁 {{ $pending->category->category_name }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: var(--space-xs); flex-shrink: 0;">
                        {{-- Tombol Sudah Dibayar --}}
                        <form action="{{ route('recurring.confirm', $pending->recurring_id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit"
                                    style="background: var(--color-income); color: white; border: none; border-radius: var(--radius-sm); padding: 7px 14px; font-size: var(--fs-xs); font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: var(--transition-fast);"
                                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                                <i class="bi bi-check-circle-fill"></i> Sudah Dibayar
                            </button>
                        </form>
                        {{-- Tombol Lewati --}}
                        <form action="{{ route('recurring.skip', $pending->recurring_id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Yakin ingin melewati periode ini? Tidak ada transaksi yang akan dicatat.')">
                            @csrf
                            <button type="submit"
                                    style="background: transparent; color: var(--text-muted); border: 1.5px solid var(--border-color); border-radius: var(--radius-sm); padding: 7px 14px; font-size: var(--fs-xs); font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: var(--transition-fast);"
                                    onmouseover="this.style.borderColor='var(--color-expense)';this.style.color='var(--color-expense)'" onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-muted)'">
                                <i class="bi bi-skip-forward-fill"></i> Lewati
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        {{-- ===== END SECTION KONFIRMASI ===== --}}

        <!-- Metrics Row -->
        <div class="mini-grid">
            <div class="metric-mini-card total">
                <div class="metric-icon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-num">{{ $recurrings->count() }}</span>
                    <span class="metric-lbl">Total Transaksi</span>
                </div>
            </div>
            <div class="metric-mini-card active">
                <div class="metric-icon">
                    <i class="bi bi-play-fill"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-num">{{ $recurrings->where('status','aktif')->count() }}</span>
                    <span class="metric-lbl">Aktif</span>
                </div>
            </div>
            <div class="metric-mini-card paused">
                <div class="metric-icon">
                    <i class="bi bi-pause-fill"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-num">{{ $recurrings->where('status','dijeda')->count() }}</span>
                    <span class="metric-lbl">Dijeda</span>
                </div>
            </div>
        </div>

        <!-- List Card (Full Width) -->
        <div class="bunrek-card">
            <div class="bunrek-card-header">
                <h2 class="bunrek-card-title">Daftar Transaksi Rutin Aktif</h2>
            </div>
            <div class="bunrek-card-body" style="padding: var(--space-md) var(--space-lg);">
                @if($recurrings->isEmpty())
                    <div style="text-align: center; padding: var(--space-2xl) var(--space-md); color: var(--text-light);">
                        <i class="bi bi-calendar-x" style="font-size: 2.5rem; display: block; margin-bottom: var(--space-sm);"></i>
                        <span style="font-size: var(--fs-base); font-weight: 500;">Belum ada daftar transaksi rutin.</span>
                        <p style="font-size: var(--fs-xs); color: var(--text-muted); margin: 0; margin-top: 4px;">Klik tombol "+ Tambah Recurring" di kanan atas untuk menambahkan transaksi rutin pertamamu.</p>
                    </div>
                @else
                    @foreach($recurrings as $rec)
                        @php
                            $isToday = $rec->next_run_date && $rec->next_run_date->isToday();
                            $isTomorrow = $rec->next_run_date && $rec->next_run_date->isTomorrow();
                            $categoryMissing = !$rec->category;
                            $hasReminder = $rec->reminder && $rec->reminder->reminder_enabled;
                        @endphp
                        <div class="recurring-card-item {{ $isToday ? 'due-today' : ($isTomorrow ? 'due-tomorrow' : '') }}">
                            <div style="display: flex; gap: var(--space-md); align-items: center; flex-wrap: wrap;">
                                
                                <!-- Left Side: Icon based on type -->
                                <div class="icon-wrapper-circle {{ $rec->amount_type === 'pemasukan' ? 'icon-income' : 'icon-expense' }}">
                                    @if($rec->amount_type === 'pemasukan')
                                        <i class="bi bi-arrow-down-left"></i>
                                    @else
                                        <i class="bi bi-arrow-up-right"></i>
                                    @endif
                                </div>

                                <!-- Middle Details -->
                                <div style="flex: 1; min-width: 200px;">
                                    <div style="font-size: var(--fs-base); font-weight: 700; color: var(--text-dark);">
                                        {{ $rec->description }}
                                    </div>
                                    <div style="display: flex; gap: var(--space-xs); align-items: center; margin-top: var(--space-xs); flex-wrap: wrap;">
                                        <span class="badge-freq">
                                            {{ \App\Helpers\RecurringHelper::getFrequencyLabel($rec->frequency) }}
                                        </span>
                                        @if($rec->category)
                                            <span class="badge-cat">
                                                📁 {{ $rec->category->category_name }}
                                            </span>
                                        @endif
                                        <span class="status-indicator status-{{ $rec->status === 'aktif' ? 'active' : ($rec->status === 'dijeda' ? 'paused' : 'ended') }}">
                                            {{ $rec->status }}
                                        </span>
                                    </div>

                                    <!-- Alerts inside card -->
                                    @if($categoryMissing)
                                        <div class="warning-banner">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Kategori tidak ditemukan. Harap edit & perbarui.
                                        </div>
                                    @endif

                                    @if($rec->needsConfirmation())
                                        @if($isToday)
                                            <div class="warning-banner" style="background-color: #fffbeb; color: #92400e; border-color: #fcd34d;">
                                                <i class="bi bi-hourglass-split" style="color: #f59e0b;"></i> Jatuh tempo hari ini — menunggu konfirmasimu!
                                            </div>
                                        @else
                                            <div class="warning-banner" style="background-color: var(--color-expense-bg); color: var(--color-expense); border-color: rgba(239,68,68,0.2);">
                                                <i class="bi bi-exclamation-circle-fill"></i> Terlambat dikonfirmasi sejak {{ $rec->next_run_date->format('d M Y') }}
                                            </div>
                                        @endif
                                    @elseif($isTomorrow && $rec->status === 'aktif')
                                        <div class="warning-banner" style="background-color: var(--primary-50); color: var(--primary-color); border-color: var(--primary-200);">
                                            <i class="bi bi-bell-fill"></i> Pengingat akan dikirim besok
                                        </div>
                                    @endif
                                </div>

                                <!-- Right Side: Money Amount & Next Run -->
                                <div style="text-align: right; min-width: 140px;">
                                    <div style="font-size: var(--fs-lg); font-weight: 800; color: {{ $rec->amount_type === 'pemasukan' ? 'var(--color-income)' : 'var(--color-expense)' }}">
                                        Rp {{ number_format($rec->amount, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: var(--fs-xs); color: var(--text-muted); margin-top: 2px;">
                                        Next: <strong style="color: var(--text-body);">{{ $rec->next_run_date ? $rec->next_run_date->format('d M Y') : '-' }}</strong>
                                    </div>
                                </div>

                                <!-- Far Right: Actions -->
                                <div style="display: flex; gap: var(--space-xs); justify-content: flex-end; align-items: center; flex-wrap: wrap;">
                                    {{-- Tombol konfirmasi manual jika overdue --}}
                                    @if($rec->needsConfirmation() && $rec->status === 'aktif')
                                        <form action="{{ route('recurring.confirm', $rec->recurring_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit"
                                                    class="action-icon-btn"
                                                    title="Konfirmasi Sudah Dibayar"
                                                    style="border-color: var(--color-income); color: var(--color-income); background: var(--color-income-bg); width: auto; padding: 0 10px; font-size: 0.7rem; font-weight: 700;">
                                                <i class="bi bi-check-circle-fill"></i>&nbsp;Konfirmasi
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Reminder indicator --}}
                                    @if($hasReminder)
                                        <span title="Reminder aktif" style="color: var(--primary-color); font-size: 0.9rem;"><i class="bi bi-bell-fill"></i></span>
                                    @else
                                        <span title="Reminder nonaktif" style="color: var(--border-color); font-size: 0.9rem;"><i class="bi bi-bell-slash"></i></span>
                                    @endif
                                    @if($rec->status !== 'selesai')
                                        <!-- Edit button -->
                                        <a href="{{ route('recurring.edit', $rec->recurring_id) }}" class="action-icon-btn" title="Edit Transaksi">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <!-- Pause/Play button -->
                                        <form action="{{ route('recurring.toggle', $rec->recurring_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="action-icon-btn btn-pause" title="{{ $rec->status === 'aktif' ? 'Jeda Transaksi' : 'Aktifkan Transaksi' }}">
                                                <i class="bi {{ $rec->status === 'aktif' ? 'bi-pause-fill' : 'bi-play-fill' }}"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete button -->
                                    <form action="{{ route('recurring.destroy', $rec->recurring_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus transaksi rutin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon-btn btn-delete" title="Hapus Permanen">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Custom Glassmorphic Add Modal Dialog -->
@if($canCreate)
<div id="addRecurringModalCustom" class="bunrek-modal-overlay">
    <div class="bunrek-modal-card">
        <div class="bunrek-modal-header">
            <h3 style="font-family: var(--font-heading); font-weight: 800; color: var(--text-dark); margin: 0; font-size: var(--fs-lg); display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-plus-circle-fill" style="color: var(--primary-color);"></i> Tambah Transaksi Rutin
            </h3>
            <button id="btnCloseAddModal" class="bunrek-modal-close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="bunrek-modal-body">
            <form action="{{ route('recurring.store') }}" method="POST">
                @csrf
                
                <div class="bunrek-form-group">
                    <label class="bunrek-label">Deskripsi / Nama</label>
                    <input type="text" name="description" class="bunrek-input" required placeholder="Contoh: Bayar kosan bulanan" value="{{ old('description') }}">
                    @error('description') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="bunrek-form-group" id="addCategoryGroup">
                        <label class="bunrek-label">Kategori</label>
                        <select name="category_id" id="addCategory" class="bunrek-select">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                @if(!in_array($cat->category_id, [10, 11]))
                                    <option value="{{ $cat->category_id }}" {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('category_id') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Tipe Transaksi</label>
                        <select name="amount_type" id="addAmountType" class="bunrek-select" required>
                            <option value="pengeluaran" {{ old('amount_type', 'pengeluaran') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                            <option value="pemasukan" {{ old('amount_type') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Nominal (Rp)</label>
                        <input type="number" name="amount" class="bunrek-input" required placeholder="0" min="1" value="{{ old('amount') }}">
                        @error('amount') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Frekuensi Ulang</label>
                        <select name="frequency" class="bunrek-select" required id="addFrequency">
                            @foreach($frequencies as $val => $label)
                                <option value="{{ $val }}" {{ old('frequency') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('frequency') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="bunrek-input" required value="{{ old('start_date', date('Y-m-d')) }}" id="addStartDate">
                        @error('start_date') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Tanggal Berakhir <small style="color: var(--text-muted); font-weight: 500;">(Opsional)</small></label>
                        <input type="date" name="end_date" class="bunrek-input" value="{{ old('end_date') }}" id="addEndDate">
                        @error('end_date') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="preview-box" id="addPreview" style="display: none;">
                    <i class="bi bi-bell-fill"></i>
                    <span id="addPreviewText"></span>
                </div>

                @php $reminder = null; @endphp
                @include('recurring.partials.reminder-form', ['isPremium' => $isPremium, 'reminder' => $reminder])

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-lg); border-top: 1px solid var(--border-light); padding-top: var(--space-md);">
                    <button type="button" id="btnCancelAddModal" class="btn-bunrek btn-outline" style="min-width: 100px;">
                        Batal
                    </button>
                    <button type="submit" class="btn-bunrek btn-primary" style="min-width: 140px;">
                        <i class="bi bi-check-lg"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show modal trigger
    $('#btnOpenAddModal').on('click', function() {
        $('#addRecurringModalCustom').addClass('active');
    });

    // Hide modal triggers
    $('#btnCloseAddModal, #btnCancelAddModal').on('click', function() {
        $('#addRecurringModalCustom').removeClass('active');
    });

    // Close modal on clicking outside card
    $('#addRecurringModalCustom').on('click', function(e) {
        if ($(e.target).is('#addRecurringModalCustom')) {
            $('#addRecurringModalCustom').removeClass('active');
        }
    });

    // Auto open modal if validation errors exist
    @if ($errors->any())
        $('#addRecurringModalCustom').addClass('active');
    @endif

    // Update dynamic preview
    function updatePreview() {
        const freq = $('#addFrequency').val();
        const startDate = $('#addStartDate').val();
        if (freq && startDate) {
            const freqLabels = { harian: 'setiap hari', mingguan: 'setiap minggu', bulanan: 'setiap bulan', tahunan: 'setiap tahun' };
            const d = new Date(startDate);
            const formatted = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            $('#addPreviewText').text('🔔 Kamu akan diingatkan ' + (freqLabels[freq] || freq) + ' mulai ' + formatted + '. Transaksi dicatat setelah kamu konfirmasi.');
            $('#addPreview').show();
        } else {
            $('#addPreview').hide();
        }
    }
    
    $('#addFrequency, #addStartDate').on('change', updatePreview);
    updatePreview();

    // Sembunyikan kategori saat tipe = Pemasukan
    function toggleAddCategory() {
        if ($('#addAmountType').val() === 'pemasukan') {
            $('#addCategoryGroup').hide();
            $('#addCategory').prop('required', false).prop('disabled', true);
        } else {
            $('#addCategoryGroup').show();
            $('#addCategory').prop('required', true).prop('disabled', false);
        }
    }
    $('#addAmountType').on('change', toggleAddCategory);
    toggleAddCategory();

    // Auto-dismiss alerts
    setTimeout(function() { 
        $('.bunrek-alert').fadeOut(500); 
    }, 5000);
});
</script>
@endpush
