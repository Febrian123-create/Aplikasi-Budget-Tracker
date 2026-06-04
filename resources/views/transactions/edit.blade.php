@extends('layouts.master')

@section('page_title', 'Edit Transaksi')

@push('styles')
<style>
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

.back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: var(--radius-md);
    border: 1.5px solid var(--border-color);
    background: var(--bg-white);
    color: var(--text-muted);
    transition: var(--transition-fast);
    text-decoration: none;
}

.back-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    background: var(--primary-50);
}

.date-display {
    background: var(--bg-light);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: var(--space-sm) var(--space-md);
    font-size: var(--fs-sm);
    color: var(--text-dark);
    font-weight: 500;
}
</style>
@endpush

@section('content')
<div class="content-inner" style="max-width: 720px; margin: 0 auto;">
    
    <!-- Page Header -->
    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-xl);">
        <a href="{{ url()->previous() }}" class="back-btn" title="Kembali">
            <i class="bi bi-arrow-left" style="font-size: 1.1rem; font-weight: bold;"></i>
        </a>
        <div>
            <h1 style="font-family: var(--font-heading); font-weight: 800; color: var(--text-dark); margin: 0; font-size: var(--fs-2xl);">Edit Transaksi</h1>
            <p style="color: var(--text-muted); font-size: var(--fs-sm); margin: 0; margin-top: 2px;">
                Mengubah detail transaksi: <strong>{{ $transaction->description }}</strong>
            </p>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bunrek-card">
        <div class="bunrek-card-header">
            <h2 class="bunrek-card-title">Detail Transaksi</h2>
        </div>
        <div class="bunrek-card-body">
            @if (session('success'))
                <div class="bunrek-alert bunrek-alert-success">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bunrek-alert bunrek-alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <ul class="mb-0" style="margin-left: var(--space-md);">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('transactions.update', $transaction->transaction_id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Tanggal (Readonly Display) -->
                <div class="bunrek-form-group">
                    <label class="bunrek-label">Tanggal Transaksi</label>
                    <div class="date-display">
                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat('d F Y') }}
                    </div>
                    <!-- Hidden input untuk tetap mengirim nilai tanggal -->
                    <input type="hidden" name="date" value="{{ $transaction->transaction_date }}">
                </div>

                <!-- Tipe & Kategori (Grid 2 Kolom) -->
                <div class="bunrek-form-group text-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div>
                        <label class="bunrek-label">Tipe Transaksi</label>
                        <div class="bunrek-radio-group">
                            <label class="bunrek-radio-label" for="income">
                                <input type="radio" name="type" value="income" id="income"
                                    {{ $transaction->transactionType->name === 'income' ? 'checked' : '' }}
                                    onchange="toggleKategori()">
                                <span>Pemasukan</span>
                            </label>
                            <label class="bunrek-radio-label" for="expense">
                                <input type="radio" name="type" value="expense" id="expense"
                                    {{ $transaction->transactionType->name === 'expense' ? 'checked' : '' }}
                                    onchange="toggleKategori()">
                                <span>Pengeluaran</span>
                            </label>
                        </div>
                        @error('type') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Jumlah & Kategori (Grid 2 Kolom) -->
                <div class="bunrek-form-group text-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div>
                        <label class="bunrek-label">Jumlah (Rp)</label>
                        <input type="number" name="amount" class="bunrek-input" required min="0"
                            value="{{ old('amount', $transaction->total_amount) }}" placeholder="0">
                        @error('amount') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label class="bunrek-label">Kategori</label>
                        <select name="category" id="kategoriSelect" class="bunrek-select">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->category_id }}"
                                    {{ $transaction->category_id == $cat->category_id ? 'selected' : '' }}>
                                    {{ $cat->category_name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="hiddenCategory" name="" value="10">
                        <p id="kategoriInfo" style="display:none; margin: 6px 0 0 0; font-size: var(--fs-xs); color: var(--text-muted); font-style: italic;">
                            <i class="bi bi-info-circle"></i> Pemasukan tidak memerlukan kategori.
                        </p>
                        @error('category') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="bunrek-form-group">
                    <label class="bunrek-label">Deskripsi</label>
                    <textarea name="description" class="bunrek-textarea" required placeholder="Deskripsi transaksi...">{{ old('description', $transaction->description) }}</textarea>
                    @error('description') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                </div>

                <!-- Footer Buttons -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-xl); border-top: 1px solid var(--border-light); padding-top: var(--space-lg);">
                    <a href="{{ url()->previous() }}" class="btn-bunrek btn-outline" style="min-width: 100px;">
                        Batal
                    </a>
                    <button type="submit" class="btn-bunrek btn-primary" style="min-width: 140px;">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleKategori() {
        const expenseRadio = document.getElementById('expense');
        const kategoriSelect = document.getElementById('kategoriSelect');
        const hiddenCategory = document.getElementById('hiddenCategory');
        const kategoriInfo = document.getElementById('kategoriInfo');

        if (expenseRadio.checked) {
            // Pengeluaran: aktifkan dropdown kategori
            kategoriSelect.disabled = false;
            kategoriSelect.setAttribute('required', 'required');
            kategoriSelect.name = 'category';
            kategoriSelect.style.opacity = '1';
            kategoriSelect.style.cursor = 'pointer';
            hiddenCategory.name = '';
            kategoriInfo.style.display = 'none';
        } else {
            // Pemasukan: nonaktifkan dropdown kategori
            kategoriSelect.disabled = true;
            kategoriSelect.removeAttribute('required');
            kategoriSelect.name = '';
            kategoriSelect.value = '';
            kategoriSelect.style.opacity = '0.4';
            kategoriSelect.style.cursor = 'not-allowed';
            hiddenCategory.value = '10';
            hiddenCategory.name = 'category';
            kategoriInfo.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleKategori(); // Set state awal berdasarkan tipe transaksi
    });
</script>
@endpush

@endsection