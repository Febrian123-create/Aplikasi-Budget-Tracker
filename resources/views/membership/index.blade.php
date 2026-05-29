@extends('layouts.master')

@section('page_title', 'Membership')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    @if (session('success'))
        <div class="bunrek-alert bunrek-alert-success">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bunrek-alert bunrek-alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    <div class="bunrek-card" style="text-align: center; border: 1.5px solid var(--primary-light); overflow: hidden;">
        <div class="bunrek-card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); color: white; display: flex; flex-direction: column; align-items: center; padding: var(--space-xl) var(--space-lg); border-bottom: none;">
            <div style="font-size: 2.5rem; margin-bottom: var(--space-sm); color: white;">
                <i class="bi bi-gem"></i>
            </div>
            <h2 class="bunrek-card-title" style="color: white; font-size: var(--fs-xl); margin-bottom: 8px;">
                {{ $premiumPackage->membership_name ?? 'Premium' }}
            </h2>
            <div style="font-family: var(--font-heading); font-weight: 800; font-size: var(--fs-3xl); line-height: 1.2;">
                Rp {{ number_format($premiumPackage->price ?? 9000, 0, ',', '.') }}
                <span style="font-size: var(--fs-sm); font-weight: 400; opacity: 0.85;">/ bulan</span>
            </div>
        </div>

        <div class="bunrek-card-body" style="padding: var(--space-xl);">
            <ul style="list-style: none; padding: 0; margin: 0 0 var(--space-xl) 0; text-align: left;">
                <li style="padding: var(--space-sm) 0; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--text-body); font-weight: 500;">Mencatat Transaksi</span>
                    <span style="color: var(--color-income); font-weight: 700;"><i class="bi bi-check-circle-fill"></i> Ya</span>
                </li>
                <li style="padding: var(--space-sm) 0; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--text-body); font-weight: 500;">Riwayat Transaksi</span>
                    <span style="color: var(--color-income); font-weight: 700;"><i class="bi bi-check-circle-fill"></i> Ya</span>
                </li>
                <li style="padding: var(--space-sm) 0; border-bottom: 1px solid var(--border-light); display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--text-body); font-weight: 500;">Visualisasi Data Grafik (Pie & Bar)</span>
                    <span style="color: var(--color-income); font-weight: 700;"><i class="bi bi-check-circle-fill"></i> Ya</span>
                </li>
            </ul>

            @if(auth()->user()->membership_id == 2)
                <button class="btn-bunrek btn-outline btn-w-full" style="cursor: not-allowed; opacity: 0.7; color: var(--color-income); border-color: var(--color-income); font-weight: 700;" disabled>
                    <i class="bi bi-check-all"></i> Paket Premium Aktif
                </button>
            @else
                <form action="{{ route('membership.upgrade') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-bunrek btn-primary btn-w-full" style="font-weight: 700;">
                        Upgrade Sekarang
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
