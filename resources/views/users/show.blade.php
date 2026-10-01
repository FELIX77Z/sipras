@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : (auth()->user()->isGuru() ? 'layouts.guru' : 'layouts.user'))

@section('title', $user->displayRole().' - '.$user->name)

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0">Detail {{ $user->displayRole() }}</h1>
        <span class="badge {{ $user->isActive() ? 'bg-success' : 'bg-danger' }}">{{ $user->status }}</span>
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Nama</dt><dd class="col-sm-9">{{ $user->name }}</dd>
            <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $user->email }}</dd>
            <dt class="col-sm-3">Role</dt><dd class="col-sm-9">{{ $user->displayRole() }}</dd>
            @if ($user->isGuru())
                <dt class="col-sm-3">NIP/NUPTK</dt><dd class="col-sm-9">{{ $user->nip ?? '-' }}</dd>
                <dt class="col-sm-3">Mata Pelajaran</dt><dd class="col-sm-9">{{ $user->major ?? '-' }}</dd>
                <dt class="col-sm-3">Jabatan</dt><dd class="col-sm-9">{{ $user->position ?? '-' }}</dd>
            @else
                <dt class="col-sm-3">NIS/NISN</dt><dd class="col-sm-9">{{ $user->nisn ?: ($user->nis ?? '-') }}</dd>
                <dt class="col-sm-3">Kelas</dt><dd class="col-sm-9">{{ $user->class ?? '-' }}</dd>
                <dt class="col-sm-3">Jurusan</dt><dd class="col-sm-9">{{ $user->major ?? '-' }}</dd>
            @endif
            <dt class="col-sm-3">Telepon</dt><dd class="col-sm-9">{{ $user->phone ?? '-' }}</dd>
        </dl>
        <div class="d-flex gap-2 flex-wrap mt-4">
            @if (auth()->user()->isSuperAdmin() && $user->isGuru())
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit Guru</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Kembali</a>
            @elseif (auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.students.edit', $user) }}" class="btn btn-primary">Edit Siswa</a>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Kembali</a>
            @elseif (auth()->user()->isGuru())
                <a href="{{ route('guru.students.edit', $user) }}" class="btn btn-primary">Edit Siswa</a>
                <a href="{{ route('guru.students.index') }}" class="btn btn-outline-secondary">Kembali</a>
            @endif
        </div>
    </div>
</div>
@endsection
