@extends('layouts.master')

@push('styles')
<style>
.profile-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
    padding: 28px;
    margin-bottom: 24px;
}
.profile-card h4 {
    font-family: var(--font-heading);
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 8px;
}
.profile-card p {
    color: var(--text-muted);
    font-size: 13px;
    margin-bottom: 20px;
}
.form-label {
    font-weight: 600;
    font-size: 13px;
    color: var(--text-body);
    margin-bottom: 6px;
}
.form-control {
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    padding: 10px 14px;
    font-size: 14px;
    color: var(--text-dark);
}
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px var(--primary-200);
}
.btn-primary {
    background: var(--primary-color);
    border: none;
    border-radius: 10px;
    padding: 10px 24px;
    font-weight: 600;
    transition: var(--transition-fast);
}
.btn-primary:hover {
    background: var(--primary-dark);
}
.btn-danger {
    border-radius: 10px;
    padding: 10px 24px;
    font-weight: 600;
}
.btn-light {
    border-radius: 10px;
    padding: 10px 24px;
    font-weight: 600;
}
.alert-success-custom {
    background-color: var(--color-income-bg);
    color: var(--color-income);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-inner">
        
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap:12px;">
            <div>
                <h3 class="fw-bold mb-1" style="color:#1a1a2e; font-family: var(--font-heading);">Pengaturan Profil</h3>
                <p class="text-muted mb-0">Kelola detail akun, keamanan, dan preferensi profil Anda.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                
                <div class="profile-card">
                    <h4>Informasi Profil</h4>
                    <p>Perbarui informasi profil akun dan alamat email Anda.</p>

                    @if (session('status') === 'profile-updated')
                        <div class="alert-success-custom">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Informasi profil Anda berhasil diperbarui.</span>
                        </div>
                    @endif

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @if($errors->has('name'))
                                <div class="text-danger mt-1" style="font-size: 12px;">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" pattern="[a-zA-Z0-9._%+-]+@(gmail\.com|[a-zA-Z0-9.-]+\.ac\.id)" title="Email harus menggunakan domain @gmail.com atau berakhiran .ac.id." required autocomplete="username">
                            @if($errors->has('email'))
                                <div class="text-danger mt-1" style="font-size: 12px;">{{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary text-white">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

                
                <div class="profile-card">
                    <h4>Ubah Password</h4>
                    <p>Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>

                    @if (session('status') === 'password-updated')
                        <div class="alert-success-custom">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Password Anda berhasil diperbarui.</span>
                        </div>
                    @endif

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="update_password_current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" id="update_password_current_password" name="current_password" class="form-control" autocomplete="current-password">
                            @if($errors->updatePassword->has('current_password'))
                                <div class="text-danger mt-1" style="font-size: 12px;">{{ $errors->updatePassword->first('current_password') }}</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="update_password_password" class="form-label">Password Baru</label>
                            <input type="password" id="update_password_password" name="password" class="form-control" autocomplete="new-password">
                            @if($errors->updatePassword->has('password'))
                                <div class="text-danger mt-1" style="font-size: 12px;">{{ $errors->updatePassword->first('password') }}</div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="update_password_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" id="update_password_password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                            @if($errors->updatePassword->has('password_confirmation'))
                                <div class="text-danger mt-1" style="font-size: 12px;">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary text-white">Perbarui Password</button>
                        </div>
                    </form>
                </div>

                
                <div class="profile-card" style="border: 1px solid rgba(239, 68, 68, 0.2); background-color: rgba(255, 255, 255, 0.8);">
                    <h4 style="color: var(--color-expense);">Hapus Akun</h4>
                    <p>Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>

                    <div class="d-flex justify-content-start">
                        <button type="button" class="btn btn-danger text-white bg-danger border-0" data-bs-toggle="modal" data-bs-target="#confirmDeletionModal">
                            Hapus Akun Saya
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="confirmDeletionModal" tabindex="-1" aria-labelledby="confirmDeletionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow: var(--shadow-xl);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 20px 24px;">
                <h5 class="modal-title fw-bold" id="confirmDeletionModalLabel" style="font-family: var(--font-heading); color: var(--text-dark);">
                    Apakah Anda yakin ingin menghapus akun?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="modal-body" style="padding: 24px;">
                    <p class="text-muted" style="font-size: 13.5px; line-height: 1.5;">
                        Setelah akun Anda dihapus, semua data dan riwayat keuangan di dalamnya akan dihapus secara permanen. 
                        Silakan masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
                    </p>

                    <div class="mt-3">
                        <label for="password" class="form-label">Password Konfirmasi</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                        @if($errors->userDeletion->has('password'))
                            <div class="text-danger mt-1" style="font-size: 12px;">{{ $errors->userDeletion->first('password') }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger text-white bg-danger border-0">Hapus Akun Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->userDeletion->isNotEmpty())
        var myModal = new bootstrap.Modal(document.getElementById('confirmDeletionModal'));
        myModal.show();
    @endif
});
</script>
@endpush
