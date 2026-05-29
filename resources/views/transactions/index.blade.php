@extends('layouts.master')

@section('page_title', 'Transactions')

@section('content')
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-label">Total Pemasukan</span>
                <div class="stat-card-icon income">
                    <i class="bi bi-arrow-down-left-circle"></i>
                </div>
            </div>
            <div class="stat-card-value text-income">
                Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-label">Total Pengeluaran</span>
                <div class="stat-card-icon expense">
                    <i class="bi bi-arrow-up-right-circle"></i>
                </div>
            </div>
            <div class="stat-card-value text-expense">
                Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>

    @php
        $membershipFeature = app(\App\Features\MembershipFeatureInterface::class);
    @endphp

    <div class="content-grid">

        <div class="bunrek-card">
            <div class="bunrek-card-header">
                <h2 class="bunrek-card-title">Tambah Transaksi</h2>
            </div>
            <div class="bunrek-card-body">
                @if (session('success'))
                    <div class="bunrek-alert bunrek-alert-success">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Tanggal</label>
                        <input type="date" name="date" class="bunrek-input" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Tipe Transaksi</label>
                        <div class="bunrek-radio-group">
                            <label class="bunrek-radio-label" for="income">
                                <input type="radio" name="type" value="income" id="income" checked
                                    onchange="toggleKategori()">
                                <span>Pemasukan</span>
                            </label>
                            <label class="bunrek-radio-label" for="expense">
                                <input type="radio" name="type" value="expense" id="expense"
                                    onchange="toggleKategori()">
                                <span>Pengeluaran</span>
                            </label>
                        </div>
                    </div>

                    <!-- Hidden input untuk kategori Income (ID: 10) -->
                    <input type="hidden" id="hiddenCategory" name="category" value="">

                    <!-- Kategori dropdown hanya untuk Pengeluaran -->
                    <div class="bunrek-form-group" id="kategoriGroup" style="display: none;">
                        <label class="bunrek-label">Kategori</label>
                        <select id="kategoriSelect" name="category" class="bunrek-select">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories ?? [] as $cat)
                                <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Jumlah (Rp)</label>
                        <input type="number" name="amount" class="bunrek-input" required placeholder="0" min="1">
                    </div>

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Deskripsi</label>
                        <textarea name="description" class="bunrek-textarea" required placeholder="Deskripsi transaksi..."></textarea>
                    </div>

                    <button type="submit" class="btn-bunrek btn-primary btn-w-full">
                        <i class="bi bi-plus-circle"></i> Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>


        <div class="bunrek-card">
            <div class="bunrek-card-header">
                <h2 class="bunrek-card-title">
                    Transaksi Hari Ini &mdash; <span
                        style="font-weight: 500; font-size: 0.95rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}</span>
                </h2>
                @if ($membershipFeature->canExportPdf())
                    <div style="display: flex; gap: var(--space-xs);">
                        <a href="{{ route('transactions.export.excel') }}"
                            class="btn-bunrek btn-sm btn-outline text-success" id="btnExportExcelTx"
                            style="border-color: rgba(16, 185, 129, 0.2); background: rgba(16, 185, 129, 0.05);">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        <a href="{{ route('transactions.export.pdf') }}"
                            class="btn-bunrek btn-sm btn-outline text-danger" id="btnExportPdfTx"
                            style="border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    </div>
                @else
                    <span
                        style="font-size: var(--fs-xs); color: var(--text-muted); background: var(--bg-light); padding: 4px 8px; border-radius: var(--radius-sm); border: 1px dashed var(--border-color);">
                        <i class="bi bi-gem text-warning"></i> Export Premium
                    </span>
                @endif
            </div>
            <div class="bunrek-card-body" style="padding: 0;">
                <div style="overflow-x: auto;">
                    <table class="bunrek-table">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions ?? [] as $t)
                                <tr>
                                    <td>
                                        @php
                                            $categoryName = \DB::table('category')
                                                ->where('category_id', $t->category_id)
                                                ->value('category_name');
                                        @endphp
                                        <strong>{{ $categoryName ?? '-' }}</strong>
                                    </td>
                                    <td>{{ $t->description }}</td>
                                    <td>
                                        @if ($t->transactionType_id == 1)
                                            <span class="badge-income">Pemasukan</span>
                                        @else
                                            <span class="badge-expense">Pengeluaran</span>
                                        @endif
                                    </td>
                                    <td
                                        style="font-weight: 600; color: {{ $t->transactionType_id == 1 ? 'var(--color-income)' : 'var(--color-expense)' }}">
                                        Rp {{ number_format($t->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="action-buttons-wrapper">
                                            <a href="{{ route('transactions.edit', $t->transaction_id) }}"
                                                class="btn-bunrek btn-sm btn-warning-sm action-btn">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('transactions.destroy', $t->transaction_id) }}"
                                                method="POST" class="action-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn-bunrek btn-sm btn-danger-sm action-btn"
                                                    onclick="openDeleteModal(this)">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="5">
                                        <div style="padding: var(--space-xl) 0; text-align: center;">
                                            <i class="bi bi-inbox"
                                                style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                            Belum ada transaksi hari ini
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Delete Confirmation -->
        <div id="deleteModal" class="delete-modal" style="display: none;">
            <div class="delete-modal-overlay" onclick="closeDeleteModal()"></div>
            <div class="delete-modal-content">
                <div class="delete-modal-header">
                    <h3 class="delete-modal-title">Konfirmasi Hapus</h3>
                    <button type="button" class="delete-modal-close" onclick="closeDeleteModal()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="delete-modal-body">
                    <div class="delete-modal-icon">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <p class="delete-modal-message">Anda yakin ingin menghapus transaksi ini?</p>
                    <p class="delete-modal-subtitle">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="delete-modal-footer">
                    <button type="button" class="btn-bunrek btn-outline" onclick="closeDeleteModal()">
                        Batal
                    </button>
                    <button type="button" class="btn-bunrek btn-danger" onclick="confirmDelete()">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                .delete-modal {
                    position: fixed;
                    inset: 0;
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    animation: modalFadeIn 0.2s ease-out;
                }

                .delete-modal-overlay {
                    position: absolute;
                    inset: 0;
                    background: rgba(0, 0, 0, 0.5);
                    backdrop-filter: blur(4px);
                    cursor: pointer;
                }

                .delete-modal-content {
                    position: relative;
                    background: var(--bg-white);
                    border-radius: var(--radius-lg);
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                    max-width: 420px;
                    width: 90%;
                    animation: modalSlideUp 0.3s ease-out;
                    overflow: hidden;
                }

                .delete-modal-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: var(--space-lg);
                    border-bottom: 1px solid var(--border-light);
                }

                .delete-modal-title {
                    margin: 0;
                    color: var(--text-dark);
                    font-family: var(--font-heading);
                    font-weight: 700;
                    font-size: var(--fs-lg);
                }

                .delete-modal-close {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    background: none;
                    border: none;
                    border-radius: var(--radius-md);
                    color: var(--text-muted);
                    cursor: pointer;
                    transition: var(--transition-fast);
                    font-size: 1.2rem;
                }

                .delete-modal-close:hover {
                    background: var(--bg-light);
                    color: var(--text-dark);
                }

                .delete-modal-body {
                    padding: var(--space-lg);
                    text-align: center;
                }

                .delete-modal-icon {
                    font-size: 2.5rem;
                    color: var(--color-expense);
                    margin-bottom: var(--space-md);
                    line-height: 1;
                }

                .delete-modal-message {
                    margin: 0 0 var(--space-sm) 0;
                    color: var(--text-dark);
                    font-weight: 500;
                    font-size: var(--fs-base);
                }

                .delete-modal-subtitle {
                    margin: 0;
                    color: var(--text-muted);
                    font-size: var(--fs-sm);
                }

                .delete-modal-footer {
                    display: flex;
                    justify-content: flex-end;
                    gap: var(--space-md);
                    padding: var(--space-lg);
                    border-top: 1px solid var(--border-light);
                    background: var(--bg-light);
                }

                .btn-danger {
                    background: var(--color-expense);
                    color: white;
                    border: 1px solid var(--color-expense);
                }

                .btn-danger:hover {
                    background: #dc2626;
                    border-color: #dc2626;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
                }

                @keyframes modalFadeIn {
                    from {
                        opacity: 0;
                    }

                    to {
                        opacity: 1;
                    }
                }

                @keyframes modalSlideUp {
                    from {
                        transform: translateY(20px);
                        opacity: 0;
                    }

                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }

                /* Action Buttons Alignment */
                .action-buttons-wrapper {
                    display: flex;
                    gap: 8px;
                    justify-content: flex-end;
                    align-items: stretch;
                }

                .action-form {
                    margin: 0;
                    padding: 0;
                    display: flex;
                    align-items: stretch;
                }

                .action-btn {
                    height: 32px;
                    padding: 0 12px !important;
                    display: inline-flex !important;
                    align-items: center;
                    justify-content: center;
                    gap: 6px;
                    margin: 0 !important;
                    line-height: 1;
                    white-space: nowrap;
                    border: none;
                    font-size: var(--fs-xs);
                    font-weight: 600;
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                function toggleKategori() {
                    const expenseRadio = document.getElementById('expense');
                    const kategoriGroup = document.getElementById('kategoriGroup');
                    const kategoriSelect = document.getElementById('kategoriSelect');
                    const hiddenCategory = document.getElementById('hiddenCategory');

                    if (expenseRadio.checked) {
                        // Pengeluaran: tampilkan dropdown
                        kategoriGroup.style.display = 'block';
                        kategoriSelect.setAttribute('required', 'required');
                        hiddenCategory.name = ''; // Disable hidden input
                        kategoriSelect.name = 'category'; // Enable dropdown
                    } else {
                        // Pemasukan: gunakan category ID 10 (income)
                        kategoriGroup.style.display = 'none';
                        kategoriSelect.removeAttribute('required');
                        kategoriSelect.value = '';
                        hiddenCategory.value = '10'; // Set category ID to 10
                        hiddenCategory.name = 'category'; // Enable hidden input
                        kategoriSelect.name = ''; // Disable dropdown
                    }
                }
                let pendingDeleteForm = null;

                function openDeleteModal(button) {
                    pendingDeleteForm = button.closest('form');
                    const modal = document.getElementById('deleteModal');
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }

                function closeDeleteModal() {
                    const modal = document.getElementById('deleteModal');
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                    pendingDeleteForm = null;
                }

                function confirmDelete() {
                    if (pendingDeleteForm) {
                        pendingDeleteForm.submit();
                    }
                }

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        const modal = document.getElementById('deleteModal');
                        if (modal && modal.style.display === 'flex') {
                            closeDeleteModal();
                        }
                    }
                });
                document.addEventListener('DOMContentLoaded', function() {
                    toggleKategori(); // Set initial state
                });
            </script>
        @endpush

    @endsection
