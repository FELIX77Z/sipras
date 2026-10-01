@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Data Siswa</h4>
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">Tambah Siswa</a>
    </div>
    <form method="GET" action="{{ route('admin.students.index') }}" class="row g-2 p-3 border-bottom">
        <div class="col-md-7"><label for="student-search" class="visually-hidden">Cari Siswa</label><input id="student-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama, email, NIS/NISN"></div>
        <div class="col-md-3"><label for="student-status" class="visually-hidden">Filter status Siswa</label><select id="student-status" name="status" class="form-select"><option value="">Semua status</option><option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option><option value="nonaktif" @selected(in_array($filters['status'] ?? '', ['nonaktif', 'inactive'], true))>Nonaktif</option></select></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary" type="submit">Terapkan</button><a class="btn btn-outline-secondary" href="{{ route('admin.students.index') }}">Reset</a></div>
    </form>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->class ?? '-' }}</td>
                            <td>{{ $student->major ?? '-' }}</td>
                            <td><span class="badge {{ $student->status === 'aktif' ? 'bg-success' : 'bg-danger' }}">{{ $student->status ?? 'aktif' }}</span></td>
                            <td><div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('impersonate', $student) }}" method="POST">@csrf<button class="btn btn-sm btn-outline-info" type="submit">Masuk sebagai Siswa</button></form>
                                <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST">@csrf<button class="btn btn-sm btn-outline-warning" type="submit">{{ $student->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                                <form action="{{ route('admin.students.reset-password', $student) }}" method="POST">@csrf<button class="btn btn-sm btn-outline-secondary" type="submit">Kirim Tautan Reset</button></form>
                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Hapus data siswa ini?')">Hapus</button></form>
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $students->links() }}</div>
    </div>
</div>
@endsection
