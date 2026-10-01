<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPRAS IFSU')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body>
<main class="auth-shell">
    <section class="auth-brand" aria-labelledby="brand-title">
        <a href="{{ route('home') }}" class="auth-logo" aria-label="Beranda SIPRAS">
            <span>SIPRAS · SMK Informatika Sumedang</span>
        </a>
        <div class="auth-brand-copy">
            <p class="auth-kicker">SMK INFORMATIKA SUMEDANG</p>
            <img class="auth-brand-emblem" src="{{ asset('images/ifsu-logo.png') }}" alt="Logo SMK Informatika Sumedang">
            <h1 id="brand-title">SIPRAS</h1>
            <p class="auth-description">Sistem Informasi Pengaduan Sarana dan Prasarana Sekolah.</p>
            <p class="auth-motto">Profesional Religius</p>
        </div>
        <div class="auth-brand-index"><span>IFSU</span><span>01 / SIPRAS</span></div>
    </section>
    <section class="auth-panel" aria-label="Form akun SIPRAS">
        <div class="auth-panel-top"><span>PORTAL SEKOLAH</span><a href="{{ route('home') }}">Beranda <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a></div>
        <div class="auth-form-wrap">{{ $slot }}</div>
        <footer class="auth-footer">&copy; {{ date('Y') }} SIPRAS IFSU <span>•</span> Sistem layanan sekolah</footer>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            button.innerHTML = `<i class="fa-solid ${isPassword ? 'fa-eye-slash' : 'fa-eye'}" aria-hidden="true"></i>`;
        });
    });
</script>
</body>
</html>
