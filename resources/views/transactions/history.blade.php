@extends('layouts.master')

@section('page_title', 'Riwayat Transaksi')

@section('content')
<div class="bunrek-card mb-4">
    <div class="bunrek-card-header">
        <h2 class="bunrek-card-title">Filter Riwayat</h2>
    </div>
    <div class="bunrek-card-body">
        
        <form method="GET" action="{{ route('transactions.history') }}" id="filterForm">
            <!-- Alert container for validation errors -->
            <div class="bunrek-alert bunrek-alert-error" id="dateValidationError" style="display: none; margin-bottom: var(--space-md);">
                <i class="bi bi-exclamation-triangle-fill"></i> <span id="dateValidationErrorMsg"></span>
            </div>
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
                    <small id="categoryHelpText" style="color: var(--text-muted); display: none; margin-top: 4px;">
                        <i class="bi bi-info-circle"></i> Kategori hanya tersedia untuk Pengeluaran
                    </small>
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
                    <input type="date" name="start_date" id="start_date" class="bunrek-input" value="{{ request('start_date') }}" max="{{ \Carbon\Carbon::today()->toDateString() }}">
                </div>

                <div class="bunrek-form-group" style="margin-bottom: 0;">
                    <label for="end_date" class="bunrek-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="bunrek-input" value="{{ request('end_date') }}" max="{{ \Carbon\Carbon::today()->toDateString() }}">
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

<div id="balance-cards-container" style="margin-bottom: var(--space-lg);">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-md);">
        <div style="background: var(--bg-white); border-radius: var(--radius-lg); border: 1px solid var(--border-light); padding: var(--space-lg); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between; gap: var(--space-sm);">
            <div>
                <div style="font-size: var(--fs-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 6px;">Transaksi Ditemukan</div>
                <div style="font-size: var(--fs-2xl); font-weight: 800; color: var(--text-dark);">{{ $transactions->total() }}</div>
            </div>
            <div style="font-size: var(--fs-sm); color: var(--text-muted);">Jumlah transaksi yang sesuai filter saat ini.</div>
        </div>

        <div style="background: var(--bg-white); border-radius: var(--radius-lg); border: 1px solid var(--border-light); padding: var(--space-lg); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between; gap: var(--space-sm);">
            <div>
                <div style="font-size: var(--fs-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 6px;">{{ $summaryTitle }}</div>
                <div style="font-size: var(--fs-2xl); font-weight: 800; color: var(--text-dark);">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
            </div>
            <div style="font-size: var(--fs-sm); color: var(--text-muted);">Total nominal transaksi yang sesuai filter.</div>
        </div>
    </div>
</div>

