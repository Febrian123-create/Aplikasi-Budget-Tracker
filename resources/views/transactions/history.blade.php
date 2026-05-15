@extends('layouts.master')
@section('content')
    <div class="container">
        <div class="page-inner">
            <h2 class="mb-4">History Transaksi</h2>

            <form method="GET" action="{{ route('transactions.history') }}" class="mb-3" id="filterForm">
                <div class="row">
                    <div class="col">
                        <label for="category_id">Kategori</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">-- Semua --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                                    {{ $cat->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <label for="transactionType_id">Jenis</label>
                        <select name="transactionType_id" id="transactionType_id" class="form-control">
                            <option value="">-- Semua --</option>
                            <option value="1" {{ request('transactionType_id') == 1 ? 'selected' : '' }}>Income</option>
                            <option value="2" {{ request('transactionType_id') == 2 ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>

                    <div class="col">
                        <label for="start_date">Tanggal Awal</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>

                    <div class="col">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>

                    <div class="col">
                        <button type="submit" class="btn btn-primary mt-4">Filter</button>
                    </div>
                </div>
            </form>

            {{-- Export Buttons --}}
            <div class="d-flex gap-2 mb-3">
                <a href="#" id="btnExportExcel" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="#" id="btnExportPdf" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date }}</td>
                            <td>{{ $transaction->category->category_name ?? '-' }}</td>
                            <td>
                                @if($transaction->transactionType_id == 1)
                                    <span class="badge bg-success">Income</span>
                                @else
                                    <span class="badge bg-danger">Expense</span>
                                @endif
                            </td>
                            <td>{{ $transaction->description }}</td>
                            <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('transactions.edit', $transaction->transaction_id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('transactions.destroy', $transaction->transaction_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus transaksi?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <h4>Total Income: Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
            <h4>Total Expense: Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
            <h4>Balance: Rp {{ number_format($balance, 0, ',', '.') }}</h4>
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

    // Update on page load
    updateExportLinks();

    // Update when filters change
    document.getElementById('category_id').addEventListener('change', updateExportLinks);
    document.getElementById('transactionType_id').addEventListener('change', updateExportLinks);
    document.getElementById('start_date').addEventListener('change', updateExportLinks);
    document.getElementById('end_date').addEventListener('change', updateExportLinks);
</script>
@endpush