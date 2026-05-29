@extends('layouts.master')

@section('page_title', 'Dashboard')

@section('content')

<div class="stats-row">
    
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-label">Overview Saldo</span>
            <div class="stat-card-icon balance">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
        <div class="stat-card-value text-balance">
            Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
        </div>
    </div>

    
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-label">Total Pemasukan</span>
            <div class="stat-card-icon income">
                <i class="bi bi-arrow-down-left-circle"></i>
            </div>
        </div>
        <div class="stat-card-value text-income">
            Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}
        </div>
    </div>

    
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-label">Total Pengeluaran</span>
            <div class="stat-card-icon expense">
                <i class="bi bi-arrow-up-right-circle"></i>
            </div>
        </div>
        <div class="stat-card-value text-expense">
            Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}
        </div>
    </div>
</div>

@php
    $membershipFeature = app(\App\Features\MembershipFeatureInterface::class);
@endphp


@if (!$membershipFeature->canViewChart())
    <div id="chart-section" class="premium-cta">
        <div class="premium-cta-icon">
            <i class="bi bi-lock-fill text-warning"></i>
        </div>
        <h4 style="font-family: var(--font-heading); font-weight: 700; margin-bottom: 8px;">Visualisasi Grafik Khusus Premium</h4>
        <p class="premium-cta-text">
            Dapatkan visualisasi grafik pengeluaran dan pemasukan interaktif yang detail dengan upgrade ke Premium.
        </p>
        <a href="{{ route('membership.index') }}" class="btn-bunrek btn-primary">
            <i class="bi bi-gem"></i> Upgrade Sekarang
        </a>
    </div>
@else
    <div id="chart-section" class="premium-cta" style="background: linear-gradient(135deg, var(--primary-50), rgba(99, 102, 241, 0.02)); border-style: solid;">
        <div class="premium-cta-icon">
            <i class="bi bi-bar-chart-line-fill text-primary"></i>
        </div>
        <h4 style="font-family: var(--font-heading); font-weight: 700; margin-bottom: 8px;">Visualisasi Grafik Siap Digunakan</h4>
        <p class="premium-cta-text">
            Analisis keuangan Anda secara visual dengan diagram interaktif kami.
        </p>
        <a href="{{ route('charts.index') }}" class="btn-bunrek btn-primary">
            <i class="bi bi-arrow-right-short"></i> Lihat Grafik Visualisasi
        </a>
    </div>
@endif

<div class="content-grid">
    
    <div class="bunrek-card">
        <div class="bunrek-card-header">
            <h2 class="bunrek-card-title">Tambah Transaksi</h2>
        </div>
        <div class="bunrek-card-body">
            @if (session('success'))
                <div class="bunrek-alert bunrek-alert-success">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="bunrek-form-group">
                    <label class="bunrek-label">Tanggal</label>
                    <input type="date" name="date" class="bunrek-input" required value="{{ date('Y-m-d') }}">
                </div>

                <div class="bunrek-form-group">
                    <label class="bunrek-label">Tipe Transaksi</label>
                    <div class="bunrek-radio-group">
                        <label class="bunrek-radio-label" for="income">
                            <input type="radio" name="type" value="income" id="income" checked>
                            <span>Pemasukan</span>
                        </label>
                        <label class="bunrek-radio-label" for="expense">
                            <input type="radio" name="type" value="expense" id="expense">
                            <span>Pengeluaran</span>
                        </label>
                    </div>
                </div>

                <div class="bunrek-form-group">
                    <label class="bunrek-label">Kategori</label>
                    <select name="category" class="bunrek-select" required>
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories ?? [] as $cat)
                            <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bunrek-form-group">
                    <label class="bunrek-label">Jumlah (Rp)</label>
                    <input type="number" name="amount" class="bunrek-input" required placeholder="0" min="1">
                </div>

                <div class="bunrek-form-group">
                    <label class="bunrek-label">Deskripsi</label>
                    <textarea name="description" class="bunrek-textarea" required placeholder="Deskripsi transaksi..."></textarea>
                </div>

                <button type="submit" class="btn-bunrek btn-primary btn-w-full">
                    <i class="bi bi-plus-circle"></i> Simpan Transaksi
                </button>
            </form>
        </div>
    </div>

    
    <div class="bunrek-card">
        <div class="bunrek-card-header">
            <h2 class="bunrek-card-title">
                Transaksi Hari Ini &mdash; <span style="font-weight: 500; font-size: 0.95rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}</span>
            </h2>
            @if ($membershipFeature->canExportPdf())
                <div style="display: flex; gap: var(--space-xs);">
                    <a href="{{ route('transactions.export.excel') }}" class="btn-bunrek btn-sm btn-outline text-success" id="btnExportExcelTx" style="border-color: rgba(16, 185, 129, 0.2); background: rgba(16, 185, 129, 0.05);">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </a>
                    <a href="{{ route('transactions.export.pdf') }}" class="btn-bunrek btn-sm btn-outline text-danger" id="btnExportPdfTx" style="border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>
                </div>
            @else
                <span style="font-size: var(--fs-xs); color: var(--text-muted); background: var(--bg-light); padding: 4px 8px; border-radius: var(--radius-sm); border: 1px dashed var(--border-color);">
                    <i class="bi bi-gem text-warning"></i> Export Premium
                </span>
            @endif
        </div>
        <div class="bunrek-card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="bunrek-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions ?? [] as $t)
                            <tr>
                                <td>
                                    @php
                                        $categoryName = \DB::table('category')
                                            ->where('category_id', $t->category_id)
                                            ->value('category_name');
                                    @endphp
                                    <strong>{{ $categoryName ?? '-' }}</strong>
                                </td>
                                <td>{{ $t->description }}</td>
                                <td>
                                    @if ($t->transactionType_id == 1)
                                        <span class="badge-income">Pemasukan</span>
                                    @else
                                        <span class="badge-expense">Pengeluaran</span>
                                    @endif
                                </td>
                                <td style="font-weight: 600; color: {{ $t->transactionType_id == 1 ? 'var(--color-income)' : 'var(--color-expense)' }}">
                                    Rp {{ number_format($t->total_amount, 0, ',', '.') }}
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 4px; justify-content: flex-end;">
                                        <a href="{{ route('transactions.edit', $t->transaction_id) }}" class="btn-bunrek btn-sm btn-warning-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('transactions.destroy', $t->transaction_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus transaksi?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-bunrek btn-sm btn-danger-sm">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="5">
                                    <div style="padding: var(--space-xl) 0;">
                                        <i class="bi bi-inbox" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                        Belum ada transaksi hari ini
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection