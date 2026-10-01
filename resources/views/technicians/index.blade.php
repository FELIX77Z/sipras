@extends($layout)

@section('title', 'Tim Teknisi')

@section('content')
<section class="dashboard-intro mb-4" aria-labelledby="technicians-title">
    <div>
        <p class="dashboard-eyebrow mb-2">Penanganan sarana sekolah</p>
        <h1 id="technicians-title">Tim Teknisi</h1>
        <p class="dashboard-intro-copy mb-0">Kelola akun teknisi dan bagikan tugas perbaikan dari rincian pengaduan.</p>
    </div>
</section>

<div class="row g-4">
    <div class="col-xl-4">
        <section class="card shadow-sm border-0 h-100" aria-labelledby="create-technician-title">
            <div class="card-header"><h2 class="h5 mb-0" id="create-technician-title">Tambah Teknisi</h2></div>
            <div class="card-body">
                <form method="POST" action="{{ route($routePrefix.'.technicians.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="technician-name" class="form-label">Nama</label>
                        <input id="technician-name" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255" autocomplete="name">
                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="technician-email" class="form-label">Email login</label>
                        <input id="technician-email" type="email" name="email" class="form-control" value="{{ old('email') }}" required maxlength="255" autocomplete="email">
                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="technician-phone" class="form-label">Nomor kontak</label>
                        <input id="technician-phone" type="tel" name="phone" class="form-control" value="{{ old('phone') }}" required maxlength="20" autocomplete="tel">
                        @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="technician-position" class="form-label">Bidang tugas</label>
                        <input id="technician-position" name="position" class="form-control" value="{{ old('position', 'Teknisi Sarana dan Prasarana') }}" maxlength="100">
                        @error('position')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="technician-password" class="form-label">Password awal</label>
                        <input id="technician-password" type="password" name="password" class="form-control" required minlength="8" autocomplete="new-password">
                        @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="technician-password-confirmation" class="form-label">Ulangi password</label>
                        <input id="technician-password-confirmation" type="password" name="password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                    <button class="btn btn-dark" type="submit"><i class="fa-solid fa-user-plus me-2" aria-hidden="true"></i>Buat akun teknisi</button>
                </form>
            </div>
        </section>
    </div>

    <div class="col-xl-8">
        <section class="card shadow-sm border-0" aria-labelledby="technician-list-title">
            <div class="card-header d-flex justify-content-between align-items-center gap-3">
                <h2 class="h5 mb-0" id="technician-list-title">Kontak dan Beban Tugas</h2>
                <span class="small text-muted">{{ $technicians->count() }} teknisi terdaftar</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light"><tr><th>Nama</th><th>Bidang tugas</th><th>Kontak</th><th>Jumlah tugas</th></tr></thead>
                    <tbody>
                        @forelse ($technicians as $technician)
                            <tr>
                                <td><strong>{{ $technician->name }}</strong><span class="d-block small text-muted">{{ $technician->email }}</span></td>
                                <td>{{ $technician->position ?: 'Teknisi Sarana dan Prasarana' }}</td>
                                <td>
                                    @if ($technician->phone)
                                        <a href="tel:{{ $technician->phone }}">{{ $technician->phone }}</a>
                                    @else
                                        <span class="text-muted">Belum diisi</span>
                                    @endif
                                </td>
                                <td>{{ $technician->assigned_complaints_count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada teknisi. Tambahkan akun pertama melalui formulir ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
<div>
    <!-- People find pleasure in different ways. I find it in keeping my mind clear. - Marcus Aurelius -->
</div>
