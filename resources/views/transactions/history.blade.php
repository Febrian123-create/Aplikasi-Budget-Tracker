@extends('layouts.master')

@section('page_title', 'Riwayat Transaksi')

@section('content')
<div class="bunrek-card mb-4">
    <div class="bunrek-card-header">
        <h2 class="bunrek-card-title">Filter Riwayat</h2>
    </div>
    <div class="bunrek-card-body">
        
        <form method="GET" action="{{ route('transactions.history') }}" id="filterForm">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--space-md);">
                <div class="bunrek-form-group" style="margin-bottom: 0;">
                    <label for="category_id" class="bunrek-label">Kategori</label>
                    <select name="category_id" id="category_id" class="bunrek-select">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                    <small id="categoryHelpText" style="color: var(--text-muted); display: none; margin-top: 4px;">
                        <i class="bi bi-info-circle"></i> Kategori hanya tersedia untuk Pengeluaran
                    </small>
                </div>

                <div class="bunrek-form-group" style="margin-bottom: 0;">
                    <label for="transactionType_id" class="bunrek-label">Jenis</label>
                    <select name="transactionType_id" id="transactionType_id" class="bunrek-select">
                        <option value="">-- Semua Jenis --</option>
                        <option value="1" {{ request('transactionType_id') == 1 ? 'selected' : '' }}>Pemasukan (Income)</option>
                        <option value="2" {{ request('transactionType_id') == 2 ? 'selected' : '' }}>Pengeluaran (Expense)</option>
                    </select>
                </div>

                <div class="bunrek-form-group" style="margin-bottom: 0;">
                    <label for="start_date" class="bunrek-label">Tanggal Awal</label>
                    <input type="date" name="start_date" id="start_date" class="bunrek-input" value="{{ request('start_date') }}">
                </div>

                <div class="bunrek-form-group" style="margin-bottom: 0;">
                    <label for="end_date" class="bunrek-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="bunrek-input" value="{{ request('end_date') }}">
                </div>
            </div>

            <div style="margin-top: var(--space-lg); display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-bunrek btn-primary">
                    <i class="bi bi-funnel"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="bunrek-card">
    <div class="bunrek-card-header">
        <h2 class="bunrek-card-title">Daftar Transaksi</h2>
        @php
            $membershipFeature = app(\App\Features\MembershipFeatureInterface::class);
        @endphp
        <div style="display: flex; gap: var(--space-xs);">
            <a href="#" id="btnExportExcel" class="btn-bunrek btn-sm btn-outline text-success" style="border-color: rgba(16, 185, 129, 0.2); background: rgba(16, 185, 129, 0.05);">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="#" id="btnExportPdf" class="btn-bunrek btn-sm btn-outline text-danger" style="border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </div>

    @if ($isCategoryFiltered)
        <div style="background: var(--bg-light); padding: var(--space-lg); border-bottom: 1px solid var(--border-light);">
            @if ($totalIncome > 0)
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: {{ $totalExpense > 0 ? 'var(--space-sm)' : '0' }};">
                    <span style="font-weight: 600; color: var(--text-body);">Total Pemasukan Kategori:</span>
                    <span style="font-size: var(--fs-lg); font-weight: 700; color: var(--color-income);">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </span>
                </div>
            @endif
            @if ($totalExpense > 0)
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 600; color: var(--text-body);">Total Pengeluaran Kategori:</span>
                    <span style="font-size: var(--fs-lg); font-weight: 700; color: var(--color-expense);">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </span>
                </div>
            @endif
            @if ($totalIncome == 0 && $totalExpense == 0)
                <div style="text-align: center; color: var(--text-muted); font-size: var(--fs-sm);">Tidak ada data untuk kategori ini.</div>
            @endif
        </div>
    @endif

    @if ($isTypeFiltered)
        <!-- Tampilkan balance untuk jenis yang dipilih -->
        <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(139, 92, 246, 0.05)); padding: var(--space-lg); border-bottom: 1px solid var(--border-light);">
            @if ($totalIncome > 0)
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 600; color: var(--text-body);">Total Pemasukan:</span>
                    <span style="font-size: var(--fs-lg); font-weight: 700; color: var(--color-income);">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </span>
                </div>
            @endif
            @if ($totalExpense > 0)
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 600; color: var(--text-body);">Total Pengeluaran:</span>
                    <span style="font-size: var(--fs-lg); font-weight: 700; color: var(--color-expense);">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </span>
                </div>
            @endif
        </div>
    @elseif (($totalIncome > 0 || $totalExpense > 0) && !$isTypeFiltered)
        <!-- Tampilkan balance pemasukan dan pengeluaran ketika hanya tanggal yang dipilih -->
        <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(139, 92, 246, 0.05)); padding: var(--space-lg); border-bottom: 1px solid var(--border-light);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-lg);">
                <div>
                    <div style="font-size: var(--fs-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; font-weight: 600;">Pemasukan</div>
                    <div style="font-size: var(--fs-lg); font-weight: 700; color: var(--color-income);">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                </div>
                <div>
                    <div style="font-size: var(--fs-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; font-weight: 600;">Pengeluaran</div>
                    <div style="font-size: var(--fs-lg); font-weight: 700; color: var(--color-expense);">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div style="margin-top: var(--space-lg); padding-top: var(--space-lg); border-top: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: var(--text-body);">Saldo:</span>
                <span style="font-size: var(--fs-lg); font-weight: 700; color: {{ $totalIncome >= $totalExpense ? 'var(--color-income)' : 'var(--color-expense)' }};">
                    Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}
                </span>
            </div>
        </div>
    @endif

    <div class="bunrek-card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="bunrek-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</td>
                            <td><strong>{{ $transaction->category->category_name ?? '-' }}</strong></td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                @if($transaction->transactionType_id == 1)
                                    <span class="badge-income">Pemasukan</span>
                                @else
                                    <span class="badge-expense">Pengeluaran</span>
                                @endif
                            </td>
                            <td style="font-weight: 600; color: {{ $transaction->transactionType_id == 1 ? 'var(--color-income)' : 'var(--color-expense)' }}">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 4px; justify-content: flex-end;">
                                    <a href="{{ route('transactions.edit', $transaction->transaction_id) }}" class="btn-bunrek btn-sm btn-warning-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('transactions.destroy', $transaction->transaction_id) }}" method="POST" onsubmit="return confirm('Hapus transaksi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-bunrek btn-sm btn-danger-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="6">
                                <div style="padding: var(--space-xl) 0;">
                                    <i class="bi bi-inbox" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                    Tidak ada transaksi ditemukan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function buildExportUrl(baseUrl) {
        var params = new URLSearchParams();
        var categoryId = document.getElementById('category_id').value;
        var transactionTypeId = document.getElementById('transactionType_id').value;
        var startDate = document.getElementById('start_date').value;
        var endDate = document.getElementById('end_date').value;

        if (categoryId) params.append('category_id', categoryId);
        if (transactionTypeId) params.append('transactionType_id', transactionTypeId);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        var queryString = params.toString();
        return baseUrl + (queryString ? '?' + queryString : '');
    }

    function updateExportLinks() {
        document.getElementById('btnExportExcel').href = buildExportUrl('{{ route("history.export.excel") }}');
        document.getElementById('btnExportPdf').href = buildExportUrl('{{ route("history.export.pdf") }}');
    }

    function updateCategoryState() {
        var transactionTypeId = document.getElementById('transactionType_id').value;
        var categorySelect = document.getElementById('category_id');
        var categoryHelpText = document.getElementById('categoryHelpText');

        if (transactionTypeId == '1') {
            // Pemasukan (Income) - disable kategori
            categorySelect.disabled = true;
            categorySelect.value = '';
            categorySelect.style.opacity = '0.4';
            categorySelect.style.cursor = 'not-allowed';
            categoryHelpText.style.display = 'block';
        } else {
            // Pengeluaran (Expense) atau Semua - enable kategori
            categorySelect.disabled = false;
            categorySelect.style.opacity = '1';
            categorySelect.style.cursor = 'auto';
            categoryHelpText.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateExportLinks();
        updateCategoryState();
    });

    document.getElementById('category_id').addEventListener('change', updateExportLinks);
    document.getElementById('transactionType_id').addEventListener('change', function () {
        updateExportLinks();
        updateCategoryState();
    });
    document.getElementById('start_date').addEventListener('change', updateExportLinks);
    document.getElementById('end_date').addEventListener('change', updateExportLinks);
</script>
@endpush