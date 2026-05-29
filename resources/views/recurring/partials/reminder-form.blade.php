{{--
    Partial: reminder-form.blade.php
    Variabel yang dibutuhkan:
    - $isPremium (bool)
    - $reminder (?Reminder) — null saat create, ada saat edit
    - $formPrefix (string, optional) — prefix input name, default ''
--}}
@php
    $prefix   = $formPrefix ?? '';
    $oldEnabled  = old($prefix . 'reminder_enabled',  $reminder?->reminder_enabled ?? false);
    $oldDays     = old($prefix . 'reminder_days',     $reminder?->reminder_days    ?? []);
    $oldChannels = old($prefix . 'reminder_channels', $reminder?->channels         ?? ['popup']);
    $oldMessage  = old($prefix . 'reminder_message',  $reminder?->custom_message   ?? '');

    $availableDays = [10 => 'H-10', 7 => 'H-7', 5 => 'H-5', 3 => 'H-3', 1 => 'H-1', 0 => 'Hari H'];
@endphp

<div x-data="{ reminderOn: {{ $oldEnabled ? 'true' : 'false' }} }"
     style="border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: var(--space-md) var(--space-lg); margin-top: var(--space-md); background: var(--bg-soft);">

    {{-- Toggle header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: var(--space-sm);">
            <i class="bi bi-bell" style="color: var(--primary-color); font-size: 1.1rem;"></i>
            <span style="font-weight: 700; font-size: var(--fs-sm); color: var(--text-dark);">Pengingat (Reminder)</span>
        </div>
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <span style="font-size: var(--fs-xs); color: var(--text-muted);" x-text="reminderOn ? 'Aktif' : 'Nonaktif'"></span>
            <div @click="reminderOn = !reminderOn"
                 :style="reminderOn ? 'background: var(--primary-color);' : 'background: var(--border-color);'"
                 style="width: 40px; height: 22px; border-radius: 999px; position: relative; transition: background 0.2s; cursor: pointer;">
                <div :style="reminderOn ? 'left: 20px;' : 'left: 2px;'"
                     style="position: absolute; top: 2px; width: 18px; height: 18px; border-radius: 50%; background: white; transition: left 0.2s; box-shadow: 0 1px 4px rgba(0,0,0,0.2);"></div>
            </div>
            <input type="hidden" name="{{ $prefix }}reminder_enabled" :value="reminderOn ? '1' : '0'">
        </label>
    </div>

    {{-- Reminder body (show only when enabled) --}}
    <div x-show="reminderOn" x-transition style="margin-top: var(--space-md);">

        {{-- Pilih hari --}}
        <div class="bunrek-form-group" style="margin-bottom: var(--space-md);">
            <label class="bunrek-label" style="margin-bottom: var(--space-xs);">Kirim Pengingat Pada</label>
            <div style="display: flex; flex-wrap: wrap; gap: var(--space-xs);">
                @foreach($availableDays as $val => $label)
                    <label style="display: flex; align-items: center; gap: 6px; background: var(--bg-white); border: 1.5px solid var(--border-color); border-radius: var(--radius-sm); padding: 5px 12px; cursor: pointer; font-size: var(--fs-xs); font-weight: 600; transition: all 0.15s;"
                           :style="''"
                           >
                        <input type="checkbox"
                               name="{{ $prefix }}reminder_days[]"
                               value="{{ $val }}"
                               {{ in_array($val, (array)$oldDays) ? 'checked' : '' }}
                               style="accent-color: var(--primary-color);">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error($prefix . 'reminder_days.*')
                <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small>
            @enderror
        </div>

        {{-- Pilih channel --}}
        <div class="bunrek-form-group" style="margin-bottom: var(--space-md);">
            <label class="bunrek-label" style="margin-bottom: var(--space-xs);">Kirim Via</label>
            <div style="display: flex; flex-wrap: wrap; gap: var(--space-xs);">
                <label style="display: flex; align-items: center; gap: 6px; background: var(--bg-white); border: 1.5px solid var(--border-color); border-radius: var(--radius-sm); padding: 5px 12px; cursor: pointer; font-size: var(--fs-xs); font-weight: 600;">
                    <input type="checkbox" name="{{ $prefix }}reminder_channels[]" value="popup"
                           {{ in_array('popup', (array)$oldChannels) ? 'checked' : '' }}
                           style="accent-color: var(--primary-color);">
                    <i class="bi bi-bell-fill" style="color: var(--primary-color);"></i> Pop-up
                </label>
                <label style="display: flex; align-items: center; gap: 6px; background: var(--bg-white); border: 1.5px solid var(--border-color); border-radius: var(--radius-sm); padding: 5px 12px; cursor: pointer; font-size: var(--fs-xs); font-weight: 600;">
                    <input type="checkbox" name="{{ $prefix }}reminder_channels[]" value="email"
                           {{ in_array('email', (array)$oldChannels) ? 'checked' : '' }}
                           style="accent-color: var(--primary-color);">
                    <i class="bi bi-envelope-fill" style="color: #3b82f6;"></i> Email
                </label>
                @if($isPremium)
                    <label style="display: flex; align-items: center; gap: 6px; background: var(--bg-white); border: 1.5px solid var(--border-color); border-radius: var(--radius-sm); padding: 5px 12px; cursor: pointer; font-size: var(--fs-xs); font-weight: 600;">
                        <input type="checkbox" name="{{ $prefix }}reminder_channels[]" value="google_calendar"
                               {{ in_array('google_calendar', (array)$oldChannels) ? 'checked' : '' }}
                               style="accent-color: var(--primary-color);">
                        <i class="bi bi-calendar-event-fill" style="color: #ef4444;"></i> Google Calendar
                    </label>
                @else
                    <label style="display: flex; align-items: center; gap: 6px; background: var(--border-light); border: 1.5px solid var(--border-light); border-radius: var(--radius-sm); padding: 5px 12px; cursor: not-allowed; font-size: var(--fs-xs); font-weight: 600; opacity: 0.6;" title="Khusus Premium">
                        <i class="bi bi-calendar-event-fill" style="color: #ef4444;"></i> Google Calendar
                        <span style="font-size: 0.65rem; background: #fbbf24; color: white; padding: 1px 5px; border-radius: 3px;">PRO</span>
                    </label>
                @endif
            </div>
        </div>

        {{-- Pesan custom --}}
        <div class="bunrek-form-group" style="margin-bottom: 0;">
            <label class="bunrek-label" style="margin-bottom: var(--space-xs);">
                Pesan Reminder <small style="color: var(--text-muted); font-weight: 500;">(Opsional)</small>
            </label>
            <textarea name="{{ $prefix }}reminder_message"
                      class="bunrek-input"
                      rows="2"
                      maxlength="500"
                      placeholder="Contoh: Jangan lupa bayar tagihan internet bulan ini!"
                      style="resize: vertical;">{{ $oldMessage }}</textarea>
            <small style="color: var(--text-muted); font-size: var(--fs-xs);">Kosongkan untuk menggunakan pesan otomatis.</small>
            @error($prefix . 'reminder_message')
                <small style="color: var(--color-expense); font-size: var(--fs-xs); display: block; margin-top: 4px;">{{ $message }}</small>
            @enderror
        </div>

    </div>
</div>
