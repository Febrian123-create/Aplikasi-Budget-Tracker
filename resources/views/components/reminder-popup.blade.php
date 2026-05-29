@auth
<div x-data="reminderPopup()" x-init="fetchPopups()" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; width: 320px; display: flex; flex-direction: column; gap: 8px; pointer-events: none;">

    <template x-for="popup in popups" :key="popup.id">
        <div style="pointer-events: auto; background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); border: 1px solid #e2e8f0; padding: 14px 16px; display: flex; gap: 12px; align-items: flex-start; animation: slideUp 0.3s ease;"
             x-show="!popup.dismissed"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            {{-- Icon --}}
            <div :style="popup.type === 'budget' ? 'background:#fef3c7;color:#d97706;' : 'background:#ede9fe;color:#7c3aed;'"
                 style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                <span x-show="popup.type === 'budget'">💰</span>
                <span x-show="popup.type !== 'budget'">🔔</span>
            </div>

            {{-- Content --}}
            <div style="flex: 1; min-width: 0;">
                <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 2px;" x-text="popup.title"></div>
                <div style="font-size: 12px; color: #64748b; line-height: 1.4;" x-text="popup.body"></div>
            </div>

            {{-- Dismiss --}}
            <button @click="dismiss(popup)"
                    style="background: none; border: none; font-size: 1.1rem; color: #94a3b8; cursor: pointer; padding: 0; line-height: 1; flex-shrink: 0;"
                    title="Tutup">&times;</button>
        </div>
    </template>
</div>

<script>
function reminderPopup() {
    return {
        popups: [],
        fetchPopups() {
            fetch('{{ route('recurring.popups.unread') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                this.popups = data.map(p => ({ ...p, dismissed: false }));
            })
            .catch(() => {});
        },
        dismiss(popup) {
            popup.dismissed = true;
            fetch('{{ url('recurring/popups') }}/' + popup.id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            }).catch(() => {});
        }
    }
}
</script>

<style>
@keyframes slideUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endauth
