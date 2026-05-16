<!DOCTYPE html>
<html lang="id">

<body class="min-h-screen py-10 px-4">
    @extends('layouts.master')
    @section('content')
        <div class="container">
            <div class="page-inner">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Overview Saldo</h6>

                                <h2 class="fw-bold text-primary">
                                    Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
                                </h2>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <h6 class="text-success">Total Pemasukan</h6>

                                <h3 class="fw-bold text-success">
                                    Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <h6 class="text-danger">Total Pengeluaran</h6>

                                <h3 class="fw-bold text-danger">
                                    Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $membershipFeature = app(\App\Features\MembershipFeatureInterface::class);
                @endphp

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                @if ($membershipFeature->canViewChart())
                                    <a href="{{ route('charts.index') }}" class="btn btn-info">Lihat Visualisasi Grafik</a>
                                @else
                                    <a href="{{ route('membership.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-lock"></i> Visualisasi Grafik (Khusus Premium - Upgrade Sekarang)
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Form Transaksi -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Tambah Transaksi</h5>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                <form action="{{ route('transactions.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" name="date" class="form-control" required
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tipe</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" value="income"
                                                id="income" checked>
                                            <label class="form-check-label" for="income">Pemasukan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" value="expense"
                                                id="expense">
                                            <label class="form-check-label" for="expense">Pengeluaran</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kategori</label>
                                        <select name="category" class="form-control" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($categories ?? [] as $cat)
                                                <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah (Rp)</label>
                                        <input type="number" name="amount" class="form-control" required placeholder="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="description" class="form-control" required
                                            placeholder="Deskripsi transaksi..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Transaksi -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Transaksi Hari Ini — {{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}</h5>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('transactions.export.excel') }}" class="btn btn-success btn-sm" id="btnExportExcelTx">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </a>
                                    <a href="{{ route('transactions.export.pdf') }}" class="btn btn-danger btn-sm" id="btnExportPdfTx">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Kategori</th>
                                                <th>Deskripsi</th>
                                                <th>Tipe</th>
                                                <th>Jumlah</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transactions ?? [] as $t)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('d M Y') }}</td>
                                                    <td> @php
                                                        $categoryName = \DB::table('category')
                                                            ->where('category_id', $t->category_id)
                                                            ->value('category_name');
                                                    @endphp
                                                        {{ $categoryName ?? '-' }}
                                                    </td>
                                                    <td>{{ $t->description }}</td>
                                                    <td>
                                                        @if ($t->transactionType_id == 1)
                                                            <span class="badge bg-success">Pemasukan</span>
                                                        @else
                                                            <span class="badge bg-danger">Pengeluaran</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ 'Rp' }} {{ number_format($t->total_amount, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            <a href="{{ route('transactions.edit', $t->transaction_id) }}"
                                                                class="btn btn-sm btn-warning">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                            <form action="{{ route('transactions.destroy', $t->transaction_id) }}"
                                                                method="POST" style="display:inline;"
                                                                onsubmit="return confirm('Hapus transaksi?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-trash"></i> Hapus
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Belum ada transaksi hari ini</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>

</html>