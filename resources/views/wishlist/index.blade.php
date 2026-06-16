@extends('layouts.master')

@section('page_title', 'Wishlist')

@section('content')
    <!-- Summary Cards -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-lg); margin-bottom: var(--space-lg);">
        <!-- Total Active Wishlists Card -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-lg); background: var(--bg-white); border-radius: var(--radius-lg); border-left: 5px solid var(--primary-color); box-shadow: var(--shadow-sm);">
            <div style="flex: 1;">
                <p
                    style="margin: 0 0 var(--space-xs) 0; color: var(--text-muted); font-size: var(--fs-sm); font-weight: 500;">
                    Wishlist Aktif
                </p>
                <p style="margin: 0; font-size: 1.75rem; font-weight: 700; color: var(--primary-color);">
                    {{ $totalActiveWishlists }}
                </p>
            </div>
            <div style="font-size: 3rem; color: rgba(88, 86, 214, 0.2); line-height: 1; margin-left: var(--space-lg);">
                <i class="bi bi-heart"></i>
            </div>
        </div>

        <!-- Total Remaining Needed Card -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-lg); background: var(--bg-white); border-radius: var(--radius-lg); border-left: 5px solid var(--color-warning); box-shadow: var(--shadow-sm);">
            <div style="flex: 1;">
                <p
                    style="margin: 0 0 var(--space-xs) 0; color: var(--text-muted); font-size: var(--fs-sm); font-weight: 500;">
                    Dana Diperlukan
                </p>
                <p style="margin: 0; font-size: 1.75rem; font-weight: 700; color: var(--color-warning);">
                    Rp {{ number_format($totalRemainingNeeded, 0, ',', '.') }}
                </p>
            </div>
            <div style="font-size: 3rem; color: rgba(251, 146, 60, 0.2); line-height: 1; margin-left: var(--space-lg);">
                <i class="bi bi-piggy-bank"></i>
            </div>
        </div>

        <!-- Saving Balance Card -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-lg); background: var(--bg-white); border-radius: var(--radius-lg); border-left: 5px solid var(--color-income); box-shadow: var(--shadow-sm);">
            <div style="flex: 1;">
                <p
                    style="margin: 0 0 var(--space-xs) 0; color: var(--text-muted); font-size: var(--fs-sm); font-weight: 500;">
                    Saving Balance
                </p>
                <p style="margin: 0; font-size: 1.75rem; font-weight: 700; color: var(--color-income);">
                    Rp {{ number_format($totalAllocated, 0, ',', '.') }}
                </p>
            </div>
            <div style="font-size: 3rem; color: rgba(52, 199, 89, 0.2); line-height: 1; margin-left: var(--space-lg);">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>

        <!-- Available Balance Card -->
        
    </div>

    <!-- Toast Notifikasi Alokasi -->
    <div id="wishlistGlobalToast" class="bunrek-alert bunrek-alert-error"
        style="display: none; position: fixed; top: 20px; right: 20px; z-index: 10050; min-width: 300px; max-width: 360px; box-shadow: 0 18px 40px rgba(0,0,0,0.12);">
        <i class="bi bi-exclamation-circle-fill"></i> <span id="wishlistGlobalToastMessage"></span>
    </div>

    <!-- Status Filter Control -->
    <div class="bunrek-card" style="margin-bottom: var(--space-lg);">
        <div class="bunrek-card-body" style="padding: var(--space-md) var(--space-lg);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); flex-wrap: wrap;">
                <span style="font-weight: 600; color: var(--text-dark); margin-right: var(--space-xs);">Filter
                    Status:</span>
                <button type="button" class="filter-btn active" onclick="filterStatus('all')">Semua</button>
                <button type="button" class="filter-btn" onclick="filterStatus('aktif')">Aktif</button>
                <button type="button" class="filter-btn" onclick="filterStatus('tercapai')">Tercapai</button>
                <button type="button" class="filter-btn" onclick="filterStatus('dibatalkan')">Dibatalkan</button>
                <button type="button" class="filter-btn" onclick="filterStatus('dibeli')">Dibeli</button>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Form Tambah Wishlist -->
        <div class="bunrek-card">
            <div class="bunrek-card-header">
                <h2 class="bunrek-card-title">Tambah Wishlist</h2>
            </div>
            <div class="bunrek-card-body">
                @if (session('success'))
                    <div class="bunrek-alert bunrek-alert-success">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bunrek-alert bunrek-alert-danger">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bunrek-alert bunrek-alert-danger">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('wishlist.store') }}" method="POST">
                    @csrf

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Nama Wishlist</label>
                        <input type="text" name="nama" class="bunrek-input @error('nama') is-invalid @enderror"
                            required placeholder="Contoh: Liburan ke Bali" value="{{ old('nama') }}">
                        @error('nama')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Target Harga (Rp)</label>
                        <input type="number" name="target_harga"
                            class="bunrek-input @error('target_harga') is-invalid @enderror" required
                            placeholder="Minimum Rp 1.000" min="1000" value="{{ old('target_harga') }}">
                        @error('target_harga')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Deadline (Opsional)</label>
                        <input type="date" name="deadline" class="bunrek-input @error('deadline') is-invalid @enderror"
                            value="{{ old('deadline') }}" min="{{ now()->toDateString() }}">
                        @error('deadline')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Catatan (Opsional)</label>
                        <textarea name="catatan" class="bunrek-textarea" placeholder="Tambahkan catatan tentang wishlist ini...">{{ old('catatan') }}</textarea>
                    </div>

                    <button type="submit" class="btn-bunrek btn-primary btn-w-full">
                        <i class="bi bi-plus-circle"></i> Simpan Wishlist
                    </button>
                </form>
            </div>
        </div>

        <!-- Grid Wishlist -->
        <div>
            <h3 style="margin-bottom: var(--space-lg); font-size: var(--fs-lg); font-weight: 700; color: var(--text-dark);">
                Daftar Wishlist
            </h3>

            <div id="wishlistGrid"
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--space-lg);">
                @forelse($wishlists as $wishlist)
                    <div class="bunrek-card wishlist-card" data-status="{{ $wishlist->status }}"
                        style="display: flex; flex-direction: column; cursor: pointer; transition: all 0.3s ease;">
                        <div class="bunrek-card-body" onclick="toggleAccordion({{ $wishlist->id }})"
                            style="flex: 1; user-select: none;">
                            <!-- Header dengan nama, expand icon, dan badge status -->
                            <div
                                style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-sm);">
                                <h4
                                    style="margin: 0; flex: 1; color: var(--text-dark); font-size: var(--fs-base); font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                    <span id="accordion-icon-{{ $wishlist->id }}" class="accordion-icon"
                                        style="transition: transform 0.3s ease; color: var(--primary-color);">
                                        <i class="bi bi-plus-lg"></i>
                                    </span>
                                    {{ $wishlist->nama }}
                                </h4>
                                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                    @if ($wishlist->status === 'tercapai')
                                        <span
                                            style="background: var(--color-income); color: white; padding: 4px 10px; border-radius: var(--radius-base); font-size: var(--fs-xs); font-weight: 600; white-space: nowrap;">
                                            <i class="bi bi-check-circle"></i> Tercapai
                                        </span>
                                    @elseif ($wishlist->status === 'dibeli')
                                        <span
                                            style="background: var(--primary-color); color: white; padding: 4px 10px; border-radius: var(--radius-base); font-size: var(--fs-xs); font-weight: 600; white-space: nowrap;">
                                            <i class="bi bi-bag-check"></i> Dibeli
                                        </span>
                                    @elseif ($wishlist->status === 'dibatalkan')
                                        <span
                                            style="background: var(--text-muted); color: white; padding: 4px 10px; border-radius: var(--radius-base); font-size: var(--fs-xs); font-weight: 600; white-space: nowrap;">
                                            <i class="bi bi-x-circle"></i> Dibatalkan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Target info -->
                            <div
                                style="margin-bottom: var(--space-sm); display: flex; justify-content: space-between; font-size: var(--fs-sm);">
                                <span style="color: var(--text-muted);">Target:</span>
                                <span style="color: var(--text-dark); font-weight: 600;">Rp
                                    {{ number_format($wishlist->target_harga, 0, ',', '.') }}</span>
                            </div>

                            <!-- Progress Bar Singkat -->
                            <div>
                                <div
                                    style="background: var(--bg-light); border-radius: 20px; height: 6px; overflow: hidden; display: flex;">
                                    <div
                                        style="background: linear-gradient(90deg, var(--color-income), var(--primary-color)); height: 100%; width: {{ $wishlist->prosesan }}%; transition: width 0.3s ease;">
                                    </div>
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                                    <span style="color: var(--text-muted); font-size: var(--fs-xs);">Progress</span>
                                    <span
                                        style="color: var(--text-dark); font-size: var(--fs-xs); font-weight: 600;">{{ $wishlist->prosesan }}%</span>
                                </div>
                            </div>

                            <!-- Detail Tersembunyi (Accordion) -->
                            <div id="accordion-content-{{ $wishlist->id }}" class="accordion-content"
                                style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; margin-top: 0;">
                                <div
                                    style="padding-top: var(--space-md); border-top: 1px solid var(--border-light); margin-top: var(--space-md); display: flex; flex-direction: column; gap: var(--space-sm);">
                                    <div style="display: flex; justify-content: space-between; font-size: var(--fs-sm);">
                                        <span style="color: var(--text-muted);">Dialokasikan:</span>
                                        <span style="color: var(--color-income); font-weight: 600;">Rp
                                            {{ number_format($wishlist->allocated_amount, 0, ',', '.') }}</span>
                                    </div>
                                    @if ($wishlist->deadline)
                                        <div
                                            style="display: flex; justify-content: space-between; font-size: var(--fs-sm);">
                                            <span style="color: var(--text-muted);">Deadline:</span>
                                            <span style="color: var(--text-dark); font-weight: 500;">
                                                {{ \Carbon\Carbon::parse($wishlist->deadline)->translatedFormat('d F Y') }}
                                            </span>
                                        </div>
                                    @endif
                                    @if ($wishlist->catatan)
                                        <div
                                            style="padding: 8px 12px; background: var(--bg-light); border-radius: var(--radius-base); color: var(--text-muted); font-size: var(--fs-xs); border-left: 3px solid var(--primary-color);">
                                            <strong>Catatan:</strong><br>
                                            {{ $wishlist->catatan }}
                                        </div>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div style="display: flex; gap: var(--space-sm); margin-top: var(--space-sm);"
                                        onclick="event.stopPropagation()">
                                        @if ($wishlist->status === 'aktif')
                                            <button type="button" class="btn-bunrek btn-sm btn-primary" style="flex: 1;"
                                                onclick="openAlokasiModal({{ $wishlist->id }}, '{{ $wishlist->nama }}', {{ $wishlist->target_harga - $wishlist->allocated_amount }})">
                                                <i class="bi bi-plus-circle"></i> Alokasi
                                            </button>
                                            <button type="button" class="btn-bunrek btn-sm btn-outline"
                                                style="flex: 1; color: var(--color-expense); border-color: var(--color-expense);"
                                                onclick="openBatalModal({{ $wishlist->id }}, '{{ $wishlist->nama }}', {{ $wishlist->allocated_amount }})">
                                                <i class="bi bi-x-circle"></i> Batalkan
                                            </button>
                                        @elseif ($wishlist->status === 'tercapai')
                                            <button type="button" class="btn-bunrek btn-sm"
                                                style="flex: 1; background-color: var(--color-income); color: white;"
                                                onclick="openKonfirmasiModal({{ $wishlist->id }}, '{{ $wishlist->nama }}')">
                                                <i class="bi bi-bag-check"></i> Konfirmasi Beli
                                            </button>
                                            <button type="button" class="btn-bunrek btn-sm btn-outline"
                                                style="flex: 1; color: var(--color-expense); border-color: var(--color-expense);"
                                                onclick="openBatalModal({{ $wishlist->id }}, '{{ $wishlist->nama }}', {{ $wishlist->allocated_amount }})">
                                                <i class="bi bi-x-circle"></i> Batalkan
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        style="grid-column: 1 / -1; padding: var(--space-xl) 0; text-align: center; color: var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
                        <p>Belum ada wishlist. Buat yang pertama sekarang!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Alokasi Dana -->
    <div id="alokasiModal" class="delete-modal" style="display: none;">
        <div class="delete-modal-overlay" onclick="closeAlokasiModal()"></div>
        <div class="delete-modal-content">
            <div class="delete-modal-header">
                <h3 class="delete-modal-title">Alokasi Dana Wishlist</h3>
                <button type="button" class="delete-modal-close" onclick="closeAlokasiModal()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="delete-modal-body" style="text-align: left;">
                <p id="modalWishlistName"
                    style="margin: 0 0 var(--space-md) 0; color: var(--text-muted); font-weight: 500;"></p>
                <div id="modalSisaKekurangan"
                    style="margin-bottom: var(--space-md); font-size: var(--fs-sm); color: var(--text-dark);"></div>
                <form id="alokasiForm" method="POST">
                    @csrf
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Jumlah Alokasi (Rp)</label>
                        <input type="number" name="jumlah" id="jumlahInput" class="bunrek-input" required
                            placeholder="Minimum Rp 1.000" min="1000" step="1000" value="{{ old('jumlah') }}">
                    </div>
                </form>
            </div>
            <div class="delete-modal-footer">
                <button type="button" class="btn-bunrek btn-outline" onclick="closeAlokasiModal()">
                    Batal
                </button>
                <button type="button" class="btn-bunrek btn-primary" onclick="submitAlokasiForm()">
                    <i class="bi bi-check-circle"></i> Alokasikan
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Batal Custom -->
    <div id="batalModal" class="delete-modal" style="display: none;">
        <div class="delete-modal-overlay" onclick="closeBatalModal()"></div>
        <div class="delete-modal-content">
            <div class="delete-modal-header">
                <h3 class="delete-modal-title">Batalkan Wishlist</h3>
                <button type="button" class="delete-modal-close" onclick="closeBatalModal()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="delete-modal-body" style="text-align: left;">
                <p id="batalModalMessage" style="margin: 0; color: var(--text-dark); line-height: 1.5;"></p>
            </div>
            <div class="delete-modal-footer">
                <button type="button" class="btn-bunrek btn-outline" onclick="closeBatalModal()">
                    Tutup
                </button>
                <form id="batalForm" method="POST" style="margin: 0; display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-bunrek"
                        style="background-color: var(--color-expense); color: white;">
                        Batalkan Wishlist
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Pembelian Custom -->
    <div id="konfirmasiModal" class="delete-modal" style="display: none;">
        <div class="delete-modal-overlay" onclick="closeKonfirmasiModal()"></div>
        <div class="delete-modal-content">
            <div class="delete-modal-header">
                <h3 class="delete-modal-title">Konfirmasi Pembelian</h3>
                <button type="button" class="delete-modal-close" onclick="closeKonfirmasiModal()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="delete-modal-body" style="text-align: left;">
                <p id="konfirmasiModalMessage" style="margin: 0; color: var(--text-dark); line-height: 1.5;"></p>
            </div>
            <div class="delete-modal-footer">
                <button type="button" class="btn-bunrek btn-outline" onclick="closeKonfirmasiModal()">
                    Batal
                </button>
                <form id="konfirmasiForm" method="POST" style="margin: 0; display: inline-block;">
                    @csrf
                    <button type="submit" class="btn-bunrek"
                        style="background-color: var(--color-income); color: white;">
                        Konfirmasi Pembelian
                    </button>
                </form>
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
                max-width: 440px;
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
            }

            .delete-modal-footer {
                display: flex;
                justify-content: flex-end;
                gap: var(--space-md);
                padding: var(--space-lg);
                border-top: 1px solid var(--border-light);
                background: var(--bg-light);
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

            .is-invalid {
                border-color: var(--color-expense) !important;
            }

            .invalid-feedback {
                display: block;
                color: var(--color-expense);
                font-size: var(--fs-xs);
                margin-top: 4px;
            }

            /* Filter buttons */
            .filter-btn {
                background: var(--bg-light);
                border: 1px solid var(--border-light);
                color: var(--text-muted);
                padding: 6px 16px;
                border-radius: 20px;
                font-size: var(--fs-sm);
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .filter-btn:hover {
                background: var(--primary-light);
                color: var(--primary-color);
            }

            .filter-btn.active {
                background: var(--primary-color);
                border-color: var(--primary-color);
                color: white;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            let currentWishlistId = null;

            // Accordion Logic
            function toggleAccordion(wishlistId) {
                const content = document.getElementById('accordion-content-' + wishlistId);
                const icon = document.getElementById('accordion-icon-' + wishlistId);
                const isExpanded = content.style.maxHeight && content.style.maxHeight !== '0px';

                // Close all other accordions
                document.querySelectorAll('.accordion-content').forEach(el => {
                    el.style.maxHeight = '0px';
                });
                document.querySelectorAll('.accordion-icon').forEach(el => {
                    el.style.transform = 'rotate(0deg)';
                    el.innerHTML = '<i class="bi bi-plus-lg"></i>';
                });

                // Toggle selected
                if (!isExpanded) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    icon.style.transform = 'rotate(45deg)';
                    icon.innerHTML = '<i class="bi bi-plus-lg"></i>';
                }
            }

            // Status Filter Logic
            function filterStatus(status) {
                // Update active button styling
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                event.target.classList.add('active');

                // Filter cards
                const cards = document.querySelectorAll('.wishlist-card');
                cards.forEach(card => {
                    const cardStatus = card.getAttribute('data-status');
                    if (status === 'all' || cardStatus === status) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Modal Alokasi Dana
            function openAlokasiModal(wishlistId, wishlistName, sisaKekurangan) {
                currentWishlistId = wishlistId;
                document.getElementById('modalWishlistName').textContent = 'Wishlist: ' + wishlistName;

                // Format sisa kekurangan ke Rupiah
                const formattedSisa = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }).format(sisaKekurangan);
                document.getElementById('modalSisaKekurangan').innerHTML =
                    `Sisa kekurangan dana: <strong>${formattedSisa}</strong>`;

                const input = document.getElementById('jumlahInput');
                input.max = sisaKekurangan;
                input.value = '';

                document.getElementById('alokasiForm').action = '/wishlist/' + wishlistId + '/alokasi';
                document.getElementById('alokasiModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
                input.focus();
            }

            function closeAlokasiModal() {
                document.getElementById('alokasiModal').style.display = 'none';
                document.body.style.overflow = '';
                currentWishlistId = null;
            }

            function submitAlokasiForm() {
                if (currentWishlistId && document.getElementById('jumlahInput').value) {
                    document.getElementById('alokasiForm').submit();
                } else {
                    alert('Silakan masukkan jumlah alokasi');
                }
            }

            // Modal Batal
            function openBatalModal(wishlistId, wishlistName, terkumpul) {
                const modal = document.getElementById('batalModal');
                const form = document.getElementById('batalForm');
                const message = document.getElementById('batalModalMessage');

                form.action = '/wishlist/' + wishlistId;

                const formattedAllocated = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }).format(terkumpul);

                if (terkumpul > 0) {
                    message.innerHTML =
                        `Apakah Anda yakin ingin membatalkan wishlist <strong>"${wishlistName}"</strong>?<br><br>Dana yang sudah dialokasikan sebesar <strong>${formattedAllocated}</strong> akan dibatalkan dan dibebaskan kembali ke saldo utama.`;
                } else {
                    message.innerHTML = `Apakah Anda yakin ingin membatalkan wishlist <strong>"${wishlistName}"</strong>?`;
                }

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeBatalModal() {
                document.getElementById('batalModal').style.display = 'none';
                document.body.style.overflow = '';
            }

            // Modal Konfirmasi Pembelian
            function openKonfirmasiModal(wishlistId, wishlistName) {
                const modal = document.getElementById('konfirmasiModal');
                const form = document.getElementById('konfirmasiForm');
                const message = document.getElementById('konfirmasiModalMessage');

                form.action = '/wishlist/' + wishlistId + '/konfirmasi';
                message.innerHTML = `Beli wishlist ini sekarang?`;

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeKonfirmasiModal() {
                document.getElementById('konfirmasiModal').style.display = 'none';
                document.body.style.overflow = '';
            }

            function showWishlistToast(message) {
                const toast = document.getElementById('wishlistGlobalToast');
                const toastMessage = document.getElementById('wishlistGlobalToastMessage');
                if (!toast || !toastMessage || !message) return;
                toastMessage.textContent = message;
                toast.style.display = 'flex';
                toast.style.opacity = '1';
                setTimeout(() => {
                    toast.style.transition = 'opacity 0.3s ease';
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        toast.style.display = 'none';
                    }, 300);
                }, 6000);
            }

            document.addEventListener('DOMContentLoaded', function() {
                const allocationError = @json(session('allocationError'));
                const allocationWishlist = @json(session('allocationWishlist'));

                if (allocationError) {
                    if (allocationWishlist && allocationWishlist.id) {
                        openAlokasiModal(allocationWishlist.id, allocationWishlist.nama, allocationWishlist.sisa);
                    }
                    showWishlistToast(allocationError);
                }
            });

            // Escape key handler
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeAlokasiModal();
                    closeBatalModal();
                    closeKonfirmasiModal();
                }
            });

            // Enter key handler
            document.getElementById('jumlahInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    submitAlokasiForm();
                }
            });
        </script>
    @endpush
@endsection
