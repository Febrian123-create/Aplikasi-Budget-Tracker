@extends('layouts.master')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-pricing">
                    <div class="card-header">
                        <h4 class="card-title">{{ $premiumPackage->membership_name ?? 'Premium' }}</h4>
                        <div class="card-price">
                            <span class="price">Rp {{ number_format($premiumPackage->price ?? 99000, 0, ',', '.') }}</span>
                            <span class="text">/ bulan</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="specification-list">
                            <li>
                                <span class="name-specification">Mencatat Transaksi</span>
                                <span class="status-specification">Yes</span>
                            </li>
                            <li>
                                <span class="name-specification">Riwayat Transaksi</span>
                                <span class="status-specification">Yes</span>
                            </li>
                            <li>
                                <span class="name-specification">Visualisasi Data Grafik (Pie & Bar)</span>
                                <span class="status-specification">Yes</span>
                            </li>
                            <li>
                                <span class="name-specification">Export Data ke PDF</span>
                                <span class="status-specification">Yes</span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        @if(auth()->user()->membership_id == 2)
                            <button class="btn btn-success btn-block" disabled>Aktif</button>
                        @else
                            <form action="{{ route('membership.upgrade') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block"><b>Upgrade Sekarang</b></button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
