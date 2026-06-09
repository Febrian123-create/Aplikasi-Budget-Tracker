@extends('layouts.master')

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
</style>
@endpush

@section('content')
<div class="content-inner" style="max-width: 720px; margin: 0 auto;">
    
    <!-- Page Header -->
    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-xl);">
        <a href="{{ route('recurring.index') }}" class="back-btn" title="Kembali ke Daftar">
            <i class="bi bi-arrow-left" style="font-size: 1.1rem; font-weight: bold;"></i>
        </a>
        <div>
            <h1 style="font-family: var(--font-heading); font-weight: 800; color: var(--text-dark); margin: 0; font-size: var(--fs-2xl);">Edit Transaksi Rutin</h1>
            <p style="color: var(--text-muted); font-size: var(--fs-sm); margin: 0; margin-top: 2px;">
                Mengubah detail transaksi rutin: <strong>{{ $recurring->description }}</strong>
            </p>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bunrek-card">
        <div class="bunrek-card-header">
            <h2 class="bunrek-card-title">Detail Transaksi Rutin</h2>
        </div>
        <div class="bunrek-card-body">
            <form action="{{ route('recurring.update', $recurring->recurring_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bunrek-form-group">
                    <label class="bunrek-label">Deskripsi / Nama</label>
                    <input type="text" name="description" class="bunrek-input" required value="{{ old('description', $recurring->description) }}">
                    @error('description') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                </div>

                <div class="bunrek-form-group text-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div id="editCategoryGroup">
                        <label class="bunrek-label">Kategori</label>
                        <select name="category_id" id="editCategory" class="bunrek-select">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                @if(!in_array($cat->category_id, [10, 11]))
                                    <option value="{{ $cat->category_id }}"
                                        {{ old('category_id', $recurring->category_id) == $cat->category_id ? 'selected' : '' }}>
                                        {{ $cat->category_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('category_id') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label class="bunrek-label">Tipe Transaksi</label>
                        <select name="amount_type" id="editAmountType" class="bunrek-select" required>
                            <option value="pengeluaran" {{ old('amount_type', $recurring->amount_type) === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                            <option value="pemasukan" {{ old('amount_type', $recurring->amount_type) === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        </select>
                        @error('amount_type') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="bunrek-form-group text-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div>
                        <label class="bunrek-label">Nominal (Rp)</label>
                        <input type="number" name="amount" class="bunrek-input" required min="1" value="{{ old('amount', intval($recurring->amount)) }}">
                        @error('amount') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label class="bunrek-label">Frekuensi Ulang</label>
                        <select name="frequency" class="bunrek-select" required id="editFrequency">
                            @foreach($frequencies as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('frequency', $recurring->frequency) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('frequency') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="bunrek-form-group text-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div>
                        <label class="bunrek-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="bunrek-input" required
                               value="{{ old('start_date', $recurring->start_date->format('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}"
                               id="editStartDate">
                        @error('start_date') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>

                    <div>
                        <label class="bunrek-label">Tanggal Berakhir <small style="color: var(--text-muted); font-weight: 500;">(Opsional)</small></label>
                        <input type="date" name="end_date" class="bunrek-input"
                               value="{{ old('end_date', $recurring->end_date ? $recurring->end_date->format('Y-m-d') : '') }}"
                               min="{{ date('Y-m-d') }}"
                               id="editEndDate">
                        @error('end_date') <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="preview-box" id="editPreview" style="display: none;">
                    <i class="bi bi-info-circle-fill"></i>
                    <span id="editPreviewText"></span>
                </div>

                @include('recurring.partials.reminder-form', ['isPremium' => $isPremium, 'reminder' => $reminder])

                <!-- Footer Buttons -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-xl); border-top: 1px solid var(--border-light); padding-top: var(--space-lg);">
                    <a href="{{ route('recurring.index') }}" class="btn-bunrek btn-outline" style="min-width: 100px;">
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function updateEditPreview() {
        const freq = $('#editFrequency').val();
        const startDate = $('#editStartDate').val();
        if (freq && startDate) {
            const freqLabels = { harian: 'setiap hari', mingguan: 'setiap minggu', bulanan: 'setiap bulan', tahunan: 'setiap tahun' };
            const d = new Date(startDate);
            const formatted = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            $('#editPreviewText').text('Akan tercatat otomatis ' + (freqLabels[freq] || freq) + ' mulai ' + formatted);
            $('#editPreview').show();
        } else {
            $('#editPreview').hide();
        }
    }
    
    $('#editStartDate').on('change', function() {
        const val = $(this).val();
        if (val) $('#editEndDate').attr('min', val);
        updateEditPreview();
    });
    $('#editFrequency').on('change', updateEditPreview);
    updateEditPreview();

    function toggleEditCategory() {
        if ($('#editAmountType').val() === 'pemasukan') {
            $('#editCategoryGroup').hide();
            $('#editCategory').prop('required', false).prop('disabled', true);
        } else {
            $('#editCategoryGroup').show();
            $('#editCategory').prop('required', true).prop('disabled', false);
        }
    }
    $('#editAmountType').on('change', toggleEditCategory);
    toggleEditCategory();
});
</script>
@endpush
