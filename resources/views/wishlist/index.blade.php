@extends('layouts.master')

@section('page_title', 'Wishlist')

@section('content')
    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-lg); margin-bottom: var(--space-lg);">
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

        <!-- Grid Wishlist Aktif & Tercapai -->
        <div>
            <h3 style="margin-bottom: var(--space-lg); font-size: var(--fs-lg); font-weight: 700; color: var(--text-dark);">
                Daftar Wishlist
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--space-lg);">
                @forelse($wishlists->whereIn('status', ['aktif', 'tercapai']) as $wishlist)
                    <div class="bunrek-card" style="display: flex; flex-direction: column;">
                        <div class="bunrek-card-body" style="flex: 1;">
                            <!-- Header dengan nama dan badge status -->
                            <div
                                style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-md);">
                                <h4
                                    style="margin: 0; flex: 1; color: var(--text-dark); font-size: var(--fs-base); font-weight: 600;">
                                    {{ $wishlist->nama }}
                                </h4>
                                @if ($wishlist->status === 'tercapai')
                                    <span
                                        style="background: var(--color-income); color: white; padding: 4px 12px; border-radius: var(--radius-base); font-size: var(--fs-xs); font-weight: 600; white-space: nowrap; margin-left: var(--space-xs);">
                                        <i class="bi bi-check-circle"></i> Tercapai
                                    </span>
                                @endif
                            </div>

                            <!-- Target & Terkumpul -->
                            <div style="margin-bottom: var(--space-md);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="color: var(--text-muted); font-size: var(--fs-sm);">Target</span>
                                    <span style="color: var(--text-dark); font-weight: 600;">Rp
                                        {{ number_format($wishlist->target_harga, 0, ',', '.') }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="color: var(--text-muted); font-size: var(--fs-sm);">Terkumpul</span>
                                    <span style="color: var(--color-income); font-weight: 600;">Rp
                                        {{ number_format($wishlist->terkumpul, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div style="margin-bottom: var(--space-md);">
                                <div
                                    style="background: var(--bg-light); border-radius: 20px; height: 8px; overflow: hidden;">
                                    <div
                                        style="background: linear-gradient(90deg, var(--color-income), var(--primary-color)); height: 100%; width: {{ $wishlist->prosesan }}%; transition: width 0.3s ease;">
                                    </div>
                                </div>
                                <span
                                    style="display: inline-block; margin-top: 6px; color: var(--text-muted); font-size: var(--fs-xs); font-weight: 500;">
                                    {{ $wishlist->prosesan }}%
                                </span>
                            </div>

                            <!-- Deadline jika ada -->
                            @if ($wishlist->deadline)
                                <div
                                    style="margin-bottom: var(--space-md); padding: 8px 12px; background: var(--bg-light); border-radius: var(--radius-base); color: var(--text-muted); font-size: var(--fs-xs);">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ \Carbon\Carbon::parse($wishlist->deadline)->translatedFormat('d F Y') }}
                                </div>
                            @endif

                            <!-- Catatan jika ada -->
                            @if ($wishlist->catatan)
                                <div
                                    style="margin-bottom: var(--space-md); padding: 8px 12px; background: var(--bg-light); border-radius: var(--radius-base); color: var(--text-muted); font-size: var(--fs-xs); border-left: 3px solid var(--primary-color);">
                                    <strong>Catatan:</strong><br>
                                    {{ Str::limit($wishlist->catatan, 100) }}
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div
                            style="display: flex; gap: var(--space-sm); margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px solid var(--border-light);">
                            @if ($wishlist->status !== 'tercapai')
                                <button type="button" class="btn-bunrek btn-sm btn-primary"
                                    onclick="openAlokasiModal({{ $wishlist->id }}, '{{ $wishlist->nama }}')">
                                    <i class="bi bi-plus-circle"></i> Alokasi
                                </button>
                            @endif
                            <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" style="flex: 1;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-bunrek btn-sm btn-outline"
                                    style="width: 100%; color: var(--color-expense); border-color: var(--color-expense);"
                                    onclick="return confirm('Batalkan wishlist ini?')">
                                    <i class="bi bi-x-circle"></i> Batalkan
                                </button>
                            </form>
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
                <form id="alokasiForm" method="POST">
                    @csrf
                    <div class="bunrek-form-group">
                        <label class="bunrek-label">Jumlah Alokasi (Rp)</label>
                        <input type="number" name="jumlah" id="jumlahInput" class="bunrek-input" required
                            placeholder="Minimum Rp 1.000" min="1000" step="1000">
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
        </style>
    @endpush

    @push('scripts')
        <script>
            let currentWishlistId = null;

            function openAlokasiModal(wishlistId, wishlistName) {
                currentWishlistId = wishlistId;
                document.getElementById('modalWishlistName').textContent = 'Wishlist: ' + wishlistName;
                document.getElementById('alokasiForm').action = '/wishlist/' + wishlistId + '/alokasi';
                document.getElementById('jumlahInput').value = '';
                document.getElementById('alokasiModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
                document.getElementById('jumlahInput').focus();
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

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const modal = document.getElementById('alokasiModal');
                    if (modal && modal.style.display === 'flex') {
                        closeAlokasiModal();
                    }
                }
            });

            // Enter key untuk submit di input jumlah
            document.getElementById('jumlahInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    submitAlokasiForm();
                }
            });
        </script>
    @endpush
@endsection
