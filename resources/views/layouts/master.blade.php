<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BUNREK - Aplikasi Budget Tracker</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    
    <link rel="stylesheet" href="{{ asset('css/bunrek-tokens.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bunrek-app.css') }}" />

    @stack('styles')

    {{-- Alpine.js untuk komponen reaktif (toggle, dropdown, dsb) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </head>
  <body>
    <div class="bunrek-wrapper">
      
      <div class="sidebar-backdrop"></div>

      
      @include('layouts.sidebar')

      
      <div class="bunrek-main">
        
        @include('layouts.header')

        
        <main class="bunrek-content">
          @yield('content')
        </main>

        
        @include('layouts.footer')
      </div>
    </div>

    
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

    
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

    
    <script>
      $(document).ready(function() {
        $('.header-toggle, .sidebar-backdrop').on('click', function() {
          $('.bunrek-sidebar').toggleClass('open');
          $('.sidebar-backdrop').toggleClass('active');
        });
      });
    </script>
    @stack('scripts')
    @include('components.reminder-popup')

    <!-- Modal Konfirmasi Logout -->
    <div class="modal fade" id="confirmLogoutModal" tabindex="-1" aria-labelledby="confirmLogoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px; border:none; box-shadow: var(--shadow-xl);">
                <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 20px 24px;">
                    <h5 class="modal-title fw-bold" id="confirmLogoutModalLabel" style="font-family: var(--font-heading); color: var(--text-dark);">
                        Konfirmasi Keluar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 24px; text-align: center;">
                    <div style="font-size: 3rem; color: var(--color-expense); margin-bottom: 16px;">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <p class="text-dark fw-medium" style="font-size: 15px; margin-bottom: 8px;">
                        Apakah anda yakin ingin keluar ?
                    </p>
                    <p class="text-muted" style="font-size: 13px; margin-bottom: 0;">
                        Sesi Anda akan diakhiri dan Anda perlu masuk kembali untuk mengakses data Anda.
                    </p>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 16px 24px; display: flex; gap: 12px; justify-content: center;">
                    <button type="button" class="btn-outline btn-bunrek" data-bs-dismiss="modal" style="border-radius: 10px; padding: 10px 24px; font-weight: 600; border: 1.5px solid var(--border-color); color: var(--text-body); transition: var(--transition-fast);">Tidak</button>
                    <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn-primary btn-bunrek" style="border: none; border-radius: 10px; padding: 10px 24px; font-weight: 600; color: white;">Ya, Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    @php
        $showStatusModal = false;
        $statusType = 'success';
        $statusTitle = '';
        $statusMessage = '';

        if (session('status')) {
            $showStatusModal = true;
            $statusType = 'success';
            $statusTitle = 'Berhasil!';
            if (session('status') === 'profile-updated') {
                $statusMessage = 'Informasi profil Anda berhasil diperbarui.';
            } elseif (session('status') === 'password-updated') {
                $statusMessage = 'Password Anda berhasil diperbarui.';
            } else {
                $statusMessage = session('status');
            }
        } elseif (session('success')) {
            $showStatusModal = true;
            $statusType = 'success';
            $statusTitle = 'Berhasil!';
            $statusMessage = session('success');
        } elseif ($errors->any()) {
            $showStatusModal = true;
            $statusType = 'error';
            $statusTitle = 'Gagal!';
            $statusMessage = implode('<br>', $errors->all());
        } elseif (isset($errors) && method_exists($errors, 'hasBag') && $errors->hasBag('updatePassword') && $errors->updatePassword->any()) {
            $showStatusModal = true;
            $statusType = 'error';
            $statusTitle = 'Gagal!';
            $statusMessage = implode('<br>', $errors->updatePassword->all());
        } elseif (isset($errors) && method_exists($errors, 'hasBag') && $errors->hasBag('userDeletion') && $errors->userDeletion->any()) {
            $showStatusModal = true;
            $statusType = 'error';
            $statusTitle = 'Gagal!';
            $statusMessage = implode('<br>', $errors->userDeletion->all());
        }
    @endphp

    @if ($showStatusModal)
    <!-- Modal Status Notifikasi (Success / Error) -->
    <div class="modal fade" id="statusNotificationModal" tabindex="-1" aria-labelledby="statusNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content" style="border-radius:16px; border:none; box-shadow: var(--shadow-xl);">
                <div class="modal-body" style="padding: 32px; text-align: center;">
                    @if ($statusType === 'success')
                        <div style="font-size: 3.5rem; color: var(--color-income); margin-bottom: 20px; animation: bounceIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    @else
                        <div style="font-size: 3.5rem; color: var(--color-expense); margin-bottom: 20px; animation: bounceIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                            <i class="bi bi-exclamation-circle-fill"></i>
                        </div>
                    @endif
                    
                    <h4 class="fw-bold" style="font-family: var(--font-heading); color: var(--text-dark); margin-bottom: 12px;">
                        {{ $statusTitle }}
                    </h4>
                    
                    <p class="text-muted" style="font-size: 14px; line-height: 1.6; margin-bottom: 24px;">
                        {!! $statusMessage !!}
                    </p>
                    
                    <button type="button" class="btn-primary btn-bunrek" data-bs-dismiss="modal" style="border: none; border-radius: 10px; padding: 10px 32px; font-weight: 600; color: white; transition: var(--transition-fast);">
                        Oke
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    @keyframes bounceIn {
        0% { transform: scale(0.3); opacity: 0; }
        50% { transform: scale(1.05); opacity: 0.8; }
        70% { transform: scale(0.9); opacity: 0.9; }
        100% { transform: scale(1); opacity: 1; }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var statusModalEl = document.getElementById('statusNotificationModal');
        if (statusModalEl) {
            var statusModal = new bootstrap.Modal(statusModalEl);
            statusModal.show();
        }
    });
    </script>
    @endif
  </body>
</html>
