@extends('layouts.master')
@section('content')
    <div class="container">
        <h2>History Transaksi</h2>

        <form method="GET" action="{{ route('transactions.history') }}" class="mb-3">
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
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date }}</td>
                        <td>{{ $transaction->category->category_name ?? '-' }}</td>
                        <td>
                            @if($transaction->transactionType_id == 1)
                                Income
                            @else
                                Expense
                            @endif
                        </td>
                        <td>{{ $transaction->description }}</td>
                        <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('transactions.destroy', $transaction->transaction_id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4>Total Income: Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
        <h4>Total Expense: Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
        <h4>Balance: Rp {{ number_format($balance, 0, ',', '.') }}</h4>
    </div>
@endsection