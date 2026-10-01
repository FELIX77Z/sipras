@extends($layout ?? 'layouts.guru')

@section('title', $mode === 'edit' ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header"><h4 class="mb-0">{{ $mode === 'edit' ? 'Edit Siswa' : 'Tambah Siswa' }}</h4></div>
    <div class="card-body">
        <form action="{{ $mode === 'edit' ? route(auth()->user()->isSuperAdmin() ? 'admin.students.update' : 'guru.students.update', $user) : route(auth()->user()->isSuperAdmin() ? 'admin.students.store' : 'guru.students.store') }}" method="POST">
            @csrf
            @if ($mode === 'edit')
                @method('PUT')
            @endif
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nama</label><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
                <div class="col-md-6"><label class="form-label">NIS</label><input type="text" name="nis" class="form-control" value="{{ old('nis', $user->nis) }}"><div class="form-text">Isi NIS atau NISN.</div></div>
                <div class="col-md-6"><label class="form-label">NISN</label><input type="text" name="nisn" class="form-control" value="{{ old('nisn', $user->nisn) }}"></div>
                <div class="col-md-4"><label class="form-label">Kelas</label><input type="text" name="class" class="form-control" value="{{ old('class', $user->class) }}" required></div>
                <div class="col-md-4"><label class="form-label">Jurusan</label><input type="text" name="major" class="form-control" value="{{ old('major', $user->major) }}" required></div>
                <div class="col-md-4"><label class="form-label">Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
                @if ($mode === 'create')
                    <div class="col-md-12"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                @endif
                <div class="col-12"><button type="submit" class="btn btn-primary">Simpan</button><a href="{{ route(auth()->user()->isSuperAdmin() ? 'admin.students.index' : 'guru.students.index') }}" class="btn btn-secondary">Kembali</a></div>
            </div>
        </form>
    </div>
</div>
@endsection
