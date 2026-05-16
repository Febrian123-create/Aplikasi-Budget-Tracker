@extends('layouts.master')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Edit Transaksi</h5>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('transactions.update', $transaction->transaction_id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="date" class="form-control" required
                                        value="{{ old('date', $transaction->transaction_date) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tipe</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" value="income"
                                            id="income" {{ $transaction->transactionType_id == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="income">Pemasukan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" value="expense"
                                            id="expense" {{ $transaction->transactionType_id == 2 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="expense">Pengeluaran</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="category" class="form-control" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->category_id }}"
                                                {{ $transaction->category_id == $cat->category_id ? 'selected' : '' }}>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jumlah (Rp)</label>
                                    <input type="number" name="amount" class="form-control" required
                                        value="{{ old('amount', $transaction->total_amount) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" class="form-control" required>{{ old('description', $transaction->description) }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
