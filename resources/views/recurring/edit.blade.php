@extends('layouts.master')

@push('styles')
<style>
.form-card {
    background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0; padding: 28px; max-width: 700px; margin: 0 auto;
}
.form-card .form-label { font-weight: 600; font-size: 13px; color: #333; }
.form-card .form-control, .form-card .form-select {
    border-radius: 10px; border: 1px solid #e0e0e0; padding: 10px 14px; font-size: 14px;
}
.form-card .form-control:focus, .form-card .form-select:focus {
    border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}
.section-title {
    font-size: 18px; font-weight: 700; color: #1a1a2e;
    display: flex; align-items: center; gap: 10px;
}
.section-title i { color: #6366f1; }
.preview-box {
    background: linear-gradient(135deg,#f3f0ff,#ede7f6); border-radius: 10px;
    padding: 14px 18px; font-size: 13px; color: #5e35b1; font-weight: 500; margin-top: 16px;
}
.preview-box i { margin-right: 6px; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-center mb-4" style="gap:12px;">
            <a href="{{ route('recurring.index') }}" class="btn btn-light" style="border-radius:10px;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0" style="color:#1a1a2e;">Edit Transaksi Rutin</h3>
                <p class="text-muted mb-0">{{ $recurring->description }}</p>
            </div>
        </div>

        <div class="form-card">
            <form action="{{ route('recurring.update', $recurring->recurring_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama / Deskripsi</label>
                        <input type="text" name="description" class="form-control" required
                               value="{{ old('description', $recurring->description) }}">
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}"
                                    {{ old('category_id', $recurring->category_id) == $cat->category_id ? 'selected' : '' }}>
                                    {{ $cat->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipe</label>
                        <select name="amount_type" class="form-select" required>
                            <option value="pengeluaran" {{ old('amount_type', $recurring->amount_type) === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                            <option value="pemasukan" {{ old('amount_type', $recurring->amount_type) === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" name="amount" class="form-control" required min="1"
                               value="{{ old('amount', intval($recurring->amount)) }}">
                        @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Frekuensi</label>
                        <select name="frequency" class="form-select" required id="editFrequency">
                            @foreach($frequencies as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('frequency', $recurring->frequency) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('frequency') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" required
                               value="{{ old('start_date', $recurring->start_date->format('Y-m-d')) }}" id="editStartDate">
                        @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Berakhir <small class="text-muted">(opsional)</small></label>
                        <input type="date" name="end_date" class="form-control"
                               value="{{ old('end_date', $recurring->end_date ? $recurring->end_date->format('Y-m-d') : '') }}" id="editEndDate">
                        @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="preview-box" id="editPreview">
                    <i class="fas fa-info-circle"></i> <span id="editPreviewText"></span>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('recurring.index') }}" class="btn btn-light" style="border-radius:10px;">Batal</a>
                    <button type="submit" class="btn btn-primary" style="border-radius:10px;background:#6366f1;border:none;padding:10px 28px;font-weight:600;">
                        Simpan Perubahan
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
            const freqLabels = {harian:'setiap hari',mingguan:'setiap minggu',bulanan:'setiap bulan',tahunan:'setiap tahun'};
            const d = new Date(startDate);
            const formatted = d.toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});
            $('#editPreviewText').text('Akan tercatat otomatis ' + (freqLabels[freq]||freq) + ' mulai ' + formatted);
            $('#editPreview').show();
        } else {
            $('#editPreview').hide();
        }
    }
    $('#editFrequency, #editStartDate').on('change', updateEditPreview);
    updateEditPreview();
});
</script>
@endpush
