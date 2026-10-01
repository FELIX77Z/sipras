<x-guest-layout>
    <h2 class="auth-heading">Masuk ke SIPRAS</h2>
    <p class="auth-subheading">Gunakan akun sekolah untuk melanjutkan.</p>

    @if (session('status'))
        <div class="alert alert-success auth-alert" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger auth-alert" role="alert">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form id="login-form" class="auth-form" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <div class="auth-password-wrap">
                <input id="password" class="form-control" type="password" name="password" autocomplete="current-password" required>
                <button type="button" data-password-toggle="password" aria-label="Tampilkan password"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
            </div>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <label class="form-check d-flex align-items-center gap-2 mb-0"><input class="form-check-input mt-0" id="remember" type="checkbox" name="remember" value="1"><span class="small">Ingat saya</span></label>
            <a class="small fw-semibold" href="{{ route('password.request') }}">Lupa password?</a>
        </div>
        <button id="login-submit" class="btn auth-submit w-100" type="submit"><span class="button-label">Masuk</span><span class="spinner-border spinner-border-sm ms-2 d-none" aria-hidden="true"></span></button>
    </form>
    <div class="auth-links"><span>Belum punya akun?</span><a href="{{ route('register') }}">Daftar Siswa</a></div>
    <script>
        document.getElementById('login-form').addEventListener('submit', () => {
            const button = document.getElementById('login-submit');
            button.disabled = true;
            button.querySelector('.button-label').textContent = 'Memeriksa akun';
            button.querySelector('.spinner-border').classList.remove('d-none');
        });
    </script>
</x-guest-layout>
