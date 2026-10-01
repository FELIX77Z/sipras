<x-guest-layout>
    <h2 class="auth-heading">Daftar sebagai Siswa</h2>
    <p class="auth-subheading">Lengkapi data sekolah untuk membuat akun SIPRAS.</p>
    @if ($errors->any())
        <div class="alert alert-danger auth-alert" role="alert"><ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form class="auth-form" method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3"><label class="form-label" for="name">Nama Lengkap</label><input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        <div class="row g-3 mb-3">
            <div class="col-sm-6"><label class="form-label" for="nis">NIS</label><input id="nis" class="form-control" type="text" name="nis" value="{{ old('nis') }}" autocomplete="off"><div class="form-text">Isi NIS atau NISN.</div>@error('nis')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
            <div class="col-sm-6"><label class="form-label" for="nisn">NISN</label><input id="nisn" class="form-control" type="text" name="nisn" value="{{ old('nisn') }}" autocomplete="off">@error('nisn')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        </div>
        <div class="mb-3"><label class="form-label" for="email">Email</label><input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        <div class="row g-3 mb-3">
            <div class="col-sm-6"><label class="form-label" for="class">Kelas</label><input id="class" class="form-control" type="text" name="class" value="{{ old('class') }}" required>@error('class')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
            <div class="col-sm-6"><label class="form-label" for="major">Jurusan</label><input id="major" class="form-control" type="text" name="major" value="{{ old('major') }}" required>@error('major')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        </div>
        <div class="mb-3"><label class="form-label" for="phone">No. HP</label><input id="phone" class="form-control" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" required>@error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        <div class="row g-3 mb-3">
            <div class="col-sm-6"><label class="form-label" for="password">Password</label><div class="auth-password-wrap"><input id="password" class="form-control" type="password" name="password" autocomplete="new-password" required><button type="button" data-password-toggle="password" aria-label="Tampilkan password"><i class="fa-solid fa-eye" aria-hidden="true"></i></button></div>@error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
            <div class="col-sm-6"><label class="form-label" for="password_confirmation">Konfirmasi Password</label><div class="auth-password-wrap"><input id="password_confirmation" class="form-control" type="password" name="password_confirmation" autocomplete="new-password" required><button type="button" data-password-toggle="password_confirmation" aria-label="Tampilkan password"><i class="fa-solid fa-eye" aria-hidden="true"></i></button></div></div>
        </div>
        <div class="form-check mb-4"><input id="agreement" class="form-check-input" type="checkbox" name="agreement" value="1" @checked(old('agreement')) required><label class="form-check-label small" for="agreement">Saya menyetujui ketentuan penggunaan.</label>@error('agreement')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        <button class="btn auth-submit w-100" type="submit">Daftar</button>
    </form>
    <div class="auth-links"><span>Sudah punya akun?</span><a href="{{ route('login') }}">Masuk</a></div>
</x-guest-layout>
