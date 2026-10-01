@extends('layouts.admin')

@section('title', $mode === 'edit' ? 'Edit Pengguna' : 'Tambah Pengguna')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header"><h4 class="mb-0">{{ $mode === 'edit' ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h4></div>
    <div class="card-body">
        <form action="{{ $mode === 'edit' ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if ($mode === 'edit')
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="col-md-6"><label class="form-label">NIP/NUPTK</label><input type="text" name="nip" class="form-control" value="{{ old('nip', $user->nip) }}"></div>
                <div class="col-md-6"><label class="form-label">Mata Pelajaran</label><input type="text" name="major" class="form-control" value="{{ old('major', $user->major) }}"></div>
                <div class="col-md-6"><label class="form-label">Jabatan</label><input type="text" name="position" class="form-control" value="{{ old('position', $user->position) }}"></div>
                <div class="col-md-6"><label class="form-label">No. HP</label><input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
                <div class="col-md-6"><label class="form-label">Role</label><input class="form-control" value="Guru" readonly></div>
                @if ($mode === 'create')
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                @endif
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