<div class="bunrek-card">
    <div class="bunrek-card-header">
        <h2 class="bunrek-card-title">Daftar Transaksi</h2>
        @php
            $membershipFeature = app(\App\Features\MembershipFeatureInterface::class);
        @endphp
        <div style="display: flex; gap: var(--space-xs);">
            <a href="#" id="btnExportExcel" class="btn-bunrek btn-sm btn-outline text-success" style="border-color: rgba(16, 185, 129, 0.2); background: rgba(16, 185, 129, 0.05);">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="#" id="btnExportPdf" class="btn-bunrek btn-sm btn-outline text-danger" style="border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
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
                                <div style="display: inline-flex; gap: 4px; justify-content: flex-end; align-items: center;">
                                    <a href="{{ route('transactions.edit', $transaction->transaction_id) }}" class="btn-bunrek btn-sm btn-warning-sm" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('transactions.destroy', $transaction->transaction_id) }}" method="POST" onsubmit="return confirm('Hapus transaksi?')" style="margin: 0; padding: 0; display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-bunrek btn-sm btn-danger-sm" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; border: none;">
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
        
        <!-- Pagination Links -->
        <div id="pagination-container" style="padding: 0 var(--space-lg) var(--space-lg) var(--space-lg);">
            {{ $transactions->appends(request()->query())->links('components.pagination') }}
        </div>
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

    function updateCategoryState() {
        var transactionTypeId = document.getElementById('transactionType_id').value;
        var categorySelect = document.getElementById('category_id');
        var categoryHelpText = document.getElementById('categoryHelpText');

        if (transactionTypeId == '1') {
            // Pemasukan (Income) - disable kategori
            categorySelect.disabled = true;
            categorySelect.value = '';
            categorySelect.style.opacity = '0.4';
            categorySelect.style.cursor = 'not-allowed';
            categoryHelpText.style.display = 'block';
        } else {
            // Pengeluaran (Expense) atau Semua - enable kategori
            categorySelect.disabled = false;
            categorySelect.style.opacity = '1';
            categorySelect.style.cursor = 'auto';
            categoryHelpText.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateExportLinks();
        updateCategoryState();
    });

    document.getElementById('category_id').addEventListener('change', updateExportLinks);
    document.getElementById('transactionType_id').addEventListener('change', function () {
        updateExportLinks();
        updateCategoryState();
    });
    document.getElementById('start_date').addEventListener('change', updateExportLinks);
    document.getElementById('end_date').addEventListener('change', updateExportLinks);

    // Validate date inputs
    function validateDates() {
        var startDateInput = document.getElementById('start_date');
        var endDateInput = document.getElementById('end_date');
        var startDate = startDateInput.value;
        var endDate = endDateInput.value;
        var today = "{{ \Carbon\Carbon::today()->toDateString() }}";
        
        var errorDiv = document.getElementById('dateValidationError');
        var errorMsg = document.getElementById('dateValidationErrorMsg');

        // Hide old error
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }

        if (startDate && startDate > today) {
            if (errorMsg && errorDiv) {
                errorMsg.innerText = 'Tanggal Awal tidak boleh melebihi hari ini.';
                errorDiv.style.display = 'block';
            } else {
                alert('Tanggal Awal tidak boleh melebihi hari ini.');
            }
            startDateInput.value = today;
            return false;
        }
        if (endDate && endDate > today) {
            if (errorMsg && errorDiv) {
                errorMsg.innerText = 'Tanggal Akhir tidak boleh melebihi hari ini.';
                errorDiv.style.display = 'block';
            } else {
                alert('Tanggal Akhir tidak boleh melebihi hari ini.');
            }
            endDateInput.value = today;
            return false;
        }
        return true;
    }

    // AJAX pagination and filter handlers
    function attachAjaxHandlers() {
        // Hijack pagination link clicks
        $(document).off('click', '#pagination-container a').on('click', '#pagination-container a', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            loadHistoryPage(url);
        });

        // Hijack filter form submission to use AJAX
        $(document).off('submit', '#filterForm').on('submit', '#filterForm', function(e) {
            e.preventDefault();
            if (!validateDates()) {
                return;
            }
            
            var actionUrl = $(this).attr('action');
            var formData = $(this).serialize();
            var url = actionUrl + '?' + formData;
            
            loadHistoryPage(url);
        });
    }

    function loadHistoryPage(url) {
        $('.bunrek-table').css('opacity', '0.5');
        $('#balance-cards-container').css('opacity', '0.5');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                var htmlDom = $(response);
                
                // Update balance cards
                $('#balance-cards-container').html(htmlDom.find('#balance-cards-container').html());
                
                // Update table body
                $('.bunrek-table tbody').html(htmlDom.find('.bunrek-table tbody').html());
                
                // Update pagination
                $('#pagination-container').html(htmlDom.find('#pagination-container').html());
                
                // Restore opacity
                $('.bunrek-table').css('opacity', '1');
                $('#balance-cards-container').css('opacity', '1');
                
                // Update URL in history without reloading
                window.history.pushState({}, '', url);
                
                // Re-update export links to reflect current filters/page
                updateExportLinks();
            },
            error: function(xhr) {
                console.error("Gagal memuat halaman: ", xhr);
                alert("Terjadi kesalahan saat memuat data.");
                $('.bunrek-table').css('opacity', '1');
                $('#balance-cards-container').css('opacity', '1');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        attachAjaxHandlers();
    });

    document.getElementById('start_date').addEventListener('change', validateDates);
    document.getElementById('end_date').addEventListener('change', validateDates);
</script>
@endpush