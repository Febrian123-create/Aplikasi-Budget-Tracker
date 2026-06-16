@extends('layouts.master')

@section('content')
<div class="content-inner" style="max-width: 560px;">

    <div style="margin-bottom: var(--space-xl);">
        <a href="{{ route('budget.index') }}" style="font-size: var(--fs-sm); color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-bottom: var(--space-sm);">
            <i class="bi bi-arrow-left"></i> Kembali ke Budget
        </a>
        <h1 style="font-family: var(--font-heading); font-weight: 800; color: var(--text-dark); margin: 0; font-size: var(--fs-2xl);">Pengaturan Alert Budget</h1>
        <p style="color: var(--text-muted); font-size: var(--fs-sm); margin: 0; margin-top: 2px;">Atur kapan dan bagaimana kamu diberitahu saat budget hampir habis</p>
    </div>

    @if(session('success'))
        <div class="bunrek-alert bunrek-alert-success" style="margin-bottom: var(--space-lg);">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bunrek-card">
        <div class="bunrek-card-body" style="padding: var(--space-lg) var(--space-xl);">
            <form action="{{ route('budget.settings.save') }}" method="POST">
                @csrf

                {{-- Threshold --}}
                <div class="bunrek-form-group" style="margin-bottom: var(--space-lg);">
                    <label class="bunrek-label">Kirim Alert Saat Pengeluaran Mencapai</label>
                    <div style="display: flex; align-items: center; gap: var(--space-md);">
                        <input type="range" name="threshold" id="thresholdRange"
                               min="50" max="100" step="5"
                               value="{{ $setting?->threshold ?? 80 }}"
                               style="flex: 1; accent-color: var(--primary-color);"
                               oninput="document.getElementById('thresholdVal').textContent = this.value + '%'">
                        <span id="thresholdVal" style="font-size: var(--fs-lg); font-weight: 800; color: var(--primary-color); min-width: 50px;">
                            {{ $setting?->threshold ?? 80 }}%
                        </span>
                    </div>
                    <small style="color: var(--text-muted); font-size: var(--fs-xs);">Rentang: 50% – 100%</small>
                    @error('threshold')
                        <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block;">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Channels --}}
                <div class="bunrek-form-group" style="margin-bottom: var(--space-lg);">
                    <label class="bunrek-label">Kirim Alert Via</label>
                    <div style="display: flex; flex-direction: column; gap: var(--space-sm); margin-top: var(--space-xs);">

                        @php $selectedChannels = $setting?->channels ?? ['popup']; @endphp

                        <label style="display: flex; align-items: center; gap: 10px; padding: var(--space-sm) var(--space-md); border: 1.5px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer;">
                            <input type="checkbox" name="channels[]" value="popup"
                                   {{ in_array('popup', $selectedChannels) ? 'checked' : '' }}
                                   style="accent-color: var(--primary-color); width: 16px; height: 16px;">
                            <i class="bi bi-bell-fill" style="color: var(--primary-color); font-size: 1rem;"></i>
                            <div>
                                <div style="font-weight: 600; font-size: var(--fs-sm);">Pop-up In-App</div>
                                <div style="font-size: var(--fs-xs); color: var(--text-muted);">Notifikasi muncul saat kamu membuka aplikasi</div>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 10px; padding: var(--space-sm) var(--space-md); border: 1.5px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer;">
                            <input type="checkbox" name="channels[]" value="email"
                                   {{ in_array('email', $selectedChannels) ? 'checked' : '' }}
                                   style="accent-color: var(--primary-color); width: 16px; height: 16px;">
                            <i class="bi bi-envelope-fill" style="color: #3b82f6; font-size: 1rem;"></i>
                            <div>
                                <div style="font-weight: 600; font-size: var(--fs-sm);">Email</div>
                                <div style="font-size: var(--fs-xs); color: var(--text-muted);">Kirim email ke {{ auth()->user()->email }}</div>
                            </div>
                        </label>

                        @if($isPremium)
                            <label style="display: flex; align-items: center; gap: 10px; padding: var(--space-sm) var(--space-md); border: 1.5px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer;">
                                <input type="checkbox" name="channels[]" value="google_calendar"
                                       {{ in_array('google_calendar', $selectedChannels) ? 'checked' : '' }}
                                       style="accent-color: var(--primary-color); width: 16px; height: 16px;">
                                <i class="bi bi-calendar-event-fill" style="color: #ef4444; font-size: 1rem;"></i>
                                <div>
                                    <div style="font-weight: 600; font-size: var(--fs-sm);">Google Calendar</div>
                                    <div style="font-size: var(--fs-xs); color: var(--text-muted);">Buat event di Google Calendar kamu</div>
                                </div>
                            </label>
                        @else
                            <div style="display: flex; align-items: center; gap: 10px; padding: var(--space-sm) var(--space-md); border: 1.5px solid var(--border-light); border-radius: var(--radius-sm); opacity: 0.5; cursor: not-allowed;">
                                <i class="bi bi-calendar-event-fill" style="color: #ef4444; font-size: 1rem;"></i>
                                <div>
                                    <div style="font-weight: 600; font-size: var(--fs-sm); display: flex; align-items: center; gap: 6px;">
                                        Google Calendar
                                        <span style="font-size: 0.65rem; background: #fbbf24; color: white; padding: 1px 6px; border-radius: 3px; font-weight: 700;">PREMIUM</span>
                                    </div>
                                    <div style="font-size: var(--fs-xs); color: var(--text-muted);">Upgrade ke Premium untuk menggunakan fitur ini</div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('channels.*')
                        <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn-bunrek btn-primary" style="width: 100%;">
                    <i class="bi bi-check-lg"></i> Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    setTimeout(function() { $('.bunrek-alert').fadeOut(500); }, 5000);
</script>
@endpush
