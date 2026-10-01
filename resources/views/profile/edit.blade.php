@extends($layout)

@section('title', 'Profil Akun')

@section('content')
<section class="dashboard-intro mb-4" aria-labelledby="profile-title">
    <div>
        <p class="dashboard-eyebrow mb-2">Pengaturan akun</p>
        <h1 id="profile-title">Profil Saya</h1>
        <p class="dashboard-intro-copy mb-0">Perbarui informasi, jaga keamanan kata sandi, dan kelola akun sekolahmu.</p>
    </div>
</section>

@if (session('status') === 'profile-updated' || session('status') === 'password-updated')
    <div class="alert alert-success" role="status">Perubahan profil berhasil disimpan.</div>
@endif

<div class="row g-4">
    <div class="col-xl-7">
        <section class="card border-0 shadow-sm h-100" aria-labelledby="profile-information-title">
            <div class="card-body p-4">
                <h2 id="profile-information-title" class="h5">Informasi akun</h2>
                <p class="text-muted small mb-4">Nama dan email yang digunakan untuk masuk SIPRAS.</p>
                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">@csrf</form>
                <form method="POST" action="{{ route('profile.update') }}" class="d-grid gap-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="form-label fw-semibold" for="name">Nama lengkap</label>
                        <input class="form-control" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" required>
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>
                        @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    @if (! $user->hasVerifiedEmail())
                        <div class="small text-muted">Email belum diverifikasi. <button class="btn btn-link btn-sm p-0 align-baseline" form="send-verification" type="submit">Kirim ulang tautan verifikasi</button></div>
                    @endif
                    <div><button class="btn btn-primary" type="submit">Simpan profil</button></div>
                </form>
            </div>
        </section>
    </div>

    <div class="col-xl-5">
        <section class="card border-0 shadow-sm h-100" aria-labelledby="password-title">
            <div class="card-body p-4">
                <h2 id="password-title" class="h5">Ganti kata sandi</h2>
                <p class="text-muted small mb-4">Gunakan kata sandi baru yang kuat dan tidak dipakai di tempat lain.</p>
                <form method="POST" action="{{ route('password.update') }}" class="d-grid gap-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="form-label fw-semibold" for="current_password">Kata sandi saat ini</label>
                        <input class="form-control" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                        @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-semibold" for="password">Kata sandi baru</label>
                        <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" required>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-semibold" for="password_confirmation">Konfirmasi kata sandi baru</label>
                        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                    </div>
                    <div><button class="btn btn-primary" type="submit">Perbarui kata sandi</button></div>
                </form>
            </div>
        </section>
    </div>

    <div class="col-12">
        <section class="card border-danger-subtle shadow-sm" aria-labelledby="delete-account-title">
            <div class="card-body p-4">
                <h2 id="delete-account-title" class="h5 text-danger">Hapus akun</h2>
                <p class="text-muted small">Penghapusan tidak dapat dibatalkan. Akun dengan riwayat pengaduan tidak dapat dihapus agar data sekolah tetap terjaga.</p>
                @if ($errors->userDeletion->has('password'))
                    <div class="alert alert-danger" role="alert">{{ $errors->userDeletion->first('password') }}</div>
                @endif
                <form method="POST" action="{{ route('profile.destroy') }}" class="row g-2 align-items-end" onsubmit="return confirm('Hapus akun ini secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <div class="col-sm-6 col-lg-4">
                        <label class="form-label fw-semibold" for="delete-password">Konfirmasi kata sandi</label>
                        <input class="form-control" id="delete-password" name="password" type="password" autocomplete="current-password" required>
                    </div>
                    <div class="col-auto"><button class="btn btn-outline-danger" type="submit">Hapus akun</button></div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection
