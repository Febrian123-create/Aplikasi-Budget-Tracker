@extends('layouts.master')

@push('styles')
<style>
.recurring-card {
    background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0; padding: 20px 24px; transition: all 0.3s; margin-bottom: 16px;
}
.recurring-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.10); transform: translateY(-2px); }
.recurring-card.due-today { border-left: 4px solid #ff9800; }
.recurring-card.due-tomorrow { border-left: 4px solid #2196f3; }

.status-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
.status-aktif { background: #e8f5e9; color: #2e7d32; }
.status-dijeda { background: #fff3e0; color: #e65100; }
.status-selesai { background: #f5f5f5; color: #757575; }

.type-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.type-pemasukan { background: rgba(46,125,50,0.1); color: #2e7d32; }
.type-pengeluaran { background: rgba(198,40,40,0.1); color: #c62828; }

.freq-badge { padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 500; background: #ede7f6; color: #5e35b1; }

.due-alert { font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px; margin-top: 6px; }
.due-alert.today { color: #e65100; }
.due-alert.tomorrow { color: #1565c0; }

.section-title { font-size: 18px; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 10px; }
.section-title i { color: #6366f1; }

.empty-state { text-align: center; padding: 48px 24px; color: #9e9e9e; }
.empty-state .empty-icon { font-size: 56px; margin-bottom: 16px; opacity: 0.4; }
.empty-state .cta-btn {
    display: inline-block; padding: 10px 28px; background: #6366f1; color: #fff;
    border-radius: 10px; text-decoration: none; font-weight: 600; transition: background 0.2s;
}
.empty-state .cta-btn:hover { background: #4f46e5; color: #fff; }

.info-banner {
    border-radius: 12px; padding: 14px 20px; display: flex; align-items: center; gap: 12px;
    font-weight: 500; margin-bottom: 20px; font-size: 14px;
}
.info-banner.success { background: linear-gradient(135deg,#e8f5e9,#c8e6c9); color: #2e7d32; border: 1px solid #a5d6a7; }
.info-banner.warning { background: linear-gradient(135deg,#fff3e0,#ffe0b2); color: #e65100; border: 1px solid #ffb74d; }
.info-banner.error { background: linear-gradient(135deg,#ffebee,#ffcdd2); color: #c62828; border: 1px solid #ef9a9a; }
.info-banner.info { background: linear-gradient(135deg,#e3f2fd,#bbdefb); color: #1565c0; border: 1px solid #90caf9; }

.metric-mini { border-radius: 14px; padding: 18px 20px; text-align: center; }
.metric-mini .metric-val { font-size: 28px; font-weight: 700; }
.metric-mini .metric-lbl { font-size: 12px; font-weight: 500; opacity: 0.8; margin-top: 4px; }
.mc-total { background: linear-gradient(135deg,#ede7f6,#d1c4e9); color: #4527a0; }
.mc-active { background: linear-gradient(135deg,#e8f5e9,#c8e6c9); color: #2e7d32; }
.mc-paused { background: linear-gradient(135deg,#fff3e0,#ffe0b2); color: #e65100; }

.card-actions { display: flex; gap: 6px; flex-wrap: wrap; }
.card-actions .btn { font-size: 12px; padding: 5px 12px; border-radius: 8px; }

.recurring-amount { font-size: 20px; font-weight: 700; }
.recurring-desc { font-size: 15px; font-weight: 600; color: #1a1a2e; margin-bottom: 4px; }
.recurring-meta { font-size: 13px; color: #757575; }

.form-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #f0f0f0; padding: 28px; }
.form-card .form-label { font-weight: 600; font-size: 13px; color: #333; }
.form-card .form-control, .form-card .form-select { border-radius: 10px; border: 1px solid #e0e0e0; padding: 10px 14px; font-size: 14px; }
.form-card .form-control:focus, .form-card .form-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }

.preview-box {
    background: linear-gradient(135deg,#f3f0ff,#ede7f6); border-radius: 10px; padding: 14px 18px;
    font-size: 13px; color: #5e35b1; font-weight: 500; margin-top: 12px; display: none;
}
.preview-box i { margin-right: 6px; }

.upgrade-card {
    background: linear-gradient(135deg,#6366f1,#8b5cf6); color: #fff; border-radius: 14px;
    padding: 20px 24px; text-align: center; margin-bottom: 20px;
}
.upgrade-card h6 { font-weight: 700; margin-bottom: 4px; }
.upgrade-card p { font-size: 13px; opacity: 0.9; margin-bottom: 0; }

.category-warning { background: #fff3e0; border: 1px solid #ffb74d; border-radius: 8px; padding: 6px 12px; font-size: 12px; color: #e65100; margin-top: 6px; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-inner">
        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap:12px;">
            <div>
                <h3 class="fw-bold mb-1" style="color:#1a1a2e;">Transaksi Rutin</h3>
                <p class="text-muted mb-0">Kelola transaksi otomatis berulang
                    @if(!$isPremium)
                        <span class="badge bg-secondary ms-1">Free</span>
                    @else
                        <span class="badge ms-1" style="background:#6366f1;">Premium</span>
                    @endif
                </p>
            </div>
            @if($canCreate)
            <button class="btn btn-primary" style="border-radius:10px;padding:10px 24px;font-weight:600;background:#6366f1;border:none;" data-bs-toggle="modal" data-bs-target="#addRecurringModal">
                <i class="fas fa-plus me-1"></i> Tambah Recurring
            </button>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="info-banner success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="info-banner error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if($executedCount > 0)
            <div class="info-banner info"><i class="fas fa-sync-alt"></i> {{ $executedCount }} transaksi rutin telah dicatat otomatis untuk periode yang terlewat.</div>
        @endif

        {{-- Membership Limit Warning --}}
        @if(!$isPremium && $activeCount >= $maxFreeRecurring)
            <div class="upgrade-card">
                <h6><i class="fas fa-crown me-1"></i> Batas Recurring Tercapai</h6>
                <p>Kamu sudah punya {{ $activeCount }}/{{ $maxFreeRecurring }} recurring aktif. Upgrade ke Premium untuk unlimited recurring & semua frekuensi.</p>
            </div>
        @endif

        {{-- Mini Metrics --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="metric-mini mc-total">
                    <div class="metric-val">{{ $recurrings->count() }}</div>
                    <div class="metric-lbl">Total Recurring</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-mini mc-active">
                    <div class="metric-val">{{ $recurrings->where('status','aktif')->count() }}</div>
                    <div class="metric-lbl">Aktif</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-mini mc-paused">
                    <div class="metric-val">{{ $recurrings->where('status','dijeda')->count() }}</div>
                    <div class="metric-lbl">Dijeda</div>
                </div>
            </div>
        </div>

        {{-- Recurring List --}}
        @if($recurrings->isEmpty())
            <div class="form-card">
                <div class="empty-state">
                    <div class="empty-icon">🔄</div>
                    <p>Belum ada transaksi rutin. Tambahkan yang pertama!</p>
                    @if($canCreate)
                    <button class="cta-btn" data-bs-toggle="modal" data-bs-target="#addRecurringModal">Tambah Recurring</button>
                    @endif
                </div>
            </div>
        @else
            @foreach($recurrings as $rec)
                @php
                    $isToday = $rec->next_run_date && $rec->next_run_date->isToday();
                    $isTomorrow = $rec->next_run_date && $rec->next_run_date->isTomorrow();
                    $categoryMissing = !$rec->category;
                @endphp
                <div class="recurring-card {{ $isToday ? 'due-today' : ($isTomorrow ? 'due-tomorrow' : '') }}">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="recurring-desc">{{ $rec->description }}</div>
                            <div class="recurring-meta">
                                <span class="type-badge type-{{ $rec->amount_type }}">{{ ucfirst($rec->amount_type) }}</span>
                                <span class="freq-badge ms-1">{{ \App\Helpers\RecurringHelper::getFrequencyLabel($rec->frequency) }}</span>
                                @if($rec->category)
                                    <span class="ms-1">📁 {{ $rec->category->category_name }}</span>
                                @endif
                            </div>
                            @if($categoryMissing)
                                <div class="category-warning"><i class="fas fa-exclamation-triangle me-1"></i> Kategori tidak ditemukan — pilih kategori baru.</div>
                            @endif
                            @if($isToday && $rec->status === 'aktif')
                                <div class="due-alert today"><i class="fas fa-bolt"></i> Akan dieksekusi hari ini</div>
                            @elseif($isTomorrow && $rec->status === 'aktif')
                                <div class="due-alert tomorrow"><i class="fas fa-clock"></i> Besok: {{ $rec->description }}</div>
                            @endif
                        </div>
                        <div class="col-md-3 text-md-center my-2 my-md-0">
                            <div class="recurring-amount {{ $rec->amount_type === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($rec->amount, 0, ',', '.') }}
                            </div>
                            <div class="recurring-meta">
                                Next: {{ $rec->next_run_date ? $rec->next_run_date->format('d M Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-2 text-md-center my-2 my-md-0">
                            <span class="status-badge status-{{ $rec->status }}">{{ ucfirst($rec->status) }}</span>
                        </div>
                        <div class="col-md-2 text-md-end">
                            <div class="card-actions justify-content-md-end">
                                @if($rec->status !== 'selesai')
                                <a href="{{ route('recurring.edit', $rec->recurring_id) }}" class="btn btn-outline-primary btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('recurring.toggle', $rec->recurring_id) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $rec->status === 'aktif' ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $rec->status === 'aktif' ? 'Jeda' : 'Aktifkan' }}">
                                        <i class="fas {{ $rec->status === 'aktif' ? 'fa-pause' : 'fa-play' }}"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('recurring.destroy', $rec->recurring_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Transaksi yang sudah tercatat tidak akan terhapus. Lanjutkan?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- Modal Tambah Recurring --}}
<div class="modal fade" id="addRecurringModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0;padding:20px 28px;">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2" style="color:#6366f1;"></i>Tambah Transaksi Rutin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('recurring.store') }}" method="POST" id="addRecurringForm">
                @csrf
                <div class="modal-body" style="padding:28px;">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nama / Deskripsi</label>
                            <input type="text" name="description" class="form-control" required placeholder="Contoh: Bayar kos" value="{{ old('description') }}">
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}" {{ old('category_id') == $cat->category_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipe</label>
                            <select name="amount_type" class="form-select" required>
                                <option value="pengeluaran" {{ old('amount_type','pengeluaran') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                                <option value="pemasukan" {{ old('amount_type') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="number" name="amount" class="form-control" required min="1" placeholder="0" value="{{ old('amount') }}">
                            @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Frekuensi</label>
                            <select name="frequency" class="form-select" required id="addFrequency">
                                @foreach($frequencies as $val => $label)
                                    <option value="{{ $val }}" {{ old('frequency') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('frequency') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ old('start_date', date('Y-m-d')) }}" id="addStartDate">
                            @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Berakhir <small class="text-muted">(opsional)</small></label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" id="addEndDate">
                            @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="preview-box" id="addPreview">
                        <i class="fas fa-info-circle"></i> <span id="addPreviewText"></span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0;padding:16px 28px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:10px;background:#6366f1;border:none;padding:10px 28px;font-weight:600;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview text update
    function updatePreview() {
        const freq = $('#addFrequency').val();
        const startDate = $('#addStartDate').val();
        if (freq && startDate) {
            const freqLabels = { harian:'setiap hari', mingguan:'setiap minggu', bulanan:'setiap bulan', tahunan:'setiap tahun' };
            const d = new Date(startDate);
            const formatted = d.toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});
            $('#addPreviewText').text('Akan tercatat otomatis ' + (freqLabels[freq]||freq) + ' mulai ' + formatted);
            $('#addPreview').show();
        } else {
            $('#addPreview').hide();
        }
    }
    $('#addFrequency, #addStartDate').on('change', updatePreview);
    updatePreview();

    // Auto-dismiss flash messages
    setTimeout(function() { $('.info-banner').fadeOut(500); }, 5000);
});
</script>
@endpush
