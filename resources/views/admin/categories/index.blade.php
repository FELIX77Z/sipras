@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Kategori Pengaduan</h4>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">Tambah Kategori</a>
    </div>
    <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 p-3 border-bottom"><div class="col-md-9"><label for="category-search" class="visually-hidden">Cari kategori</label><input id="category-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Cari kategori"></div><div class="col-md-3 d-flex gap-2"><button class="btn btn-primary" type="submit">Cari</button><a class="btn btn-outline-secondary" href="{{ route('admin.categories.index') }}">Reset</a></div></form>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->nama }}</td>
                            <td>{{ $category->deskripsi ?? '-' }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $categories->links() }}</div>
    </div>
</div>
@endsection
