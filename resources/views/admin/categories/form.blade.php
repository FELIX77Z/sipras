@extends('layouts.admin')

@section('title', $mode === 'edit' ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header"><h4 class="mb-0">{{ $mode === 'edit' ? 'Edit Kategori' : 'Tambah Kategori' }}</h4></div>
    <div class="card-body">
        <form action="{{ $mode === 'edit' ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
            @csrf
            @if ($mode === 'edit')
                @method('PUT')
            @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $category->nama) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $category->deskripsi) }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
