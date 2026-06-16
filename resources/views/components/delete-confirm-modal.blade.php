@props([
    'title' => 'Konfirmasi Hapus',
    'message' => 'Anda yakin ingin menghapus item ini?',
    'subtitle' => 'Tindakan ini tidak dapat dibatalkan.',
])

<!-- Modal Delete Confirmation -->
<div id="deleteModal" class="delete-modal" style="display: none;">
    <div class="delete-modal-overlay" onclick="closeDeleteModal()"></div>
    <div class="delete-modal-content">
        <div class="delete-modal-header">
            <h3 class="delete-modal-title">{{ $title }}</h3>
            <button type="button" class="delete-modal-close" onclick="closeDeleteModal()">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="delete-modal-body">
            <div class="delete-modal-icon">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <p class="delete-modal-message">{{ $message }}</p>
            <p class="delete-modal-subtitle">{{ $subtitle }}</p>
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
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes modalSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
@endpush

@push('scripts')
    <script>
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

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('deleteModal');
                if (modal && modal.style.display === 'flex') {
                    closeDeleteModal();
                }
            }
        });
    </script>
@endpush
