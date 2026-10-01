@extends('layouts.admin')

@section('title', 'Data Pengguna')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Data Guru</h4>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Tambah Guru</a>
    </div>
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 p-3 border-bottom">
        <div class="col-md-7"><label for="teacher-search" class="visually-hidden">Cari Guru</label><input id="teacher-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama, email, NIP/NUPTK"></div>
        <div class="col-md-3"><label for="teacher-status" class="visually-hidden">Filter status Guru</label><select id="teacher-status" name="status" class="form-select"><option value="">Semua status</option><option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option><option value="nonaktif" @selected(in_array($filters['status'] ?? '', ['nonaktif', 'inactive'], true))>Nonaktif</option></select></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary" type="submit">Terapkan</button><a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Reset</a></div>
    </form>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-secondary">{{ $user->displayRole() }}</span></td>
                            <td><span class="badge {{ $user->status === 'aktif' ? 'bg-success' : 'bg-danger' }}">{{ $user->status }}</span></td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @if ($user->isGuru() || $user->isUser())
                                    <form action="{{ route('impersonate', $user) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-info" type="submit">Masuk sebagai {{ $user->displayRole() }}</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning" type="submit">{{ $user->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary" type="submit">Kirim Tautan Reset</button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Hapus pengguna ini?')">Hapus</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $users->links() }}</div>
    </div>
</div>
@endsection
