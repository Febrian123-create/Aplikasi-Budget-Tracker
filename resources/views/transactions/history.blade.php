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
        @if ($membershipFeature->canExportPdf())
            <div style="display: flex; gap: var(--space-xs);">
                <a href="#" id="btnExportExcel" class="btn-bunrek btn-sm btn-outline text-success" style="border-color: rgba(16, 185, 129, 0.2); background: rgba(16, 185, 129, 0.05);">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
                <a href="#" id="btnExportPdf" class="btn-bunrek btn-sm btn-outline text-danger" style="border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);">
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
@if ($membershipFeature->canExportPdf())
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

    // Update on page load
    updateExportLinks();

    // Update when filters change
    document.getElementById('category_id').addEventListener('change', updateExportLinks);
    document.getElementById('transactionType_id').addEventListener('change', updateExportLinks);
    document.getElementById('start_date').addEventListener('change', updateExportLinks);
    document.getElementById('end_date').addEventListener('change', updateExportLinks);
</script>
@endif
@endpush